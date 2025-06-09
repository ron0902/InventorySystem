<?php
require_once('includes/load.php');

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    // Delete items first
    $db->query("DELETE FROM inspection_items WHERE inspection_id='{$id}'");
    if ($db->query("DELETE FROM inspection WHERE id='{$id}'")) {
        $session->msg("s", "Inspection deleted.");
    } else {
        $session->msg("d", "Delete failed.");
    }
} else {
    $session->msg("d", "Missing inspection id.");
}
redirect('add_inspection.php');
?>