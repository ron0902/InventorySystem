<?php
require_once('includes/load.php');

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($db->query("DELETE FROM rfq_summary WHERE id='{$id}'")) {
        $session->msg("s", "Summary deleted.");
    } else {
        $session->msg("d", "Delete failed.");
    }
} else {
    $session->msg("d", "Missing summary id.");
}
redirect('add_summary.php');
?>