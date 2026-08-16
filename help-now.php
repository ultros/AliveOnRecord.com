<?php
declare(strict_types=1);

require __DIR__ . '/includes/site_shell.php';

$nonce = base64_encode(random_bytes(18));
aor_send_security_headers($nonce);
aor_render_page_start([
    'title' => 'Suicide Crisis Help Now: Call, Text & Safety Steps | Alive On Record',
    'description' => 'Get immediate suicide crisis support in the U.S., Canada, or internationally. Find 988 contact options and practical steps to create safety right now.',
    'canonical_path' => 'help-now.php',
    'active' => 'help-now',
    'schema_type' => 'MedicalWebPage',
    'nonce' => $nonce,
]);
?>

<main class="content-main" id="main-content">
    <header class="content-header">
        <p class="eyebrow">Immediate crisis support</p>
        <h1>Help is available right now.</h1>
        <p class="lede">If you are thinking about suicide, feel unable to stay safe, or are worried you may act soon, pause here and connect with another person now.</p>

        <div class="urgent-panel">
            <h2>If there is immediate danger</h2>
            <p>Call your local emergency number or go to the nearest emergency department. In the United States or Canada, call 911. If you can, move away from anything you could use to hurt yourself and stay near another person.</p>
            <div class="urgent-actions">
                <a class="button" href="tel:911">Call 911 in the U.S. or Canada</a>
            </div>
        </div>
    </header>

    <section class="article-section" aria-labelledby="crisis-lines">
        <h2 id="crisis-lines">Talk with a crisis counselor</h2>

        <h3>United States</h3>
        <p>Call or text <a href="tel:988"><strong>988</strong></a>, or use the <a href="https://chat.988lifeline.org/">988 Lifeline chat</a>. The official U.S. 988 Lifeline describes its service as free, confidential, and judgment-free. A trained counselor will ask about your safety, listen, and help you identify support.</p>

        <h3>Canada</h3>
        <p>Call or text <a href="tel:988"><strong>9-8-8</strong></a> at any time. Canada’s official 9-8-8 Suicide Crisis Helpline is available 24 hours a day, every day, for people thinking about suicide and people worried about someone else.</p>

        <h3>Outside the United States or Canada</h3>
        <p>Use the <a href="https://findahelpline.com/i/iasp">IASP / Find a Helpline directory</a> to locate a current crisis line in your country. If your safety is at immediate risk, call your local emergency number.</p>
    </section>

    <section class="article-section" aria-labelledby="next-minutes">
        <h2 id="next-minutes">Create safety for the next few minutes</h2>
        <ol class="action-list">
            <li><strong>Move toward people.</strong> Go to a shared or public space, or ask someone you trust to stay with you.</li>
            <li><strong>Put distance between you and danger.</strong> Ask another person to secure or remove firearms, medications, or other items you could use to hurt yourself.</li>
            <li><strong>Tell one person plainly.</strong> You can say, “I am having thoughts of suicide and I need you to stay with me while we get help.”</li>
            <li><strong>Contact crisis support.</strong> Call or text 988 in the U.S. or Canada, or use a local crisis line elsewhere.</li>
            <li><strong>Delay irreversible action.</strong> Focus only on getting through the next ten minutes, then the ten minutes after that. Avoid alcohol or drugs while you seek help.</li>
        </ol>
    </section>

    <section class="article-section" aria-labelledby="words-to-use">
        <h2 id="words-to-use">If starting the conversation feels hard</h2>
        <p>You do not need perfect words. A direct sentence is enough.</p>
        <div class="language-card"><p>“I don’t feel safe alone right now. Can you stay with me and help me contact support?”</p></div>
        <div class="language-card"><p>“I am thinking about suicide. I need help getting through this moment.”</p></div>
        <div class="language-card"><p>“Please help me make my surroundings safer and call 988 with me.”</p></div>
    </section>

    <section class="article-section" aria-labelledby="after-crisis">
        <h2 id="after-crisis">When the immediate danger has eased</h2>
        <p>Ask a mental health professional or crisis counselor to help you build a personalized safety plan. A safety plan lists your warning signs, coping strategies, people and places that can help, professional contacts, and steps to reduce access to lethal means.</p>
        <a class="button button--secondary" href="safety-plan.php">Learn about safety planning</a>
    </section>

    <section class="source-note" aria-labelledby="help-sources">
        <h2 id="help-sources">Authoritative sources</h2>
        <div class="source-links">
            <a href="https://988lifeline.org/get-help/what-to-expect/">U.S. 988: What to expect</a>
            <a href="https://988.ca/get-help/what-to-expect">Canada 9-8-8: What to expect</a>
            <a href="https://www.samhsa.gov/resource/988/safety-plan">SAMHSA safety plan</a>
            <a href="https://www.emro.who.int/mhps/suicide.html">WHO crisis-support guidance</a>
        </div>
    </section>
</main>

<?php aor_render_page_end(); ?>
