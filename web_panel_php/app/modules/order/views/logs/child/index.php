<?php if(!empty($items)): ?>
<div class="col-md-12">
    <div class="order-activity-list">
        <?php foreach ($items as $key => $item): 
            $item_id = esc($item['id']);
            $item_status = (in_array($item['status'], [ORDER_STATUS_FAIL, ORDER_STATUS_ERROR, ORDER_STATUS_AWAITING])) ? 'pending' : $item['status'];
            $status_class = 'badge-' . strtolower($item_status);
            $created_date = convert_timezone($item['created'], "user");
        ?>
        <div class="order-activity-item tr_<?=$item['ids']?>">
            <div class="order-item-header">
                <span class="order-id-pill">#<?=$item_id?></span>
                <span class="order-date"><?=date("M d, H:i", strtotime($created_date))?></span>
            </div>
            
            <h5 class="order-service-name">
                <span class="text-primary mr-1"><?=$item['service_id']?></span> 
                <?=esc($item['service_name'])?>
            </h5>

            <div class="d-flex justify-content-between align-items-end mt-3">
                <div class="order-details-pills">
                    <span class="order-pill"><?=lang('Quantity')?>: <strong><?=number_format($item['quantity'])?></strong></span>
                    <span class="order-pill"><?=get_option("currency_symbol", "$")?><?=show_price_format($item['charge'])?></span>
                    <?php if($item['remains'] > 0): ?>
                        <span class="order-pill"><?=lang('Remains')?>: <?=number_format($item['remains'])?></span>
                    <?php endif; ?>
                </div>
                
                <div class="text-right">
                    <div class="mb-2">
                        <?=show_item_status($controller_name, $item['id'], $item_status, '', 'user')?>
                    </div>
                    <div class="order-actions">
                        <?php
                            if (is_table_exists(ORDERS_REFILL)) {
                                echo show_item_refill_button($controller_name, $item);
                            }
                            if (is_table_exists(ORDERS_CANCEL)) {
                                echo show_item_cancel_button($controller_name, $item);
                            }
                        ?>
                    </div>
                </div>
            </div>

            <?php if(!empty($item['link'])): ?>
            <div class="mt-2 pt-2 border-top border-dim" style="font-size: 11px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                <a href="<?=esc($item['link'])?>" target="_blank" class="text-muted">
                    <i class="fe fe-link mr-1"></i> <?=esc($item['link'])?>
                </a>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="col-md-12">
    <?php echo show_pagination($pagination); ?>
</div>

<?php else: ?>
    <div class="col-md-12 text-center py-5">
        <?=show_empty_item()?>
    </div>
<?php endif; ?>
