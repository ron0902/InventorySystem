<?php
$page_title = 'All Purchase Orders';
require_once('includes/load.php');

// Check user permission
page_require_level(1);

// Pull out all purchase orders from the database
$all_purchase_orders = find_all('purchase_orders');
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
          <span>Purchase Orders</span>
        </strong>
        <a href="add_po.php" class="btn btn-info pull-right">Add New Purchase Order</a>
      </div>
      <div class="panel-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>PO Number</th>
              <th>Supplier Name</th>
              <th>Date</th>
              <th>Total Amount</th>
              <th class="text-center" style="width: 150px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($all_purchase_orders as $po): ?>
              <tr>
                <td class="text-center"><?php echo count_id(); ?></td>
                <td><?php echo remove_junk(ucwords($po['po_id'])); ?></td>
                <td><?php echo remove_junk(ucwords($po['supplier_name'])); ?></td>
                <td><?php echo read_date($po['date']); ?></td>
                <td><?php echo remove_junk(ucwords($po['total_amount'])); ?></td>
                <td class="text-center">
                  <div class="btn-group">
                    <a href="edit_po.php?id=<?php echo (int)$po['po_id']; ?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">
                      <i class="glyphicon glyphicon-pencil"></i>
                    </a>
                    <a href="delete_po.php?id=<?php echo (int)$po['po_id']; ?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
                      <i class="glyphicon glyphicon-remove"></i>
                    </a>
                    <a href="print-layout/PO.php?id=<?php echo (int)$po['po_id']; ?>" class="btn btn-xs btn-info" data-toggle="tooltip" title="Print">
                      <i class="glyphicon glyphicon-print"></i>
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>