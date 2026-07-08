<table class="table table-hover table-vcenter card-table">
  <?php 
    echo render_table_thead($columns, false, false, false); 
    echo render_report_tbody($controller_name, ['data_reports' => $data_reports, 'data_type' => 'model'], ['task' => $task]); 
  ?>
</table>