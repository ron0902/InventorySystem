<?php
$page_title = 'Add Summary';
require_once 'includes/load.php';

// Insert Logic
if (isset($_POST['add_summary'])) {
    $rfq_id = (int)$_POST['rfq_id'];
    $establishment_names = $_POST['establishment_name'];
    $representatives = $_POST['representative'];
    $designations = $_POST['designation'];
    $dates = $_POST['date'];

    foreach ($establishment_names as $i => $est_name) {
        if (!empty($est_name) && !empty($representatives[$i]) && !empty($designations[$i]) && !empty($dates[$i])) {
            $est_name = remove_junk($db->escape($est_name));
            $rep = remove_junk($db->escape($representatives[$i]));
            $desig = remove_junk($db->escape($designations[$i]));
            $date = remove_junk($db->escape($dates[$i]));
            $query = "INSERT INTO rfq_summary (rfq_id, establishment_name, representative, designation, date)
                      VALUES ('{$rfq_id}', '{$est_name}', '{$rep}', '{$desig}', '{$date}')";
            $db->query($query);
        }
    }
    $session->msg('s', "Summary added successfully.");
    redirect('add_summary.php', false);
}
?>

<?php include_once 'layouts/header.php'; ?>
<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
</div>

<!-- SUMMARY FORM -->
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-list-alt"></span>
                    <span>Add Summary</span>
                </strong>
            </div>
            <div class="panel-body">
                <form method="post" action="add_summary.php" class="clearfix">
                    <div class="form-group">
                        <label for="rfq_id">RFQ No.</label>
                        <select name="rfq_id" class="form-control" required>
                            <?php
                            $rfqs = find_all('rfq');
                            foreach ($rfqs as $rfq):
                            ?>
                            <option value="<?php echo $rfq['id']; ?>"><?php echo $rfq['rfq_no']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div id="summary-rows">
                        <div class="summary-row" style="border: 1px solid #ddd; padding: 10px; margin-bottom: 10px;">
                            <input type="text" name="establishment_name[]" class="form-control" placeholder="Establishment Name" required style="margin-bottom:5px;">
                            <input type="text" name="representative[]" class="form-control" placeholder="Representative" required style="margin-bottom:5px;">
                            <input type="text" name="designation[]" class="form-control" placeholder="Designation" required style="margin-bottom:5px;">
                            <input type="date" name="date[]" class="form-control" required>
                        </div>
                    </div>
                    <button type="button" id="add-summary-row" class="btn btn-success">Add Another Row</button>
                    <button type="submit" name="add_summary" class="btn btn-primary">Add Summary</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MANAGE SUMMARY SECTION -->
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-cog"></span>
                    <span>Manage Summary</span>
                </strong>
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>RFQ No</th>
                            <th>Establishment</th>
                            <th>Representative</th>
                            <th>Designation</th>
                            <th>Date</th>
                            <th style="width:120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $summaries = find_all('rfq_summary');
                        foreach ($summaries as $summary):
                            $rfq = find_by_id('rfq', $summary['rfq_id']);
                        ?>
                        <tr>
                            <td><?php echo remove_junk($rfq['rfq_no']); ?></td>
                            <td><?php echo remove_junk($summary['establishment_name']); ?></td>
                            <td><?php echo remove_junk($summary['representative']); ?></td>
                            <td><?php echo remove_junk($summary['designation']); ?></td>
                            <td><?php echo remove_junk($summary['date']); ?></td>
                            <td>
                                <a href="edit_summary.php?id=<?php echo (int)$summary['id']; ?>" 
                                class="btn btn-warning btn-xs" 
                                style="color:white; margin-right:2px;" 
                                title="Edit">
                                    <span class="glyphicon glyphicon-pencil"></span>
                                </a>
                                <a href="delete_summary.php?id=<?php echo (int)$summary['id']; ?>" 
                                class="btn btn-danger btn-xs" 
                                style="color:white; margin-right:2px;" 
                                title="Delete"
                                onclick="return confirm('Are you sure you want to delete this summary?');">
                                    <span class="glyphicon glyphicon-remove"></span>
                                </a>
                                <a href="print-layout/SUMMARY.php?id=<?php echo (int)$rfq['id']; ?>" 
                                class="btn btn-info btn-xs" 
                                style="color:white;" 
                                title="Print" 
                                target="_blank">
                                    <span class="glyphicon glyphicon-print"></span>
                                </a>
                            </td>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('add-summary-row').addEventListener('click', function() {
    var row = document.createElement('div');
    row.className = 'summary-row';
    row.style.border = "1px solid #ddd";
    row.style.padding = "10px";
    row.style.marginBottom = "10px";
    row.innerHTML = `
        <input type="text" name="establishment_name[]" class="form-control" placeholder="Establishment Name" required style="margin-bottom:5px;">
        <input type="text" name="representative[]" class="form-control" placeholder="Representative" required style="margin-bottom:5px;">
        <input type="text" name="designation[]" class="form-control" placeholder="Designation" required style="margin-bottom:5px;">
        <input type="date" name="date[]" class="form-control" required>
        <button type="button" class="btn btn-danger remove-summary-row" style="margin-top:5px;">Remove</button>
    `;
    document.getElementById('summary-rows').appendChild(row);

    row.querySelector('.remove-summary-row').addEventListener('click', function() {
        row.remove();
    });
});
</script>

<?php include_once 'layouts/footer.php'; ?>