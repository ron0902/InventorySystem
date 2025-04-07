<?php
require_once('includes/load.php');
page_require_level(1);

if (!isset($_GET['supplier_id']) || empty($_GET['supplier_id'])) {
    $session->msg("d", "Missing supplier id.");
    redirect('supplierdetails.php');
}
// Get the supplier ID from the URL parameter
$supplier_id = (int)$_GET['supplier_id'];

// Debugging: Check if supplier_id is being retrieved correctly
if (!$supplier_id) {
    die("Error: Invalid supplier ID.");
}

// Call the delete function with the correct supplier ID
$delete_id = delete_by_id('suppliers', $supplier_id);
if ($delete_id) {
    $session->msg("s", "Supplier deleted.");
    redirect('supplierdetails.php');
} else {
    $session->msg("d", "Supplier deletion failed.");
    redirect('supplierdetails.php');
}
?>