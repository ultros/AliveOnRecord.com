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
$pledgeOptions = aor_pledge_options();
$errors = [];
$displayName = '';
$selectedPledge = 'whole-life';
$customPledge = '';
$pledgeText = '';
$pledgeSource = '';
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
    $selectedPledge = isset($_POST['pledge_choice']) && is_string($_POST['pledge_choice'])
        ? trim($_POST['pledge_choice'])
        : '';
    $customPledge = isset($_POST['custom_pledge']) && is_string($_POST['custom_pledge'])
        ? aor_normalize_pledge_text($_POST['custom_pledge'])
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
    } elseif (!aor_public_text_is_affirmative($displayName)) {
        $errors[] = 'Choose a public name suited to this affirmative life-pledge record.';
        $displayName = '';
    }

    if ($selectedPledge === 'custom') {
        $customLength = aor_text_length($customPledge);
        if ($customLength < 20 || $customLength > 500) {
            $errors[] = 'Write a custom pledge between 20 and 500 characters.';
        } elseif (!aor_public_text_is_affirmative($customPledge)) {
            $errors[] = 'Keep your custom pledge entirely affirmative and centered on your permanent commitment to living fully.';
            $customPledge = '';
        } else {
            $pledgeText = $customPledge;
            $pledgeSource = 'custom';
        }
    } elseif (array_key_exists($selectedPledge, $pledgeOptions)) {
        $pledgeText = $pledgeOptions[$selectedPledge];
        $pledgeSource = $selectedPledge;
    } else {
        $errors[] = 'Choose one of the listed pledges or write your own.';
    }

    if (($_POST['life_pledge'] ?? null) !== '1') {
        $errors[] = 'Confirm your permanent commitment before publishing.';
    }

    if (($_POST['authorship_confirmation'] ?? null) !== '1') {
        $errors[] = 'Confirm that you are submitting this pledge as your own commitment.';
    }

    if (($_POST['publication_consent'] ?? null) !== '1') {
        $errors[] = 'Consent to publication of the listed public fields is required.';
    }

    if ($lastSubmission > 0 && time() - $lastSubmission < 60) {
        $errors[] = 'Please wait before submitting another pledge from this session.';
    }

    if ($errors === []) {
        try {
            $pledge = aor_create_pledge($displayName, $pledgeText, $pledgeSource);
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

aor_render_page_start([
    'title' => 'Make a Permanent Life Pledge | Alive On Record',
    'description' => 'Choose a permanent pledge to live your whole life fully, or write your own commitment and place it on the public record.',
    'canonical_path' => 'take-pledge.php',
    'active' => 'pledge',
    'schema_type' => 'WebPage',
    'nonce' => $nonce,
    'affirmative' => true,
]);
?>

<main id="main-content">
    <p class="eyebrow">A permanent public commitment</p>
    <h1>Choose your whole life.</h1>
    <p class="lede">Choose a pledge that speaks for you—or write your own—and place your commitment to living fully on the public record.</p>

    <div class="page-grid">
        <section class="panel" aria-labelledby="form-title">
            <h2 id="form-title">Your permanent life pledge</h2>
            <p>Take the words seriously. Your pledge represents a wholehearted commitment to the entire life ahead of you, for all your days.</p>

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
                    <h2>Your permanent life pledge is now on the public record.</h2>
                    <blockquote class="confirmation-pledge"><?= aor_escape($confirmation['pledge_text']) ?></blockquote>
                    <span><?= aor_escape($confirmation['display_name']) ?> · <?= aor_escape(aor_format_submission_date($confirmation['submitted_at_utc'])) ?></span>
                    <strong class="success-id"><?= aor_escape($confirmation['public_id']) ?></strong>
                    <span class="label">Private removal code — shown once</span>
                    <strong class="success-id"><?= aor_escape($confirmation['removal_code']) ?></strong>
                    <p>Keep both codes. To request removal of the public record, email <a href="<?= aor_escape($removalHref) ?>"><?= aor_escape($config['removal_email']) ?></a> and include the pledge ID and private removal code.</p>
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
                        <small class="field-note" id="display-name-note">Use your name or a public name you choose. It will appear with your pledge.</small>
                    </div>

                    <fieldset class="pledge-options">
                        <legend>Choose your pledge</legend>
                        <?php foreach ($pledgeOptions as $optionKey => $optionText): ?>
                            <label class="pledge-option">
                                <input type="radio" name="pledge_choice" value="<?= aor_escape($optionKey) ?>"<?= $selectedPledge === $optionKey ? ' checked' : '' ?> required>
                                <span><?= aor_escape($optionText) ?></span>
                            </label>
                        <?php endforeach; ?>
                        <label class="pledge-option pledge-option--custom">
                            <input type="radio" name="pledge_choice" value="custom"<?= $selectedPledge === 'custom' ? ' checked' : '' ?> required>
                            <span><strong>Write my own pledge</strong> — a personal, permanent commitment to living my life fully.</span>
                        </label>
                    </fieldset>

                    <div class="field custom-pledge-field">
                        <label for="custom-pledge">Your custom pledge</label>
                        <textarea id="custom-pledge" name="custom_pledge" minlength="20" maxlength="500" rows="5" aria-describedby="custom-pledge-note"><?= aor_escape($customPledge) ?></textarea>
                        <small class="field-note" id="custom-pledge-note">Complete this only when “Write my own pledge” is selected. Use 20–500 characters expressing your full and permanent commitment to life.</small>
                    </div>

                    <fieldset class="checks">
                        <legend>Your confirmations</legend>
                        <label class="check-row">
                            <input type="checkbox" name="life_pledge" value="1" required>
                            <span>I make this pledge as my permanent, wholehearted commitment to living my life fully, for all my days.</span>
                        </label>
                        <label class="check-row">
                            <input type="checkbox" name="authorship_confirmation" value="1" required>
                            <span>I am submitting this pledge as my own commitment under the public name shown above.</span>
                        </label>
                        <label class="check-row">
                            <input type="checkbox" name="publication_consent" value="1" required>
                            <span>I consent to public display of my name, exact pledge, pledge ID, version, and submission date and time.</span>
                        </label>
                    </fieldset>

                    <div class="button-row">
                        <button class="button" type="submit">Place my pledge on record</button>
                        <a class="button button--secondary" href="pledges.php">Read public pledges</a>
                    </div>
                </form>
            <?php else: ?>
                <div class="error-box" role="alert">
                    <h2>New entries are temporarily unavailable.</h2>
                    <p>Please return later to place your pledge on the public record.</p>
                </div>
            <?php endif; ?>
        </section>

        <aside class="panel" aria-labelledby="record-title">
            <h2 id="record-title">What your record includes</h2>
            <p>Each entry is intentionally simple and lasting.</p>
            <ul class="privacy-list">
                <li><strong>Your public name</strong>Displayed with the pledge exactly as submitted after spacing is normalized.</li>
                <li><strong>Your exact pledge</strong>The selected statement or your custom words appear on the public pledge wall.</li>
                <li><strong>Your place in the record</strong>A unique pledge ID, pledge version, and UTC submission time accompany the commitment.</li>
                <li><strong>Your private removal code</strong>A one-time code is shown after publication. Keep it with your pledge ID.</li>
                <li><strong>Your privacy</strong>No participant email address or IP-address field is added to the pledge database.</li>
            </ul>
            <p class="removal-note">Record removal requests: <a href="mailto:<?= aor_escape($config['removal_email']) ?>?subject=Alive%20On%20Record%20pledge%20removal%20request"><?= aor_escape($config['removal_email']) ?></a>. Include the pledge ID and private removal code.</p>
        </aside>
    </div>
</main>

<?php aor_render_page_end(['affirmative' => true]); ?>
