<div id="main-modal-content">
  <div class="modal-right">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <form class="form actionForm" action="<?= admin_url($controller_name . '/store') ?>" data-redirect="<?= admin_url($controller_name) ?>" method="POST">
          <div class="modal-header bg-pantone">
            <h4 class="modal-title"><i class="fa fa-edit"></i> Edit/Add Account</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="form-body">
              <div class="row justify-content-md-center">
                <div class="col-md-12 col-sm-12 col-xs-12">
                  <div class="form-group">
                    <label>Username</label>
                    <input type="text" class="form-control square" name="username" value="<?= (!empty($item['username'])) ? $item['username'] : '' ?>">
                  </div>
                  <div class="form-group">
                    <label>Password</label>
                    <input type="text" class="form-control square" name="password" value="<?= (!empty($item['password'])) ? $item['password'] : '' ?>">
                  </div>
                  <div class="form-group">
                    <label>Proxy</label>
                    <input type="text" class="form-control square" name="proxy" value="<?= (!empty($item['proxy'])) ? $item['proxy'] : '' ?>">
                  </div>
                  <div class="form-group">
                    <label>Gender (Tagging)</label>
                    <select name="gender" class="form-control square">
                      <option value="neutral" <?= (!empty($item['gender']) && $item['gender'] == 'neutral') ? 'selected' : '' ?>>Neutral</option>
                      <option value="male" <?= (!empty($item['gender']) && $item['gender'] == 'male') ? 'selected' : '' ?>>Male</option>
                      <option value="female" <?= (!empty($item['gender']) && $item['gender'] == 'female') ? 'selected' : '' ?>>Female</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label>Interest Tags (e.g., music, fashion)</label>
                    <input type="text" class="form-control square" name="interest_tags" value="<?= (!empty($item['interest_tags'])) ? $item['interest_tags'] : '' ?>">
                  </div>
                  <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control square">
                      <option value="1" <?= (!empty($item['status']) && $item['status'] == 1) ? 'selected' : '' ?>>Active</option>
                      <option value="0" <?= (!empty($item['status']) && $item['status'] == 0) ? 'selected' : '' ?>>Deactive</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <input type="hidden" name="id" value="<?= (!empty($item['id'])) ? $item['id'] : '' ?>">
            <button type="submit" class="btn btn-primary btn-min-width mr-1 mb-1">Submit</button>
            <button type="button" class="btn btn-dark btn-min-width mr-1 mb-1" data-dismiss="modal">Close</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
