<?php
declare(strict_types=1);

/* =========================================================
   CONFIGURATION — EDIT MANUALLY WHEN THE RECORD CHANGES
   ========================================================= */

$siteName = 'Alive On Record';
$ownerName = 'Jesse';
$recordStatus = 'ACTIVE';
$recordCreated = 'August 16, 2026';
$lastPersonalVerification = 'August 16, 2026'; // Manual verification only.
$recordVersion = '0.1.0';
$statementId = 'AOR-STATEMENT-0001';
$originalPublicationDate = 'August 16, 2026';
$latestRevisionDate = 'August 16, 2026';
$displayTimezone = 'America/Denver';
$canonicalUrl = 'https://aliveonrecord.com/';

$timeline = [
    [
        'date' => 'August 16, 2026',
        'title' => 'Public Record Established',
        'type' => 'Verified',
        'id' => 'AOR-EVENT-0001',
        'description' => 'AliveOnRecord.com established as a public continuity and personal safety record.',
        'source' => 'Statement AOR-STATEMENT-0001',
    ],
    [
        'date' => 'Not yet published',
        'title' => 'Future Record Placeholder',
        'type' => 'Unverified',
        'id' => 'AOR-EVENT-PENDING',
        'description' => 'Reserved for a future dated entry. No event or claim is asserted by this placeholder.',
        'source' => 'Not yet published',
    ],
];

$evidenceItems = [
    [
        'id' => 'AOR-EVIDENCE-0001',
        'title' => 'Initial Public Statement',
        'date' => 'August 16, 2026',
        'type' => 'Documented Record',
        'description' => 'The initial statement of intent published on this website.',
        'filename' => 'Not yet published',
        'sha256' => 'Not yet published',
        'url' => null,
    ],
    [
        'id' => 'AOR-EVIDENCE-PENDING',
        'title' => 'Future Evidence Placeholder',
        'date' => 'Not yet published',
        'type' => 'Unverified',
        'description' => 'Reserved for a future primary document. This placeholder is not evidence of any event or claim.',
        'filename' => 'Not yet published',
        'sha256' => 'Not yet published',
        'url' => null,
    ],
];

$classifications = [
    'Personal Observation' => 'A firsthand account by the site owner.',
    'Documented Record' => 'Information represented in an identified document or record.',
    'Third-Party Statement' => 'A statement attributed to someone other than the site owner.',
    'Allegation' => 'A claim that has not been established as an adjudicated finding.',
    'Opinion / Analysis' => 'Interpretation, inference, or personal assessment.',
    'Unverified' => 'Information not yet independently corroborated.',
    'Verified' => 'Information checked against the cited supporting record.',
];

$continuityFields = [
    'Trusted contact' => 'Not publicly listed',
    'Attorney' => 'Not publicly listed',
    'Archive location' => 'Not publicly listed',
    'Independent backup location' => 'Not publicly listed',
    'Emergency instructions' => 'Not publicly listed',
];

$archiveFields = [
    'Internet Archive snapshot' => 'Not yet published',
    'Offline backup' => 'Not yet published',
    'Trusted-party copy' => 'Not yet published',
    'Signed PDF' => 'Not yet published',
    'External mirror' => 'Not yet published',
];

/* =========================================================
   RUNTIME — PAGE RENDER INFORMATION, NOT PERSONAL VERIFICATION
   ========================================================= */

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function badgeClass(string $type): string
{
    return match ($type) {
        'Verified' => 'badge--verified',
        'Documented Record' => 'badge--documented',
        'Personal Observation' => 'badge--observation',
        'Third-Party Statement' => 'badge--third-party',
        'Allegation' => 'badge--allegation',
        'Opinion / Analysis' => 'badge--opinion',
        default => 'badge--unverified',
    };
}

try {
    $timezone = new DateTimeZone($displayTimezone);
} catch (Exception) {
    $timezone = new DateTimeZone('UTC');
}

$pageGenerated = (new DateTimeImmutable('now', $timezone))->format('F j, Y \a\t g:i:s A T');
$cspNonce = base64_encode(random_bytes(18));

header_remove('X-Powered-By');
header('Content-Type: text/html; charset=UTF-8');
header("Content-Security-Policy: default-src 'none'; style-src 'nonce-{$cspNonce}'; img-src 'self' data:; font-src 'self'; connect-src 'none'; media-src 'self'; object-src 'none'; frame-src 'none'; frame-ancestors 'none'; form-action 'self'; base-uri 'none'; manifest-src 'self'");
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');
header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=(), usb=(), interest-cohort=()');
header('X-Frame-Options: DENY');

$statusIsActive = strtoupper($recordStatus) === 'ACTIVE';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Alive On Record | Personal Safety &amp; Continuity Record</title>
    <meta name="description" content="A public personal safety, intent, continuity, and verification record.">
    <meta name="theme-color" content="#101311">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= e($canonicalUrl) ?>">
    <meta property="og:title" content="Alive On Record | Personal Safety &amp; Continuity Record">
    <meta property="og:description" content="A public personal safety, intent, continuity, and verification record.">
    <meta property="og:site_name" content="<?= e($siteName) ?>">
    <meta name="twitter:card" content="summary">
    <link rel="canonical" href="<?= e($canonicalUrl) ?>">
    <style nonce="<?= e($cspNonce) ?>">
        :root {
            color-scheme: dark;
            --ink: #f3f3ed;
            --muted: #a5aaa4;
            --dim: #777d78;
            --canvas: #0c0f0d;
            --surface: #121613;
            --surface-2: #171c18;
            --line: #2b312c;
            --line-strong: #414941;
            --active: #9be29b;
            --active-deep: #17341e;
            --warning: #e6bd73;
            --warning-deep: #332815;
            --blue: #9dc7d6;
            --purple: #c1addd;
            --radius: 18px;
            --max: 1180px;
        }

        *, *::before, *::after { box-sizing: border-box; }

        html { scroll-behavior: smooth; scroll-padding-top: 9rem; }

        body {
            margin: 0;
            background:
                linear-gradient(rgba(255,255,255,.018) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.018) 1px, transparent 1px),
                radial-gradient(circle at 72% 0%, rgba(91, 144, 96, .10), transparent 34rem),
                var(--canvas);
            background-size: 44px 44px, 44px 44px, auto, auto;
            color: var(--ink);
            font-family: Inter, ui-sans-serif, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            font-size: 16px;
            line-height: 1.65;
            text-rendering: optimizeLegibility;
        }

        a { color: inherit; }
        a:hover { color: var(--active); }

        :focus-visible {
            outline: 3px solid var(--active);
            outline-offset: 4px;
            border-radius: 3px;
        }

        .skip-link {
            position: fixed;
            z-index: 100;
            top: .75rem;
            left: .75rem;
            padding: .7rem 1rem;
            background: var(--ink);
            color: var(--canvas);
            transform: translateY(-180%);
            transition: transform .15s ease;
        }

        .skip-link:focus { transform: translateY(0); }

        .site-header {
            position: sticky;
            z-index: 50;
            top: 0;
            border-bottom: 1px solid rgba(255,255,255,.08);
            background: rgba(12, 15, 13, .90);
            backdrop-filter: blur(18px);
        }

        .header-inner {
            width: min(calc(100% - 2rem), var(--max));
            min-height: 70px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 2rem;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: .7rem;
            font-size: .78rem;
            font-weight: 750;
            letter-spacing: .16em;
            text-decoration: none;
            white-space: nowrap;
        }

        .brand-mark {
            width: .7rem;
            height: .7rem;
            border: 2px solid var(--active);
            border-radius: 50%;
            box-shadow: 0 0 0 4px rgba(155, 226, 155, .09);
        }

        .nav-list {
            display: flex;
            gap: .25rem;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .nav-list a {
            display: block;
            padding: .6rem .7rem;
            color: var(--muted);
            font-size: .78rem;
            text-decoration: none;
        }

        .nav-list a:hover { color: var(--ink); }

        main { overflow: hidden; }

        .wrap { width: min(calc(100% - 2rem), var(--max)); margin-inline: auto; }

        .hero {
            min-height: 82vh;
            padding: clamp(5rem, 11vw, 9rem) 0 5rem;
            display: grid;
            align-items: center;
        }

        .eyebrow {
            margin: 0 0 1.25rem;
            color: var(--active);
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .19em;
            text-transform: uppercase;
        }

        .hero h1 {
            max-width: 11ch;
            margin: 0;
            font-size: clamp(4rem, 11vw, 9.6rem);
            font-weight: 760;
            letter-spacing: -.072em;
            line-height: .82;
        }

        .hero-subtitle {
            margin: 2rem 0 0;
            color: var(--muted);
            font-size: clamp(1.15rem, 2.6vw, 1.75rem);
            letter-spacing: -.02em;
        }

        .hero-grid {
            margin-top: clamp(3rem, 7vw, 6rem);
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 1rem;
        }

        .hero-status,
        .hero-summary {
            min-height: 210px;
            padding: clamp(1.5rem, 3vw, 2.2rem);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            background: linear-gradient(145deg, rgba(23, 28, 24, .96), rgba(16, 19, 17, .94));
            box-shadow: 0 20px 65px rgba(0,0,0,.18);
        }

        .hero-status { display: flex; flex-direction: column; justify-content: space-between; }

        .status-label,
        .micro-label {
            color: var(--muted);
            font-size: .68rem;
            font-weight: 750;
            letter-spacing: .15em;
            text-transform: uppercase;
        }

        .status-value {
            display: flex;
            align-items: center;
            gap: .85rem;
            margin: .5rem 0 1.5rem;
            color: var(--active);
            font-size: clamp(2.2rem, 6vw, 4.6rem);
            font-weight: 760;
            letter-spacing: -.06em;
            line-height: 1;
        }

        .status-dot {
            flex: 0 0 auto;
            width: .74em;
            height: .74em;
            border: .18em solid rgba(155, 226, 155, .14);
            border-radius: 50%;
            background: var(--active);
            background-clip: padding-box;
        }

        .status-value.is-inactive { color: var(--warning); }
        .status-value.is-inactive .status-dot { background: var(--warning); border-color: rgba(230, 189, 115, .14); }

        .status-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem 3rem;
            color: var(--ink);
        }

        .status-meta span { display: block; margin-top: .25rem; }

        .hero-summary { display: flex; flex-direction: column; justify-content: space-between; }
        .hero-summary p { max-width: 38rem; margin: 0; color: #d2d5d0; font-size: 1.05rem; }
        .record-code { margin-top: 2rem; color: var(--dim); font: 600 .75rem/1.6 ui-monospace, SFMono-Regular, Consolas, monospace; letter-spacing: .04em; }

        .text-link {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            margin-top: 1.4rem;
            color: var(--ink);
            font-size: .78rem;
            font-weight: 750;
            letter-spacing: .07em;
            text-decoration: none;
            text-transform: uppercase;
        }

        .text-link::after { content: "→"; color: var(--active); font-size: 1rem; }

        .section { padding: clamp(5rem, 10vw, 9rem) 0; border-top: 1px solid var(--line); }

        .section-heading {
            display: grid;
            grid-template-columns: minmax(14rem, .7fr) 1.3fr;
            gap: 2rem;
            align-items: start;
            margin-bottom: clamp(2.5rem, 6vw, 5rem);
        }

        .section-heading h2 {
            margin: 0;
            font-size: clamp(2.2rem, 5vw, 4.4rem);
            letter-spacing: -.055em;
            line-height: .98;
        }

        .section-intro { max-width: 43rem; margin: .35rem 0 0; color: var(--muted); font-size: 1.05rem; }

        .statement-panel {
            position: relative;
            padding: clamp(2rem, 6vw, 5rem);
            border: 1px solid var(--line-strong);
            border-radius: var(--radius);
            background: linear-gradient(145deg, rgba(29, 35, 30, .94), rgba(17, 21, 18, .98));
        }

        .statement-panel::before {
            content: "";
            position: absolute;
            inset: -1px auto -1px -1px;
            width: 4px;
            border-radius: var(--radius) 0 0 var(--radius);
            background: var(--active);
        }

        .statement-copy { max-width: 900px; }
        .statement-copy p { margin: 0 0 1.3em; font-size: clamp(1.35rem, 2.6vw, 2.1rem); line-height: 1.42; letter-spacing: -.025em; }
        .statement-copy p:last-child { margin-bottom: 0; }

        .statement-meta {
            margin-top: 3.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--line);
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
        }

        .statement-meta strong { display: block; margin-top: .35rem; font-size: .92rem; font-weight: 620; overflow-wrap: anywhere; }

        .standard-callout {
            margin-bottom: 1rem;
            padding: 1.4rem 1.5rem;
            border: 1px solid rgba(230,189,115,.28);
            border-radius: 14px;
            background: rgba(51, 40, 21, .38);
            color: #e8d7b8;
        }

        .standard-callout strong { color: var(--warning); }

        .classification-grid,
        .card-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }

        .classification-item,
        .record-card,
        .continuity-card {
            padding: 1.5rem;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: rgba(18, 22, 19, .8);
        }

        .classification-item p { margin: .9rem 0 0; color: var(--muted); }

        .badge {
            display: inline-flex;
            align-items: center;
            min-height: 28px;
            padding: .28rem .65rem;
            border: 1px solid var(--line-strong);
            border-radius: 999px;
            font-size: .7rem;
            font-weight: 760;
            letter-spacing: .07em;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .badge--verified { border-color: rgba(155,226,155,.38); background: var(--active-deep); color: var(--active); }
        .badge--documented { border-color: rgba(157,199,214,.36); background: #152a31; color: var(--blue); }
        .badge--observation { border-color: rgba(193,173,221,.35); background: #261e31; color: var(--purple); }
        .badge--third-party { border-color: rgba(202,207,203,.3); background: #252a26; color: #d1d5d2; }
        .badge--allegation { border-color: rgba(230,189,115,.42); background: var(--warning-deep); color: var(--warning); }
        .badge--opinion { border-color: rgba(193,173,221,.35); background: #27202f; color: var(--purple); }
        .badge--unverified { border-color: #424842; background: #202420; color: #b5bab5; }

        .status-board {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            border: 1px solid var(--line);
            border-radius: var(--radius);
            overflow: hidden;
            background: var(--surface);
        }

        .metric { min-height: 150px; padding: 1.5rem; border-right: 1px solid var(--line); border-bottom: 1px solid var(--line); }
        .metric:nth-child(3n) { border-right: 0; }
        .metric:nth-last-child(-n+3) { border-bottom: 0; }
        .metric strong { display: block; margin-top: 1.5rem; font-size: clamp(1.15rem, 2vw, 1.55rem); line-height: 1.25; overflow-wrap: anywhere; }
        .metric--active strong { color: var(--active); }
        .metric--generated { background: rgba(255,255,255,.02); }
        .metric-note { display: block; margin-top: .6rem; color: var(--warning); font-size: .75rem; line-height: 1.4; }

        .timeline { position: relative; max-width: 920px; margin-left: auto; }
        .timeline::before { content: ""; position: absolute; top: .5rem; bottom: .5rem; left: 8.75rem; width: 1px; background: var(--line-strong); }

        .timeline-entry { position: relative; display: grid; grid-template-columns: 7rem 1fr; gap: 3.5rem; padding-bottom: 3.5rem; }
        .timeline-entry:last-child { padding-bottom: 0; }
        .timeline-date { color: var(--muted); font-size: .8rem; line-height: 1.45; }
        .timeline-entry::after { content: ""; position: absolute; top: .35rem; left: 8.4rem; width: .65rem; height: .65rem; border: 2px solid var(--canvas); border-radius: 50%; background: var(--active); box-shadow: 0 0 0 1px var(--line-strong); }
        .timeline-content { padding: 0 0 0 .25rem; }
        .timeline-content h3 { margin: .85rem 0 .55rem; font-size: 1.55rem; letter-spacing: -.025em; }
        .timeline-content p { margin: 0; color: var(--muted); }
        .entry-meta { margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid var(--line); display: flex; flex-wrap: wrap; gap: .5rem 2rem; color: var(--dim); font: .74rem/1.5 ui-monospace, SFMono-Regular, Consolas, monospace; }

        .record-card { display: flex; flex-direction: column; min-height: 340px; }
        .record-card h3 { margin: 1.3rem 0 .65rem; font-size: 1.45rem; line-height: 1.2; }
        .record-card > p { margin: 0 0 1.5rem; color: var(--muted); }
        .record-details { margin: auto 0 0; padding: 1rem 0 0; border-top: 1px solid var(--line); }
        .record-details div { display: grid; grid-template-columns: 8rem 1fr; gap: 1rem; padding: .3rem 0; font-size: .78rem; }
        .record-details dt { color: var(--dim); }
        .record-details dd { margin: 0; overflow-wrap: anywhere; }
        .record-link { margin-top: 1rem; font-size: .78rem; font-weight: 700; }

        .allegation-note {
            margin-top: 1rem;
            padding: 1rem 1.2rem;
            border-left: 2px solid var(--warning);
            background: rgba(51,40,21,.25);
            color: #d7c8ad;
            font-size: .9rem;
        }

        .continuity-layout { display: grid; grid-template-columns: 1fr 1.2fr; gap: 1rem; }
        .continuity-copy { padding-right: 2rem; }
        .continuity-copy p { margin-top: 0; color: var(--muted); font-size: 1.07rem; }
        .continuity-fields { border: 1px solid var(--line); border-radius: var(--radius); overflow: hidden; }
        .continuity-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; padding: 1.25rem 1.5rem; background: var(--surface); border-bottom: 1px solid var(--line); }
        .continuity-row:last-child { border-bottom: 0; }
        .continuity-row dt { color: var(--muted); }
        .continuity-row dd { margin: 0; text-align: right; font-weight: 650; }

        .verification-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .verification-panel { padding: clamp(1.5rem, 4vw, 2.5rem); border: 1px solid var(--line); border-radius: var(--radius); background: var(--surface); }
        .verification-panel h3 { margin: 0 0 1rem; font-size: 1.5rem; }
        .verification-panel p { color: var(--muted); }
        .verification-list { margin: 1.75rem 0 0; padding: 0; list-style: none; }
        .verification-list li { display: flex; justify-content: space-between; gap: 1rem; padding: .9rem 0; border-top: 1px solid var(--line); font-size: .85rem; }
        .verification-list span:first-child { color: var(--muted); }
        .verification-list strong { text-align: right; overflow-wrap: anywhere; }
        .key-placeholder { margin-top: 1.5rem; padding: 1rem; border: 1px dashed var(--line-strong); border-radius: 10px; color: var(--muted); font: .78rem/1.6 ui-monospace, SFMono-Regular, Consolas, monospace; }

        .archive-grid { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: .75rem; }
        .archive-item { min-height: 145px; padding: 1.2rem; border: 1px solid var(--line); border-radius: 12px; background: rgba(18,22,19,.8); }
        .archive-item span { display: block; margin-bottom: 2rem; color: var(--muted); font-size: .76rem; }
        .archive-item strong { font-size: .82rem; }

        .site-footer { padding: 4rem 0 2rem; border-top: 1px solid var(--line); background: #090b0a; }
        .footer-top { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: end; }
        .footer-brand { margin: 0; font-size: clamp(2rem, 5vw, 4rem); font-weight: 750; letter-spacing: -.055em; line-height: 1; }
        .footer-subtitle { margin: .75rem 0 0; color: var(--muted); }
        .footer-meta { display: grid; grid-template-columns: 1fr 1fr; gap: .7rem 1.5rem; margin: 0; }
        .footer-meta div { min-width: 0; }
        .footer-meta dt { color: var(--dim); font-size: .66rem; letter-spacing: .08em; text-transform: uppercase; }
        .footer-meta dd { margin: .2rem 0 0; font-size: .78rem; overflow-wrap: anywhere; }
        .footer-notice { margin: 3rem 0 0; padding-top: 1.5rem; border-top: 1px solid var(--line); color: var(--dim); font-size: .75rem; }

        @media (max-width: 900px) {
            .header-inner { display: block; padding-top: 1rem; }
            .nav-list { margin: .55rem -1rem 0; padding: 0 1rem .65rem; overflow-x: auto; scrollbar-width: thin; }
            .nav-list a { padding: .4rem .65rem; }
            .hero { min-height: auto; }
            .hero-grid, .section-heading, .continuity-layout { grid-template-columns: 1fr; }
            .statement-meta { grid-template-columns: repeat(2, 1fr); }
            .status-board { grid-template-columns: repeat(2, 1fr); }
            .metric:nth-child(n) { border-right: 1px solid var(--line); border-bottom: 1px solid var(--line); }
            .metric:nth-child(2n) { border-right: 0; }
            .metric:nth-last-child(-n+2) { border-bottom: 0; }
            .continuity-copy { padding-right: 0; }
            .archive-grid { grid-template-columns: repeat(2, 1fr); }
            .archive-item:last-child { grid-column: 1 / -1; }
            .footer-top { grid-template-columns: 1fr; }
        }

        @media (max-width: 620px) {
            html { scroll-padding-top: 8rem; }
            .wrap { width: min(calc(100% - 1.25rem), var(--max)); }
            .hero { padding-top: 4.5rem; }
            .hero h1 { font-size: clamp(3.5rem, 20vw, 6rem); }
            .hero-grid, .classification-grid, .card-grid, .verification-grid { grid-template-columns: 1fr; }
            .hero-status, .hero-summary { min-height: 0; }
            .statement-meta, .status-board { grid-template-columns: 1fr; }
            .metric:nth-child(n) { border-right: 0; border-bottom: 1px solid var(--line); }
            .metric:last-child { border-bottom: 0; }
            .timeline { margin-left: 0; }
            .timeline::before { left: .35rem; }
            .timeline-entry { grid-template-columns: 1fr; gap: .8rem; padding-left: 2rem; }
            .timeline-entry::after { left: 0; }
            .timeline-date { font-size: .74rem; }
            .record-details div { grid-template-columns: 1fr; gap: 0; }
            .continuity-row { grid-template-columns: 1fr; gap: .25rem; }
            .continuity-row dd { text-align: left; }
            .verification-list li { display: block; }
            .verification-list strong { display: block; margin-top: .3rem; text-align: left; }
            .archive-grid, .footer-meta { grid-template-columns: 1fr; }
            .archive-item:last-child { grid-column: auto; }
        }

        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            *, *::before, *::after { transition-duration: .01ms !important; animation-duration: .01ms !important; animation-iteration-count: 1 !important; }
        }

        @media print {
            :root { color-scheme: light; }
            body { background: #fff; color: #111; }
            .site-header, .skip-link { display: none; }
            .hero { min-height: auto; padding-top: 2rem; }
            .hero-status, .hero-summary, .statement-panel, .classification-item, .record-card, .continuity-fields, .verification-panel, .archive-item { color: #111; background: #fff; border-color: #aaa; box-shadow: none; }
            .section { break-inside: avoid; border-color: #bbb; }
        }
    </style>
</head>
<body>
<a class="skip-link" href="#main-content">Skip to main content</a>

<header class="site-header">
    <div class="header-inner">
        <a class="brand" href="#top" aria-label="Alive On Record, return to top">
            <span class="brand-mark" aria-hidden="true"></span>
            ALIVE ON RECORD
        </a>
        <nav aria-label="Primary navigation">
            <ul class="nav-list">
                <li><a href="#statement">Statement</a></li>
                <li><a href="#status">Status</a></li>
                <li><a href="#timeline">Timeline</a></li>
                <li><a href="#evidence">Evidence</a></li>
                <li><a href="#continuity">Continuity</a></li>
                <li><a href="#verification">Verification</a></li>
                <li><a href="pledge.html">Personal Pledge</a></li>
                <li><a href="take-pledge.php">Take the Pledge</a></li>
            </ul>
        </nav>
    </div>
</header>

<main id="main-content">
    <!-- ========================================================
         HERO / RECORD OVERVIEW
         ======================================================== -->
    <section class="hero" id="top" aria-labelledby="hero-title">
        <div class="wrap">
            <p class="eyebrow">Personal Safety, Intent &amp; Continuity Record</p>
            <h1 id="hero-title">ALIVE ON RECORD</h1>
            <p class="hero-subtitle">A dated public statement. A clear standard of evidence. A record designed for continuity.</p>

            <div class="hero-grid">
                <div class="hero-status">
                    <div>
                        <span class="status-label">Record status</span>
                        <div class="status-value<?= $statusIsActive ? '' : ' is-inactive' ?>">
                            <span class="status-dot" aria-hidden="true"></span>
                            <?= e($recordStatus) ?>
                        </div>
                    </div>
                    <div class="status-meta">
                        <div><span class="micro-label">Last Personal Verification</span><span><?= e($lastPersonalVerification) ?></span></div>
                        <div><span class="micro-label">Owner</span><span><?= e($ownerName) ?></span></div>
                    </div>
                </div>
                <div class="hero-summary">
                    <div>
                        <p>This site maintains a public, dated record of my personal safety intentions, continuity information, and supporting documentation.</p>
                        <a class="text-link" href="pledge.html">Read my personal pledge</a>
                        <br>
                        <a class="text-link" href="take-pledge.php">Make your own public pledge</a>
                    </div>
                    <div class="record-code">STATEMENT <?= e($statementId) ?><br>RECORD VERSION <?= e($recordVersion) ?></div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================================
         STATEMENT OF INTENT
         ======================================================== -->
    <section class="section" id="statement" aria-labelledby="statement-title">
        <div class="wrap">
            <div class="section-heading">
                <h2 id="statement-title">Statement of Intent</h2>
                <p class="section-intro">A direct statement of present intent, published openly and maintained as part of this personal record.</p>
            </div>

            <article class="statement-panel">
                <div class="statement-copy">
                    <p>I am not suicidal. I have no intention of harming myself or deliberately causing my own death.</p>
                    <p>If I unexpectedly disappear, become incapacitated, am detained without normal communication, or die under unusual or unexplained circumstances, I request that the circumstances receive a thorough and independent investigation.</p>
                    <p>This statement is intentionally public and is maintained as part of my personal safety and continuity record.</p>
                </div>
                <div class="statement-meta" aria-label="Statement metadata">
                    <div><span class="micro-label">Statement ID</span><strong><?= e($statementId) ?></strong></div>
                    <div><span class="micro-label">Original publication</span><strong><?= e($originalPublicationDate) ?></strong></div>
                    <div><span class="micro-label">Latest revision</span><strong><?= e($latestRevisionDate) ?></strong></div>
                    <div><span class="micro-label">Version</span><strong><?= e($recordVersion) ?></strong></div>
                </div>
            </article>
        </div>
    </section>

    <!-- ========================================================
         EVIDENCE STANDARD
         ======================================================== -->
    <section class="section" id="standard" aria-labelledby="standard-title">
        <div class="wrap">
            <div class="section-heading">
                <h2 id="standard-title">How information on this site is classified</h2>
                <p class="section-intro">Labels distinguish firsthand accounts, records, allegations, and analysis. They are part of the record and should be read with each entry.</p>
            </div>
            <div class="standard-callout" role="note">
                <strong>Publication is not proof.</strong> The presence of a claim on this website does not automatically establish that claim as fact. Readers should review the classification, cited source, and any primary documents.
            </div>
            <div class="classification-grid">
                <?php foreach ($classifications as $type => $description): ?>
                    <article class="classification-item">
                        <span class="badge <?= e(badgeClass($type)) ?>"><?= e($type) ?></span>
                        <p><?= e($description) ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
            <div class="allegation-note">
                Entries classified as <strong>Allegation</strong> reflect the site owner’s allegation or recollection and should not be interpreted as an adjudicated finding unless supporting records establish otherwise.
            </div>
        </div>
    </section>

    <!-- ========================================================
         STATUS
         ======================================================== -->
    <section class="section" id="status" aria-labelledby="status-title">
        <div class="wrap">
            <div class="section-heading">
                <h2 id="status-title">Record Status</h2>
                <p class="section-intro">The manually maintained record fields and server render information are shown separately to preserve their meaning.</p>
            </div>
            <div class="status-board" aria-label="Current record status">
                <div class="metric metric--active"><span class="micro-label">Record Status</span><strong><?= e($recordStatus) ?></strong></div>
                <div class="metric"><span class="micro-label">Last Personal Verification</span><strong><?= e($lastPersonalVerification) ?></strong></div>
                <div class="metric"><span class="micro-label">Record Created</span><strong><?= e($recordCreated) ?></strong></div>
                <div class="metric"><span class="micro-label">Statement ID</span><strong><?= e($statementId) ?></strong></div>
                <div class="metric"><span class="micro-label">Record Version</span><strong><?= e($recordVersion) ?></strong></div>
                <div class="metric metric--generated">
                    <span class="micro-label">Page Generated</span>
                    <strong><?= e($pageGenerated) ?></strong>
                    <small class="metric-note">Server information only — not a personal verification event.</small>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================================
         TIMELINE
         ======================================================== -->
    <section class="section" id="timeline" aria-labelledby="timeline-title">
        <div class="wrap">
            <div class="section-heading">
                <h2 id="timeline-title">Chronology</h2>
                <p class="section-intro">Dated entries are rendered from a simple PHP record list. Each entry carries a classification, reference, and stable record ID.</p>
            </div>
            <div class="timeline">
                <?php foreach ($timeline as $entry): ?>
                    <article class="timeline-entry">
                        <time class="timeline-date"><?= e($entry['date']) ?></time>
                        <div class="timeline-content">
                            <span class="badge <?= e(badgeClass($entry['type'])) ?>"><?= e($entry['type']) ?></span>
                            <h3><?= e($entry['title']) ?></h3>
                            <p><?= e($entry['description']) ?></p>
                            <?php if ($entry['type'] === 'Allegation'): ?>
                                <p class="allegation-note">This entry reflects the site owner’s allegation or recollection and should not be interpreted as an adjudicated finding unless supporting records establish otherwise.</p>
                            <?php endif; ?>
                            <div class="entry-meta">
                                <span>RECORD ID: <?= e($entry['id']) ?></span>
                                <span>SOURCE: <?= e($entry['source']) ?></span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ========================================================
         EVIDENCE INDEX
         ======================================================== -->
    <section class="section" id="evidence" aria-labelledby="evidence-title">
        <div class="wrap">
            <div class="section-heading">
                <h2 id="evidence-title">Evidence &amp; Document Index</h2>
                <p class="section-intro">An auditable index for future primary documents. Placeholder records are explicitly marked and no hashes or supporting materials are fabricated.</p>
            </div>
            <div class="card-grid">
                <?php foreach ($evidenceItems as $item): ?>
                    <article class="record-card">
                        <div><span class="badge <?= e(badgeClass($item['type'])) ?>"><?= e($item['type']) ?></span></div>
                        <h3><?= e($item['title']) ?></h3>
                        <p><?= e($item['description']) ?></p>
                        <dl class="record-details">
                            <div><dt>Evidence ID</dt><dd><?= e($item['id']) ?></dd></div>
                            <div><dt>Date</dt><dd><?= e($item['date']) ?></dd></div>
                            <div><dt>Filename</dt><dd><?= e($item['filename']) ?></dd></div>
                            <div><dt>SHA-256</dt><dd><?= e($item['sha256']) ?></dd></div>
                        </dl>
                        <?php if (is_string($item['url']) && $item['url'] !== ''): ?>
                            <a class="record-link" href="<?= e($item['url']) ?>">Open supporting file</a>
                        <?php else: ?>
                            <span class="record-link">File: Not yet published</span>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ========================================================
         CONTINUITY
         ======================================================== -->
    <section class="section" id="continuity" aria-labelledby="continuity-title">
        <div class="wrap">
            <div class="section-heading">
                <h2 id="continuity-title">Continuity Instructions</h2>
                <p class="section-intro">If the owner becomes unexpectedly unable to maintain this site, this record is intended to help trusted parties identify and preserve relevant information.</p>
            </div>
            <div class="continuity-layout">
                <div class="continuity-copy">
                    <p>No private contact details are published in this initial version. The fields at right are deliberate placeholders and should be replaced only with information suitable for a public record.</p>
                    <p>Trusted parties preserving this record should retain original files, document provenance, timestamps, and published hashes where available.</p>
                </div>
                <dl class="continuity-fields">
                    <?php foreach ($continuityFields as $label => $value): ?>
                        <div class="continuity-row"><dt><?= e($label) ?></dt><dd><?= e($value) ?></dd></div>
                    <?php endforeach; ?>
                </dl>
            </div>
        </div>
    </section>

    <!-- ========================================================
         VERIFICATION
         ======================================================== -->
    <section class="section" id="verification" aria-labelledby="verification-title">
        <div class="wrap">
            <div class="section-heading">
                <h2 id="verification-title">Technical Verification</h2>
                <p class="section-intro">Technical checks can help preserve document integrity. They do not replace source evaluation or prove a person’s status.</p>
            </div>
            <div class="verification-grid">
                <article class="verification-panel">
                    <h3>Record identifiers</h3>
                    <ul class="verification-list">
                        <li><span>Record Version</span><strong><?= e($recordVersion) ?></strong></li>
                        <li><span>Statement ID</span><strong><?= e($statementId) ?></strong></li>
                        <li><span>Last Personal Verification</span><strong><?= e($lastPersonalVerification) ?></strong></li>
                        <li><span>Page Generated</span><strong><?= e($pageGenerated) ?></strong></li>
                    </ul>
                </article>
                <article class="verification-panel">
                    <h3>Document integrity</h3>
                    <p>SHA-256 hashes may be published for important documents so independent copies can later be compared against the published record.</p>
                    <p>A matching hash can show that two digital files are identical. It does not, by itself, establish that the contents are true.</p>
                    <div class="key-placeholder">Public Signing Key: Not yet published</div>
                </article>
            </div>
        </div>
    </section>

    <!-- ========================================================
         ARCHIVE INFORMATION
         ======================================================== -->
    <section class="section" id="archive" aria-labelledby="archive-title">
        <div class="wrap">
            <div class="section-heading">
                <h2 id="archive-title">Archive Information</h2>
                <p class="section-intro">Independent archived copies may exist in the future. No external copy is represented as available until a real location is published here.</p>
            </div>
            <div class="archive-grid">
                <?php foreach ($archiveFields as $label => $value): ?>
                    <div class="archive-item"><span><?= e($label) ?></span><strong><?= e($value) ?></strong></div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<!-- ========================================================
     FOOTER / RECORD NOTICE
     ======================================================== -->
<footer class="site-footer">
    <div class="wrap">
        <div class="footer-top">
            <div>
                <p class="footer-brand">AliveOnRecord.com</p>
                <p class="footer-subtitle">Personal Safety, Intent &amp; Continuity Record</p>
            </div>
            <dl class="footer-meta">
                <div><dt>Statement ID</dt><dd><?= e($statementId) ?></dd></div>
                <div><dt>Record Version</dt><dd><?= e($recordVersion) ?></dd></div>
                <div><dt>Last Personal Verification</dt><dd><?= e($lastPersonalVerification) ?></dd></div>
                <div><dt>Page Generated</dt><dd><?= e($pageGenerated) ?></dd></div>
            </dl>
        </div>
        <p class="footer-notice">Information published here is provided as a personal record. Allegations and opinions are identified as such and should not be mistaken for adjudicated findings.</p>
    </div>
</footer>
</body>
</html>
