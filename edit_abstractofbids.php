<?php
require_once 'includes/load.php';

// Handle status change
if (isset($_GET['action']) && $_GET['action'] === 'done' && isset($_GET['id'])) {
    $abstract_id = (int)$_GET['id'];
    $sql = "UPDATE abstractofbids SET status='Done' WHERE id='{$abstract_id}'";
    if ($db->query($sql)) {
        $session->msg('s', 'Abstract of Bids marked as Done.');
    } else {
        $session->msg('d', 'Failed to update status.');
    }
    redirect('add_abstractofbids.php');
}

if (!isset($_GET['id'])) {
    $session->msg("d", "Missing abstract id.");
    redirect('add_abstractofbids.php');
}
$id = (int)$_GET['id'];
$abstract = find_by_id('abstractofbids', $id);
if (!$abstract) {
    $session->msg("d", "Abstract not found.");
    redirect('add_abstractofbids.php');
}
$abstract_items = find_by_sql("SELECT * FROM abstractofbids_items WHERE abstract_id='{$id}'");

if (isset($_POST['update_abstract'])) {
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

        $query = "UPDATE abstractofbids SET 
                    abstract_no='{$abstract_no}', 
                    abstract_date='{$abstract_date}', 
                    project_name='{$project_name}', 
                    lot='{$lot}', 
                    pr_number='{$pr_number}', 
                    abc='{$abc}', 
                    rfq_number='{$rfq_number}', 
                    bid_opening='{$bid_opening}'
                  WHERE id='{$id}'";

        if ($db->query($query)) {
            // Remove old items
            $db->query("DELETE FROM abstractofbids_items WHERE abstract_id='{$id}'");

            // Insert new items
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
                        ('{$id}', '{$item_no}', '{$particulars}', '{$unit}', '{$quantity}', '{$unit_cost}', '{$total}',
                         '{$bidder1_unit_cost}', '{$bidder1_total}', '{$bidder2_unit_cost}', '{$bidder2_total}', '{$bidder3_unit_cost}', '{$bidder3_total}')";
                    if (!$db->query($query)) {
                        $db->query("ROLLBACK");
                        $session->msg('d', 'Failed to update Abstract of Bids item!');
                        redirect('edit_abstractofbids.php?id=' . $id, false);
                    }
                }
            }

            $db->query("COMMIT");
            $session->msg('s', "Abstract of Bids updated successfully.");
            redirect('add_abstractofbids.php', false);
        } else {
            $db->query("ROLLBACK");
            $session->msg('d', 'Failed to update Abstract of Bids!');
            redirect('edit_abstractofbids.php?id=' . $id, false);
        }
    } else {
        $session->msg("d", implode("<br>", $errors));
        redirect('edit_abstractofbids.php?id=' . $id, false);
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
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-edit"></span>
                    <span>Edit Abstract of Bids</span>
                </strong>
            </div>
            <div class="panel-body">
                <form method="post" action="" class="clearfix">
                    <div class="form-group">
                        <input type="text" class="form-control" name="abstract_no" value="<?php echo remove_junk($abstract['abstract_no']); ?>" placeholder="Abstract No." required>
                    </div>
                    <div class="form-group">
                        <input type="date" class="form-control" name="abstract_date" value="<?php echo remove_junk($abstract['abstract_date']); ?>" placeholder="Date" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="project_name" value="<?php echo remove_junk($abstract['project_name']); ?>" placeholder="Project Name" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="lot" value="<?php echo remove_junk($abstract['lot']); ?>" placeholder="Lot">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="pr_number" value="<?php echo remove_junk($abstract['pr_number']); ?>" placeholder="PR Number">
                    </div>
                    <div class="form-group">
                        <input type="number" step="0.01" class="form-control" name="abc" value="<?php echo remove_junk($abstract['abc']); ?>" placeholder="ABC" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="rfq_number" value="<?php echo remove_junk($abstract['rfq_number']); ?>" placeholder="RFQ Number">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="bid_opening" value="<?php echo remove_junk($abstract['bid_opening']); ?>" placeholder="Bid Opening">
                    </div>
                    <div id="abstract-items">
                        <?php foreach ($abstract_items as $item): ?>
                        <div class="form-group abstract-item" style="border: 1px solid #ddd; padding: 10px;">
                            <input type="text" class="form-control" name="item_no[]" value="<?php echo remove_junk($item['item_no']); ?>" placeholder="Item No." required style="margin-bottom:6px;">
                            <input type="text" class="form-control" name="particulars[]" value="<?php echo remove_junk($item['particulars']); ?>" placeholder="Particulars" required style="margin-bottom:6px;">
                            <input type="text" class="form-control" name="unit[]" value="<?php echo remove_junk($item['unit']); ?>" placeholder="Unit" required style="margin-bottom:6px;">
                            <input type="number" class="form-control" name="quantity[]" value="<?php echo remove_junk($item['quantity']); ?>" placeholder="Quantity" required style="margin-bottom:6px;">
                            <input type="number" step="0.01" class="form-control" name="unit_cost[]" value="<?php echo remove_junk($item['unit_cost']); ?>" placeholder="Unit Cost" style="margin-bottom:6px;">
                            <input type="number" step="0.01" class="form-control" name="total[]" value="<?php echo remove_junk($item['total']); ?>" placeholder="Total" style="margin-bottom:6px;">
                            <input type="number" step="0.01" class="form-control" name="bidder1_unit_cost[]" value="<?php echo remove_junk($item['bidder1_unit_cost']); ?>" placeholder="Bidder 1 Unit Cost" style="margin-bottom:6px;">
                            <input type="number" step="0.01" class="form-control" name="bidder1_total[]" value="<?php echo remove_junk($item['bidder1_total']); ?>" placeholder="Bidder 1 Total" style="margin-bottom:6px;">
                            <input type="number" step="0.01" class="form-control" name="bidder2_unit_cost[]" value="<?php echo remove_junk($item['bidder2_unit_cost']); ?>" placeholder="Bidder 2 Unit Cost" style="margin-bottom:6px;">
                            <input type="number" step="0.01" class="form-control" name="bidder2_total[]" value="<?php echo remove_junk($item['bidder2_total']); ?>" placeholder="Bidder 2 Total" style="margin-bottom:6px;">
                            <input type="number" step="0.01" class="form-control" name="bidder3_unit_cost[]" value="<?php echo remove_junk($item['bidder3_unit_cost']); ?>" placeholder="Bidder 3 Unit Cost" style="margin-bottom:6px;">
                            <input type="number" step="0.01" class="form-control" name="bidder3_total[]" value="<?php echo remove_junk($item['bidder3_total']); ?>" placeholder="Bidder 3 Total" style="margin-bottom:6px;">
                            <button type="button" class="btn btn-danger remove-abstract-item" style="margin-top: 10px;">Remove</button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" id="add-abstract-item" class="btn btn-success">Add Another Item</button>
                    <button type="submit" name="update_abstract" class="btn btn-primary">Update Abstract</button>
                    <a href="add_abstractofbids.php" class="btn btn-default">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
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

    itemDiv.querySelector('.remove-abstract-item').addEventListener('click', function() {
        itemDiv.remove();
    });
});

// Remove item for existing items
document.querySelectorAll('.remove-abstract-item').forEach(function(btn) {
    btn.addEventListener('click', function() {
        btn.closest('.abstract-item').remove();
    });
});
</script>

<?php include_once 'layouts/footer.php'; ?>