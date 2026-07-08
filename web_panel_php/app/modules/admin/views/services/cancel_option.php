<?php
  if (is_table_exists(ORDERS_CANCEL)) {
    $class_cancel_option = '';
  } else {
    $class_cancel_option = 'd-none';
  }
?>

<div class="col-md-12 <?php echo $class_cancel_option; ?>">
  <div class="form-group service-cancel">
    <div class="form-label">Cancel
      <label class="custom-switch">
        <span class="custom-switch-description m-r-20"><i class="fa fa-question-circle"></i></span>
        <input type="hidden" name="cancel" value="0">
        <input type="checkbox" name="cancel" id='cancel-option' class="custom-switch-input" data-toggle="collapse" data-target="#cancel-from" aria-expanded="false" aria-controls="refill" value="1" <?php echo (isset($item['cancel']) && $item['cancel']) ? 'checked' : '' ?>>
        <span class="custom-switch-indicator"></span>
      </label>
    </div>
  </div>
</div>
<div class="col-md-12  <?php echo $class_cancel_option; ?> collapse <?php echo (isset($item['cancel']) && $item['cancel']) ? 'show' : '' ?>" id="cancel-from">
  <div class="form-group">
    <label class="m-r-20">Cancel type
      <i class="fa fa-question-circle test_popover" data-toggle="popover" data-trigger="hover" data-placement="right" data-content="Provider - Cancel button will be synchronized to with provider. If the service support a cancel button, user can click cancel button. Cancellation Request will not work if the service does not support this feature <br><br> Manual - Need to send order ID to provider by manual when user click Cancel button" data-title="Cancel Type"></i>
    </label>
    <select name="cancel_type" class="form-control square cancel-type-option">
      <option value="0" <?php echo (isset($item['cancel']) && !$item['cancel']) ? 'selected' : '' ?> class='cancel-manual'> Manual </option>
      <?php if(isset($item['cancel']) && $item['cancel']) {?>          
      <option value="1" <?php echo (isset($item['cancel_type']) && $item['cancel_type']) ? 'selected' : '' ?> class='cancel-provider'> Provider </option>
      <?php }?>
    </select>
  </div>
</div>

<script>
  $(document).ready(function(){
    $('[data-toggle="popover"]').popover({html : true});
  });
</script>