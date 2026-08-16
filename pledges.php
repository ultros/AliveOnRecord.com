<?php
declare(strict_types=1);

require __DIR__ . '/includes/site_shell.php';

$nonce = base64_encode(random_bytes(18));
aor_send_security_headers($nonce);

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
} catch (Throwable $exception) {
    error_log('Alive On Record pledge wall unavailable: ' . $exception->getMessage());
    $databaseReady = false;
}
?>
<?php
aor_render_page_start([
    'title' => 'Permanent Life Pledges | Alive On Record',
    'description' => 'Read permanent public commitments from people who have pledged to live their whole lives fully, for all their days.',
    'canonical_path' => 'pledges.php',
    'active' => 'wall',
    'schema_type' => 'CollectionPage',
    'nonce' => $nonce,
    'affirmative' => true,
]);
?>

<main id="main-content">
    <p class="eyebrow">A lasting community record</p>
    <h1>Permanent life pledges</h1>
    <p class="lede">Words from people who have chosen their whole lives and placed that commitment on the public record.</p>

    <div class="notice" role="note">
        Each new entry displays the exact pledge chosen or written by its author. Earlier entries remain part of the same permanent-life record.
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
                    <span class="pledge-card__badge">Permanent life pledge</span>
                    <h2><?= aor_escape($pledge['display_name']) ?></h2>
                    <?php $pledgeText = trim((string) ($pledge['pledge_text'] ?? '')); ?>
                    <p><?= $pledgeText !== '' ? '“' . aor_escape($pledgeText) . '”' : 'Permanent life commitment recorded.' ?></p>
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

<?php aor_render_page_end(['affirmative' => true]); ?>
