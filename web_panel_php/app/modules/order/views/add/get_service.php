<div class="col-md-12 col-sm-12 col-xs-12">
  <div class="form-group">
    <label><?=lang("service_name")?></label>
    <input class="form-control" name="service_name" type="text" value="<?=$service['name']?>" readonly>
  </div>
</div>   

<div class="col-md-4 col-sm-12 col-xs-12">
  <div class="form-group">
    <label><?=lang("minimum_amount")?></label>
    <input class="form-control" type="text" name="service_min" value="<?=$service['min']?>"  readonly>
  </div>
</div>

<div class="col-md-4 col-sm-12 col-xs-12">
  <div class="form-group">
    <label><?=lang("maximum_amount")?></label>
    <input class="form-control"  type="text" name="service_max" value="<?=$service['max']?>" readonly>
  </div>
</div>

<div class="col-md-4 col-sm-12 col-xs-12">
  <div class="form-group">
    <label><?=lang("price_per_1000")?> (<?=get_option("currency_symbol", "")?>)</label>
    <?php
      $user_price = get_user_price(session('uid'), (object)$service);
    ?>
    <input class="form-control" type="text" name="service_price_show" value="<?=show_price_format($user_price);?>" readonly>
    <input class="form-control" type="hidden" name="service_price" value="<?=$user_price;?>">
  </div>
</div>

<div class="col-md-12 col-sm-12 col-xs-12">
  <div class="form-group">
    <label for="userinput8" style="display: flex; justify-content: space-between; align-items: center;">
      <span><?=lang("Description")?></span>
      <?php if (!empty($service['desc'])) : ?>
      <button type="button" class="btn btn-sm btn-outline-warning" data-toggle="collapse" data-target="#premiumServiceDesc" aria-expanded="false" style="border-radius: 20px; font-size: 11px; padding: 2px 12px; border-color: rgba(212,175,55,0.4); color: var(--premium-gold);">
        <i class="fe fe-info mr-1"></i> <?=lang('view_details')?>
      </button>
      <?php endif; ?>
    </label>
    <?php
      if (!empty($service['desc'])) { ?>
      <div class="collapse" id="premiumServiceDesc">
        <div class="card bg-premium-dark border-gold-dim mt-2">
          <div class="card-body p-3 text-white-50" style="font-size: 13px; line-height: 1.6; background: rgba(255,255,255,0.03); border-radius: 12px;">
            <?php
              $desc = html_entity_decode($service['desc'], ENT_QUOTES);
              $desc = str_replace("\n", "<br>", $desc);
              echo strip_tags($desc, "<br>");
            ?>
          </div>
        </div>
      </div>
      <?php } else { ?>
      <textarea rows="3" class="form-control" name="service_desc" id="service_desc" disabled placeholder="No description available"></textarea>
    <?php }?>  
    
  </div>
</div>
