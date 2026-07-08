<?php
    $item_link_detail = cn($controller_name ."/" . $item['id']);
    $item_status = $item['status'];
    $status_class = 'badge-' . strtolower($item_status);
    $changed_date = convert_timezone($item['changed'], 'user');
?>

<div class="item tr_<?=$item['ids']?> p-0 mb-3" style="overflow: hidden; border: 1px solid var(--border-dim); border-radius: 16px; background: #131315;">
    <a href="<?=$item_link_detail?>" class="p-3 d-flex align-items-center text-decoration-none w-100 h-100">
        <div class="mr-3" style="width: 48px; height: 48px; min-width: 48px; background: #1A1A1C; border-radius: 12px; display: flex; align-items: center; justify-content: center; border: 1px solid var(--border-dim);">
            <i class="fe fe-message-square text-primary" style="font-size: 20px;"></i>
        </div>
        
        <div class="w-100">
            <div class="d-flex justify-content-between align-items-start">
                <h5 class="subject mb-1 text-white" style="font-size: 15px; font-weight: 600;">
                    #<?=$item['id']?> - <?=esc($item['subject'])?>
                    <?php if ($item['user_read']): ?>
                        <span class="badge badge-warning ml-2" style="font-size: 10px; padding: 2px 6px;"><?=lang("Unread")?></span>
                    <?php endif; ?>
                </h5>
                <span class="badge <?=$status_class?> ml-2">
                    <?=ticket_status_title($item['status'])?>
                </span>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-1">
                <span class="time text-muted" style="font-size: 12px;">
                    <i class="fe fe-clock mr-1"></i> <?=date("M d, H:i", strtotime($changed_date))?>
                </span>
                <span class="text-primary" style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                    <?=lang('View')?> <i class="fe fe-chevron-right ml-1"></i>
                </span>
            </div>
        </div>
    </a>
</div>