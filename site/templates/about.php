<?php snippet('header') ?>

<main>
  <?php // Changed field name to heroBlocks to match the blueprint ?>
  <?= $page->heroBlocks()->toBlocks() ?>

  <?php snippet('layouts', ['field' => $page->layout()]) ?>
</main>

<aside class="contact">
  <div class="container">
    <h2 class="h1">Get in contact</h2>
    <div class="grid" style="--gutter: 1.5rem">
      <section class="column text" style="--columns: 4">
        <h3>Address</h3>
        <?= $page->address()->kt() ?>
      </section>
      <section class="column text" style="--columns: 4">
        <h3>Email</h3>
        <p><?= Html::email($page->email()) ?></p>
        <h3>Phone</h3>
        <p><?= Html::tel($page->phone()) ?></p>
      </section>
      <section class="column text" style="--columns: 4">
        <h3>On the web</h3>
        <ul>
          <?php foreach ($page->social()->toStructure() as $social): ?>
          <li><?= Html::a($social->url(), $social->platform()) ?></li>
          <?php endforeach ?>
        </ul>
      </section>
    </div>
  </div>
</aside>

<?php snippet('footer') ?>