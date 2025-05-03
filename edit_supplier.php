<?php
$page_title = 'Edit Supplier';
require_once('includes/load.php');
// Check user permission level
page_require_level(1);

// Fetch supplier data
$supplier = find_by_id('suppliers', (int)$_GET['supplier_id'], 'supplier_id');
if (!$supplier) {
    $session->msg("d", "Missing supplier id.");
    redirect('supplierdetails.php');
}

if (isset($_POST['edit_supplier'])) {
  $req_fields = array('supplier-name', 'supplier-address', 'contact_number', 'supplier-email');
  validate_fields($req_fields);

  // Sanitize and escape input data
  $name = remove_junk($db->escape($_POST['supplier-name']));
  $address = remove_junk($db->escape($_POST['supplier-address']));
  $contact_number = remove_junk($db->escape($_POST['contact_number']));
  $email = remove_junk($db->escape($_POST['supplier-email']));

  if (empty($errors)) {
      // Update the SQL query to include new fields
      $sql = "UPDATE suppliers SET ";
      $sql .= "name='{$name}', address='{$address}', contact_number='{$contact_number}', email='{$email}' ";
      $sql .= "WHERE supplier_id='{$supplier['supplier_id']}'"; // Use 'supplier_id' here

      $result = $db->query($sql);
      if ($result && $db->affected_rows() === 1) {
          $session->msg("s", "Successfully updated Supplier");
          redirect('supplierdetails.php', false);
      } else {
          $session->msg("d", "Sorry! Failed to Update");
          redirect('supplierdetails.php', false);
      }
  } else {
      $session->msg("d", $errors);
      redirect('supplierdetails.php', false);
  }
}
?>

<?php include_once('layouts/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
    <div class="col-md-5">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>Editing <?php echo remove_junk(ucfirst($supplier['name'])); ?></span>
                </strong>
            </div>
            <div class="panel-body">
                <form method="post" action="edit_supplier.php?id=<?php echo (int)$supplier['supplier_id']; ?>">
                    <div class="form-group">
                        <label for="supplier-name">Supplier Name</label>
                        <input type="text" class="form-control" name="supplier-name" value="<?php echo remove_junk(ucfirst($supplier['name'])); ?>">
                    </div>
                    <div class="form-group">
                        <label for="supplier-address">Address</label>
                        <input type="text" class="form-control" name="supplier-address" value="<?php echo remove_junk($supplier['address']); ?>">
                    </div>
                    <div class="form-group">
    <label for="supplier-contact">Contact Number</label>
    <input type="text" class="form-control" name="contact_number" value="<?php echo remove_junk($supplier['contact_number']); ?>">
</div>
                    <div class="form-group">
                        <label for="supplier-email">Email</label>
                        <input type="email" class="form-control" name="supplier-email" value="<?php echo remove_junk($supplier['email']); ?>">
                    </div>
                    <button type="submit" name="edit_supplier" class="btn btn-primary">Update Supplier</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>