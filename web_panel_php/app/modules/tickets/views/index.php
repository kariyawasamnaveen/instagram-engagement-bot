<div class="row justify-content-center">
    <div class="col-md-10 col-xl-8">
        <!-- Support Header -->
        <div class="page-header d-flex align-items-center justify-content-between mb-4 mt-2">
            <h1 class="page-title mb-0" style="font-size: 24px; font-weight: 700;">
                <i class="fe fe-message-circle mr-2 text-primary"></i> <?=lang("Tickets")?>
            </h1>
            <a href="<?=cn($controller_name . "/add")?>" class="btn btn-primary d-none d-sm-flex align-items-center btn-pill px-4 py-2">
                <i class="fe fe-plus mr-2"></i> <?=lang("add_new")?>
            </a>
        </div>

        <!-- Search Area -->
        <div class="form-group mb-4">
            <div class="input-group search-area" style="background: #131315; border-radius: 12px; border: 1px solid var(--border-dim); overflow: hidden;">
                <span class="input-group-text bg-transparent border-0 pl-3">
                    <i class="fe fe-search text-muted"></i>
                </span>
                <input type="text" name="query" class="form-control bg-transparent border-0 text-white py-3" placeholder="<?=lang('Search_for_')?>" style="box-shadow: none;">
            </div>
        </div>

        <?php if(!empty($items)): ?>
            <div class="ticket-lists">
                <?php foreach ($items as $key => $item): 
                    $this->load->view('child/index', ['controller_name' => $controller_name, 'item' => $item]);
                endforeach; ?>
            </div>
            
            <div class="mt-4">
                <?php echo show_pagination($pagination); ?>
            </div>
        <?php else: ?>
            <div class="card p-5 text-center">
                <div class="mb-3">
                    <i class="fe fe-message-square text-muted" style="font-size: 48px;"></i>
                </div>
                <h4 class="text-white"><?=lang('no_tickets_found')?></h4>
                <p class="text-muted mb-4"><?=lang('if_you_have_any_questions_please_open_a_new_ticket')?></p>
                <a href="<?=cn($controller_name . "/add")?>" class="btn btn-primary btn-pill px-5">
                    <?=lang("add_new_ticket")?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Floating Action Button for Mobile -->
<a href="<?=cn($controller_name . "/add")?>" class="btn btn-primary btn-add-ticket-floating d-flex d-sm-none">
    <i class="fe fe-plus" style="font-size: 24px;"></i>
</a>
