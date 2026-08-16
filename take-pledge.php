<?php
declare(strict_types=1);

require __DIR__ . '/includes/site_shell.php';

$nonce = base64_encode(random_bytes(18));
aor_send_security_headers($nonce);
aor_start_secure_session();

$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if (!in_array($requestMethod, ['GET', 'POST'], true)) {
    header('Allow: GET, POST');
    http_response_code(405);
    exit;
}

$config = aor_config();
$errors = [];
$displayName = '';
$databaseReady = true;
$confirmation = $_SESSION['pledge_confirmation'] ?? null;
unset($_SESSION['pledge_confirmation']);

try {
    aor_database();
} catch (Throwable $exception) {
    error_log('Alive On Record pledge database unavailable: ' . $exception->getMessage());
    $databaseReady = false;
}

if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_SESSION['form_started_at']) || !is_int($_SESSION['form_started_at'])) {
    $_SESSION['form_started_at'] = time();
}

if ($requestMethod === 'POST') {
    $contentLength = isset($_SERVER['CONTENT_LENGTH']) ? (int) $_SERVER['CONTENT_LENGTH'] : 0;
    $postedToken = isset($_POST['csrf_token']) && is_string($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    $honeypot = isset($_POST['website']) && is_string($_POST['website']) ? trim($_POST['website']) : '';
    $displayName = isset($_POST['display_name']) && is_string($_POST['display_name'])
        ? aor_normalize_display_name($_POST['display_name'])
        : '';
    $formAge = time() - (int) ($_SESSION['form_started_at'] ?? time());
    $lastSubmission = (int) ($_SESSION['last_submission_at'] ?? 0);

    if (!$databaseReady) {
        $errors[] = 'The pledge service is temporarily unavailable. Please try again later.';
    }

    if ($contentLength > 16384) {
        $errors[] = 'The submission was larger than expected.';
    }

    if (!hash_equals((string) $_SESSION['csrf_token'], $postedToken)) {
        $errors[] = 'This form expired. Please reload the page and try again.';
    }

    if ($honeypot !== '' || $formAge < 2 || $formAge > 7200) {
        $errors[] = 'The form could not be verified. Please reload the page and try again.';
    }

    $displayNameLength = aor_text_length($displayName);
    if ($displayNameLength < 2 || $displayNameLength > 80) {
        $errors[] = 'Enter a public display name between 2 and 80 characters.';
    }

    if (($_POST['life_pledge'] ?? null) !== '1') {
        $errors[] = 'Confirm the life and safety pledge before submitting.';
    }

    if (($_POST['identity_notice'] ?? null) !== '1') {
        $errors[] = 'Confirm that you understand identity is not independently verified.';
    }

    if (($_POST['publication_consent'] ?? null) !== '1') {
        $errors[] = 'Consent to publication of the listed public fields is required.';
    }

    if ($lastSubmission > 0 && time() - $lastSubmission < 60) {
        $errors[] = 'Please wait before submitting another pledge from this session.';
    }

    if ($errors === []) {
        try {
            $pledge = aor_create_pledge($displayName);
            session_regenerate_id(true);
            $_SESSION['pledge_confirmation'] = $pledge;
            $_SESSION['last_submission_at'] = time();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            unset($_SESSION['form_started_at']);
            session_write_close();
            header('Location: take-pledge.php', true, 303);
            exit;
        } catch (Throwable $exception) {
            error_log('Alive On Record pledge insert failed: ' . $exception->getMessage());
            $errors[] = 'The pledge could not be saved. Please try again later.';
        }
    }

    $_SESSION['form_started_at'] = time();
}
?>
<?php
aor_render_page_start([
    'title' => 'Take the Life & Safety Pledge | Alive On Record',
    'description' => 'Make a voluntary public pledge to choose the next safe step, seek connection, and contact support when suicidal thoughts or overwhelming distress arise.',
    'canonical_path' => 'take-pledge.php',
    'active' => 'pledge',
    'schema_type' => 'WebPage',
    'nonce' => $nonce,
]);
?>

<main id="main-content">
    <p class="eyebrow">A voluntary public commitment</p>
    <h1>Take the next safe step.</h1>
    <p class="lede">Publish a dated commitment to keep reaching for life, tell someone when you need help, and choose connection over isolation.</p>

    <div class="page-grid">
        <section class="panel" aria-labelledby="form-title">
            <h2 id="form-title">Your public pledge</h2>
            <p>Read the pledge carefully. Submit it only if it reflects your present intent.</p>

            <blockquote class="pledge-words">
                I choose to keep reaching for life and connection. If thoughts of suicide or self-harm arise, I will pause, tell someone, create distance from immediate danger, and contact crisis or professional support. I understand that asking for help is an act of strength. I make this pledge voluntarily and in good faith as a commitment to take the next safe step.
            </blockquote>

            <div class="notice" role="note">
                This pledge is not a “no-suicide contract.” It does not prove current safety, lower clinical risk, verify identity, or replace emergency help, professional care, or a collaborative safety plan.
            </div>

            <?php if (is_array($confirmation)): ?>
                <?php
                $removalSubject = rawurlencode('Pledge removal request: ' . (string) $confirmation['public_id']);
                $removalBody = rawurlencode(
                    "Please remove this public pledge.\n\n"
                    . 'Pledge ID: ' . (string) $confirmation['public_id'] . "\n"
                    . 'Removal code: ' . (string) $confirmation['removal_code']
                );
                $removalHref = 'mailto:' . $config['removal_email'] . '?subject=' . $removalSubject . '&body=' . $removalBody;
                ?>
                <div class="success-box" role="status">
                    <h2>Your pledge is now on the public record.</h2>
                    <span><?= aor_escape($confirmation['display_name']) ?> · <?= aor_escape(aor_format_submission_date($confirmation['submitted_at_utc'])) ?></span>
                    <strong class="success-id"><?= aor_escape($confirmation['public_id']) ?></strong>
                    <span class="label">Private removal code — shown once</span>
                    <strong class="success-id"><?= aor_escape($confirmation['removal_code']) ?></strong>
                    <p>Keep both codes. To request removal, email <a href="<?= aor_escape($removalHref) ?>"><?= aor_escape($config['removal_email']) ?></a> and include the pledge ID and private removal code.</p>
                </div>
            <?php endif; ?>

            <?php if ($errors !== []): ?>
                <div class="error-box" role="alert">
                    <h2>Please correct the following:</h2>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= aor_escape($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($databaseReady): ?>
                <form method="post" action="take-pledge.php">
                    <input type="hidden" name="csrf_token" value="<?= aor_escape($_SESSION['csrf_token']) ?>">

                    <div class="honeypot" aria-hidden="true">
                        <label for="website">Leave this field empty</label>
                        <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="field">
                        <label for="display-name">Public display name</label>
                        <input id="display-name" name="display_name" type="text" minlength="2" maxlength="80" required autocomplete="name" value="<?= aor_escape($displayName) ?>" aria-describedby="display-name-note">
                        <small class="field-note" id="display-name-note">This can be your name or a consistent pseudonym. It will be public.</small>
                    </div>

                    <fieldset class="checks">
                        <legend>Required confirmations</legend>
                        <label class="check-row">
                            <input type="checkbox" name="life_pledge" value="1" required>
                            <span>I have read the pledge above, it reflects a commitment I choose to make now, and I make it voluntarily.</span>
                        </label>
                        <label class="check-row">
                            <input type="checkbox" name="identity_notice" value="1" required>
                            <span>I understand that Alive On Record does not independently verify my identity or continuously verify my safety.</span>
                        </label>
                        <label class="check-row">
                            <input type="checkbox" name="publication_consent" value="1" required>
                            <span>I consent to public display of my display name, pledge ID, pledge version, and submission date/time.</span>
                        </label>
                    </fieldset>

                    <div class="button-row">
                        <button class="button" type="submit">Publish my pledge</button>
                        <a class="button button--secondary" href="pledges.php">View public pledges</a>
                    </div>
                </form>
            <?php else: ?>
                <div class="error-box" role="alert">
                    <h2>Submissions are temporarily unavailable.</h2>
                    <p>The public pledge page remains available. Please try this form again later.</p>
                </div>
            <?php endif; ?>
        </section>

        <aside class="panel" aria-labelledby="privacy-title">
            <h2 id="privacy-title">What is recorded</h2>
            <p>The pledge database is intentionally minimal.</p>
            <ul class="privacy-list">
                <li><strong>Public display name</strong>Published exactly as entered after whitespace normalization.</li>
                <li><strong>Pledge record</strong>A random pledge ID, pledge version, and UTC submission time.</li>
                <li><strong>No email collection</strong>The application does not ask for or store participant email addresses.</li>
                <li><strong>No IP field</strong>The application does not add IP addresses to the pledge database. Normal hosting access logs may still exist.</li>
                <li><strong>Removal code</strong>A private code is shown once. Only its SHA-256 hash is stored, so the original code cannot be recovered from the database.</li>
                <li><strong>Identity status</strong>Self-submitted and not independently verified.</li>
                <li><strong>Clinical limits</strong>A public pledge is not a risk assessment, treatment, or safety plan.</li>
            </ul>
            <p class="removal-note">Removal requests: <a href="mailto:<?= aor_escape($config['removal_email']) ?>?subject=Alive%20On%20Record%20pledge%20removal%20request"><?= aor_escape($config['removal_email']) ?></a>. Include the pledge ID and private removal code.</p>
        </aside>
    </div>
</main>

<?php aor_render_page_end(); ?>
