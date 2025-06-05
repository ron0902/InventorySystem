<?php
  $page_title = 'All Purchase Requests';
  require_once('includes/load.php');
?>
<?php
// Checkin What level user has permission to view this page
//pull out all purchase requests from database
 $all_purchase_requests = find_all('purchase_requests');
?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
   <div class="col-md-12">
     <?php echo display_msg($msg); ?>
   </div>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Purchase Requests</span>
       </strong>
         <a href="add_pr.php" class="btn btn-info pull-right">Add New Purchase Request</a>
      </div>
     <div class="panel-body">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th class="text-center" style="width: 50px;">#</th>
            <th>Entity Name</th>
            <th>Fund Cluster</th>
            <th>Office/Section</th>
            <th>PR Number</th>
            <th>Responsibility Center Code</th>
            <th>Purpose</th>
            <th>Date Requested</th>
            <th class="text-center" style="width: 150px;">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach($all_purchase_requests as $pr): ?>
          <tr>
           <td class="text-center"><?php echo count_id();?></td>
           <td><?php echo remove_junk(ucwords($pr['entity_name']))?></td>
           <td><?php echo remove_junk(ucwords($pr['fund_cluster']))?></td>
           <td><?php echo remove_junk(ucwords($pr['office_section']))?></td>
           <td><?php echo remove_junk(ucwords($pr['pr_no']))?></td>
           <td><?php echo remove_junk(ucwords($pr['responsibility_center_code']))?></td>
           <td><?php echo remove_junk(ucwords($pr['purpose']))?></td>
           <td><?php echo read_date($pr['date_requested'])?></td>
           <td class="text-center">
             <div class="btn-group">
                <a href="edit_pr.php?id=<?php echo (int)$pr['request_id'];?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">
                  <i class="glyphicon glyphicon-pencil"></i>
               </a>
                <a href="delete_pr.php?id=<?php echo (int)$pr['request_id'];?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
                  <i class="glyphicon glyphicon-remove"></i>
                </a>
                <a href="print-layout/PR.php?id=<?php echo (int)$pr['request_id'];?>" class="btn btn-xs btn-info" data-toggle="tooltip" title="Print">
  <i class="glyphicon glyphicon-print"></i>
</a>
                </div>
           </td>
          </tr>
        <?php endforeach;?>
       </tbody>
     </table>
     </div>
    </div>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?>