<?php
declare(strict_types=1);

require __DIR__ . '/includes/site_shell.php';

$nonce = base64_encode(random_bytes(18));
aor_send_security_headers($nonce);
aor_render_page_start([
    'title' => 'Suicide Prevention: Warning Signs & How to Help | Alive On Record',
    'description' => 'Learn common suicide warning signs, what to do when risk may be rising, and how safety planning, connection, and professional support can help.',
    'canonical_path' => 'suicide-prevention.php',
    'active' => 'prevention',
    'schema_type' => 'MedicalWebPage',
    'nonce' => $nonce,
]);
?>

<main class="content-main" id="main-content">
    <header class="content-header">
        <p class="eyebrow">Suicide prevention guide</p>
        <h1>Notice the signs. Ask directly. Connect to help.</h1>
        <p class="lede">Suicide is complex, and no single sign predicts what someone will do. New, escalating, or concerning changes deserve attention—especially after a painful event, loss, or major change.</p>
        <div class="urgent-panel">
            <h2>Concerned about immediate safety?</h2>
            <p>In the U.S. or Canada, call or text 988. If someone has already seriously harmed themselves or is in immediate danger, call emergency services now.</p>
            <div class="urgent-actions"><a class="button" href="help-now.php">Get crisis help now</a></div>
        </div>
    </header>

    <section class="article-section" aria-labelledby="warning-signs">
        <h2 id="warning-signs">Possible warning signs</h2>
        <p>The National Institute of Mental Health advises paying attention to changes in what a person says, feels, and does. Warning signs can include:</p>
        <ul>
            <li>Talking about wanting to die, feeling like a burden, or experiencing intense guilt or shame.</li>
            <li>Feeling hopeless, trapped, empty, extremely distressed, agitated, enraged, or in unbearable emotional or physical pain.</li>
            <li>Researching or making a plan for suicide.</li>
            <li>Withdrawing, saying goodbye, giving away meaningful belongings, or putting affairs in order unexpectedly.</li>
            <li>Taking unusual risks, showing major mood swings, or changing sleep, eating, alcohol, or drug use.</li>
        </ul>
        <p>Seek help promptly when these signs are new, have increased, or appear connected to a major stressor.</p>
    </section>

    <section class="article-section" aria-labelledby="what-helps">
        <h2 id="what-helps">Actions that can support safety</h2>
        <ol class="action-list">
            <li><strong>Ask about suicide clearly.</strong> Asking “Are you thinking about suicide?” does not cause suicidal thoughts and can open an honest conversation.</li>
            <li><strong>Listen without judgment.</strong> Take the person seriously. Avoid debating, lecturing, minimizing, or rushing to solve everything.</li>
            <li><strong>Reduce immediate danger.</strong> Help create distance from firearms, medications, or other lethal means when it is safe to do so.</li>
            <li><strong>Connect to care.</strong> Contact 988, a mental health professional, a trusted person, or emergency care depending on urgency.</li>
            <li><strong>Follow up.</strong> Ongoing, supportive contact after a crisis or discharge can matter.</li>
        </ol>
    </section>

    <section class="article-section" aria-labelledby="pledge-limits">
        <h2 id="pledge-limits">A pledge is not suicide-prevention treatment</h2>
        <p>The Alive On Record pledge is a voluntary public statement about choosing connection and help-seeking. It is not a “no-suicide contract.” Agreeing to a pledge does not show that risk has decreased, and declining one does not establish that someone is in immediate danger.</p>
        <div class="medical-note">Clinical guidance distinguishes written promises from collaborative safety plans. A safety plan contains specific, usable steps for recognizing a crisis, coping, contacting support, and reducing access to lethal means.</div>
        <p><a href="safety-plan.php">Learn what belongs in a safety plan</a>.</p>
    </section>

    <section class="article-section" aria-labelledby="responsible-language">
        <h2 id="responsible-language">Use direct, respectful language</h2>
        <p>Responsible communication avoids sensationalizing suicide, describing methods, or presenting suicide as an inevitable response to hardship. Better communication makes room for recovery, coping, treatment, and support.</p>
        <div class="language-card"><p>Say: “died by suicide” rather than language that frames death as a crime or success.</p></div>
        <div class="language-card"><p>Say: “Are you thinking about suicide?” Direct language can make it easier to answer honestly.</p></div>
    </section>

    <section class="source-note" aria-labelledby="prevention-sources">
        <h2 id="prevention-sources">Authoritative sources</h2>
        <div class="source-links">
            <a href="https://www.nimh.nih.gov/health/publications/warning-signs-of-suicide">NIMH warning signs</a>
            <a href="https://www.nimh.nih.gov/health/publications/5-action-steps-to-help-someone-having-thoughts-of-suicide">NIMH five action steps</a>
            <a href="https://www.samhsa.gov/resource/988/safety-plan">SAMHSA safety plan</a>
            <a href="https://www.who.int/publications/i/item/9789240076846">WHO responsible communication</a>
        </div>
    </section>
</main>

<?php aor_render_page_end(); ?>
