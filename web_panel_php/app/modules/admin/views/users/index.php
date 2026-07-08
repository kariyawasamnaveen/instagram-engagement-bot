<?php 
  // Page header
  $is_add_new_btn = (staff_has_permission($controller_name, 'add')) ? 'add-new' : '';
  echo show_page_header($controller_name, ['page-options' => $is_add_new_btn, 'page-options-type' => 'ajax-modal']);
  // Page header Filter
  echo show_page_header_filter($controller_name, ['items_status_count' => $items_status_count, 'params' => $params]);
?>

<?php $this->load->view('common_block/table_blade'); ?>


<div id="customRate" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"></i> Edit custom rates (ID: 1)</h4>
        <button type="button" class="close" data-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <select name="service-id" class="select-service-item" class="form-control custom-select">
                <option value='{"service_id": "1189", "rate": "0.53", "name": "Instagram Likes [100 - 3K] [Instant] [Exclusive]"}' data-rate="1" data-data='{"rate": "0.53", "name": "Instagram Likes [100 - 3K] [Instant] [Exclusive]"}'>128 - Instagram Likes [100 - 3K] [Instant] [Exclusive] [$0.18]</option>
                <option value='{"service_id": "123", "rate": "0.78", "name": "Instagram Likes [100 - 3K] [Instant] [Exclusive]"}' data-rate="1" data-data='{"rate": "0.53", "name": "Instagram Likes [100 - 3K] [Instant] [Exclusive]"}'>123 - Instagram Likes [100 - 3K] [Instant] [Exclusive] [$0.18]</option>
              </select>
            </div>
          </div>
        </div>
        
        <div class="o-auto" style="height: 20rem">
          <ul class="list-unstyled list-separated services-group-items">

            <div class="s-items">
              <li class="list-separated-item s-item">
                <div class="row align-items-center">
                  <div class="col">
                    111
                  </div>
                  <div class="col-md-7">
                    Facebook [Real Relevant Comments - Custom Comments]
                  </div>
                  <div class="col-md-1">
                    0.53
                  </div>
                  <div class="col-md-2">
                    <input type="hidden" class="form-control" value="customRates[1123][price]">
                    <input type="text" class="form-control" >
                  </div>
                  <div class="col-md-1">
                    <button class="btn btn-secondary btn-remove-item" type="button"><i class="fe fe-trash-2"></i></button>
                  </div>
                </div>
              </li>
            </div>

            <div class="s-item-more d-none">
              <li class="list-separated-item s-item" id="item__serviceID__">
                <div class="row align-items-center">
                  <div class="col">
                    __serviceID__
                  </div>
                  <div class="col-md-7">
                    __serviceName__
                  </div>
                  <div class="col-md-1">
                    __serviceRate__
                  </div>
                  <div class="col-md-2">
                    <input type="hidden" class="form-control" value="customRates[__serviceID__][rate_id]">
                    <input type="number" class="form-control" value="customRates[__serviceID__][price]">
                  </div>
                  <div class="col-md-1">
                    <button class="btn btn-secondary btn-remove-item" type="button"><i class="fe fe-trash-2"></i></button>
                  </div>
                </div>
              </li>
            </div>
            
          </ul>
        </div>
       
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
