<?php
  require_once('load.php');

/*--------------------------------------------------------------*/
/* Function for find all database table rows by table name
/*--------------------------------------------------------------*/
function find_all($table) {
   global $db;
   if(tableExists($table))
   {
     return find_by_sql("SELECT * FROM ".$db->escape($table));
   }
}
/*--------------------------------------------------------------*/
/* Function for Perform queries
/*--------------------------------------------------------------*/
function find_by_sql($sql)
{
  global $db;
  $result = $db->query($sql);
  $result_set = $db->while_loop($result);
 return $result_set;
}
/*--------------------------------------------------------------*/
/*  Function for Find data from table by id
/*--------------------------------------------------------------*/
function find_by_id($table, $id, $column = null) {
  global $db;
  $id = (int)$id;

  // Dynamically determine the primary key column based on the table name
  if (!$column) {
    switch ($table) {
      case 'borrowers':
        $column = 'borrower_id';
        break;
      case 'suppliers':
        $column = 'supplier_id';
        break;
      case 'stocks':
        $column = 'stocks_id';
        break;
      case 'principals': // Add support for principals table
        $column = 'principal_id';
        break;
      case 'users':
        $column = 'id';
        break;
      default:
        $column = 'id'; // Default primary key column
    }
  }

  $sql = "SELECT * FROM {$db->escape($table)} WHERE {$db->escape($column)} = '{$db->escape($id)}' LIMIT 1";
  $result = $db->query($sql);
  return ($result && $db->num_rows($result) > 0) ? $db->fetch_assoc($result) : null;
}
/*--------------------------------------------------------------*/
/* Function for Delete data from table by id
/*--------------------------------------------------------------*/
function delete_by_id($table, $id) {
  global $db;
  if (tableExists($table)) {
    // Dynamically determine the primary key column based on the table name
    switch ($table) {
      case 'borrowers':
        $primary_key_column = 'borrower_id';
        break;
      case 'suppliers':
        $primary_key_column = 'supplier_id';
        break;
      case 'stocks':
        $primary_key_column = 'stocks_id';
        break;
      case 'principals':
        $primary_key_column = 'principal_id';
        break;
      case 'users':
        $primary_key_column = 'id';
        break;
      default:
        $primary_key_column = 'id';
    }
    $sql = "DELETE FROM " . $db->escape($table);
    $sql .= " WHERE {$primary_key_column}=" . $db->escape($id);
    $sql .= " LIMIT 1";
    $db->query($sql);
    return ($db->affected_rows() === 1) ? true : false;
  }
}
/*--------------------------------------------------------------*/
/* Function for Count id  By table name
/*--------------------------------------------------------------*/
function count_by_id($table) {
  global $db;
  if (tableExists($table)) {
    // Replace 'id' with the correct primary key column name
    $primary_key_column = ($table === 'stocks') ? 'stocks_id' : 'id';
    $sql = "SELECT COUNT({$primary_key_column}) AS total FROM " . $db->escape($table);
    $result = $db->query($sql);
    return ($db->fetch_assoc($result));
  }
}
/*--------------------------------------------------------------*/
/* Determine if database table exists
/*--------------------------------------------------------------*/
function tableExists($table){
  global $db;
  $table_exit = $db->query('SHOW TABLES FROM '.DB_NAME.' LIKE "'.$db->escape($table).'"');
      if($table_exit) {
        if($db->num_rows($table_exit) > 0)
              return true;
         else
              return false;
      }
  }
 /*--------------------------------------------------------------*/
 /* Login with the data provided in $_POST,
 /* coming from the login form.
/*--------------------------------------------------------------*/
function authenticate($username='', $password='') {
  global $db;
  $username = $db->escape($username);
  $password = $db->escape($password);
  $sql  = sprintf("SELECT id,username,password,role FROM users WHERE username ='%s' LIMIT 1", $username);
  $result = $db->query($sql);
  if($db->num_rows($result)){
    $user = $db->fetch_assoc($result);
    $password_request = sha1($password);
    if($password_request === $user['password'] ){
      return $user['id'];
    }
  }
 return false;
}
  /*--------------------------------------------------------------*/
  /* Login with the data provided in $_POST,
  /* coming from the login_v2.php form.
  /* If you used this method then remove authenticate function.
 /*--------------------------------------------------------------*/
function authenticate_v2($username='', $password='') {
  global $db;
  $username = $db->escape($username);
  $password = $db->escape($password);
  $sql  = sprintf("SELECT id,username,password,role FROM users WHERE username ='%s' LIMIT 1", $username);
  $result = $db->query($sql);
  if($db->num_rows($result)){
    $user = $db->fetch_assoc($result);
    $password_request = sha1($password);
    if($password_request === $user['password'] ){
      return $user;
    }
  }
 return false;
}


  /*--------------------------------------------------------------*/
  /* Find current log in user by session id
  /*--------------------------------------------------------------*/
  function current_user(){
      static $current_user;
      global $db;
      if(!$current_user){
         if(isset($_SESSION['user_id'])):
             $user_id = intval($_SESSION['user_id']);
             $current_user = find_by_id('users',$user_id);
        endif;
      }
    return $current_user;
  }
  /*--------------------------------------------------------------*/
  /* Find all user by
  /* Joining users table and user gropus table
  /*--------------------------------------------------------------*/
 function find_all_user(){
    global $db;
    $sql = "SELECT id, name, username, role, status, last_login FROM users ORDER BY name ASC";
    $result = find_by_sql($sql);
    return $result;
}
  /*--------------------------------------------------------------*/
  /* Function to update the last log in of a user
  /*--------------------------------------------------------------*/

 function updateLastLogIn($user_id)
  {
    global $db;
    $date = make_date();
    $sql = "UPDATE users SET last_login='{$date}' WHERE id ='{$user_id}' LIMIT 1";
    $result = $db->query($sql);
    return ($result && $db->affected_rows() === 1 ? true : false);
  }

  /*--------------------------------------------------------------*/
  /* Find all Group name
  /*--------------------------------------------------------------*/
  function find_by_groupName($val)
  {
    global $db;
    $sql = "SELECT group_name FROM user_groups WHERE group_name = '{$db->escape($val)}' LIMIT 1 ";
    $result = $db->query($sql);
    return($db->num_rows($result) === 0 ? true : false);
  }
  /*--------------------------------------------------------------*/
  /* Find group level
  /*--------------------------------------------------------------*/
  function find_by_groupLevel($level)
  {
    global $db;
    $sql = "SELECT group_level FROM user_groups WHERE group_level = '{$db->escape($level)}' LIMIT 1 ";
    $result = $db->query($sql);
    return($db->num_rows($result) === 0 ? true : false);
  }

   /*--------------------------------------------------------------*/
   /* Function for Finding all product name
   /* JOIN with categorie  and media database table
   /*--------------------------------------------------------------*/
   function join_product_table(){
    global $db;
    $sql  = "SELECT p.stocks_id, p.name, p.quantity, p.unit_cost, p.date, c.name AS categorie";
    $sql .= " FROM stocks p";
    $sql .= " LEFT JOIN categories c ON c.id = p.categorie_id";
    $sql .= " ORDER BY p.stocks_id ASC";
    return find_by_sql($sql);
}
  /*--------------------------------------------------------------*/
  /* Function for Finding all product name
  /* Request coming from ajax.php for auto suggest
  /*--------------------------------------------------------------*/

  function find_product_by_title($product_name){
    global $db;
    $p_name = remove_junk($db->escape($product_name));
    $sql = "SELECT name FROM stocks WHERE name like '%$p_name%' LIMIT 5";
    $result = find_by_sql($sql);
    return $result;
  }

  /*--------------------------------------------------------------*/
  /* Function for Finding all product info by product title
  /* Request coming from ajax.php
  /*--------------------------------------------------------------*/
  function find_all_product_info_by_title($title){
    global $db;
    $sql  = "SELECT * FROM stocks ";
    $sql .= " WHERE name ='{$title}'";
    $sql .=" LIMIT 1";
    return find_by_sql($sql);
  }

  /*--------------------------------------------------------------*/
  /* Function for Update product quantity
  /*--------------------------------------------------------------*/
  function update_product_qty($qty,$p_id){
    global $db;
    $qty = (int) $qty;
    $id  = (int)$p_id;
    $sql = "UPDATE stocks SET quantity=quantity -'{$qty}' WHERE id = '{$id}'";
    $result = $db->query($sql);
    return($db->affected_rows() === 1 ? true : false);

  }
  /*--------------------------------------------------------------*/
  /* Function for Display Recent product Added
  /*--------------------------------------------------------------*/
  function find_recent_product_added($limit){
    global $db;
    $sql   = " SELECT p.stocks_id, p.description, c.name AS categorie";
    $sql  .= " FROM stocks p";
    $sql  .= " LEFT JOIN categories c ON c.id = p.categorie_id";
    $sql  .= " ORDER BY p.stocks_id DESC LIMIT ".$db->escape((int)$limit);
    return find_by_sql($sql);
}
 /*--------------------------------------------------------------*/
  /* Function for Display Recent purchase requests
  /*--------------------------------------------------------------*/
  function find_recent_purchase_requests($limit){
    global $db;
    $sql  = " SELECT pr.request_id, pr.stocks_id, pr.quantity, pr.requested_by, pr.request_date, p.name AS product_name";
    $sql .= " FROM purchase_requests pr";
    $sql .= " LEFT JOIN stocks p ON p.stocks_id = pr.stocks_id";
    $sql .= " ORDER BY pr.request_id DESC LIMIT ".$db->escape((int)$limit);
    return find_by_sql($sql);
  }
/*--------------------------------------------------------------*/
  /* Function for purchase requests ID 
  /*--------------------------------------------------------------*/
  function find_items_by_pr_id($request_id) {
    global $db;
    $sql  = "SELECT * FROM purchase_request_items WHERE purchase_request_id='{$db->escape($request_id)}'";
    return find_by_sql($sql);
}

function find_items_by_po_id($po_id) {
  global $db;
  $sql  = "SELECT * FROM purchase_order_items WHERE po_id='{$db->escape($po_id)}'";
  return find_by_sql($sql);
}
/*--------------------------------------------------------------*/
/* Function for finding a single row by column and value
/*--------------------------------------------------------------*/

?>
