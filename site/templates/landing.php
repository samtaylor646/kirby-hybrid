<?php
/**
 * landing.php — Westport Partners Landing Page Template
 * Kirby 5.3 / Tailwind 4 / GSAP
 *
 * Field output rules:
 *   Writer fields   → ->value()      (stores HTML, do not re-process)
 *   Textarea fields → ->kirbytext()  (stores markdown, needs conversion)
 *   Text fields     → ->html()       (plain text, escaped for output)
 *
 * highlight() — wraps Writer field <em> tags as .highlight spans.
 *
 * Two layout fields are rendered via snippet loops:
 *   $page->mission_body()    — inside the Mission section
 *   $page->editorial_blocks() — between Solutions and Strategy
 */

function highlight(string $html): string {
    return str_replace(['<em>', '</em>'], ['<span class="highlight">', '</span>'], $html);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $site->title()->html() ?> | <?= strip_tags($page->hero_title()->value()) ?></title>
    <?= vite()->css('src/index.css') ?>
    <?= vite()->js('src/index.js') ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<div id="cursor"></div>
<div id="cursor-follower"></div>

<?php /* ── NAV ──────────────────────────────────────────────────────────────── */ ?>
<?php
$logoType = $page->nav_logo_type()->value();

// ── Resolve gap ──────────────────────────────────────────────────────────────
$gapPresets  = ['sm' => '4px', 'md' => '8px', 'lg' => '16px', 'xl' => '24px'];
$gapPreset   = $page->nav_logo_gap_preset()->value() ?? 'md';
$gapValue    = $gapPreset === 'custom'
    ? ($page->nav_logo_gap_custom()->value() ?? '8') . 'px'
    : ($gapPresets[$gapPreset] ?? '8px');

// ── Resolve padding ───────────────────────────────────────────────────────────
$padPresets  = ['none' => '0px', 'sm' => '4px', 'md' => '8px', 'lg' => '16px'];
$padPreset   = $page->nav_logo_padding_preset()->value() ?? 'none';
$padValue    = $padPreset === 'custom'
    ? ($page->nav_logo_padding_custom()->value() ?? '0') . 'px'
    : ($padPresets[$padPreset] ?? '0px');

// ── Resolve margin ────────────────────────────────────────────────────────────
$marPresets  = ['none' => '0px', 'sm' => '8px', 'md' => '16px', 'lg' => '24px'];
$marPreset   = $page->nav_logo_margin_preset()->value() ?? 'none';
$marValue    = $marPreset === 'custom'
    ? ($page->nav_logo_margin_custom()->value() ?? '0') . 'px'
    : ($marPresets[$marPreset] ?? '0px');

// ── Resolve flex direction from text position ─────────────────────────────────
$position    = $page->nav_logo_text_position()->value() ?? 'right';
$flexDir     = match($position) {
    'left'  => 'row-reverse',
    'right' => 'row',
    'above' => 'column-reverse',
    'below' => 'column',
    default => 'row',
};
$alignItems  = ($position === 'left' || $position === 'right') ? 'center' : 'flex-start';

$logoStyle   = "display:flex;flex-direction:{$flexDir};align-items:{$alignItems};gap:{$gapValue};padding:{$padValue};margin:{$marValue};";
?>
<nav class="landing-nav" id="nav">
    <a href="/" class="nav-logo" style="<?= $logoType === 'both' ? 'display:flex;align-items:center;' : '' ?>">
        <?php if ($logoType === 'image' || $logoType === 'both'): ?>
            <?php
            $logoFile = $logoType === 'both'
                ? $page->nav_logo_image_both()->toFile()
                : $page->nav_logo_image()->toFile();
            ?>
            <?php if ($logoType === 'both' && $logoFile): ?>
                <span class="nav-logo-inner" style="<?= $logoStyle ?>">
                    <img
                        src="<?= $logoFile->url() ?>"
                        alt="<?= $page->nav_logo_both_text()->html() ?>"
                        class="nav-logo-image"
                    >
                    <span class="nav-logo-text"><?= $page->nav_logo_both_text()->html() ?></span>
                </span>
            <?php elseif ($logoFile): ?>
                <img
                    src="<?= $logoFile->url() ?>"
                    alt="Logo"
                    class="nav-logo-image"
                >
            <?php endif ?>
        <?php else: ?>
            <?= $page->nav_logo()->html() ?>
        <?php endif ?>
    </a>
    <a href="<?= $page->nav_cta_link()->html() ?>" class="btn-magnetic nav-cta">
        <?= $page->nav_cta_text()->html() ?>
    </a>
</nav>

<?php /* ── HERO ─────────────────────────────────────────────────────────────── */ ?>
<header class="hero">
    <?php if ($video = $page->hero_video()->toFile()): ?>
    <video class="hero-video" autoplay muted loop playsinline>
        <source src="<?= $video->url() ?>" type="video/mp4">
    </video>
    <?php endif ?>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1 class="hero-title">
            <?= $page->hero_title()->value() ?>
        </h1>
        <div class="reveal" style="transition-delay:0.4s;">
            <a href="<?= $page->hero_cta_link()->html() ?>" class="btn-magnetic" id="ctaBtn">
                <?= $page->hero_cta_text()->html() ?>
            </a>
        </div>
    </div>
    <div class="absolute bottom-10 left-1/2 -translate-x-1/2 z-20">
        <div id="scrollIndicator" class="flex flex-col items-center gap-3 reveal">
            <span class="text-[10px] uppercase tracking-[0.3em] text-white/40 ml-[0.3em]">Scroll</span>
            <svg width="10" height="70" viewBox="0 0 10 70" fill="none" class="opacity-50">
                <line x1="5" y1="0" x2="5" y2="69" stroke="white" stroke-width="1"/>
                <path d="M1 64L5 69L9 64" stroke="white" stroke-width="1"/>
            </svg>
        </div>
    </div>
</header>

<main>

    <?php /* ── MISSION ──────────────────────────────────────────────────────── */ ?>
    <section id="mission" class="section-padding max-w-7xl mx-auto">
        <span class="label reveal">
            01 / <?= $page->mission_label()->html() ?>
        </span>
        <?php foreach ($page->mission_body()->toLayouts() as $layout): ?>
        <?php foreach ($layout->columns() as $column): ?>
        <?php foreach ($column->blocks() as $block): ?>
        <?php snippet('blocks/' . $block->type(), ['block' => $block]) ?>
        <?php endforeach ?>
        <?php endforeach ?>
        <?php endforeach ?>
    </section>

    <?php /* ── SOLUTIONS ────────────────────────────────────────────────────── */ ?>
    <section id="solutions" class="section-padding bg-primary">
        <div class="max-w-7xl mx-auto">
            <span class="label reveal">
                02 / <?= $page->solutions_label()->html() ?>
            </span>
            <?php $cardIndex = 0; foreach ($page->solutions_cards()->toStructure() as $card): $cardIndex++; ?>
            <div class="service-card reveal">
                <div class="text-[10px] font-bold uppercase tracking-[0.4em] opacity-60 mt-2">
                    <?= str_pad($cardIndex, 2, '0', STR_PAD_LEFT) ?> // <?= $card->category()->html() ?>
                </div>
                <div>
                    <h3 class="text-5xl mb-8 font-light tracking-tight">
                        <?= $card->title()->html() ?>
                    </h3>
                    <div class="card-copy overflow-hidden">
                        <div class="text-white/50 max-w-2xl text-xl leading-relaxed mb-10">
                            <?= $card->body()->kirbytext() ?>
                        </div>
                        <?php
                        $tags = array_filter(array_map('trim', explode("\n", $card->tags()->value() ?? '')));
                        if (!empty($tags)): ?>
                        <div class="grid grid-cols-2 gap-4 text-xs font-mono opacity-40">
                            <?php foreach (array_slice($tags, 0, 4) as $tag): ?>
                            <span><?= html($tag) ?></span>
                            <?php endforeach ?>
                        </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
            <?php endforeach ?>
        </div>
    </section>

    <?php /* ── EDITORIAL ────────────────────────────────────────────────────── */ ?>
    <?php if ($page->editorial_blocks()->isNotEmpty()): ?>
    <section id="editorial" class="section-padding max-w-7xl mx-auto">
        <?php foreach ($page->editorial_blocks()->toLayouts() as $layout): ?>
        <?php foreach ($layout->columns() as $column): ?>
        <?php foreach ($column->blocks() as $block): ?>
        <?php snippet('blocks/' . $block->type(), ['block' => $block]) ?>
        <?php endforeach ?>
        <?php endforeach ?>
        <?php endforeach ?>
    </section>
    <?php endif ?>

    <?php /* ── STRATEGY ─────────────────────────────────────────────────────── */ ?>
    <section id="strategy" class="section-padding bg-black min-h-screen">
        <div class="max-w-7xl mx-auto">
            <div id="strategyHeader">
                <span class="label reveal">
                    03 / <?= $page->strategy_label()->html() ?>
                </span>
                <h2 class="text-6xl font-light mb-6 tracking-tighter reveal">
                    <?= highlight($page->strategy_heading()->value()) ?>
                </h2>
            </div>
            <div class="strategy-grid reveal" id="strategyGrid">
                <?php $cellIndex = 0; foreach ($page->strategy_cells()->toStructure() as $cell): $cellIndex++; ?>
                <div class="strategy-cell" onclick="expandStrategyCell(this)">
                    <div class="close-trigger" onclick="closeStrategyCell(event, this)">Close</div>
                    <div class="cell-main-content">
                        <span class="text-white/20 font-mono text-xs mb-6 block">
                            <?= str_pad($cellIndex, 2, '0', STR_PAD_LEFT) ?> // <?= $cell->title()->html() ?>
                        </span>
                        <h4 class="text-2xl mb-6">
                            <?= $cell->title()->html() ?>
                        </h4>
                        <p class="text-base text-white/40 leading-relaxed">
                            <?= $cell->body()->value() ?>
                        </p>
                        <?php
                        $expandedItems = array_filter(array_map('trim', explode("\n", $cell->expanded_items()->value() ?? '')));
                        if (!empty($expandedItems) || $cell->expanded_body()->isNotEmpty()): ?>
                        <div class="cell-content-expanded">
                            <?php if ($cell->expanded_body()->isNotEmpty()): ?>
                            <p class="text-white/60 mb-6 text-lg">
                                <?= $cell->expanded_body()->value() ?>
                            </p>
                            <?php endif ?>
                            <?php if (!empty($expandedItems)): ?>
                            <ul class="text-xs space-y-3 opacity-50 font-mono">
                                <?php foreach ($expandedItems as $item): ?>
                                <li>> <?= html($item) ?></li>
                                <?php endforeach ?>
                            </ul>
                            <?php endif ?>
                        </div>
                        <?php endif ?>
                    </div>

                    <?php $mediaType = $cell->cell_media_type()->value(); ?>

                    <?php if ($mediaType === 'image' && ($media = $cell->cell_image()->toFile())): ?>
                    <div class="cell-side-image">
                        <img src="<?= $media->url() ?>" alt="<?= $cell->title()->html() ?>">
                    </div>

                    <?php elseif ($mediaType === 'video' && ($media = $cell->cell_video()->toFile())): ?>
                    <div class="cell-side-image">
                        <video autoplay muted loop playsinline>
                            <source src="<?= $media->url() ?>" type="video/mp4">
                        </video>
                    </div>

                    <?php elseif ($mediaType === 'gallery'): ?>
                    <?php $galleryFiles = $cell->cell_gallery()->toFiles(); ?>
                    <?php if ($galleryFiles->count() > 0): ?>
                    <div class="cell-side-image cell-side-gallery" data-gallery>
                        <?php foreach ($galleryFiles as $i => $img): ?>
                        <img
                            src="<?= $img->url() ?>"
                            alt="<?= $cell->title()->html() ?>"
                            class="gallery-slide<?= $i === 0 ? ' active' : '' ?>"
                        >
                        <?php endforeach ?>
                    </div>
                    <?php endif ?>
                    <?php endif ?>
                </div>
                <?php endforeach ?>
            </div>
        </div>
    </section>

    <?php /* ── AI CONSULTANT ────────────────────────────────────────────────── */ ?>
    <section id="consultant" class="section-padding bg-[#080808]">
        <div class="max-w-7xl mx-auto">
            <span class="label reveal">
                04 / <?= $page->consultant_label()->html() ?>
            </span>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
                <div class="reveal">
                    <h2 class="text-6xl font-light mb-10 leading-tight tracking-tight">
                        <?= highlight($page->consultant_heading()->value()) ?>
                    </h2>
                    <p class="text-white/50 text-xl leading-relaxed mb-10">
                        <?= $page->consultant_intro()->html() ?>
                    </p>
                </div>
                <div class="ai-panel reveal">
                    <div id="aiForm">
                        <label class="text-[10px] uppercase tracking-[0.3em] opacity-30 mb-4 block">
                            <?= $page->consultant_input_label()->html() ?>
                        </label>
                        <input
                            type="text"
                            id="userInput"
                            class="ai-input"
                            placeholder="<?= $page->consultant_input_placeholder()->html() ?>"
                        >
                        <button
                            onclick="generateAIAudit()"
                            id="aiSubmitBtn"
                            class="btn-magnetic mt-10 w-full"
                            data-label="<?= $page->consultant_button_text()->html() ?>"
                        >
                            <?= $page->consultant_button_text()->html() ?>
                        </button>
                    </div>
                    <div id="aiResponse" class="ai-response">
                        <div id="responseText"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<?php /* ── FOOTER ───────────────────────────────────────────────────────────── */ ?>
<footer class="landing-footer">
    <div class="max-w-7xl mx-auto px-8 py-16">
        <div class="footer-grid">
            <div class="footer-brand">
                <p class="footer-company"><?= $page->footer_company()->html() ?></p>
                <p class="footer-tagline"><?= $page->footer_tagline()->html() ?></p>
            </div>
            <div class="footer-links">
                <p class="footer-col-label">Solutions</p>
                <?php foreach ($page->footer_solutions_links()->toStructure() as $link): ?>
                <a href="#<?= $link->anchor()->html() ?>"><?= $link->label()->html() ?></a>
                <?php endforeach ?>
            </div>
            <div class="footer-links">
                <p class="footer-col-label">Company</p>
                <?php foreach ($page->footer_company_links()->toStructure() as $link): ?>
                <a href="<?= $link->anchor()->html() ?>"><?= $link->label()->html() ?></a>
                <?php endforeach ?>
            </div>
            <div class="footer-compliance">
                <p class="footer-cage"><?= $page->footer_cage()->html() ?></p>
                <p class="footer-uei"><?= $page->footer_uei()->html() ?></p>
                <?php foreach ($page->footer_compliance_links()->toStructure() as $link): ?>
                <a href="<?= $link->url()->html() ?>"><?= $link->label()->html() ?></a>
                <?php endforeach ?>
            </div>
        </div>
        <div class="footer-bottom">
            <span class="footer-location"><?= $page->footer_location()->html() ?></span>
            <span class="footer-copyright"><?= $page->footer_copyright()->html() ?></span>
            <span class="footer-watermark"><?= $page->footer_watermark()->html() ?></span>
        </div>
    </div>
</footer>

</body>
</html>