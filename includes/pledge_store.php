<?php
declare(strict_types=1);

/**
 * Shared pledge storage and security helpers.
 *
 * This file is local-only application code. The Apache rules block direct web
 * access to /includes/, and this file emits no output when loaded directly.
 */

function aor_config(): array
{
    $configuredPath = getenv('AOR_DB_PATH');
    $defaultPath = dirname(__DIR__, 2)
        . DIRECTORY_SEPARATOR . 'aliveonrecord-data'
        . DIRECTORY_SEPARATOR . 'pledges.sqlite';

    return [
        'database_path' => is_string($configuredPath) && trim($configuredPath) !== ''
            ? trim($configuredPath)
            : $defaultPath,
        'pledge_version' => '3.0',
        'consent_version' => '3.1',
        'removal_email' => 'jesse.shelley@aliveonrecord.com',
        'public_list_limit' => 100,
    ];
}

function aor_escape(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function aor_pledge_options(): array
{
    return [
        'whole-life' => 'I pledge to live my life fully, honor every chapter, and remain committed to my future for all my days.',
        'choose-life' => 'I choose my whole life—permanently. I will keep growing, loving, creating, and showing up for every day ahead.',
        'long-journey' => 'I am here for the long journey. I pledge to live, connect, contribute, and welcome every unwritten chapter.',
        'life-in-full' => 'My life is mine to live in full. I commit to protecting it, building it, and carrying it forward for the rest of my natural life.',
        'every-season' => 'I pledge myself to every season ahead—to the people I will love, the work I will do, the joy I will discover, and the person I will become.',
    ];
}

function aor_clear_migration_name(): string
{
    return '2026-08-16-clear-pre-location-pledges';
}

function aor_path_is_absolute(string $path): bool
{
    return str_starts_with($path, '/')
        || str_starts_with($path, '\\\\')
        || preg_match('/^[A-Za-z]:[\\\\\/]/', $path) === 1;
}

function aor_database(): PDO
{
    static $database = null;

    if ($database instanceof PDO) {
        return $database;
    }

    if (!extension_loaded('pdo_sqlite')) {
        throw new RuntimeException('PDO SQLite extension is not loaded.');
    }

    $config = aor_config();
    $databasePath = $config['database_path'];
    $databaseDirectory = dirname($databasePath);

    if (!aor_path_is_absolute($databasePath)) {
        throw new RuntimeException('The SQLite database path must be absolute.');
    }

    if (!is_dir($databaseDirectory) && !@mkdir($databaseDirectory, 0700, true) && !is_dir($databaseDirectory)) {
        throw new RuntimeException('The SQLite database directory is unavailable.');
    }

    $database = new PDO('sqlite:' . $databasePath, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $database->exec('PRAGMA foreign_keys = ON');
    $database->exec('PRAGMA busy_timeout = 5000');
    $database->exec('PRAGMA journal_mode = WAL');
    $database->exec('PRAGMA secure_delete = ON');

    $database->exec(
        "CREATE TABLE IF NOT EXISTS pledges (
            id INTEGER PRIMARY KEY,
            public_id TEXT NOT NULL UNIQUE,
            display_name TEXT NOT NULL CHECK (length(display_name) BETWEEN 2 AND 80),
            location TEXT NOT NULL CHECK (length(location) BETWEEN 2 AND 100),
            pledge_text TEXT,
            pledge_source TEXT,
            pledge_version TEXT NOT NULL,
            consent_version TEXT NOT NULL,
            removal_token_hash TEXT NOT NULL CHECK (length(removal_token_hash) = 64),
            submitted_at_utc TEXT NOT NULL
        )"
    );

    $columns = $database->query('PRAGMA table_info(pledges)')->fetchAll();
    $columnNames = array_map(static fn (array $column): string => (string) $column['name'], $columns);

    if (!in_array('pledge_text', $columnNames, true)) {
        $database->exec('ALTER TABLE pledges ADD COLUMN pledge_text TEXT');
    }

    if (!in_array('pledge_source', $columnNames, true)) {
        $database->exec('ALTER TABLE pledges ADD COLUMN pledge_source TEXT');
    }

    if (!in_array('location', $columnNames, true)) {
        $database->exec('ALTER TABLE pledges ADD COLUMN location TEXT');
    }

    $database->exec(
        "CREATE TABLE IF NOT EXISTS aor_migrations (
            name TEXT PRIMARY KEY,
            applied_at_utc TEXT NOT NULL
        )"
    );

    $pledgesCleared = false;
    $database->exec('BEGIN IMMEDIATE');
    try {
        $migrationName = aor_clear_migration_name();
        $migrationCheck = $database->prepare('SELECT 1 FROM aor_migrations WHERE name = :name');
        $migrationCheck->execute([':name' => $migrationName]);

        if ($migrationCheck->fetchColumn() === false) {
            $database->exec('DELETE FROM pledges');
            $pledgesCleared = true;
            $recordMigration = $database->prepare(
                'INSERT INTO aor_migrations (name, applied_at_utc) VALUES (:name, :applied_at_utc)'
            );
            $recordMigration->execute([
                ':name' => $migrationName,
                ':applied_at_utc' => (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format(DateTimeInterface::ATOM),
            ]);
        }

        $database->exec('COMMIT');
    } catch (Throwable $exception) {
        $database->exec('ROLLBACK');
        throw $exception;
    }

    if ($pledgesCleared) {
        $database->exec('PRAGMA wal_checkpoint(TRUNCATE)');
    }

    $database->exec(
        "CREATE INDEX IF NOT EXISTS idx_pledges_submitted
        ON pledges(submitted_at_utc DESC, id DESC)"
    );

    $database->exec('PRAGMA optimize');

    return $database;
}

function aor_normalize_display_name(string $name): string
{
    $name = trim($name);
    $name = preg_replace('/[\x00-\x1F\x7F]/u', '', $name) ?? '';
    $name = preg_replace('/\s+/u', ' ', $name) ?? '';

    return trim($name);
}

function aor_normalize_pledge_text(string $pledgeText): string
{
    $pledgeText = trim($pledgeText);
    $pledgeText = preg_replace('/[\x00-\x1F\x7F]/u', ' ', $pledgeText) ?? '';
    $pledgeText = preg_replace('/\s+/u', ' ', $pledgeText) ?? '';

    return trim($pledgeText);
}

function aor_normalize_location(string $location): string
{
    $location = trim($location);
    $location = preg_replace('/[\x00-\x1F\x7F]/u', '', $location) ?? '';
    $location = preg_replace('/\s+/u', ' ', $location) ?? '';

    return trim($location);
}

function aor_location_is_valid(string $location): bool
{
    $length = aor_text_length($location);

    return $length >= 2
        && $length <= 100
        && preg_match("/^[\\p{L}\\p{M}\\p{N} .,'’()\\-]+$/u", $location) === 1;
}

function aor_public_text_is_affirmative(string $text): bool
{
    return preg_match(
        '/\b(?:suicid(?:e|al)|self[-\s]?harm|crisis|emergency|danger|distress|depress(?:ion|ed)?|hopeless(?:ness)?|problem|issue|death|dying|kill(?:ing|ed)?|safe|safety|pain|struggl\w*|suffer\w*)\b/ui',
        $text
    ) !== 1;
}

function aor_text_length(string $value): int
{
    $count = preg_match_all('/./us', $value, $matches);

    return $count === false ? strlen($value) : $count;
}

function aor_create_public_id(): string
{
    return 'AOR-PLEDGE-' . strtoupper(bin2hex(random_bytes(6)));
}

function aor_create_removal_code(): string
{
    return 'AOR-REMOVE-' . strtoupper(bin2hex(random_bytes(12)));
}

function aor_create_pledge(string $displayName, string $location, string $pledgeText, string $pledgeSource): array
{
    $displayName = aor_normalize_display_name($displayName);
    $location = aor_normalize_location($location);
    $pledgeText = aor_normalize_pledge_text($pledgeText);
    $pledgeSource = trim($pledgeSource);
    $pledgeOptions = aor_pledge_options();
    $validSources = array_keys($pledgeOptions);
    $validSources[] = 'custom';

    if (aor_text_length($displayName) < 2 || aor_text_length($displayName) > 80) {
        throw new InvalidArgumentException('The public display name is invalid.');
    }

    if (!aor_public_text_is_affirmative($displayName)) {
        throw new InvalidArgumentException('The public display name is not appropriate for the pledge record.');
    }

    if (!aor_location_is_valid($location)) {
        throw new InvalidArgumentException('The public location is invalid.');
    }

    if (aor_text_length($pledgeText) < 20 || aor_text_length($pledgeText) > 500) {
        throw new InvalidArgumentException('The pledge text is invalid.');
    }

    if (!in_array($pledgeSource, $validSources, true)) {
        throw new InvalidArgumentException('The pledge source is invalid.');
    }

    if ($pledgeSource === 'custom' && !aor_public_text_is_affirmative($pledgeText)) {
        throw new InvalidArgumentException('The custom pledge must be affirmative.');
    }

    if ($pledgeSource !== 'custom' && $pledgeText !== $pledgeOptions[$pledgeSource]) {
        throw new InvalidArgumentException('The prepared pledge text does not match its source.');
    }

    $database = aor_database();
    $config = aor_config();
    $submittedAt = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format(DateTimeInterface::ATOM);
    $removalCode = aor_create_removal_code();
    $removalTokenHash = hash('sha256', $removalCode);

    for ($attempt = 0; $attempt < 3; $attempt++) {
        $publicId = aor_create_public_id();

        try {
            $statement = $database->prepare(
                'INSERT INTO pledges
                    (public_id, display_name, location, pledge_text, pledge_source, pledge_version, consent_version, removal_token_hash, submitted_at_utc)
                 VALUES
                    (:public_id, :display_name, :location, :pledge_text, :pledge_source, :pledge_version, :consent_version, :removal_token_hash, :submitted_at_utc)'
            );
            $statement->execute([
                ':public_id' => $publicId,
                ':display_name' => $displayName,
                ':location' => $location,
                ':pledge_text' => $pledgeText,
                ':pledge_source' => $pledgeSource,
                ':pledge_version' => $config['pledge_version'],
                ':consent_version' => $config['consent_version'],
                ':removal_token_hash' => $removalTokenHash,
                ':submitted_at_utc' => $submittedAt,
            ]);

            return [
                'public_id' => $publicId,
                'display_name' => $displayName,
                'location' => $location,
                'pledge_text' => $pledgeText,
                'pledge_source' => $pledgeSource,
                'pledge_version' => $config['pledge_version'],
                'submitted_at_utc' => $submittedAt,
                'removal_code' => $removalCode,
            ];
        } catch (PDOException $exception) {
            if ($attempt === 2 || !str_contains(strtolower($exception->getMessage()), 'unique')) {
                throw $exception;
            }
        }
    }

    throw new RuntimeException('A pledge ID could not be created.');
}

function aor_list_public_pledges(): array
{
    $config = aor_config();
    $limit = max(1, min(500, (int) $config['public_list_limit']));
    $statement = aor_database()->prepare(
        'SELECT public_id, display_name, location, pledge_text, pledge_source, pledge_version, submitted_at_utc
         FROM pledges
         ORDER BY submitted_at_utc DESC, id DESC
         LIMIT :limit'
    );
    $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
    $statement->execute();

    return $statement->fetchAll();
}

function aor_public_pledge_count(): int
{
    $statement = aor_database()->query('SELECT COUNT(*) FROM pledges');

    return (int) $statement->fetchColumn();
}

function aor_remove_pledge(string $publicId, string $removalCode): bool
{
    $database = aor_database();
    $lookup = $database->prepare('SELECT removal_token_hash FROM pledges WHERE public_id = :public_id');
    $lookup->execute([':public_id' => $publicId]);
    $storedHash = $lookup->fetchColumn();
    $candidateHash = hash('sha256', $removalCode);

    if (!is_string($storedHash) || !hash_equals($storedHash, $candidateHash)) {
        return false;
    }

    $statement = $database->prepare('DELETE FROM pledges WHERE public_id = :public_id');
    $statement->execute([':public_id' => $publicId]);

    return $statement->rowCount() === 1;
}

function aor_format_submission_date(string $date): string
{
    try {
        return (new DateTimeImmutable($date))->setTimezone(new DateTimeZone('UTC'))->format('F j, Y \a\t g:i A \U\T\C');
    } catch (Exception) {
        return 'Date unavailable';
    }
}

function aor_start_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_name('AORPLEDGE');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function aor_send_security_headers(?string $scriptNonce = null): void
{
    $scriptPolicy = $scriptNonce === null ? "'none'" : "'nonce-{$scriptNonce}'";
    header_remove('X-Powered-By');
    header('Content-Type: text/html; charset=UTF-8');
    header("Content-Security-Policy: default-src 'none'; style-src 'self'; script-src {$scriptPolicy}; img-src 'self' data:; font-src 'self'; connect-src 'none'; media-src 'none'; object-src 'none'; frame-src 'none'; frame-ancestors 'none'; form-action 'self'; base-uri 'none'");
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: no-referrer');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=(), usb=()');
    header('X-Frame-Options: DENY');
    header('Cache-Control: no-store, private');
}
