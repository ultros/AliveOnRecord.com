<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require dirname(__DIR__) . '/includes/pledge_store.php';

$publicId = strtoupper(trim((string) ($argv[1] ?? '')));
$removalCode = strtoupper(trim((string) ($argv[2] ?? '')));

if (
    !preg_match('/^AOR-PLEDGE-[A-F0-9]{12}$/', $publicId)
    || !preg_match('/^AOR-REMOVE-[A-F0-9]{24}$/', $removalCode)
) {
    fwrite(STDERR, "Usage: php bin/remove-pledge.php AOR-PLEDGE-XXXXXXXXXXXX AOR-REMOVE-XXXXXXXXXXXXXXXXXXXXXXXX\n");
    exit(2);
}

try {
    if (!aor_remove_pledge($publicId, $removalCode)) {
        fwrite(STDERR, "The pledge ID and removal code did not match.\n");
        exit(1);
    }

    fwrite(STDOUT, "Permanently removed {$publicId} from the pledge database.\n");
} catch (Throwable) {
    fwrite(STDERR, "The pledge database could not be updated.\n");
    exit(1);
}
