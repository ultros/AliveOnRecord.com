<?php
declare(strict_types=1);

require_once __DIR__ . '/pledge_store.php';

function aor_render_page_start(array $options): void
{
    $title = (string) ($options['title'] ?? 'Alive On Record');
    $description = (string) ($options['description'] ?? 'A public pledge for life, connection, and support.');
    $canonicalPath = ltrim((string) ($options['canonical_path'] ?? ''), '/');
    $canonicalUrl = 'https://aliveonrecord.com/' . $canonicalPath;
    $active = (string) ($options['active'] ?? '');
    $schemaType = (string) ($options['schema_type'] ?? 'WebPage');
    $nonce = (string) ($options['nonce'] ?? '');
    $dateModified = (string) ($options['date_modified'] ?? '2026-08-16');

    $schema = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'WebSite',
                '@id' => 'https://aliveonrecord.com/#website',
                'url' => 'https://aliveonrecord.com/',
                'name' => 'Alive On Record',
                'description' => 'A public pledge for life, connection, and support, with suicide prevention resources.',
                'publisher' => ['@id' => 'https://aliveonrecord.com/#organization'],
            ],
            [
                '@type' => 'Organization',
                '@id' => 'https://aliveonrecord.com/#organization',
                'name' => 'Alive On Record',
                'url' => 'https://aliveonrecord.com/',
                'email' => 'jesse.shelley@aliveonrecord.com',
            ],
            [
                '@type' => $schemaType,
                '@id' => $canonicalUrl . '#webpage',
                'url' => $canonicalUrl,
                'name' => $title,
                'description' => $description,
                'isPartOf' => ['@id' => 'https://aliveonrecord.com/#website'],
                'dateModified' => $dateModified,
                'inLanguage' => 'en-US',
            ],
        ],
    ];

    $schemaJson = json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    ?>
<!doctype html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= aor_escape($title) ?></title>
    <meta name="description" content="<?= aor_escape($description) ?>">
    <meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">
    <meta name="theme-color" content="#0a0d0b">
    <link rel="canonical" href="<?= aor_escape($canonicalUrl) ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Alive On Record">
    <meta property="og:locale" content="en_US">
    <meta property="og:url" content="<?= aor_escape($canonicalUrl) ?>">
    <meta property="og:title" content="<?= aor_escape($title) ?>">
    <meta property="og:description" content="<?= aor_escape($description) ?>">
    <meta property="og:image" content="https://aliveonrecord.com/media/og.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="Alive On Record — Take the next safe step. A public pledge for life, connection and support.">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= aor_escape($title) ?>">
    <meta name="twitter:description" content="<?= aor_escape($description) ?>">
    <meta name="twitter:image" content="https://aliveonrecord.com/media/og.png">
    <link rel="stylesheet" href="community.css">
    <?php if ($schemaJson !== false): ?>
        <script type="application/ld+json" nonce="<?= aor_escape($nonce) ?>"><?= $schemaJson ?></script>
    <?php endif; ?>
</head>
<body>
<a class="skip-link" href="#main-content">Skip to main content</a>

<div class="crisis-bar" role="region" aria-label="Immediate crisis help">
    <div class="crisis-bar__inner">
        <strong>Need help now?</strong>
        <span>U.S. or Canada: call or text <a href="tel:988">988</a></span>
        <a href="help-now.php">See crisis options</a>
    </div>
</div>

<header class="site-bar">
    <div class="site-bar__inner">
        <a class="brand" href="index.php" aria-label="Alive On Record home">Alive On Record</a>
        <nav class="site-nav" aria-label="Primary navigation">
            <a href="take-pledge.php"<?= $active === 'pledge' ? ' aria-current="page"' : '' ?>>Take the pledge</a>
            <a href="pledges.php"<?= $active === 'wall' ? ' aria-current="page"' : '' ?>>Public pledges</a>
            <a href="suicide-prevention.php"<?= $active === 'prevention' ? ' aria-current="page"' : '' ?>>Prevention</a>
            <a href="help-someone.php"<?= $active === 'help-someone' ? ' aria-current="page"' : '' ?>>Help someone</a>
        </nav>
    </div>
</header>
<?php
}

function aor_render_page_end(): void
{
    ?>
<footer class="site-footer">
    <div class="site-footer__inner site-footer__grid">
        <div>
            <strong>ALIVE ON RECORD</strong>
            <p>A public pledge for life, connection, and support.</p>
        </div>
        <nav aria-label="Prevention resources">
            <a href="help-now.php">Get help now</a>
            <a href="suicide-prevention.php">Suicide prevention</a>
            <a href="help-someone.php">Help someone</a>
            <a href="safety-plan.php">Make a safety plan</a>
        </nav>
        <nav aria-label="Site information">
            <a href="take-pledge.php">Take the pledge</a>
            <a href="pledges.php">Public pledges</a>
            <a href="mailto:jesse.shelley@aliveonrecord.com">Pledge removal requests</a>
        </nav>
    </div>
    <div class="site-footer__bottom">AliveOnRecord.com does not provide medical care or emergency services. A pledge is not a safety plan, risk assessment, or substitute for professional help.</div>
</footer>
</body>
</html>
<?php
}
