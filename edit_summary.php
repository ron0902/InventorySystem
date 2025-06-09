<?php
require_once('includes/load.php');

if (!isset($_GET['id'])) {
    $session->msg("d", "Missing summary id.");
    redirect('add_summary.php');
}
$id = (int)$_GET['id'];
$summary = find_by_id('rfq_summary', $id);
if (!$summary) {
    $session->msg("d", "Summary not found.");
    redirect('add_summary.php');
}

if (isset($_POST['update_summary'])) {
    $rfq_id = (int)$_POST['rfq_id'];
    $establishment_name = remove_junk($db->escape($_POST['establishment_name']));
    $representative = remove_junk($db->escape($_POST['representative']));
    $designation = remove_junk($db->escape($_POST['designation']));
    $date = remove_junk($db->escape($_POST['date']));

    $query = "UPDATE rfq_summary SET 
                rfq_id='{$rfq_id}', 
                establishment_name='{$establishment_name}', 
                representative='{$representative}', 
                designation='{$designation}', 
                date='{$date}' 
              WHERE id='{$id}'";
    if ($db->query($query)) {
        $session->msg('s', "Summary updated successfully.");
        redirect('add_summary.php', false);
    } else {
        $session->msg('d', 'Failed to update summary!');
        redirect('edit_summary.php?id=' . $id, false);
    }
}

include_once 'layouts/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-edit"></span>
                    <span>Edit Summary</span>
                </strong>
            </div>
            <div class="panel-body">
                <form method="post" action="" class="clearfix">
                    <div class="form-group">
                        <label for="rfq_id">RFQ No.</label>
                        <select name="rfq_id" class="form-control" required>
                            <?php
                            $rfqs = find_all('rfq');
                            foreach ($rfqs as $rfq):
                            ?>
                            <option value="<?php echo $rfq['id']; ?>" <?php if ($rfq['id'] == $summary['rfq_id']) echo 'selected'; ?>>
                                <?php echo $rfq['rfq_no']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <input type="text" name="establishment_name" class="form-control" placeholder="Establishment Name" required style="margin-bottom:5px;" value="<?php echo remove_junk($summary['establishment_name']); ?>">
                    <input type="text" name="representative" class="form-control" placeholder="Representative" required style="margin-bottom:5px;" value="<?php echo remove_junk($summary['representative']); ?>">
                    <input type="text" name="designation" class="form-control" placeholder="Designation" required style="margin-bottom:5px;" value="<?php echo remove_junk($summary['designation']); ?>">
                    <input type="date" name="date" class="form-control" required value="<?php echo remove_junk($summary['date']); ?>">
                    <br>
                    <button type="submit" name="update_summary" class="btn btn-primary">Update Summary</button>
                    <a href="add_summary.php" class="btn btn-default">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once 'layouts/footer.php'; ?>