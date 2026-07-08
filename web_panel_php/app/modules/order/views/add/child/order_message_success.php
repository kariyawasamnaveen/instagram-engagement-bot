<style>
  .order-success {
    color: #005c68!important;
    box-shadow: rgb(29 39 59 / 4%) 0 2px 4px 0 !important;
  }
</style>


<div id="order-message-area">
    <div class="order-success d-none">
        <div class="alert alert-info alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"></span></button>
        <h3><span class="fe fe-check"></span><?php echo lang('order_received'); ?></h3>
        <div class="p-b-20"> <?php echo lang('thank_you_your_order_has_been_received'); ?></div>
        <small>
            <ul>
            <li class="id"><?php echo lang('order_id'); ?>: <span>123456</span></li>
            <li class="service_name"><?php echo lang('service_name'); ?>: <span>Test</span></li>
            <li class="link"><?php echo lang('Link'); ?>: <span>link</span></li>
            <li class="quantity"><?php echo lang('Quantity'); ?>: <span>Quantity</span></li>
            <li class="username"><?php echo lang('Username'); ?>: <span>Username</span></li>
            <li class="posts"><?php echo lang('Posts'); ?>: <span>Posts</span></li>
            <li class="charge"><?php echo lang('Charge'); ?>: <span>Charge</span></li>
            <li class="balance"><?php echo lang('Balance'); ?>: <span>Balance</span></li>
            </ul>
        </small>
        </div>
    </div>
</div>