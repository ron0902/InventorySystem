<?php
require_once('includes/load.php');
$id = (int)$_GET['id'];
if (delete_by_id('offices', $id)) {
    $session->msg("s", "Office deleted.");
} else {
    $session->msg("d", "Delete failed.");
}
redirect('teacherdetails.php');
?>