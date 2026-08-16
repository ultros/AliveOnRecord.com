<?php
declare(strict_types=1);

require __DIR__ . '/includes/pledge_store.php';

aor_send_security_headers();
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
} catch (Throwable) {
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
        } catch (Throwable) {
            $errors[] = 'The pledge could not be saved. Please try again later.';
        }
    }

    $_SESSION['form_started_at'] = time();
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Take the Pledge | Alive On Record</title>
    <meta name="description" content="Make and publish a personal pledge to preserve life, safety, agency, and continuity.">
    <meta name="theme-color" content="#0a0d0b">
    <meta name="robots" content="index,follow">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://aliveonrecord.com/take-pledge.php">
    <meta property="og:title" content="Take the Pledge | Alive On Record">
    <meta property="og:description" content="Make and publish a personal pledge to preserve life, safety, agency, and continuity.">
    <link rel="canonical" href="https://aliveonrecord.com/take-pledge.php">
    <link rel="stylesheet" href="community.css">
</head>
<body>
<a class="skip-link" href="#main-content">Skip to main content</a>

<header class="site-bar">
    <div class="site-bar__inner">
        <a class="brand" href="index.php">Alive On Record</a>
        <nav class="site-nav" aria-label="Pledge navigation">
            <a href="pledge.html">Jesse’s pledge</a>
            <a href="pledges.php">Public pledges</a>
        </nav>
    </div>
</header>

<main id="main-content">
    <p class="eyebrow">A public statement of personal intent</p>
    <h1>Take the pledge</h1>
    <p class="lede">Add your own dated pledge to preserve your life and safety, seek support when needed, and communicate your intentions clearly.</p>

    <div class="page-grid">
        <section class="panel" aria-labelledby="form-title">
            <h2 id="form-title">Your public pledge</h2>
            <p>Read the pledge carefully. Submit it only if it reflects your present intent.</p>

            <blockquote class="pledge-words">
                I choose life. I intend to remain alive, preserve my health and safety, and seek appropriate support when I cannot manage a crisis alone. I do not intend to harm myself or deliberately cause my own death. I make this pledge voluntarily and in good faith as a statement of my present intent.
            </blockquote>

            <div class="notice" role="note">
                A submitted pledge records intent at the time of submission. It is not continuous proof of anyone’s safety, identity, or current condition, and it is not a substitute for emergency or professional support.
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
                            <span>I have read the pledge above, it reflects my present intent, and I make it voluntarily.</span>
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
            </ul>
            <p class="removal-note">Removal requests: <a href="mailto:<?= aor_escape($config['removal_email']) ?>?subject=Alive%20On%20Record%20pledge%20removal%20request"><?= aor_escape($config['removal_email']) ?></a>. Include the pledge ID and private removal code.</p>
        </aside>
    </div>
</main>

<footer class="site-footer">
    <div class="site-footer__inner">
        <strong>ALIVEONRECORD.COM</strong>
        <span>Public pledge service · Pledge version <?= aor_escape($config['pledge_version']) ?></span>
    </div>
</footer>
</body>
</html>
