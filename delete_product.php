<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);
?>

<?php
// Check if the 'product_id' parameter exists in the URL
if (!isset($_GET['product_id']) || empty($_GET['product_id'])) {
  $session->msg("d", "Missing product id.");
  redirect('product.php');
}

// Get the product ID from the URL parameter
$product_id = (int)$_GET['product_id'];

// Call the delete function with the correct product ID
$delete_id = delete_by_id('products', $product_id);
if ($delete_id) {
    $session->msg("s", "Product deleted.");
    redirect('product.php');
} else {
    $session->msg("d", "Product deletion failed.");
    redirect('product.php');
}
?>
