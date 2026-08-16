<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require dirname(__DIR__) . '/includes/pledge_store.php';

try {
    $database = aor_database();
    $integrity = (string) $database->query('PRAGMA integrity_check')->fetchColumn();
    $columns = $database->query('PRAGMA table_info(pledges)')->fetchAll();
    $indexes = $database->query(
        "SELECT name FROM sqlite_schema WHERE type = 'index' AND tbl_name = 'pledges' ORDER BY name"
    )->fetchAll(PDO::FETCH_COLUMN);
    $queryPlan = $database->query(
        "EXPLAIN QUERY PLAN
         SELECT public_id, display_name, location, pledge_text, pledge_source, pledge_version, submitted_at_utc
         FROM pledges
         ORDER BY submitted_at_utc DESC, id DESC
         LIMIT 100"
    )->fetchAll();

    $columnNames = array_map(static fn (array $column): string => (string) $column['name'], $columns);
    $planDetails = array_map(static fn (array $row): string => (string) $row['detail'], $queryPlan);
    $expectedIndexPresent = in_array('idx_pledges_submitted', $indexes, true);
    $clearMigration = $database->prepare('SELECT 1 FROM aor_migrations WHERE name = :name');
    $clearMigration->execute([':name' => aor_clear_migration_name()]);
    $clearMigrationApplied = $clearMigration->fetchColumn() !== false;
    $expectedIndexUsed = false;
    foreach ($planDetails as $detail) {
        if (str_contains($detail, 'idx_pledges_submitted')) {
            $expectedIndexUsed = true;
            break;
        }
    }

    echo 'Integrity: ' . $integrity . PHP_EOL;
    echo 'Columns: ' . implode(', ', $columnNames) . PHP_EOL;
    echo 'Indexes: ' . implode(', ', $indexes) . PHP_EOL;
    echo 'Public-list query plan: ' . implode(' | ', $planDetails) . PHP_EOL;
    echo 'Pre-location pledge clear applied: ' . ($clearMigrationApplied ? 'yes' : 'no') . PHP_EOL;

    if ($integrity !== 'ok' || !$expectedIndexPresent || !$expectedIndexUsed || !$clearMigrationApplied) {
        exit(1);
    }
} catch (Throwable) {
    fwrite(STDERR, "The pledge database check failed.\n");
    exit(1);
}
