<?php
declare(strict_types=1);

require __DIR__ . '/includes/site_shell.php';

$nonce = base64_encode(random_bytes(18));
aor_send_security_headers($nonce);
aor_render_page_start([
    'title' => 'How to Help Someone Who May Be Suicidal | Alive On Record',
    'description' => 'Learn how to ask about suicide, listen without judgment, help create safety, connect someone with 988 or care, and follow up.',
    'canonical_path' => 'help-someone.php',
    'active' => 'help-someone',
    'schema_type' => 'MedicalWebPage',
    'nonce' => $nonce,
]);
?>

<main class="content-main" id="main-content">
    <header class="content-header">
        <p class="eyebrow">Supporting another person</p>
        <h1>You do not need perfect words to help.</h1>
        <p class="lede">Your role is not to diagnose or solve everything. Be direct, stay present, reduce immediate danger when possible, and help the person connect with trained support.</p>
        <div class="urgent-panel">
            <h2>If the person may act now</h2>
            <p>Stay with them if you can do so safely. In the U.S. or Canada, call or text 988 for guidance. If they have already seriously harmed themselves or face immediate danger, call emergency services.</p>
            <div class="urgent-actions"><a class="button" href="help-now.php">See immediate-help options</a></div>
        </div>
    </header>

    <section class="article-section" aria-labelledby="five-steps">
        <h2 id="five-steps">Five evidence-informed steps</h2>
        <ol class="action-list">
            <li><strong>Ask.</strong> Use a direct question: “Are you thinking about suicide?” NIMH notes that asking does not increase suicidal thoughts or behavior.</li>
            <li><strong>Be there.</strong> Listen with patience and without judgment. Acknowledge the pain they describe instead of arguing with it or minimizing it.</li>
            <li><strong>Help keep them safe.</strong> Ask whether they have a plan. When it is safe, help reduce access to lethal items or places and create time for the crisis to pass.</li>
            <li><strong>Help them connect.</strong> Contact 988, a clinician, a trusted person, a spiritual adviser, or another appropriate source of support.</li>
            <li><strong>Follow up.</strong> Check in after the immediate crisis and after transitions such as discharge from care. Supportive ongoing contact can make a difference.</li>
        </ol>
    </section>

    <section class="article-section" aria-labelledby="what-to-say">
        <h2 id="what-to-say">Words you can use</h2>
        <div class="language-card"><p>“I’ve noticed you seem different lately, and I’m worried about you.”</p></div>
        <div class="language-card"><p>“Are you thinking about suicide?”</p></div>
        <div class="language-card"><p>“I’m here to listen. You do not have to carry this alone.”</p></div>
        <div class="language-card"><p>“Can we call or text 988 together?”</p></div>
    </section>

    <section class="article-section" aria-labelledby="avoid">
        <h2 id="avoid">What to avoid</h2>
        <ul>
            <li>Do not shame, challenge, lecture, or describe suicide as selfish.</li>
            <li>Do not promise secrecy when someone’s life may be at risk.</li>
            <li>Do not rely on a public pledge or verbal promise as proof of safety.</li>
            <li>Do not leave someone alone when you believe immediate danger is present, unless staying would put you in danger.</li>
            <li>Do not take sole responsibility. Contact trained support and involve other trusted people.</li>
        </ul>
    </section>

    <section class="article-section" aria-labelledby="care-for-you">
        <h2 id="care-for-you">Take care of yourself, too</h2>
        <p>Supporting someone through suicidal distress can be intense. You can contact 988 for advice even when you are calling about someone else. Share responsibility with trusted people and seek support for yourself afterward.</p>
    </section>

    <section class="source-note" aria-labelledby="helper-sources">
        <h2 id="helper-sources">Authoritative sources</h2>
        <div class="source-links">
            <a href="https://www.nimh.nih.gov/health/publications/5-action-steps-to-help-someone-having-thoughts-of-suicide">NIMH five action steps</a>
            <a href="https://988.ca/get-help/talk-to-someone-you-are-worried-about">Canada 9-8-8: helping someone</a>
            <a href="https://988lifeline.org/help-someone-else/">U.S. 988: help someone else</a>
        </div>
    </section>
</main>

<?php aor_render_page_end(); ?>
