<?php 
    $item_created = show_item_datetime($item['created'], 'long');
    $item_status = $item['status'];
    $status_class = 'badge-' . strtolower($item_status);
?>

<div class="row justify-content-center">
    <div class="col-md-10 col-xl-8">
        <!-- Ticket Header Card -->
        <div class="card mb-4 overflow-hidden border-dim" style="background: #131315; border-radius: 20px;">
            <div class="card-header border-bottom border-dim p-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <a href="<?=cn($controller_name)?>" class="btn btn-dark btn-pill p-2 mr-3" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: #1A1A1C;">
                        <i class="fe fe-arrow-left"></i>
                    </a>
                    <div>
                        <h4 class="mb-0 text-white" style="font-weight: 700;">#<?=$item['id']?> - <?=esc($item['subject'])?></h4>
                        <span class="text-muted small"><?=lang('Created')?>: <?=$item_created?></span>
                    </div>
                </div>
                <span class="badge <?=$status_class?> px-3 py-2" style="font-size: 11px;">
                    <?=ticket_status_title($item['status'])?>
                </span>
            </div>
            
            <div class="card-body p-4 bg-transparent messages" id="frame">
                <div class="content">
                    <ul class="p-0">
                        <!-- Initial Message (Description) -->
                        <li class="sent">
                            <div class="message">
                                <div class="msg-content"><?=nl2br(esc($item['description']))?></div>
                                <span class="text-muted small text-white-50 mt-1"><?=lang('You')?> • <?=$item_created?></span>
                            </div>
                        </li>

                        <!-- Conversation Flow -->
                        <?php
                            if ($items_ticket_message) {
                                foreach ($items_ticket_message as $key => $item_message) {
                                    echo show_item_ticket_message_detail($controller_name, $item_message, 'user');
                                }
                            }
                        ?>
                    </ul>
                </div>
            </div>

            <!-- Reply Form -->
            <?php if ($item['status'] != 'closed'): 
                $form_url = cn($controller_name."/store_message/");
                $redirect_url = cn($controller_name . '/') . $item['id'];
                $form_attributes = ['class' => 'card-footer border-top border-dim p-4 bg-dark-dim actionForm', 'data-redirect' => $redirect_url, 'method' => "POST"];
                $form_hidden = ['ids' => @$item['ids']];
            ?>
                <?php echo form_open($form_url, $form_attributes, $form_hidden); ?>
                    <div class="input-group" style="background: #1A1A1C; border-radius: 14px; border: 1px solid var(--border-dim); padding: 5px; overflow: hidden;">
                        <textarea name="message" class="form-control bg-transparent border-0 text-white px-3" rows="1" placeholder="<?=lang('Type_your_message_here...')?>" style="box-shadow: none; resize: none;"></textarea>
                        <button type="submit" class="btn btn-primary btn-pill px-4" style="margin: 2px;">
                            <i class="fe fe-send"></i>
                        </button>
                    </div>
                <?php echo form_close(); ?>
            <?php else: ?>
                <div class="card-footer text-center p-4 border-top border-dim">
                    <span class="text-muted small"><i class="fe fe-lock mr-1"></i> <?=lang('This_ticket_is_closed_and_cannot_be_replied_to')?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
