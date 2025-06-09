<?php
require_once('includes/load.php');
$teacher = find_by_id('teachers', (int)$_GET['id']);
$all_offices = find_all('offices');
if (!$teacher) {
    $session->msg("d", "Missing teacher id.");
    redirect('teacherdetails.php');
}
if (isset($_POST['update_teacher'])) {
    $req_fields = array('teacher-name', 'teacher-address', 'teacher-contact', 'teacher-email', 'office-id');
    validate_fields($req_fields);
    $name = remove_junk($db->escape($_POST['teacher-name']));
    $address = remove_junk($db->escape($_POST['teacher-address']));
    $contact = remove_junk($db->escape($_POST['teacher-contact']));
    $email = remove_junk($db->escape($_POST['teacher-email']));
    $office_id = (int)$db->escape($_POST['office-id']);
    if (empty($errors)) {
        $sql = "UPDATE teachers SET name='{$name}', address='{$address}', contact_number='{$contact}', email='{$email}', office_id='{$office_id}' WHERE id='{$teacher['id']}'";
        if ($db->query($sql)) {
            $session->msg("s", "Teacher updated.");
            redirect('teacherdetails.php', false);
        } else {
            $session->msg("d", "Update failed.");
            redirect('edit_teacher.php?id=' . (int)$teacher['id'], false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('edit_teacher.php?id=' . (int)$teacher['id'], false);
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
                    <span>Edit Teacher</span>
                </strong>
            </div>
            <div class="panel-body">
                <form method="post" action="">
                    <div class="form-group">
                        <input type="text" class="form-control" name="teacher-name" value="<?php echo remove_junk($teacher['name']); ?>" placeholder="Teacher Name" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="teacher-address" value="<?php echo remove_junk($teacher['address']); ?>" placeholder="Teacher Address">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="teacher-contact" value="<?php echo remove_junk($teacher['contact_number']); ?>" placeholder="Contact Number">
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control" name="teacher-email" value="<?php echo remove_junk($teacher['email']); ?>" placeholder="Email Address">
                    </div>
                    <div class="form-group">
                        <label for="office-id">Assign to Office</label>
                        <select class="form-control" name="office-id" required>
                            <option value="">Select Office</option>
                            <?php foreach ($all_offices as $office): ?>
                                <option value="<?php echo (int)$office['id']; ?>" <?php if ($teacher['office_id'] == $office['id']) echo 'selected'; ?>>
                                    <?php echo remove_junk(ucfirst($office['name'])); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="update_teacher" class="btn btn-primary">Update Teacher</button>
                    <a href="teacherdetails.php" class="btn btn-default">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>