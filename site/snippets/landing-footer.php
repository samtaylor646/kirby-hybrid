<?php
/**
 * landing-footer.php — Landing Page Footer Snippet
 * Fields sourced from the landing page blueprint footer tab.
 */
$landingPage = $site->find('landing') ?? $pages->first();
?>
<footer class="bg-black text-white px-8 lg:px-20 pt-20 pb-10 border-t border-white/5" id="contact">

    <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-20">

        <?php /* Column 1 — Brand */ ?>
        <div class="col-span-1 md:col-span-1">
            <h3 class="text-lg font-bold uppercase tracking-tighter mb-6">
                <?= $landingPage->footer_company()->html() ?>
            </h3>
            <p class="text-xs text-white/40 max-w-xs leading-relaxed">
                <?= $landingPage->footer_tagline()->html() ?>
            </p>
        </div>

        <?php /* Column 2 — Solutions Links */ ?>
        <div>
            <h4 class="text-[10px] uppercase tracking-widest text-white/30 mb-6 font-bold">Solutions</h4>
            <?php foreach ($landingPage->footer_solutions_links()->toStructure() as $link): ?>
            <a href="<?= $link->anchor()->html() ?>" class="footer-link">
                <?= $link->label()->html() ?>
            </a>
            <?php endforeach ?>
        </div>

        <?php /* Column 3 — Company Links */ ?>
        <div>
            <h4 class="text-[10px] uppercase tracking-widest text-white/30 mb-6 font-bold">Company</h4>
            <?php foreach ($landingPage->footer_company_links()->toStructure() as $link): ?>
            <a href="<?= $link->anchor()->html() ?>" class="footer-link">
                <?= $link->label()->html() ?>
            </a>
            <?php endforeach ?>
        </div>

        <?php /* Column 4 — Compliance */ ?>
        <div>
            <h4 class="text-[10px] uppercase tracking-widest text-white/30 mb-6 font-bold">Compliance</h4>
            <?php if ($landingPage->footer_cage()->isNotEmpty()): ?>
            <div class="text-[10px] font-mono text-white/40 mb-2">
                <?= $landingPage->footer_cage()->html() ?>
            </div>
            <?php endif ?>
            <?php if ($landingPage->footer_uei()->isNotEmpty()): ?>
            <div class="text-[10px] font-mono text-white/40 mb-2">
                <?= $landingPage->footer_uei()->html() ?>
            </div>
            <?php endif ?>
            <?php foreach ($landingPage->footer_compliance_links()->toStructure() as $link): ?>
            <a href="<?= $link->url()->html() ?>" class="footer-link">
                <?= $link->label()->html() ?>
            </a>
            <?php endforeach ?>
        </div>

    </div>

    <div class="flex flex-col md:flex-row justify-between items-end gap-8 pt-10 border-t border-white/5">
        <div>
            <h2 class="text-[12vw] font-light tracking-tighter opacity-5 leading-[0.7]">
                <?= $landingPage->footer_watermark()->html() ?>
            </h2>
        </div>
        <div class="text-right">
            <?php if ($landingPage->footer_location()->isNotEmpty()): ?>
            <div class="text-[10px] uppercase tracking-widest opacity-20 mb-2">
                <?= $landingPage->footer_location()->html() ?>
            </div>
            <?php endif ?>
            <div class="text-[10px] uppercase tracking-widest opacity-40">
                <?= $landingPage->footer_copyright()->html() ?>
            </div>
        </div>
    </div>

</footer>