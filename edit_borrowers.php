<?php
  $page_title = 'Edit Borrower';
  require_once('includes/load.php');


  // Check if borrower_id is provided
  if(isset($_GET['borrower_id'])){
    $borrower_id = (int)$_GET['borrower_id'];
    $borrower = find_by_id('borrowers', $borrower_id);
    if(!$borrower){
      $session->msg("d", "Missing Borrower ID.");
      redirect('borrowers.php');
    }
  } else {
    $session->msg("d", "Missing Borrower ID.");
    redirect('borrowers.php');
  }

  if(isset($_POST['update_borrower'])){
    $req_fields = array('borrower-name', 'borrower-address', 'borrower-contact', 'borrower-email');
    validate_fields($req_fields);
    $name = remove_junk($db->escape($_POST['borrower-name']));
    $address = remove_junk($db->escape($_POST['borrower-address']));
    $contact = remove_junk($db->escape($_POST['borrower-contact']));
    $email = remove_junk($db->escape($_POST['borrower-email']));
    if(empty($errors)){
      $sql  = "UPDATE borrowers SET ";
      $sql .= "name='{$name}', address='{$address}', contact_number='{$contact}', email='{$email}' ";
      $sql .= "WHERE borrower_id='{$borrower_id}'";
      if($db->query($sql)){
        $session->msg("s", "Borrower updated successfully.");
        redirect('borrowers.php', false);
      } else {
        $session->msg("d", "Failed to update borrower.");
        redirect('edit_borrowers.php?borrower_id='.$borrower_id, false);
      }
    } else {
      $session->msg("d", $errors);
      redirect('edit_borrowers.php?borrower_id='.$borrower_id, false);
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
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Edit Borrower</span>
       </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="edit_borrowers.php?borrower_id=<?php echo (int)$borrower['borrower_id']; ?>">
          <div class="form-group">
              <input type="text" class="form-control" name="borrower-name" value="<?php echo remove_junk($borrower['name']); ?>" placeholder="Borrower Name" required>
          </div>
          <div class="form-group">
              <input type="text" class="form-control" name="borrower-address" value="<?php echo remove_junk($borrower['address']); ?>" placeholder="Borrower Address">
          </div>
          <div class="form-group">
              <input type="text" class="form-control" name="borrower-contact" value="<?php echo remove_junk($borrower['contact_number']); ?>" placeholder="Contact Number">
          </div>
          <div class="form-group">
              <input type="email" class="form-control" name="borrower-email" value="<?php echo remove_junk($borrower['email']); ?>" placeholder="Email Address">
          </div>
          <button type="submit" name="update_borrower" class="btn btn-primary">Update Borrower</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?>