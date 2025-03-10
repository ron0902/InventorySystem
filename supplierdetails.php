<?php
  $page_title = 'All Suppliers';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
  
  $all_suppliers = find_all('suppliers');
?>
<?php
 if(isset($_POST['add_supplier'])){
   $req_fields = array('supplier-name', 'supplier-address', 'supplier-contact', 'supplier-email');
   validate_fields($req_fields);
   $name = remove_junk($db->escape($_POST['supplier-name']));
   $address = remove_junk($db->escape($_POST['supplier-address']));
   $contact = remove_junk($db->escape($_POST['supplier-contact']));
   $email = remove_junk($db->escape($_POST['supplier-email']));
   if(empty($errors)){
      $sql  = "INSERT INTO suppliers (name, address, contact_number, email)";
      $sql .= " VALUES ('{$name}', '{$address}', '{$contact}', '{$email}')";
      if($db->query($sql)){
        $session->msg("s", "Successfully Added New Supplier");
        redirect('supplierdetails.php',false);
      } else {
        $session->msg("d", "Sorry Failed to insert.");
        redirect('supplierdetails.php',false);
      }
   } else {
     $session->msg("d", $errors);
     redirect('supplierdetailss.php',false);
   }
 }
?>
<?php include_once('layouts/header.php'); ?>

  <div class="row">
     <div class="col-md-12">
       <?php echo display_msg($msg); ?>
     </div>
  </div>
   <div class="row">
    <div class="col-md-5">
      <div class="panel panel-default">
        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>Add New Supplier</span>
         </strong>
        </div>
        <div class="panel-body">
          <form method="post" action="supplierdetails.php">
            <div class="form-group">
                <input type="text" class="form-control" name="supplier-name" placeholder="Supplier Name" required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="supplier-address" placeholder="Supplier Address">
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="supplier-contact" placeholder="Contact Number">
            </div>
            <div class="form-group">
                <input type="email" class="form-control" name="supplier-email" placeholder="Email Address">
            </div>
            <button type="submit" name="add_supplier" class="btn btn-primary">Add Supplier</button>
        </form>
        </div>
      </div>
    </div>
    <div class="col-md-7">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>All Suppliers</span>
       </strong>
      </div>
        <div class="panel-body">
          <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>Supplier Name</th>
                    <th>Address</th>
                    <th>Contact Number</th>
                    <th>Email</th>
                    <th class="text-center" style="width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
              <?php foreach ($all_suppliers as $supplier):?>
                <tr>
                    <td class="text-center"><?php echo count_id();?></td>
                    <td><?php echo remove_junk(ucfirst($supplier['name'])); ?></td>
                    <td><?php echo remove_junk($supplier['address']); ?></td>
                    <td><?php echo remove_junk($supplier['contact_number']); ?></td>
                    <td><?php echo remove_junk($supplier['email']); ?></td>
                    <td class="text-center">
                      <div class="btn-group">
                        <a href="edit_supplier.php?id=<?php echo (int)$supplier['id'];?>"  class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">
                          <span class="glyphicon glyphicon-edit"></span>
                        </a>
                        <a href="delete_supplier.php?id=<?php echo (int)$supplier['id'];?>"  class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
                          <span class="glyphicon glyphicon-trash"></span>
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
  </div>
  <?php include_once('layouts/footer.php'); ?>