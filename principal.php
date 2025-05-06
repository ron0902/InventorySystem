<?php
  $page_title = 'All Principals';
  require_once('includes/load.php');
  page_require_level(1);
  $all_principals = find_all('principals');
?>

<?php
 if(isset($_POST['add_principal'])){
   $req_fields = array('principal-name', 'principal-address', 'principal-contact', 'principal-email');
   validate_fields($req_fields);
   $name = remove_junk($db->escape($_POST['principal-name']));
   $address = remove_junk($db->escape($_POST['principal-address']));
   $contact = remove_junk($db->escape($_POST['principal-contact']));
   $email = remove_junk($db->escape($_POST['principal-email']));
   if(empty($errors)){
      $sql  = "INSERT INTO principals (name, address, contact_number, email)";
      $sql .= " VALUES ('{$name}', '{$address}', '{$contact}', '{$email}')";
      if($db->query($sql)){
        $session->msg("s", "Successfully Added New Principal");
        redirect('principal.php',false);
      } else {
        $session->msg("d", "Sorry Failed to insert.");
        redirect('principal.php',false);
      }
   } else {
     $session->msg("d", $errors);
     redirect('principal.php',false);
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
          <span>Add New Principal</span>
       </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="principal.php">
          <div class="form-group">
              <input type="text" class="form-control" name="principal-name" placeholder="Principal Name" required>
          </div>
          <div class="form-group">
              <input type="text" class="form-control" name="principal-address" placeholder="Principal Address">
          </div>
          <div class="form-group">
              <input type="text" class="form-control" name="principal-contact" placeholder="Contact Number">
          </div>
          <div class="form-group">
              <input type="email" class="form-control" name="principal-email" placeholder="Email Address">
          </div>
          <button type="submit" name="add_principal" class="btn btn-primary">Add Principal</button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-md-7">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>All Principals</span>
       </strong>
      </div>
      <div class="panel-body">
        <table class="table table-bordered table-striped table-hover">
          <thead>
              <tr>
                  <th class="text-center" style="width: 50px;">#</th>
                  <th>Principal Name</th>
                  <th>Address</th>
                  <th>Contact Number</th>
                  <th>Email</th>
                  <th class="text-center" style="width: 100px;">Actions</th>
              </tr>
          </thead>
          <tbody>
            <?php foreach ($all_principals as $principal):?>
              <tr>
                  <td class="text-center"><?php echo count_id();?></td>
                  <td><?php echo remove_junk(ucfirst($principal['name'])); ?></td>
                  <td><?php echo remove_junk($principal['address']); ?></td>
                  <td><?php echo remove_junk($principal['contact_number']); ?></td>
                  <td><?php echo remove_junk($principal['email']); ?></td>
                  <td class="text-center">
                    <div class="btn-group">
                      <a href="edit_principal.php?principal_id=<?php echo (int)$principal['principal_id'];?>"  class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">
                        <span class="glyphicon glyphicon-edit"></span>
                      </a>
                      <a href="delete_principal.php?principal_id=<?php echo (int)$principal['principal_id'];?>"  class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
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
<?php include_once('layouts/footer.php'); ?>