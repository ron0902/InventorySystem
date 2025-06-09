<?php
require_once('includes/load.php');

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    // Delete RFQ items first (to maintain referential integrity)
    $db->query("DELETE FROM rfq_items WHERE rfq_id='{$id}'");
    // Then delete the RFQ
    if ($db->query("DELETE FROM rfq WHERE id='{$id}'")) {
        $session->msg("s", "RFQ deleted.");
    } else {
        $session->msg("d", "Delete failed.");
    }
} else {
    $session->msg("d", "Missing RFQ id.");
}
redirect('add_rfq.php');
?>