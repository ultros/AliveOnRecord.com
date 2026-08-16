<?php
declare(strict_types=1);

require __DIR__ . '/includes/pledge_store.php';

aor_send_security_headers();

$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if (!in_array($requestMethod, ['GET', 'HEAD'], true)) {
    header('Allow: GET, HEAD');
    http_response_code(405);
    exit;
}

$config = aor_config();
$pledges = [];
$pledgeCount = 0;
$databaseReady = true;

try {
    $pledges = aor_list_public_pledges();
    $pledgeCount = aor_public_pledge_count();
} catch (Throwable) {
    $databaseReady = false;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Public Pledges | Alive On Record</title>
    <meta name="description" content="Self-submitted public pledges to preserve life, safety, agency, and continuity.">
    <meta name="theme-color" content="#0a0d0b">
    <meta name="robots" content="index,follow">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://aliveonrecord.com/pledges.php">
    <meta property="og:title" content="Public Pledges | Alive On Record">
    <meta property="og:description" content="Self-submitted public pledges to preserve life, safety, agency, and continuity.">
    <link rel="canonical" href="https://aliveonrecord.com/pledges.php">
    <link rel="stylesheet" href="community.css">
</head>
<body>
<a class="skip-link" href="#main-content">Skip to main content</a>

<header class="site-bar">
    <div class="site-bar__inner">
        <a class="brand" href="index.php">Alive On Record</a>
        <nav class="site-nav" aria-label="Pledge navigation">
            <a href="pledge.html">Jesse’s pledge</a>
            <a href="take-pledge.php">Take the pledge</a>
        </nav>
    </div>
</header>

<main id="main-content">
    <p class="eyebrow">Community record</p>
    <h1>Public pledges</h1>
    <p class="lede">A record of people who have voluntarily published the Alive On Record life and safety pledge.</p>

    <div class="notice" role="note">
        These pledges are self-submitted. Alive On Record does not independently verify participant identity or continuously verify anyone’s safety or present condition.
    </div>

    <div class="wall-header">
        <div>
            <a class="button" href="take-pledge.php">Take the pledge</a>
        </div>
        <?php if ($databaseReady): ?>
            <span class="wall-count"><?= aor_escape(number_format($pledgeCount)) ?> published <?= $pledgeCount === 1 ? 'pledge' : 'pledges' ?></span>
        <?php endif; ?>
    </div>

    <?php if (!$databaseReady): ?>
        <div class="error-box" role="alert">
            <h2>The pledge record is temporarily unavailable.</h2>
            <p>Please try again later.</p>
        </div>
    <?php elseif ($pledges === []): ?>
        <div class="empty-state">No community pledges have been published yet.</div>
    <?php else: ?>
        <section class="pledge-list" aria-label="Published community pledges">
            <?php foreach ($pledges as $pledge): ?>
                <article class="pledge-card">
                    <span class="pledge-card__badge">Self-submitted · Identity unverified</span>
                    <h2><?= aor_escape($pledge['display_name']) ?></h2>
                    <p>“I choose life and make the Alive On Record life and safety pledge voluntarily and in good faith.”</p>
                    <div class="pledge-card__meta">
                        <?= aor_escape($pledge['public_id']) ?><br>
                        PLEDGE VERSION <?= aor_escape($pledge['pledge_version']) ?><br>
                        SUBMITTED <?= aor_escape(aor_format_submission_date($pledge['submitted_at_utc'])) ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>

    <p class="removal-note">To request permanent removal of a pledge, email <a href="mailto:<?= aor_escape($config['removal_email']) ?>?subject=Alive%20On%20Record%20pledge%20removal%20request"><?= aor_escape($config['removal_email']) ?></a> and include the pledge ID and private removal code issued at submission.</p>
</main>

<footer class="site-footer">
    <div class="site-footer__inner">
        <strong>ALIVEONRECORD.COM</strong>
        <span>Self-submitted pledges · Identity not independently verified</span>
    </div>
</footer>
</body>
</html>
