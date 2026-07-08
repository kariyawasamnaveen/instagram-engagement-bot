
<style> 
  .table-responsive td,
  .table-responsive td a {
    white-space: normal !important;
    word-break: break-word !important;
    overflow-wrap: break-word !important;
    max-width: 300px;
  }
  .table-responsive .dropdown-menu > a {
    white-space: nowrap !important;
  }
</style>

<div class="row">
  
  <div class="col-md-12 col-xl-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><?=lang("Lists")?></h3>
            <div class="card-options">
                <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
                <a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="<?= get_table_class(); ?>">
                <?php echo $table_thead_html; ?>
                <tbody id="table-body">
                    
                </tbody>
            </table>
        </div>
    </div>
  </div>
  
  <div id="pagination">
  </div>
  
</div>

