<?php
require_once 'includes/load.php';
if (!isset($_GET['id'])) {
    $session->msg("d", "Missing RFQ id.");
    redirect('add_rfq.php');
}
$rfq_id = (int)$_GET['id'];

// Fetch RFQ
$rfq = find_by_id('rfq', $rfq_id);
if (!$rfq) {
    $session->msg("d", "RFQ not found.");
    redirect('add_rfq.php');
}

// Fetch RFQ items
$rfq_items = find_by_sql("SELECT * FROM rfq_items WHERE rfq_id='{$rfq_id}'");

if (isset($_POST['update_rfq'])) {
    $rfq_fields = ['rfq_no', 'rfq_date', 'company_name', 'company_address'];
    validate_fields($rfq_fields);

    $rfq_errors = [];

    // Validate items
    if (!isset($_POST['rfq_item_name']) || !is_array($_POST['rfq_item_name'])) {
        $rfq_errors[] = "RFQ items must be added.";
    } else {
        foreach ($_POST['rfq_item_name'] as $qty) {
            if (empty($qty)) {
                $rfq_errors[] = "All RFQ item names are required.";
            }
        }
    }

    if (empty($rfq_errors)) {
        $rfq_no = remove_junk($db->escape($_POST['rfq_no']));
        $rfq_date = remove_junk($db->escape($_POST['rfq_date']));
        $company_name = remove_junk($db->escape($_POST['company_name']));
        $company_address = remove_junk($db->escape($_POST['company_address']));

        $db->query("START TRANSACTION");

        $query = "UPDATE rfq SET 
                    rfq_no='{$rfq_no}', 
                    rfq_date='{$rfq_date}', 
                    company_name='{$company_name}', 
                    company_address='{$company_address}'
                  WHERE id='{$rfq_id}'";
        if ($db->query($query)) {
            // Remove old items
            $db->query("DELETE FROM rfq_items WHERE rfq_id='{$rfq_id}'");

            // Insert new items
            if (isset($_POST['rfq_item_name']) && is_array($_POST['rfq_item_name'])) {
                foreach ($_POST['rfq_item_name'] as $key => $item_name) {
                    if (!empty($item_name) && isset($_POST['rfq_quantity'][$key]) && isset($_POST['rfq_unit'][$key])) {
                        $item_name = remove_junk($db->escape($item_name));
                        $quantity = remove_junk($db->escape($_POST['rfq_quantity'][$key]));
                        $unit = remove_junk($db->escape($_POST['rfq_unit'][$key]));

                        $query = "INSERT INTO rfq_items (rfq_id, item_description, unit, quantity)
                                  VALUES ('{$rfq_id}', '{$item_name}', '{$unit}', '{$quantity}')";
                        if (!$db->query($query)) {
                            $db->query("ROLLBACK");
                            $session->msg('d', 'Failed to update RFQ item!');
                            redirect('edit_rfq.php?id=' . $rfq_id, false);
                        }
                    }
                }
            }

            $db->query("COMMIT");
            $session->msg('s', "RFQ updated successfully.");
            redirect('add_rfq.php', false);
        } else {
            $db->query("ROLLBACK");
            $session->msg('d', 'Failed to update RFQ!');
            redirect('edit_rfq.php?id=' . $rfq_id, false);
        }
    } else {
        $session->msg("d", implode("<br>", $rfq_errors));
        redirect('edit_rfq.php?id=' . $rfq_id, false);
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
                    <span>Edit RFQ</span>
                </strong>
            </div>
            <div class="panel-body">
                <form method="post" action="" class="clearfix">
                    <div class="form-group">
                        <input type="text" class="form-control" name="rfq_no" value="<?php echo remove_junk($rfq['rfq_no']); ?>" placeholder="RFQ Number" required>
                    </div>
                    <div class="form-group">
                        <input type="date" class="form-control" name="rfq_date" value="<?php echo remove_junk($rfq['rfq_date']); ?>" placeholder="RFQ Date" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="company_name" value="<?php echo remove_junk($rfq['company_name']); ?>" placeholder="Company Name" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="company_address" value="<?php echo remove_junk($rfq['company_address']); ?>" placeholder="Company Address" required>
                    </div>
                    <div id="rfq-items">
                        <?php foreach ($rfq_items as $item): ?>
                        <div class="form-group rfq-item" style="border: 1px solid #ddd; padding: 10px;">
                            <div class="input-group" style="margin-top: 10px;">
                                <span class="input-group-addon">
                                    <i class="glyphicon glyphicon-th-large"></i>
                                </span>
                                <input type="text" class="form-control" name="rfq_item_name[]" value="<?php echo remove_junk($item['item_description']); ?>" placeholder="Item Name" required>
                            </div>
                            <div class="input-group" style="margin-top: 10px;">
                                <span class="input-group-addon">
                                    <i class="glyphicon glyphicon-shopping-cart"></i>
                                </span>
                                <input type="number" class="form-control" name="rfq_quantity[]" value="<?php echo remove_junk($item['quantity']); ?>" placeholder="Quantity" required>
                            </div>
                            <div class="input-group" style="margin-top: 10px;">
                                <span class="input-group-addon">
                                    <i class="glyphicon glyphicon-scale"></i>
                                </span>
                                <input type="text" class="form-control" name="rfq_unit[]" value="<?php echo remove_junk($item['unit']); ?>" placeholder="Unit" required>
                            </div>
                            <button type="button" class="btn btn-danger remove-rfq-item" style="margin-top: 10px;">Remove</button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" id="add-rfq-item" class="btn btn-success">Add Another Item</button>
                    <button type="submit" name="update_rfq" class="btn btn-primary">Update RFQ</button>
                    <a href="add_rfq.php" class="btn btn-default">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('add-rfq-item').addEventListener('click', function() {
    var itemDiv = document.createElement('div');
    itemDiv.className = 'form-group rfq-item';
    itemDiv.style.border = "1px solid #ddd";
    itemDiv.style.padding = "10px";
    itemDiv.innerHTML = `
        <div class="input-group" style="margin-top: 10px;">
            <span class="input-group-addon">
                <i class="glyphicon glyphicon-th-large"></i>
            </span>
            <input type="text" class="form-control" name="rfq_item_name[]" placeholder="Item Name" required>
        </div>
        <div class="input-group" style="margin-top: 10px;">
            <span class="input-group-addon">
                <i class="glyphicon glyphicon-shopping-cart"></i>
            </span>
            <input type="number" class="form-control" name="rfq_quantity[]" placeholder="Quantity" required>
        </div>
        <div class="input-group" style="margin-top: 10px;">
            <span class="input-group-addon">
                <i class="glyphicon glyphicon-scale"></i>
            </span>
            <input type="text" class="form-control" name="rfq_unit[]" placeholder="Unit" required>
        </div>
        <button type="button" class="btn btn-danger remove-rfq-item" style="margin-top: 10px;">Remove</button>
    `;
    document.getElementById('rfq-items').appendChild(itemDiv);

    itemDiv.querySelector('.remove-rfq-item').addEventListener('click', function() {
        itemDiv.remove();
    });
});

// Remove item for existing items
document.querySelectorAll('.remove-rfq-item').forEach(function(btn) {
    btn.addEventListener('click', function() {
        btn.closest('.rfq-item').remove();
    });
});
</script>

<?php include_once 'layouts/footer.php'; ?>