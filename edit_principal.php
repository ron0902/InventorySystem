<?php
  $page_title = 'Edit Principal';
  require_once('includes/load.php');
  page_require_level(1);

  // Check if principal_id is provided
  if(isset($_GET['principal_id'])){
    $principal_id = (int)$_GET['principal_id'];
    $principal = find_by_id('principals', $principal_id);
    if(!$principal){
      $session->msg("d", "Missing Principal ID.");
      redirect('principal.php');
    }
  } else {
    $session->msg("d", "Missing Principal ID.");
    redirect('principal.php');
  }

  if(isset($_POST['update_principal'])){
    $req_fields = array('principal-name', 'principal-address', 'principal-contact', 'principal-email');
    validate_fields($req_fields);
    $name = remove_junk($db->escape($_POST['principal-name']));
    $address = remove_junk($db->escape($_POST['principal-address']));
    $contact = remove_junk($db->escape($_POST['principal-contact']));
    $email = remove_junk($db->escape($_POST['principal-email']));
    if(empty($errors)){
      $sql  = "UPDATE principals SET ";
      $sql .= "name='{$name}', address='{$address}', contact_number='{$contact}', email='{$email}' ";
      $sql .= "WHERE principal_id='{$principal_id}'";
      if($db->query($sql)){
        $session->msg("s", "Principal updated successfully.");
        redirect('principal.php', false);
      } else {
        $session->msg("d", "Failed to update principal.");
        redirect('edit_principal.php?principal_id='.$principal_id, false);
      }
    } else {
      $session->msg("d", $errors);
      redirect('edit_principal.php?principal_id='.$principal_id, false);
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
          <span>Edit Principal</span>
       </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="edit_principal.php?principal_id=<?php echo (int)$principal['principal_id']; ?>">
          <div class="form-group">
              <input type="text" class="form-control" name="principal-name" value="<?php echo remove_junk($principal['name']); ?>" placeholder="Principal Name" required>
          </div>
          <div class="form-group">
              <input type="text" class="form-control" name="principal-address" value="<?php echo remove_junk($principal['address']); ?>" placeholder="Principal Address">
          </div>
          <div class="form-group">
              <input type="text" class="form-control" name="principal-contact" value="<?php echo remove_junk($principal['contact_number']); ?>" placeholder="Contact Number">
          </div>
          <div class="form-group">
              <input type="email" class="form-control" name="principal-email" value="<?php echo remove_junk($principal['email']); ?>" placeholder="Email Address">
          </div>
          <button type="submit" name="update_principal" class="btn btn-primary">Update Principal</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?>