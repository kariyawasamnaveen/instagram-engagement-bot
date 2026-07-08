<div id="main-modal-content">
  <div class="modal-right">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <form class="form actionForm" action="<?= admin_url($controller_name . '/store') ?>" data-redirect="<?= admin_url($controller_name) ?>" method="POST">
          <div class="modal-header bg-pantone">
            <h4 class="modal-title"><i class="fa fa-plus"></i> Bulk Import Accounts</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="form-body">
              <div class="row justify-content-md-center">
                <div class="col-md-12 col-sm-12 col-xs-12">
                  <div class="alert alert-info">
                    Format: <strong>username|password|proxy|gender|tags</strong> (1 account per line)<br>
                    Gender: male, female, or neutral.
                  </div>
                  <div class="form-group">
                    <label>Paste accounts list</label>
                    <textarea rows="10" name="bulk_items" class="form-control square" placeholder="userA|passA|proxyA:port|female|fashion"></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-min-width mr-1 mb-1">Upload Now</button>
            <button type="button" class="btn btn-dark btn-min-width mr-1 mb-1" data-dismiss="modal">Close</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
