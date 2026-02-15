<?php
/**
 * landing-nav.php — Landing Page Navigation Snippet
 * Fields sourced from the landing page blueprint nav tab.
 */
$landingPage = $site->find('landing') ?? $pages->first();

$logoType = $landingPage->nav_logo_type()->or('text')->value();

// ── Resolve logo height ───────────────────────────────────────────────────────
$heightPresets = ['sm' => '24px', 'md' => '32px', 'lg' => '48px', 'xl' => '64px'];

$imageHeightPreset = $landingPage->nav_logo_image_height_preset()->or('md')->value();
$imageHeight = $imageHeightPreset === 'custom'
    ? ($landingPage->nav_logo_image_height_custom()->or('32')->value()) . 'px'
    : ($heightPresets[$imageHeightPreset] ?? '32px');

$bothHeightPreset = $landingPage->nav_logo_both_height_preset()->or('md')->value();
$bothHeight = $bothHeightPreset === 'custom'
    ? ($landingPage->nav_logo_both_height_custom()->or('32')->value()) . 'px'
    : ($heightPresets[$bothHeightPreset] ?? '32px');

// ── Resolve spacing (both mode only) ─────────────────────────────────────────
$gapPresets = ['sm' => '4px', 'md' => '8px', 'lg' => '16px', 'xl' => '24px'];
$padPresets = ['none' => '0px', 'sm' => '4px', 'md' => '8px', 'lg' => '16px'];
$marPresets = ['none' => '0px', 'sm' => '8px', 'md' => '16px', 'lg' => '24px'];

$gapPreset = $landingPage->nav_logo_gap_preset()->or('md')->value();
$gapValue  = $gapPreset === 'custom'
    ? ($landingPage->nav_logo_gap_custom()->or('8')->value()) . 'px'
    : ($gapPresets[$gapPreset] ?? '8px');

$padPreset = $landingPage->nav_logo_padding_preset()->or('none')->value();
$padValue  = $padPreset === 'custom'
    ? ($landingPage->nav_logo_padding_custom()->or('0')->value()) . 'px'
    : ($padPresets[$padPreset] ?? '0px');

$marPreset = $landingPage->nav_logo_margin_preset()->or('none')->value();
$marValue  = $marPreset === 'custom'
    ? ($landingPage->nav_logo_margin_custom()->or('0')->value()) . 'px'
    : ($marPresets[$marPreset] ?? '0px');

$position   = $landingPage->nav_logo_text_position()->or('right')->value();
$flexDir    = match($position) {
    'left'  => 'row-reverse',
    'right' => 'row',
    'above' => 'column-reverse',
    'below' => 'column',
    default => 'row',
};
$isHorizontal = ($position === 'left' || $position === 'right');
$alignItems   = $isHorizontal ? 'center' : 'flex-start';
$innerStyle   = "display:inline-flex;flex-direction:{$flexDir};align-items:{$alignItems};gap:{$gapValue};padding:{$padValue};margin:{$marValue};";
?>
<nav id="mainNav" class="fixed top-0 w-full px-6 lg:px-10 pt-4 pb-2 z-100 flex justify-between items-center min-h-[64px] bg-linear-to-b from-black/50 to-transparent transition-all duration-300 ease-in-out">

    <?php /* ── LOGO ─────────────────────────────────────────────────────────── */ ?>
    <a href="<?= $site->url() ?>" class="flex items-center shrink-0 text-sm font-bold tracking-tighter uppercase no-underline text-white">

        <?php if ($logoType === 'text'): ?>
            <?= $landingPage->nav_logo()->or($site->title())->html() ?>

        <?php elseif ($logoType === 'image'): ?>
            <?php $logoFile = $landingPage->nav_logo_image()->toFile() ?>
            <?php if ($logoFile): ?>
                <img
                    src="<?= $logoFile->url() ?>"
                    alt="<?= $landingPage->nav_logo()->or($site->title())->html() ?>"
                    class="block w-auto"
                    style="height:<?= $imageHeight ?>;"
                >
            <?php else: ?>
                <?= $landingPage->nav_logo()->or($site->title())->html() ?>
            <?php endif ?>

        <?php elseif ($logoType === 'both'): ?>
            <?php $logoFile = $landingPage->nav_logo_image_both()->toFile() ?>
            <span style="<?= $innerStyle ?>">
                <?php if ($logoFile): ?>
                    <img
                        src="<?= $logoFile->url() ?>"
                        alt="<?= $landingPage->nav_logo_both_text()->or($site->title())->html() ?>"
                        class="block w-auto"
                        style="height:<?= $bothHeight ?>;"
                    >
                <?php endif ?>
                <span class="nav-logo-text">
                    <?= $landingPage->nav_logo_both_text()->or($site->title())->html() ?>
                </span>
            </span>

        <?php endif ?>

    </a>

    <?php /* ── ANCHOR LINKS ─────────────────────────────────────────────────── */ ?>
    <div class="hidden md:flex gap-10 items-center text-[10px] uppercase font-bold tracking-[0.2em]">
        <a href="#mission"    class="hover:opacity-60 transition-opacity no-underline text-white">Mission</a>
        <a href="#solutions"  class="hover:opacity-60 transition-opacity no-underline text-white">Solutions</a>
        <a href="#strategy"   class="hover:opacity-60 transition-opacity no-underline text-white">Strategy</a>
        <a href="#consultant" class="hover:opacity-60 transition-opacity no-underline text-white">✨ AI Architect</a>
    </div>

    <?php /* ── CTA ──────────────────────────────────────────────────────────── */ ?>
    <a
        href="<?= $landingPage->nav_cta_link()->or('#contact')->html() ?>"
        class="btn-magnetic py-2! px-6! text-[9px]! shrink-0"
    >
        <?= $landingPage->nav_cta_text()->or('Contact')->html() ?>
    </a>

</nav>