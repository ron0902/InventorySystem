<?php
  $page_title = 'Add User';
  require_once('includes/load.php');

  if(isset($_POST['add_user'])){

   $req_fields = array('full-name','username','password','role');
   validate_fields($req_fields);

  if(empty($errors)){
    $name   = remove_junk($db->escape($_POST['full-name']));
    $username   = remove_junk($db->escape($_POST['username']));
    $password   = remove_junk($db->escape($_POST['password']));
    $role = remove_junk($db->escape($_POST['role']));
    $password = sha1($password);
    $query = "INSERT INTO users (name,username,password,role) VALUES ('{$name}', '{$username}', '{$password}', '{$role}')";
    if($db->query($query)){
      //success
      $session->msg('s',"User account has been created! ");
      redirect('add_user.php', false);
    } else {
      //failed
      $session->msg('d',' Sorry failed to create account!');
      redirect('add_user.php', false);
    }
} else {
  $session->msg("d", $errors);
  redirect('add_user.php',false);
}
 }
?>
<?php include_once('layouts/header.php'); ?>
  <?php echo display_msg($msg); ?>
  <div class="row">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Add New User</span>
       </strong>
      </div>
      <div class="panel-body">
        <div class="col-md-6">
          <form method="post" action="add_user.php">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="full-name" placeholder="Full Name">
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" name="username" placeholder="Username">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" name ="password"  placeholder="Password">
            </div>
            <div class="form-group">
              <label for="role">User Role</label>
                <select class="form-control" name="role">
                  <option value="admin">Admin</option>
                  <option value="special">Special</option>
                  <option value="user">User</option>
                </select>
            </div>
            <div class="form-group">
              <label for="status">Status</label>
                <select class="form-control" name="status">
                  <option value="1">Active</option>
                  <option value="0">Deactive</option>
                </select>
            </div>
            <div class="form-group clearfix">
              <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
            </div>
        </form>
        </div>
      </div>
    </div>
  </div>
<?php include_once('layouts/footer.php'); ?>