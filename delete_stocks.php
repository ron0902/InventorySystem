<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page

?>

<?php
// Check if the 'stocks_id' parameter exists in the URL
if (!isset($_GET['stocks_id']) || empty($_GET['stocks_id'])) {
  $session->msg("d", "Missing stocks id.");
  redirect('stocks.php');
}

// Get the stocks ID from the URL parameter
$stocks_id = (int)$_GET['stocks_id'];

// Call the delete function with the correct stocks ID
$delete_id = delete_by_id('stocks', $stocks_id);
if ($delete_id) {
    $session->msg("s", "stocks deleted.");
    redirect('stocks.php');
} else {
    $session->msg("d", "stocks deletion failed.");
    redirect('stocks.php');
}
?>
