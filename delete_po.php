<?php
require_once 'includes/load.php';

?>

<?php
if (isset($_GET['id'])) {
  $po_id = (int)$_GET['id']; 
  $po_data = find_by_id('purchase_orders', $po_id, 'po_id'); 

  if (!$po_data) {
    $session->msg('d', 'Purchase order not found.');
    redirect('mange_po.php', false); 
  }

  $items = find_items_by_po_id($po_id); 
} else {
  $session->msg('d', 'Missing purchase order ID.');
  redirect('mange_po.php', false); 
}

$po = find_by_id('purchase_orders', (int)$_GET['id'], 'po_id');
$all_categories = find_all('categories');
$all_departments = find_all('departments');
$all_users = find_all('users');

if (!$po) {
  $session->msg("d", "Missing purchase order id."); 
  redirect('mange_po.php');
}
?>

<?php
if (isset($_GET['id'])) {
  $po_id = (int)$_GET['id'];
  $delete_id = $po_id;
  if ($delete_id) {
    $db->query("DELETE FROM purchase_order_items WHERE po_id = '{$po_id}'");
    $db->query("DELETE FROM purchase_orders WHERE po_id = '{$po_id}'");
    $session->msg("s", "Purchase order deleted.");
    redirect('mange_po.php');
  } else {
    $session->msg("d", "Purchase order deletion failed or missing parameter.");
    redirect('mange_po.php');
  }
} else {
  $session->msg("d", "Missing purchase order ID.");
  redirect('manage_po.php');
}
?>sss