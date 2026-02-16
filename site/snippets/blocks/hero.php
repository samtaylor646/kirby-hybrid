<?php
/** * Hero Block Snippet
 * Handles Image/Video background, Focal Point, GSAP Classes, Themes, and Alignment
 */
$id = "hero-" . $block->id();
$type = $block->bg_type()->value();
$theme = $block->theme()->or('light')->value();
$align = $block->text_align()->or('center')->value();
$overlay = $block->overlay_opacity()->or(40)->value() / 100;

// Heights
$hMobile = $block->height_mobile()->or(70)->value();
$hDesktop = $block->height_desktop()->or(100)->value();

// Media & Focal Point
$imgFile = $block->image()->toFile();
$focalPoint = ($imgFile && $imgFile->focal()) ? $imgFile->focal() : '50% 50%';

// Flex Alignment Mapping
$flexAlign = [
    'left'   => 'flex-start',
    'center' => 'center',
    'right'  => 'flex-end'
];
?>

<style>
  #<?= $id ?> {
    --hero-h: <?= $hMobile ?>vh;
    --focal: <?= $focalPoint ?>;
    --text-color: <?= ($theme === 'dark') ? '#000000' : '#ffffff' ?>;
    --btn-bg: <?= ($theme === 'dark') ? '#000000' : '#ffffff' ?>;
    --btn-text: <?= ($theme === 'dark') ? '#ffffff' : '#000000' ?>;
    --overlay-color: <?= ($theme === 'dark') ? '255, 255, 255' : '0, 0, 0' ?>;
    --content-align: <?= $flexAlign[$align] ?>;
    --text-align: <?= $align ?>;
    
    position: relative;
    width: 100%;
    height: var(--hero-h);
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: var(--content-align);
    color: var(--text-color);
    text-align: var(--text-align);
    background-color: #000;
  }

  @media (min-width: 1024px) {
    #<?= $id ?> { --hero-h: <?= $hDesktop ?>vh; }
  }

  /* Media Layers */
  .hero-media {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: var(--focal);
    z-index: 0;
  }

  /* Responsive Overlay */
  #<?= $id ?>::after {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(var(--overlay-color), <?= $overlay ?>);
    z-index: 1;
  }

  /* Content Styling */
  #<?= $id ?> .hero-content {
    position: relative;
    z-index: 2;
    padding: 2rem 5%;
    max-width: 50rem;
    opacity: 0;
    transform: translateY(40px);
  }

  .hero-subheading {
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    margin-bottom: 0.75rem;
    font-size: 0.9rem;
  }

  .hero-title {
    font-size: clamp(2.5rem, 10vw, 5rem);
    line-height: 1.1;
    margin-bottom: 1.5rem;
  }

  .hero-text {
    font-size: 1.25rem;
    line-height: 1.6;
    margin-bottom: 2rem;
    opacity: 0.9;
  }

  .hero-cta {
    display: inline-block;
    padding: 1rem 2.5rem;
    background: var(--btn-bg);
    color: var(--btn-text);
    text-decoration: none;
    font-weight: 700;
    border-radius: 2px;
    transition: transform 0.3s ease;
  }

  .hero-cta:hover { transform: scale(1.05); }
</style>

<section id="<?= $id ?>" class="gsap-hero-section">
  
  <?php if ($type === 'image' && $imgFile): ?>
    <img src="<?= $imgFile->url() ?>" class="hero-media" alt="<?= $imgFile->alt() ?>">
  <?php elseif ($type === 'video' && $video = $block->video()->toFile()): ?>
    <video 
      data-src="<?= $video->url() ?>" 
      class="hero-media js-hero-video" 
      muted loop playsinline>
    </video>
  <?php endif ?>

  <div class="hero-content">
    <?php if ($block->subheading()->isNotEmpty()): ?>
      <p class="hero-subheading"><?= $block->subheading()->html() ?></p>
    <?php endif ?>

    <h1 class="hero-title"><?= $block->title()->html() ?></h1>

    <?php if ($block->text()->isNotEmpty()): ?>
      <div class="hero-text"><?= $block->text()->kt() ?></div>
    <?php endif ?>

    <?php if ($block->cta_link()->isNotEmpty() && $block->cta_text()->isNotEmpty()): ?>
      <a href="<?= $block->cta_link() ?>" class="hero-cta">
        <?= $block->cta_text()->html() ?>
      </a>
    <?php endif ?>
  </div>
</section>