<?php include_once('includes/load.php'); ?>
<?php
$req_fields = array('username','password' );
validate_fields($req_fields);
$username = remove_junk($_POST['username']);
$password = remove_junk($_POST['password']);

if(empty($errors)){
  $user_id = authenticate($username, $password);
  if($user_id){
    // Create session with id
    $session->login($user_id);
    // Update Sign in time
    updateLastLogIn($user_id);

    // Fetch user info to check role
    $user = find_by_id('users', $user_id);
    if ($user && $user['role'] == 'admin') {
      $session->msg("s", "Welcome to Inventory Management System");
      redirect('admin.php', false); // Admin dashboard
    } elseif ($user && $user['role'] == 'user') {
      $session->msg("s", "Welcome to Inventory Management System");
      redirect('home.php', false); // User dashboard
    } elseif ($user && $user['role'] == 'special') {
      $session->msg("s", "Welcome to Inventory Management System");
      redirect('special.php', false); // Special dashboard
    } else {
      $session->msg("d", "Role not recognized.");
      redirect('index.php', false);
    }

  } else {
    $session->msg("d", "Sorry Username/Password incorrect.");
    redirect('index.php',false);
  }

} else {
   $session->msg("d", $errors);
   redirect('index.php',false);
}
?>
