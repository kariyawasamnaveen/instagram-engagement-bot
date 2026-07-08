<div class="row drip-feed-option d-none">
    <div class="col-md-12">
        <div class="form-group">
            <div class="form-label"><?=lang("dripfeed")?> 
                <label class="custom-switch">
                    <span class="custom-switch-description m-r-20">
                        
                        <i class="fa fa-question-circle" data-toggle="popover" data-trigger="hover" data-placement="right" data-content="<?=lang("drip_feed_desc")?>" data-title="<?=lang("what_is_dripfeed")?>"></i>
                    </span>
                    <input type="checkbox" name="is_drip_feed" class="is_drip_feed custom-switch-input" data-toggle="collapse" data-target="#drip-feed" aria-expanded="false" aria-controls="drip-feed">
                    <span class="custom-switch-indicator"></span>
                </label>
            </div>
        </div>

        <div class="row collapse" id="drip-feed">
            <div class="col-md-6">
                <div class="form-group">
                    <label><?=lang("Runs")?></label>
                    <input class="form-control square ajaxDripFeedRuns" type="number" name="runs" value="1">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label><?=lang("interval_in_minutes")?></label>
                    <select name="interval" class="form-control square">
                        <?php for ($i = 1; $i <= 1440; $i++) : ?>
                            <?php    if ($i % 10 == 0) : ?>
                                <option value="<?= $i ?>" <?= $i == 30 ? "selected" : ''?>><?= $i ?></option>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label><?=lang("total_quantity")?></label>
                    <input class="form-control square" name="total_quantity" type="number" disabled>
                </div>
            </div>
        </div>
    </div>
</div>