<?php
  $page_title = 'All Borrowers';
  require_once('includes/load.php');
  $all_borrowers = find_all('borrowers');
?>

<?php
 if(isset($_POST['add_borrower'])){
   $req_fields = array('borrower-name', 'borrower-address', 'borrower-contact', 'borrower-email');
   validate_fields($req_fields);
   $name = remove_junk($db->escape($_POST['borrower-name']));
   $address = remove_junk($db->escape($_POST['borrower-address']));
   $contact = remove_junk($db->escape($_POST['borrower-contact']));
   $email = remove_junk($db->escape($_POST['borrower-email']));
   if(empty($errors)){
      $sql  = "INSERT INTO borrowers (name, address, contact_number, email)";
      $sql .= " VALUES ('{$name}', '{$address}', '{$contact}', '{$email}')";
      if($db->query($sql)){
        $session->msg("s", "Successfully Added New Borrower");
        redirect('borrowers.php',false);
      } else {
        $session->msg("d", "Sorry Failed to insert.");
        redirect('borrowers.php',false);
      }
   } else {
     $session->msg("d", $errors);
     redirect('borrowers.php',false);
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
          <span>Add New Borrower</span>
       </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="borrowers.php">
          <div class="form-group">
              <input type="text" class="form-control" name="borrower-name" placeholder="Borrower Name" required>
          </div>
          <div class="form-group">
              <input type="text" class="form-control" name="borrower-address" placeholder="Borrower Address">
          </div>
          <div class="form-group">
              <input type="text" class="form-control" name="borrower-contact" placeholder="Contact Number">
          </div>
          <div class="form-group">
              <input type="email" class="form-control" name="borrower-email" placeholder="Email Address">
          </div>
          <button type="submit" name="add_borrower" class="btn btn-primary">Add Borrower</button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-md-7">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>All Borrowers</span>
       </strong>
      </div>
      <div class="panel-body">
        <table class="table table-bordered table-striped table-hover">
          <thead>
              <tr>
                  <th class="text-center" style="width: 50px;">#</th>
                  <th>Borrower Name</th>
                  <th>Address</th>
                  <th>Contact Number</th>
                  <th>Email</th>
                  <th class="text-center" style="width: 100px;">Actions</th>
              </tr>
          </thead>
          <tbody>
            <?php foreach ($all_borrowers as $borrower):?>
              <tr>
                  <td class="text-center"><?php echo count_id();?></td>
                  <td><?php echo remove_junk(ucfirst($borrower['name'])); ?></td>
                  <td><?php echo remove_junk($borrower['address']); ?></td>
                  <td><?php echo remove_junk($borrower['contact_number']); ?></td>
                  <td><?php echo remove_junk($borrower['email']); ?></td>
                  <td class="text-center">
                    <div class="btn-group">
                      <a href="edit_borrowers.php?borrower_id=<?php echo (int)$borrower['borrower_id'];?>"  class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">
                        <span class="glyphicon glyphicon-edit"></span>
                      </a>
                      <a href="delete_borrower.php?borrower_id=<?php echo (int)$borrower['borrower_id'];?>"  class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
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