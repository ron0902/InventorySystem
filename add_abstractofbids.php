<?php
$page_title = 'Add Abstract of Bids';
require_once 'includes/load.php';

// Insert Logic
if (isset($_POST['add_abstract'])) {
    $fields = ['abstract_no', 'abstract_date', 'project_name', 'lot', 'pr_number', 'abc', 'rfq_number', 'bid_opening'];
    validate_fields($fields);

    $errors = [];

    // Validate items
    if (!isset($_POST['item_no']) || !is_array($_POST['item_no'])) {
        $errors[] = "Items must be added.";
    } else {
        foreach ($_POST['item_no'] as $qty) {
            if (empty($qty)) {
                $errors[] = "All item numbers are required.";
            }
        }
    }

    if (empty($errors)) {
        $abstract_no = remove_junk($db->escape($_POST['abstract_no']));
        $abstract_date = remove_junk($db->escape($_POST['abstract_date']));
        $project_name = remove_junk($db->escape($_POST['project_name']));
        $lot = remove_junk($db->escape($_POST['lot']));
        $pr_number = remove_junk($db->escape($_POST['pr_number']));
        $abc = remove_junk($db->escape($_POST['abc']));
        $rfq_number = remove_junk($db->escape($_POST['rfq_number']));
        $bid_opening = remove_junk($db->escape($_POST['bid_opening']));

        $db->query("START TRANSACTION");

        $query = "INSERT INTO abstractofbids (abstract_no, abstract_date, project_name, lot, pr_number, abc, rfq_number, bid_opening, status)
                  VALUES ('{$abstract_no}', '{$abstract_date}', '{$project_name}', '{$lot}', '{$pr_number}', '{$abc}', '{$rfq_number}', '{$bid_opening}', 'Pending')";

        if ($db->query($query)) {
            $abstract_id = $db->insert_id();

            if (isset($_POST['item_no']) && is_array($_POST['item_no'])) {
                foreach ($_POST['item_no'] as $key => $item_no) {
                    $particulars = remove_junk($db->escape($_POST['particulars'][$key]));
                    $unit = remove_junk($db->escape($_POST['unit'][$key]));
                    $quantity = remove_junk($db->escape($_POST['quantity'][$key]));
                    $unit_cost = remove_junk($db->escape($_POST['unit_cost'][$key]));
                    $total = remove_junk($db->escape($_POST['total'][$key]));
                    $bidder1_unit_cost = remove_junk($db->escape($_POST['bidder1_unit_cost'][$key]));
                    $bidder1_total = remove_junk($db->escape($_POST['bidder1_total'][$key]));
                    $bidder2_unit_cost = remove_junk($db->escape($_POST['bidder2_unit_cost'][$key]));
                    $bidder2_total = remove_junk($db->escape($_POST['bidder2_total'][$key]));
                    $bidder3_unit_cost = remove_junk($db->escape($_POST['bidder3_unit_cost'][$key]));
                    $bidder3_total = remove_junk($db->escape($_POST['bidder3_total'][$key]));

                    $query = "INSERT INTO abstractofbids_items 
                        (abstract_id, item_no, particulars, unit, quantity, unit_cost, total, 
                         bidder1_unit_cost, bidder1_total, bidder2_unit_cost, bidder2_total, bidder3_unit_cost, bidder3_total)
                        VALUES
                        ('{$abstract_id}', '{$item_no}', '{$particulars}', '{$unit}', '{$quantity}', '{$unit_cost}', '{$total}',
                         '{$bidder1_unit_cost}', '{$bidder1_total}', '{$bidder2_unit_cost}', '{$bidder2_total}', '{$bidder3_unit_cost}', '{$bidder3_total}')";
                    if (!$db->query($query)) {
                        $db->query("ROLLBACK");
                        $session->msg('d', 'Failed to add Abstract of Bids item!');
                        redirect('add_abstractofbids.php', false);
                    }
                }
            }

            $db->query("COMMIT");
            $session->msg('s', "Abstract of Bids added successfully.");
            redirect('add_abstractofbids.php', false);
        } else {
            $db->query("ROLLBACK");
            $session->msg('d', 'Failed to add Abstract of Bids!');
            redirect('add_abstractofbids.php', false);
        }
    } else {
        $session->msg("d", implode("<br>", $errors));
        redirect('add_abstractofbids.php', false);
    }
}
?>

<?php include_once 'layouts/header.php'; ?>
<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
</div>

<!-- ABSTRACT OF BIDS FORM -->
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-list-alt"></span>
                    <span>Add Abstract of Bids</span>
                </strong>
            </div>
            <div class="panel-body">
                <form method="post" action="add_abstractofbids.php" class="clearfix">
                    <div class="form-group">
                        <input type="text" class="form-control" name="abstract_no" placeholder="Abstract No." required>
                    </div>
                    <div class="form-group">
                        <input type="date" class="form-control" name="abstract_date" placeholder="Date" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="project_name" placeholder="Project Name" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="lot" placeholder="Lot">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="pr_number" placeholder="PR Number">
                    </div>
                    <div class="form-group">
                        <input type="number" step="0.01" class="form-control" name="abc" placeholder="ABC" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="rfq_number" placeholder="RFQ Number">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="bid_opening" placeholder="Bid Opening">
                    </div>
                    <div id="abstract-items">
                      <div class="form-group abstract-item" style="border: 1px solid #ddd; padding: 10px;">
                        <input type="text" class="form-control" name="item_no[]" placeholder="Item No." required style="margin-bottom:6px;">
                        <input type="text" class="form-control" name="particulars[]" placeholder="Particulars" required style="margin-bottom:6px;">
                        <input type="text" class="form-control" name="unit[]" placeholder="Unit" required style="margin-bottom:6px;">
                        <input type="number" class="form-control" name="quantity[]" placeholder="Quantity" required style="margin-bottom:6px;">
                        <input type="number" step="0.01" class="form-control" name="unit_cost[]" placeholder="Unit Cost" style="margin-bottom:6px;">
                        <input type="number" step="0.01" class="form-control" name="total[]" placeholder="Total" style="margin-bottom:6px;">
                        <input type="number" step="0.01" class="form-control" name="bidder1_unit_cost[]" placeholder="Bidder 1 Unit Cost" style="margin-bottom:6px;">
                        <input type="number" step="0.01" class="form-control" name="bidder1_total[]" placeholder="Bidder 1 Total" style="margin-bottom:6px;">
                        <input type="number" step="0.01" class="form-control" name="bidder2_unit_cost[]" placeholder="Bidder 2 Unit Cost" style="margin-bottom:6px;">
                        <input type="number" step="0.01" class="form-control" name="bidder2_total[]" placeholder="Bidder 2 Total" style="margin-bottom:6px;">
                        <input type="number" step="0.01" class="form-control" name="bidder3_unit_cost[]" placeholder="Bidder 3 Unit Cost" style="margin-bottom:6px;">
                        <input type="number" step="0.01" class="form-control" name="bidder3_total[]" placeholder="Bidder 3 Total" style="margin-bottom:6px;">
                         </div>      
                    </div>
                    <button type="button" id="add-abstract-item" class="btn btn-success">Add Another Item</button>
                    <button type="submit" name="add_abstract" class="btn btn-primary">Add Abstract</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MANAGE ABSTRACT OF BIDS SECTION -->
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-cog"></span>
                    <span>Manage Abstract of Bids</span>
                </strong>
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Abstract No</th>
                            <th>Date</th>
                            <th>Project Name</th>
                            <th>Lot</th>
                            <th>PR Number</th>
                            <th>ABC</th>
                            <th>Status</th>
                            <th style="width:120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch all Abstracts
                        $abstracts = find_all('abstractofbids');
                        foreach ($abstracts as $abstract):
                        ?>
                        <tr>
                            <td><?php echo remove_junk($abstract['abstract_no']); ?></td>
                            <td><?php echo remove_junk($abstract['abstract_date']); ?></td>
                            <td><?php echo remove_junk($abstract['project_name']); ?></td>
                            <td><?php echo remove_junk($abstract['lot']); ?></td>
                            <td><?php echo remove_junk($abstract['pr_number']); ?></td>
                            <td><?php echo remove_junk($abstract['abc']); ?></td>
                            <td><?php echo remove_junk($abstract['status']); ?></td>
                            <td>
                                <a href="edit_abstractofbids.php?id=<?php echo (int)$abstract['id']; ?>" 
                                   class="btn btn-warning btn-xs" 
                                   style="color:white; margin-right:2px;" 
                                   title="Edit">
                                    <span class="glyphicon glyphicon-pencil"></span>
                                </a>
                                <a href="delete_abstractofbids.php?id=<?php echo (int)$abstract['id']; ?>" 
                                   class="btn btn-danger btn-xs" 
                                   style="color:white; margin-right:2px;" 
                                   title="Delete"
                                   onclick="return confirm('Are you sure you want to delete this Abstract?');">
                                    <span class="glyphicon glyphicon-remove"></span>
                                </a>
                                <a href="print-layout/ABSTRACTOFBIDS.php?id=<?php echo (int)$abstract['id']; ?>" 
                                   class="btn btn-info btn-xs" 
                                   style="color:white;" 
                                   title="Print" 
                                   target="_blank">
                                    <span class="glyphicon glyphicon-print"></span>
                                </a>
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
// Add Abstract item dynamically
document.getElementById('add-abstract-item').addEventListener('click', function() {
    var itemDiv = document.createElement('div');
    itemDiv.className = 'form-group abstract-item';
    itemDiv.style.border = "1px solid #ddd";
    itemDiv.style.padding = "10px";
    itemDiv.innerHTML = `
        <input type="text" class="form-control" name="item_no[]" placeholder="Item No." required style="margin-bottom:6px;">
        <input type="text" class="form-control" name="particulars[]" placeholder="Particulars" required style="margin-bottom:6px;">
        <input type="text" class="form-control" name="unit[]" placeholder="Unit" required style="margin-bottom:6px;">
        <input type="number" class="form-control" name="quantity[]" placeholder="Quantity" required style="margin-bottom:6px;">
        <input type="number" step="0.01" class="form-control" name="unit_cost[]" placeholder="Unit Cost" style="margin-bottom:6px;">
        <input type="number" step="0.01" class="form-control" name="total[]" placeholder="Total" style="margin-bottom:6px;">
        <input type="number" step="0.01" class="form-control" name="bidder1_unit_cost[]" placeholder="Bidder 1 Unit Cost" style="margin-bottom:6px;">
        <input type="number" step="0.01" class="form-control" name="bidder1_total[]" placeholder="Bidder 1 Total" style="margin-bottom:6px;">
        <input type="number" step="0.01" class="form-control" name="bidder2_unit_cost[]" placeholder="Bidder 2 Unit Cost" style="margin-bottom:6px;">
        <input type="number" step="0.01" class="form-control" name="bidder2_total[]" placeholder="Bidder 2 Total" style="margin-bottom:6px;">
        <input type="number" step="0.01" class="form-control" name="bidder3_unit_cost[]" placeholder="Bidder 3 Unit Cost" style="margin-bottom:6px;">
        <input type="number" step="0.01" class="form-control" name="bidder3_total[]" placeholder="Bidder 3 Total" style="margin-bottom:6px;">
        <button type="button" class="btn btn-danger remove-abstract-item" style="margin-top: 10px;">Remove</button>
    `;
    document.getElementById('abstract-items').appendChild(itemDiv);

    // Remove item
    itemDiv.querySelector('.remove-abstract-item').addEventListener('click', function() {
        itemDiv.remove();
    });
});
</script>

<?php include_once 'layouts/footer.php'; ?>