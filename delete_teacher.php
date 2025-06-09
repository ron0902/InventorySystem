<?php
require_once('includes/load.php');
$id = (int)$_GET['id'];
if (delete_by_id('teachers', $id)) {
    $session->msg("s", "Teacher deleted.");
} else {
    $session->msg("d", "Delete failed.");
}
redirect('teacherdetails.php');
?>