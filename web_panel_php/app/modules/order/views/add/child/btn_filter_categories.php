<div class="col-md-10 col-xl-10">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="fe fe-heart"></i> <?= lang('Choose_your_preferred_social_network')?></h3>
        <div class="card-options">
          <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
        </div>
      </div>
      <?php
        $social_media = filter_categories_by_keyword($filter_categories);
      ?>
      <div class="card-body social-cat">
        <div class="btn-list social-btn">
          <?php foreach ($social_media as $key => $item_social) : ?>
            <button class="brand-category btn round btn-secondary" data-id="<?= esc($key); ?>">
              <?= $item_social['icon_class']; ?>
              <?= $item_social['name']; ?>
            </button>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
</div>