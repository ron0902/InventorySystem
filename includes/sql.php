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
function find_by_id($table, $id) {
  global $db;
  $primary_key_column = ($table === 'products') ? 'product_id' : (($table === 'suppliers') ? 'supplier_id' : 'id');
  $sql = "SELECT * FROM {$table} WHERE {$primary_key_column} = '{$id}' LIMIT 1";
  return $db->query($sql)->fetch_assoc();
}
/*--------------------------------------------------------------*/
/* Function for Delete data from table by id
/*--------------------------------------------------------------*/
function delete_by_id($table, $id) {
  global $db;
  if (tableExists($table)) {
    $primary_key_column = ($table === 'products') ? 'product_id' : (($table === 'suppliers') ? 'supplier_id' : 'id');
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
    // Replace 'id' with the correct primary key column name, e.g., 'product_id'
    $primary_key_column = ($table === 'products') ? 'product_id' : 'id';
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
    $sql  = sprintf("SELECT id,username,password,user_level FROM users WHERE username ='%s' LIMIT 1", $username);
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
     $sql  = sprintf("SELECT id,username,password,user_level FROM users WHERE username ='%s' LIMIT 1", $username);
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
      $results = array();
      $sql = "SELECT u.id,u.name,u.username,u.user_level,u.status,u.last_login,";
      $sql .="g.group_name ";
      $sql .="FROM users u ";
      $sql .="LEFT JOIN user_groups g ";
      $sql .="ON g.group_level=u.user_level ORDER BY u.name ASC";
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
  /* Function for cheaking which user level has access to page
  /*--------------------------------------------------------------*/
   function page_require_level($require_level){
     global $session;
     $current_user = current_user();
     $login_level = find_by_groupLevel($current_user['user_level']);
     //if user not login
     if (!$session->isUserLoggedIn(true)):
            $session->msg('d','Please login...');
            redirect('index.php', false);
            //if Group status Deactive
           elseif(is_array($login_level) && $login_level['group_status'] === '0'):
                 $session->msg('d','This level user has been band!');
                 redirect('home.php',false);
            //cheackin log in User level and Require level is Less than or equal to
           elseif($current_user['user_level'] <= (int)$require_level):
                    return true;
            else:
                  $session->msg("d", "Sorry! you dont have permission to view the page.");
                  redirect('home.php', false);
              endif;
      
           }
   /*--------------------------------------------------------------*/
   /* Function for Finding all product name
   /* JOIN with categorie  and media database table
   /*--------------------------------------------------------------*/
   function join_product_table(){
    global $db;
    $sql  = "SELECT p.product_id, p.name, p.quantity, p.unit_cost, p.date, c.name AS categorie";
    $sql .= " FROM products p";
    $sql .= " LEFT JOIN categories c ON c.id = p.categorie_id";
    $sql .= " ORDER BY p.product_id ASC";
    return find_by_sql($sql);
}
  /*--------------------------------------------------------------*/
  /* Function for Finding all product name
  /* Request coming from ajax.php for auto suggest
  /*--------------------------------------------------------------*/

  function find_product_by_title($product_name){
    global $db;
    $p_name = remove_junk($db->escape($product_name));
    $sql = "SELECT name FROM products WHERE name like '%$p_name%' LIMIT 5";
    $result = find_by_sql($sql);
    return $result;
  }

  /*--------------------------------------------------------------*/
  /* Function for Finding all product info by product title
  /* Request coming from ajax.php
  /*--------------------------------------------------------------*/
  function find_all_product_info_by_title($title){
    global $db;
    $sql  = "SELECT * FROM products ";
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
    $sql = "UPDATE products SET quantity=quantity -'{$qty}' WHERE id = '{$id}'";
    $result = $db->query($sql);
    return($db->affected_rows() === 1 ? true : false);

  }
  /*--------------------------------------------------------------*/
  /* Function for Display Recent product Added
  /*--------------------------------------------------------------*/
  function find_recent_product_added($limit){
    global $db;
    $sql   = " SELECT p.product_id,p.name,c.name AS categorie";
    $sql  .= " FROM products p";
    $sql  .= " LEFT JOIN categories c ON c.id = p.categorie_id";
    $sql  .= " ORDER BY p.product_id DESC LIMIT ".$db->escape((int)$limit);
    return find_by_sql($sql);
 }
 /*--------------------------------------------------------------*/
  /* Function for Display Recent purchase requests
  /*--------------------------------------------------------------*/
  function find_recent_purchase_requests($limit){
    global $db;
    $sql  = " SELECT pr.request_id, pr.product_id, pr.quantity, pr.requested_by, pr.request_date, p.name AS product_name";
    $sql .= " FROM purchase_requests pr";
    $sql .= " LEFT JOIN products p ON p.product_id = pr.product_id";
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



// Function to check if Purchase Order and Invoice match
function check_order_completeness($po_id, $invoice_id) {
  global $db;  

  $po_sql = "SELECT * FROM purchase_orders WHERE po_id = '{$po_id}'";
  $po_result = $db->query($po_sql);

  if ($po_result && $po_result->num_rows > 0) {
      $po_details = $po_result->fetch_assoc();

      // Fetch Invoice details
      $invoice_sql = "SELECT * FROM invoices WHERE invoice_id = '{$invoice_id}'";
      $invoice_result = $db->query($invoice_sql);

      if ($invoice_result && $invoice_result->num_rows > 0) {
          $invoice_details = $invoice_result->fetch_assoc();
          if ($po_details['po_no'] === $invoice_details['po_no'] && 
              $po_details['total_cost'] === $invoice_details['total_cost']) {
              // Now check if all items in the invoice match the PO
              $po_items_sql = "SELECT * FROM po_items WHERE po_id = '{$po_id}'";
              $invoice_items_sql = "SELECT * FROM invoice_items WHERE invoice_id = '{$invoice_id}'";
              
              $po_items = $db->query($po_items_sql);
              $invoice_items = $db->query($invoice_items_sql);

              if ($po_items && $invoice_items && $po_items->num_rows == $invoice_items->num_rows) {
                  $match = true;

                  while ($po_item = $po_items->fetch_assoc()) {
                      $invoice_item = $invoice_items->fetch_assoc();
                      
                      // Check if item descriptions and quantities match
                      if ($po_item['item_id'] !== $invoice_item['item_id'] ||
                          $po_item['quantity'] !== $invoice_item['quantity']) {
                          $match = false;
                          break;
                      }
                  }

                  if ($match) {
                      return "Order is complete"; // Items match, order is complete
                  } else {
                      return "Order is not complete - Items mismatch"; // Items do not match
                  }
              } else {
                  return "Order is not complete - Items count mismatch"; // Mismatch in number of items
              }
          } else {
              return "Order is not complete - PO and Invoice totals do not match"; // PO and Invoice total do not match
          }
      } else {
          return "Invoice not found"; // Invoice doesn't exist
      }
  } else {
      return "Purchase Order not found"; // PO doesn't exist
  }
}
?>
