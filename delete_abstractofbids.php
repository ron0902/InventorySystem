<?php
require_once('includes/load.php');

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    // Delete items first to maintain referential integrity
    $db->query("DELETE FROM abstractofbids_items WHERE abstract_id='{$id}'");
    if ($db->query("DELETE FROM abstractofbids WHERE id='{$id}'")) {
        $session->msg("s", "Abstract of Bids deleted.");
    } else {
        $session->msg("d", "Delete failed.");
    }
} else {
    $session->msg("d", "Missing abstract id.");
}
redirect('add_abstractofbids.php');
?>