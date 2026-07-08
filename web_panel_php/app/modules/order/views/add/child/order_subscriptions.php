<div class="row order-subscriptions d-none">

    <div class="col-md-6">
    <div class="form-group">
        <label><?=lang("Username")?></label>
        <input class="form-control square" type="text" name="sub_username">
    </div>
    </div>

    <div class="col-md-6">
    <div class="form-group">
        <label><?=lang("New_posts")?></label>
        <input class="form-control square" type="number" placeholder="<?=lang("minimum_1_post")?>" name="sub_posts">
    </div>
    </div>

    <div class="col-md-6">
    <div class="form-group">
        <label><?=lang("Quantity")?></label>
        <input class="form-control square" type="number" name="sub_min" placeholder="<?=lang("min")?>">
    </div>
    </div>
    <div class="col-md-6">
    <div class="form-group">
        <label>&nbsp;</label>
        <input class="form-control square" type="number" name="sub_max" placeholder="<?=lang("max")?>">
    </div>
    </div>

    <div class="col-md-6">
    <div class="form-group">
        <label><?=lang("Delay")?> (<?=lang("minutes")?>)</label>
        <select name="sub_delay" class="form-control square">
        <option value="0"><?=lang("No_delay")?></option>
        <option value="5">5</option>
        <option value="10">10</option>
        <option value="15">15</option>
        <option value="30">30</option>
        <option value="60">60</option>
        <option value="90">90</option>
        </select>
    </div>
    </div>

    <div class="col-md-6">
    <div class="form-group">
        <label><?=lang("Expiry")?></label>
        <div class="input-group">
        <input type="text" class="form-control datepicker" name="sub_expiry" onkeydown="return false" name="expiry" placeholder="" id="expiry">
        <span class="input-group-append">
            <button class="btn btn-info" type="button" onclick="document.getElementById('expiry').value = ''"><i class="fe fe-trash-2"></i></button>
        </span>
        </div>
    </div>
    </div>

</div>