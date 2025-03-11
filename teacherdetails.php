<?php
$page_title = 'Teachers and Offices';
require_once('includes/load.php');
// Check user permission level
page_require_level(1);

// Fetch all teachers and offices
$all_teachers = find_all('teachers'); // Assuming you have a 'teachers' table
$all_offices = find_all('offices');   // Fetch all offices for the dropdown

// Handle form submission for adding a teacher
if (isset($_POST['add_teacher'])) {
    $req_fields = array('teacher-name', 'teacher-address', 'teacher-contact', 'teacher-email', 'office-id');
    validate_fields($req_fields);
    $name = remove_junk($db->escape($_POST['teacher-name']));
    $address = remove_junk($db->escape($_POST['teacher-address']));
    $contact = remove_junk($db->escape($_POST['teacher-contact']));
    $email = remove_junk($db->escape($_POST['teacher-email']));
    $office_id = (int)$db->escape($_POST['office-id']); // Get the selected office ID
    if (empty($errors)) {
        $sql  = "INSERT INTO teachers (name, address, contact_number, email, office_id)";
        $sql .= " VALUES ('{$name}', '{$address}', '{$contact}', '{$email}', '{$office_id}')";
        if ($db->query($sql)) {
            $session->msg("s", "Successfully Added New Teacher");
            redirect('teacherdetails.php', false); // Redirect to the same page
        } else {
            $session->msg("d", "Sorry, Failed to insert teacher.");
            redirect('teacherdetails.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('teacherdetails.php', false);
    }
}

// Handle form submission for adding an office
if (isset($_POST['add_office'])) {
    $req_fields = array('office-name', 'office-contact', 'office-email');
    validate_fields($req_fields);
    $name = remove_junk($db->escape($_POST['office-name']));
    $contact = remove_junk($db->escape($_POST['office-contact']));
    $email = remove_junk($db->escape($_POST['office-email']));
    if (empty($errors)) {
        $sql  = "INSERT INTO offices (name, contact_number, email)";
        $sql .= " VALUES ('{$name}', '{$contact}', '{$email}')";
        if ($db->query($sql)) {
            $session->msg("s", "Successfully Added New Office");
            redirect('teacherdetails.php', false); // Redirect to the same page
        } else {
            $session->msg("d", "Sorry, Failed to insert office.");
            redirect('teacherdetails.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('teacherdetails.php', false);
    }
}
?>
<?php include_once('layouts/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
</div>

<!-- Add Teacher Form -->
<!-- Add Teacher Form -->
<di class="row">
    <div class="col-md-5">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>Add New Teacher</span>
                </strong>
            </div>
            <div class="panel-body">
                <form method="post" action="teacherdetails.php">
                    <div class="form-group">
                        <input type="text" class="form-control" name="teacher-name" placeholder="Teacher Name" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="teacher-address" placeholder="Teacher Address">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="teacher-contact" placeholder="Contact Number">
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control" name="teacher-email" placeholder="Email Address">
                    </div>
                    <div class="form-group">
                        <label for="office-id">Assign to Office</label>
                        <select class="form-control" name="office-id" required>
                            <option value="">Select Office</option>
                            <?php foreach ($all_offices as $office): ?>
                                <option value="<?php echo (int)$office['id']; ?>">
                                    <?php echo remove_junk(ucfirst($office['name'])); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="add_teacher" class="btn btn-primary">Add Teacher</button>
                </form>
            </div>
        </div>
    </div>


    <!-- Add Office Form -->
    <div class="col-md-5">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>Add New Office</span>
                </strong>
            </div>
            <div class="panel-body">
                <form method="post" action="teacherdetails.php">
                    <div class="form-group">
                        <input type="text" class="form-control" name="office-name" placeholder="Office Name" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="office-contact" placeholder="Contact Number">
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control" name="office-email" placeholder="Email Address">
                    </div>
                    <button type="submit" name="add_office" class="btn btn-primary">Add Office</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- List of Teachers -->
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>List of Teachers</span>
                </strong>
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th>Teacher Name</th>
                            <th>Address</th>
                            <th>Contact Number</th>
                            <th>Email</th>
                            <th>Assigned Office</th>
                            <th class="text-center" style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_teachers as $teacher): ?>
                            <?php
                            // Fetch the office name for the teacher
                            $office = find_by_id('offices', (int)$teacher['office_id']);
                            $office_name = $office ? $office['name'] : 'Not Assigned';
                            ?>
                            <tr>
                                <td class="text-center"><?php echo count_id(); ?></td>
                                <td><?php echo remove_junk(ucfirst($teacher['name'])); ?></td>
                                <td><?php echo remove_junk($teacher['address']); ?></td>
                                <td><?php echo remove_junk($teacher['contact_number']); ?></td>
                                <td><?php echo remove_junk($teacher['email']); ?></td>
                                <td><?php echo remove_junk($office_name); ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="edit_teacher.php?id=<?php echo (int)$teacher['id']; ?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">
                                            <span class="glyphicon glyphicon-edit"></span>
                                        </a>
                                        <a href="delete_teacher.php?id=<?php echo (int)$teacher['id']; ?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
                                            <span class="glyphicon glyphicon-trash"></span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- List of Offices -->
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>List of Offices</span>
                </strong>
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th>Office Name</th>
                            <th>Contact Number</th>
                            <th>Email</th>
                            <th class="text-center" style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_offices as $office): ?>
                            <tr>
                                <td class="text-center"><?php echo count_id(); ?></td>
                                <td><?php echo remove_junk(ucfirst($office['name'])); ?></td>
                                <td><?php echo remove_junk($office['contact_number']); ?></td>
                                <td><?php echo remove_junk($office['email']); ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="edit_office.php?id=<?php echo (int)$office['id']; ?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">
                                            <span class="glyphicon glyphicon-edit"></span>
                                        </a>
                                        <a href="delete_office.php?id=<?php echo (int)$office['id']; ?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
                                            <span class="glyphicon glyphicon-trash"></span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>