<?php
require_once('includes/load.php');
$office = find_by_id('offices', (int)$_GET['id']);
if (!$office) {
    $session->msg("d", "Missing office id.");
    redirect('teacherdetails.php');
}
if (isset($_POST['update_office'])) {
    $req_fields = array('office-name', 'office-contact', 'office-email');
    validate_fields($req_fields);
    $name = remove_junk($db->escape($_POST['office-name']));
    $contact = remove_junk($db->escape($_POST['office-contact']));
    $email = remove_junk($db->escape($_POST['office-email']));
    if (empty($errors)) {
        $sql = "UPDATE offices SET name='{$name}', contact_number='{$contact}', email='{$email}' WHERE id='{$office['id']}'";
        if ($db->query($sql)) {
            $session->msg("s", "Office updated.");
            redirect('teacherdetails.php', false);
        } else {
            $session->msg("d", "Update failed.");
            redirect('edit_office.php?id=' . (int)$office['id'], false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('edit_office.php?id=' . (int)$office['id'], false);
    }
}
include_once('layouts/header.php');
?>

<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-edit"></span>
                    <span>Edit Office</span>
                </strong>
            </div>
            <div class="panel-body">
                <form method="post" action="">
                    <div class="form-group">
                        <input type="text" class="form-control" name="office-name" value="<?php echo remove_junk($office['name']); ?>" placeholder="Office Name" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="office-contact" value="<?php echo remove_junk($office['contact_number']); ?>" placeholder="Contact Number">
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control" name="office-email" value="<?php echo remove_junk($office['email']); ?>" placeholder="Email Address">
                    </div>
                    <button type="submit" name="update_office" class="btn btn-primary">Update Office</button>
                    <a href="teacherdetails.php" class="btn btn-default">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>