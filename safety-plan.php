<?php
declare(strict_types=1);

require __DIR__ . '/includes/site_shell.php';

$nonce = base64_encode(random_bytes(18));
aor_send_security_headers($nonce);
aor_render_page_start([
    'title' => 'Suicide Safety Plan: Six Steps & Official Template | Alive On Record',
    'description' => 'Learn the six parts of a suicide safety plan and download the official SAMHSA template for use with a counselor, clinician, or trusted supporter.',
    'canonical_path' => 'safety-plan.php',
    'active' => 'safety-plan',
    'schema_type' => 'MedicalWebPage',
    'nonce' => $nonce,
]);
?>

<main class="content-main" id="main-content">
    <header class="content-header">
        <p class="eyebrow">Prepare before a crisis</p>
        <h1>Build a plan you can use when thinking is hard.</h1>
        <p class="lede">A safety plan is a short, personalized list of warning signs, coping steps, people, services, and ways to make your environment safer. Build it collaboratively with a clinician, counselor, or trained crisis supporter when possible.</p>
        <div class="button-row">
            <a class="button" href="https://www.samhsa.gov/resource/988/safety-plan">Get the official SAMHSA template</a>
            <a class="button button--secondary" href="help-now.php">I need help now</a>
        </div>
    </header>

    <section class="article-section" aria-labelledby="six-parts">
        <h2 id="six-parts">The six parts of a safety plan</h2>
        <ol class="action-list">
            <li><strong>Warning signs.</strong> List thoughts, feelings, situations, or behaviors that tell you a crisis may be developing.</li>
            <li><strong>Internal coping strategies.</strong> Record safe actions you can try on your own to create time and lower the intensity of the moment.</li>
            <li><strong>People and places for distraction.</strong> Identify supportive environments or people who can help you feel less isolated without requiring you to disclose everything immediately.</li>
            <li><strong>People you can ask for help.</strong> Write names and current contact information for people you can tell directly that you need support.</li>
            <li><strong>Professionals and crisis services.</strong> Include clinicians, local urgent care or emergency departments, and crisis lines such as 988.</li>
            <li><strong>A safer environment.</strong> Plan how firearms, medications, or other lethal means will be removed, locked, or transferred to another responsible person.</li>
        </ol>
    </section>

    <section class="article-section" aria-labelledby="plan-vs-pledge">
        <h2 id="plan-vs-pledge">Safety plan versus public pledge</h2>
        <div class="page-grid">
            <article class="panel">
                <h3>Safety plan</h3>
                <p>Private, specific, actionable, and ideally created collaboratively. It tells you what to do, who to contact, and how to reduce danger during a crisis.</p>
            </article>
            <article class="panel">
                <h3>Alive On Record pledge</h3>
                <p>Public, voluntary, and symbolic. It expresses a commitment to seek connection and help, but it cannot assess risk or replace a safety plan or care.</p>
            </article>
        </div>
    </section>

    <section class="article-section" aria-labelledby="keep-current">
        <h2 id="keep-current">Keep the plan usable</h2>
        <ul>
            <li>Save it where you can reach it quickly, including a paper copy if helpful.</li>
            <li>Tell the people named in the plan what you may need from them.</li>
            <li>Confirm phone numbers and service details periodically.</li>
            <li>Review the plan after a crisis, major life change, or change in treatment.</li>
            <li>Practice the first steps before you need them.</li>
        </ul>
    </section>

    <section class="source-note" aria-labelledby="safety-sources">
        <h2 id="safety-sources">Authoritative sources</h2>
        <div class="source-links">
            <a href="https://www.samhsa.gov/resource/988/safety-plan">SAMHSA safety plan and printable template</a>
            <a href="https://988lifeline.org/">U.S. 988 Lifeline</a>
            <a href="https://988.ca/">Canada 9-8-8 Helpline</a>
        </div>
    </section>
</main>

<?php aor_render_page_end(); ?>
