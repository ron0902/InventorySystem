<?php
require_once('includes/load.php');

if (isset($_GET['borrower_id'])) {
    $id = (int)$_GET['borrower_id'];
    if (delete_by_id('borrowers', $id, 'borrower_id')) {
        $session->msg("s", "Borrower deleted.");
    } else {
        $session->msg("d", "Delete failed.");
    }
} else {
    $session->msg("d", "Missing borrower id.");
}
redirect('borrowers.php');
?>