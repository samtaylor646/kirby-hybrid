<?php
/**
 * header.php — Site Navigation Snippet
 * Fields sourced from the landing page blueprint nav tab.
 * On non-landing pages, falls back to site title if nav_logo is not set.
 */
$landingPage = $site->find('landing') ?? $pages->first();
?>
<nav id="mainNav" class="fixed top-0 w-full p-6 lg:p-10 z-100 flex justify-between items-center bg-linear-to-b from-black/50 to-transparent transition-all duration-300 ease-in-out">

    <a href="<?= $site->url() ?>" class="text-sm font-bold tracking-tighter uppercase">
        <?= $landingPage->nav_logo()->or($site->title())->html() ?>
    </a>

    <div class="hidden md:flex gap-10 text-[10px] uppercase font-bold tracking-[0.2em] opacity-100 pointer-events-auto">
            <a href="#mission" class="hover:opacity-60 transition-opacity no-underline text-white">Mission</a>
            <a href="#solutions" class="hover:opacity-60 transition-opacity no-underline text-white">Solutions</a>
            <a href="#strategy" class="hover:opacity-60 transition-opacity no-underline text-white">Strategy</a>
            <a href="#consultant" class="hover:opacity-60 transition-opacity no-underline text-white">✨ AI Architect</a>
        </div>

    <a
        href="<?= $landingPage->nav_cta_link()->or('#contact')->html() ?>"
        class="btn-magnetic py-2! px-6! text-[9px]!"
    >
        <?= $landingPage->nav_cta_text()->or('Contact')->html() ?>
    </a>

</nav>
