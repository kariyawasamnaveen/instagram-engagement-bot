<div id="main-modal-content">
  <div class="modal-right">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <form class="actionForm" action="<?= admin_url($controller_name . '/store') ?>" method="POST">
          <div class="modal-header bg-pantone">
            <h4 class="modal-title"><i class="fa fa-edit"></i> Edit Proxy</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Proxy (IP:Port or User:Pass@IP:Port)</label>
              <input type="text" class="form-control" name="proxy" value="<?= !empty($item['proxy']) ? $item['proxy'] : '' ?>">
            </div>
            <div class="form-group">
              <label>Type</label>
              <select name="type" class="form-control">
                <option value="http" <?= (!empty($item['type']) && $item['type'] == 'http') ? 'selected' : '' ?>>HTTP</option>
                <option value="socks5" <?= (!empty($item['type']) && $item['type'] == 'socks5') ? 'selected' : '' ?>>SOCKS5</option>
              </select>
            </div>
            <div class="form-group">
              <label>Status</label>
              <select name="status" class="form-control">
                <option value="1" <?= (!empty($item['status']) && $item['status'] == 1) ? 'selected' : '' ?>>Active</option>
                <option value="0" <?= (!empty($item['status']) && $item['status'] == 0) ? 'selected' : '' ?>>Deactive</option>
              </select>
            </div>
            <input type="hidden" name="ids" value="<?= !empty($item['ids']) ? $item['ids'] : '' ?>">
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
