<div class="row justify-content-center mt-3">
    <div class="col-md-10 col-xl-8">
        <!-- Add Ticket Card -->
        <div class="card p-4 border-dim" style="background: #131315; border-radius: 20px;">
            <div class="card-header pb-4 pt-0 px-0 border-bottom border-dim d-flex align-items-center">
                <a href="<?=cn($controller_name)?>" class="btn btn-dark btn-pill p-2 mr-3" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: #1A1A1C;">
                    <i class="fe fe-arrow-left"></i>
                </a>
                <h4 class="card-title text-white mb-0" style="font-weight: 700;">
                    <i class="fe fe-edit mr-2 text-primary"></i> <?=lang("add_new_ticket")?>
                </h4>
            </div>
            
            <div class="card-body p-0 pt-4">
                <?php 
                    $form_url = cn($controller_name. "/store/");
                    $redirect_url = cn($controller_name);
                    $form_attributes = ['class' => 'form actionForm', 'data-redirect' => $redirect_url, 'method' => "POST"];
                    echo form_open($form_url, $form_attributes); 
                ?>
                    <div class="form-body">
                        <div class="row">
                            <?php 
                                // We use the elements defined in the controller or helper
                                // But we'll refine them for the new UI if needed.
                                // The render_elements_form typically outputs form-groups.
                                echo render_elements_form($elements); 
                            ?>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-4 border-top border-dim">
                        <button type="submit" class="btn btn-primary btn-block btn-lg btn-pill py-3" style="font-weight: 700; box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.3);">
                            <i class="fe fe-send mr-2"></i> <?=lang('Submit')?>
                        </button>
                        <a href="<?=cn($controller_name)?>" class="btn btn-dark btn-block btn-pill py-3 mt-3" style="background: #1A1A1C; border: 1px solid var(--border-dim); color: var(--text-dim);">
                            <?=lang('Cancel')?>
                        </a>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
