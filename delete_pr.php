<?php
  require_once 'includes/load.php';
  // Checkin What level user has permission to view this page
  page_require_level(1);
?>
<?php
if (isset($_GET['id'])) {
  $request_id = (int)$_GET['id'];
  $pr_data = find_by_id('purchase_requests', $request_id, 'request_id');

  if (!$pr_data) {
    $session->msg('d', 'Purchase request not found.');
    redirect('mange_pr.php', false);
  }

  $items = find_items_by_pr_id($request_id);
} else {
  $session->msg('d', 'Missing purchase request ID.');
  redirect('mange_pr.php', false);
}

$pr = find_by_id('purchase_requests', (int)$_GET['id'], 'request_id');
$all_categories = find_all('categories');
$all_departments = find_all('departments');
$all_users = find_all('users');

if (!$pr) {
  $session->msg("d", "Missing purchase request id.");
  redirect('mange_pr.php');
}
?>
<?php
  if (isset($_GET['id'])) {
    $request_id = (int)$_GET['id'];
    $delete_id = $request_id; // Define $delete_id
    if ($delete_id) {
      // Delete related items from purchase_request_items table first
      $db->query("DELETE FROM purchase_request_items WHERE purchase_request_id = '{$request_id}'");
      // Then delete the purchase request
      $db->query("DELETE FROM purchase_requests WHERE request_id = '{$request_id}'");
      $session->msg("s", "Purchase request deleted.");
      redirect('mange_pr.php');
    } else {
      $session->msg("d", "Purchase request deletion failed or missing parameter.");
      redirect('mange_pr.php');
    }
  } else {
    $session->msg("d", "Missing purchase request ID.");
    redirect('mange_pr.php');
  }
?>