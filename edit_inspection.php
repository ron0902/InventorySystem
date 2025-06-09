<?php
require_once 'includes/load.php';

if (!isset($_GET['id'])) {
    $session->msg("d", "Missing inspection id.");
    redirect('add_inspection.php');
}
$id = (int)$_GET['id'];
$inspection = find_by_id('inspection', $id);
if (!$inspection) {
    $session->msg("d", "Inspection not found.");
    redirect('add_inspection.php');
}
$inspection_items = find_by_sql("SELECT * FROM inspection_items WHERE inspection_id='{$id}'");

if (isset($_POST['update_inspection'])) {
    $fields = ['supplier', 'iar_no', 'po_no', 'date', 'invoice_no', 'purpose', 'date_inspected', 'date_received'];
    validate_fields($fields);

    $errors = [];

    // Validate items
    if (!isset($_POST['stock_no']) || !is_array($_POST['stock_no'])) {
        $errors[] = "Items must be added.";
    } else {
        foreach ($_POST['stock_no'] as $qty) {
            if (empty($qty)) {
                $errors[] = "All stock numbers are required.";
            }
        }
    }

    if (empty($errors)) {
        $supplier = remove_junk($db->escape($_POST['supplier']));
        $iar_no = remove_junk($db->escape($_POST['iar_no']));
        $po_no = remove_junk($db->escape($_POST['po_no']));
        $date = remove_junk($db->escape($_POST['date']));
        $invoice_no = remove_junk($db->escape($_POST['invoice_no']));
        $purpose = remove_junk($db->escape($_POST['purpose']));
        $date_inspected = remove_junk($db->escape($_POST['date_inspected']));
        $date_received = remove_junk($db->escape($_POST['date_received']));
        $status = isset($_POST['status']) ? $_POST['status'] : '';
        $complete = ($status == 'complete') ? 1 : 0;
        $quantity = ($status == 'quantity') ? 1 : 0;

        $db->query("START TRANSACTION");

        $query = "UPDATE inspection SET 
                    supplier='{$supplier}', iar_no='{$iar_no}', po_no='{$po_no}', date='{$date}', 
                    invoice_no='{$invoice_no}', purpose='{$purpose}', date_inspected='{$date_inspected}', 
                    date_received='{$date_received}', complete='{$complete}', quantity='{$quantity}'
                  WHERE id='{$id}'";

        if ($db->query($query)) {
            // Remove old items
            $db->query("DELETE FROM inspection_items WHERE inspection_id='{$id}'");

            // Insert new items
            if (isset($_POST['stock_no']) && is_array($_POST['stock_no'])) {
                foreach ($_POST['stock_no'] as $key => $stock_no) {
                    $unit = remove_junk($db->escape($_POST['unit'][$key]));
                    $description = remove_junk($db->escape($_POST['description'][$key]));
                    $qty = remove_junk($db->escape($_POST['quantity'][$key]));

                    $query = "INSERT INTO inspection_items (inspection_id, stock_no, unit, description, quantity)
                              VALUES ('{$id}', '{$stock_no}', '{$unit}', '{$description}', '{$qty}')";
                    if (!$db->query($query)) {
                        $db->query("ROLLBACK");
                        $session->msg('d', 'Failed to update inspection item!');
                        redirect('edit_inspection.php?id=' . $id, false);
                    }
                }
            }

            $db->query("COMMIT");
            $session->msg('s', "Inspection updated successfully.");
            redirect('add_inspection.php', false);
        } else {
            $db->query("ROLLBACK");
            $session->msg('d', 'Failed to update inspection!');
            redirect('edit_inspection.php?id=' . $id, false);
        }
    } else {
        $session->msg("d", implode("<br>", $errors));
        redirect('edit_inspection.php?id=' . $id, false);
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
                    <span>Edit Inspection and Acceptance Report</span>
                </strong>
            </div>
            <div class="panel-body">
                <form method="post" action="" class="clearfix">
                    <div class="form-group">
                        <select class="form-control" name="supplier" required>
                            <option value="">Select Supplier</option>
                            <?php
                            $suppliers = find_all('suppliers');
                            foreach ($suppliers as $supplier):
                            ?>
                                <option value="<?php echo remove_junk($supplier['name']); ?>" <?php if ($supplier['name'] == $inspection['supplier']) echo 'selected'; ?>>
                                    <?php echo remove_junk($supplier['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="iar_no" value="<?php echo remove_junk($inspection['iar_no']); ?>" placeholder="IAR No." required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="po_no" value="<?php echo remove_junk($inspection['po_no']); ?>" placeholder="PO No." required>
                    </div>
                    <div class="form-group">
                        <input type="date" class="form-control" name="date" value="<?php echo remove_junk($inspection['date']); ?>" placeholder="Date" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="invoice_no" value="<?php echo remove_junk($inspection['invoice_no']); ?>" placeholder="Invoice No." required>
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" name="purpose" placeholder="Purpose" required><?php echo remove_junk($inspection['purpose']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Date Inspected</label>
                        <input type="date" class="form-control" name="date_inspected" value="<?php echo remove_junk($inspection['date_inspected']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Date Received</label>
                        <input type="date" class="form-control" name="date_received" value="<?php echo remove_junk($inspection['date_received']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label><input type="radio" name="status" value="complete" <?php if ($inspection['complete']) echo 'checked'; ?>> Complete</label>
                        <label style="margin-left:20px;"><input type="radio" name="status" value="quantity" <?php if ($inspection['quantity']) echo 'checked'; ?>> Quantity</label>
                    </div>
                    <div id="inspection-items">
                        <?php foreach ($inspection_items as $item): ?>
                        <div class="form-group inspection-item" style="border: 1px solid #ddd; padding: 10px;">
                            <input type="text" class="form-control" name="stock_no[]" value="<?php echo remove_junk($item['stock_no']); ?>" placeholder="Stock No." required style="margin-bottom:6px;">
                            <input type="text" class="form-control" name="unit[]" value="<?php echo remove_junk($item['unit']); ?>" placeholder="Unit" required style="margin-bottom:6px;">
                            <input type="text" class="form-control" name="description[]" value="<?php echo remove_junk($item['description']); ?>" placeholder="Description" required style="margin-bottom:6px;">
                            <input type="number" class="form-control" name="quantity[]" value="<?php echo remove_junk($item['quantity']); ?>" placeholder="Quantity" required style="margin-bottom:6px;">
                            <button type="button" class="btn btn-danger remove-inspection-item" style="margin-top: 10px;">Remove</button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" id="add-inspection-item" class="btn btn-success">Add Another Item</button>
                    <button type="submit" name="update_inspection" class="btn btn-primary">Update Inspection</button>
                    <a href="add_inspection.php" class="btn btn-default">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('add-inspection-item').addEventListener('click', function() {
    var itemDiv = document.createElement('div');
    itemDiv.className = 'form-group inspection-item';
    itemDiv.style.border = "1px solid #ddd";
    itemDiv.style.padding = "10px";
    itemDiv.innerHTML = `
        <input type="text" class="form-control" name="stock_no[]" placeholder="Stock No." required style="margin-bottom:6px;">
        <input type="text" class="form-control" name="unit[]" placeholder="Unit" required style="margin-bottom:6px;">
        <input type="text" class="form-control" name="description[]" placeholder="Description" required style="margin-bottom:6px;">
        <input type="number" class="form-control" name="quantity[]" placeholder="Quantity" required style="margin-bottom:6px;">
        <button type="button" class="btn btn-danger remove-inspection-item" style="margin-top: 10px;">Remove</button>
    `;
    document.getElementById('inspection-items').appendChild(itemDiv);

    itemDiv.querySelector('.remove-inspection-item').addEventListener('click', function() {
        itemDiv.remove();
    });
});

// Remove item for existing items
document.querySelectorAll('.remove-inspection-item').forEach(function(btn) {
    btn.addEventListener('click', function() {
        btn.closest('.inspection-item').remove();
    });
});
</script>

<?php include_once 'layouts/footer.php'; ?>