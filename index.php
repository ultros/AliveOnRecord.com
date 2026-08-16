<?php
declare(strict_types=1);

require __DIR__ . '/includes/site_shell.php';

$nonce = base64_encode(random_bytes(18));
aor_send_security_headers($nonce);

$pledgeCount = null;
try {
    $pledgeCount = aor_public_pledge_count();
} catch (Throwable $exception) {
    error_log('Alive On Record pledge count unavailable: ' . $exception->getMessage());
    // The public site remains useful while the optional registry is unavailable.
}

aor_render_page_start([
    'title' => 'Alive On Record | Make a Permanent Life Pledge',
    'description' => 'Choose a permanent public pledge to live your whole life fully, write your own commitment, and explore separate suicide prevention resources.',
    'canonical_path' => '',
    'active' => 'home',
    'schema_type' => 'WebPage',
    'nonce' => $nonce,
]);
?>

<main id="main-content">
    <section class="landing-hero" aria-labelledby="home-title">
        <div>
            <p class="eyebrow">Your life · All your days</p>
            <h1 id="home-title">Choose your whole life.</h1>
            <p class="lede">Alive On Record is a public place to make a permanent commitment to living fully—through every season, chapter, and unwritten day ahead.</p>
            <div class="button-row">
                <a class="button" href="take-pledge.php">Make your life pledge</a>
                <a class="button button--secondary" href="pledges.php">Read public pledges</a>
            </div>
        </div>
        <aside class="hero-record" aria-label="Community pledge status">
            <span class="hero-record__mark" aria-hidden="true"></span>
            <span class="label">Community record</span>
            <strong><?= $pledgeCount === null ? 'Open' : aor_escape(number_format($pledgeCount)) ?></strong>
            <p><?= $pledgeCount === null ? 'Pledge submissions are available when the registry is online.' : ($pledgeCount === 1 ? 'published pledge' : 'published pledges') ?></p>
            <a href="pledges.php">View the public pledge wall →</a>
        </aside>
    </section>

    <section class="truth-panel" aria-labelledby="pledge-means-title">
        <div>
            <p class="eyebrow">What this pledge means</p>
            <h2 id="pledge-means-title">A permanent yes to every chapter ahead.</h2>
        </div>
        <div>
            <p>Choose from five carefully written life pledges or write your own. Every option expresses an enduring commitment to live the whole of your life fully and deliberately.</p>
            <p><strong>Your exact words become part of the public record.</strong> The commitment is permanent; the published record can still be removed using the private code issued to you.</p>
        </div>
    </section>

    <section class="section-block" aria-labelledby="how-title">
        <div class="section-heading-inline">
            <p class="eyebrow">How it works</p>
            <h2 id="how-title">One clear statement. Three simple steps.</h2>
        </div>
        <ol class="steps-grid">
            <li><span>01</span><h3>Choose your words</h3><p>Select one of five permanent-life pledges or write a personal commitment of your own.</p></li>
            <li><span>02</span><h3>Place it on record</h3><p>Publish your chosen name, exact pledge, and the date your commitment was made.</p></li>
            <li><span>03</span><h3>Keep your removal code</h3><p>A private one-time code lets you request permanent deletion of your entry.</p></li>
        </ol>
    </section>

    <section class="section-block" aria-labelledby="prevention-title">
        <div class="section-heading-inline">
            <p class="eyebrow">Suicide prevention resources</p>
            <h2 id="prevention-title">Practical information for difficult moments.</h2>
            <p>Clear, nonjudgmental guidance based on authoritative public-health and crisis-support sources.</p>
        </div>
        <div class="resource-grid">
            <article class="resource-card resource-card--urgent">
                <span class="card-kicker">Immediate support</span>
                <h3>Get help right now</h3>
                <p>Find U.S., Canadian, and international crisis options, plus a few steps to create time and distance from immediate danger.</p>
                <a href="help-now.php">See crisis support options →</a>
            </article>
            <article class="resource-card">
                <span class="card-kicker">Learn the signs</span>
                <h3>Suicide prevention</h3>
                <p>Recognize warning signs and understand what actions can support safety, connection, and care.</p>
                <a href="suicide-prevention.php">Read the prevention guide →</a>
            </article>
            <article class="resource-card">
                <span class="card-kicker">Be there</span>
                <h3>Help someone you’re worried about</h3>
                <p>Ask directly, listen without judgment, help reduce danger, connect them with support, and follow up.</p>
                <a href="help-someone.php">Learn how to help →</a>
            </article>
            <article class="resource-card">
                <span class="card-kicker">Prepare</span>
                <h3>Make a safety plan</h3>
                <p>Learn the six parts of a collaborative safety plan and download the official SAMHSA template.</p>
                <a href="safety-plan.php">Start a safety plan →</a>
            </article>
        </div>
    </section>

    <section class="source-note" aria-labelledby="source-title">
        <h2 id="source-title">Built around responsible suicide-prevention communication</h2>
        <p>The site avoids sensational language and method details, emphasizes hope and help-seeking, and links directly to current official resources. Prevention content is informational and should not replace individualized care.</p>
        <div class="source-links">
            <a href="https://www.nimh.nih.gov/health/publications/warning-signs-of-suicide">NIMH warning signs</a>
            <a href="https://www.samhsa.gov/resource/988/safety-plan">SAMHSA safety plan</a>
            <a href="https://988lifeline.org/">988 Lifeline</a>
            <a href="https://www.who.int/publications/i/item/9789240076846">WHO responsible communication</a>
        </div>
    </section>
</main>

<?php aor_render_page_end(); ?>
