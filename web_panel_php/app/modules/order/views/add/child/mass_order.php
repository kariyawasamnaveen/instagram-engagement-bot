<form class="form actionForm" action="<?=cn($controller_name."/ajax_mass_order")?>" data-redirect="<?=cn($controller_name)?>" method="POST">
    <div class="x_content row">
        <div class="col-md-6 col-sm-12 col-xs-12">
            <div class="content-header-title">
            <h6> <?=lang("one_order_per_line_in_format")?></h6>
            </div>
            <div class="form-group">
            <textarea id="editor" rows="14" name="mass_order" class="form-control square" placeholder="service_id|quantity|link"></textarea>
            </div>
            <div class="form-group">
            <label class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" name="agree">
                <span class="custom-control-label text-uppercase"><?=lang("yes_i_have_confirmed_the_order")?></span>
            </label>
            </div>
        </div>
        <div class="col-md-6 col-sm-12 col-xs-12">
            <div class="mass_order_error" id="result_notification">
            <div class="content-header-title">
                <h6><i class="fa fa-info-circle"></i> <?=lang("note")?></h6>
            </div>
            <div class="form-group">
                <?=lang("here_you_can_place_your_orders_easy_please_make_sure_you_check_all_the_prices_and_delivery_times_before_you_place_a_order_after_a_order_submited_it_cannot_be_canceled")?>
            </div>
            </div>
        </div>
        </div>
        <div class="form-actions left">
        <button type="submit" class="btn btn-primary btn-spinner-border btn-lg mr-1 mb-1">
            <?=lang("place_order")?>
        </button>
    </div>
</form>