<?php
require_once('includes/load.php');

if (!isset($_GET['id'])) {
    $session->msg("d", "Missing invoice id.");
    redirect('invoice.php');
}
$id = (int)$_GET['id'];
$invoice = find_by_id('invoices', $id, 'invoice_id');
if (!$invoice) {
    $session->msg("d", "Invoice not found.");
    redirect('invoice.php');
}
$invoice_items = find_by_sql("SELECT * FROM invoice_items WHERE invoice_id='{$id}'");
$all_suppliers = find_all('suppliers');
$all_pos = find_all('purchase_orders');

if (isset($_POST['update_invoice'])) {
    $req_fields = ['supplier-id', 'po-id', 'invoice-number', 'invoice-amount', 'due-date'];
    validate_fields($req_fields);

    if (empty($errors)) {
        $supplier_id = (int)$_POST['supplier-id'];
        $po_id = (int)$_POST['po-id'];
        $invoice_number = remove_junk($db->escape($_POST['invoice-number']));
        $amount = remove_junk($db->escape($_POST['invoice-amount']));
        $due_date = remove_junk($db->escape($_POST['due-date']));

        $sql = "UPDATE invoices SET supplier_id='{$supplier_id}', po_id='{$po_id}', invoice_number='{$invoice_number}', amount='{$amount}', due_date='{$due_date}' WHERE invoice_id='{$id}'";
        if ($db->query($sql)) {
            // Remove old items
            $db->query("DELETE FROM invoice_items WHERE invoice_id='{$id}'");

            // Insert new items
            $item_descriptions = isset($_POST['item-description']) ? $_POST['item-description'] : [];
            $stock_property_nos = isset($_POST['stock-property-no']) ? $_POST['stock-property-no'] : [];
            $units = isset($_POST['unit']) ? $_POST['unit'] : [];
            $quantities = isset($_POST['quantity']) ? $_POST['quantity'] : [];
            $unit_costs = isset($_POST['unit-cost']) ? $_POST['unit-cost'] : [];
            $amounts = isset($_POST['amount']) ? $_POST['amount'] : [];

            if (!empty($item_descriptions)) {
                for ($i = 0; $i < count($item_descriptions); $i++) {
                    $item_description = remove_junk($db->escape($item_descriptions[$i]));
                    $stock_property_no = remove_junk($db->escape($stock_property_nos[$i]));
                    $unit = remove_junk($db->escape($units[$i]));
                    $quantity = (int)$quantities[$i];
                    $unit_cost = (float)$unit_costs[$i];
                    $amount_item = (float)$amounts[$i];

                    $sql_item = "INSERT INTO invoice_items (invoice_id, po_id, stock_property_no, unit, description, quantity, unit_cost, amount) 
                                 VALUES ('{$id}', '{$po_id}', '{$stock_property_no}', '{$unit}', '{$item_description}', '{$quantity}', '{$unit_cost}', '{$amount_item}')";
                    $db->query($sql_item);
                }
            }

            $session->msg("s", "Invoice updated successfully.");
            redirect('invoice.php');
        } else {
            $session->msg("d", "Failed to update invoice.");
            redirect('edit_invoice.php?id=' . $id, false);
        }
    } else {
        $session->msg("d", implode("<br>", $errors));
        redirect('edit_invoice.php?id=' . $id, false);
    }
}

include_once('layouts/header.php');
?>

<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong><span class="glyphicon glyphicon-th"></span> Edit Invoice</strong>
            </div>
            <div class="panel-body">
                <form method="post" action="">
                    <div class="form-group">
                        <label for="supplier-id">Supplier</label>
                        <select class="form-control" name="supplier-id" required>
                            <option value="">Select Supplier</option>
                            <?php foreach ($all_suppliers as $supplier): ?>
                                <option value="<?php echo (int)$supplier['supplier_id']; ?>" <?php if ($supplier['supplier_id'] == $invoice['supplier_id']) echo 'selected'; ?>>
                                    <?php echo remove_junk(ucfirst($supplier['name'])); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="po-id">Purchase Order</label>
                        <select class="form-control" name="po-id" required>
                            <option value="">Select Purchase Order</option>
                            <?php foreach ($all_pos as $po): ?>
                                <option value="<?php echo (int)$po['po_id']; ?>" <?php if ($po['po_id'] == $invoice['po_id']) echo 'selected'; ?>>
                                    <?php echo remove_junk($po['po_number']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <input type="text" class="form-control" name="invoice-number" value="<?php echo remove_junk($invoice['invoice_number']); ?>" placeholder="Invoice Number" required>
                    </div>
                    <div class="form-group">
                        <input type="number" class="form-control" name="invoice-amount" value="<?php echo remove_junk($invoice['amount']); ?>" placeholder="Total Amount" required>
                    </div>
                    <div class="form-group">
                        <input type="date" class="form-control" name="due-date" value="<?php echo remove_junk($invoice['due_date']); ?>" placeholder="Due Date" required>
                    </div>

                    <h4>Invoice Items</h4>
                    <div id="invoice-items">
                        <?php foreach ($invoice_items as $item): ?>
                        <div class="form-group invoice-item" style="border: 1px solid #ddd; padding: 10px;">
                            <input type="text" class="form-control" name="item-description[]" value="<?php echo remove_junk($item['description']); ?>" placeholder="Description" required style="margin-bottom:6px;">
                            <input type="text" class="form-control" name="stock-property-no[]" value="<?php echo remove_junk($item['stock_property_no']); ?>" placeholder="Stock/Property No." required style="margin-bottom:6px;">
                            <input type="text" class="form-control" name="unit[]" value="<?php echo remove_junk($item['unit']); ?>" placeholder="Unit" required style="margin-bottom:6px;">
                            <input type="number" class="form-control" name="quantity[]" value="<?php echo remove_junk($item['quantity']); ?>" placeholder="Quantity" required style="margin-bottom:6px;">
                            <input type="number" step="0.01" class="form-control" name="unit-cost[]" value="<?php echo remove_junk($item['unit_cost']); ?>" placeholder="Unit Cost" style="margin-bottom:6px;">
                            <input type="number" step="0.01" class="form-control" name="amount[]" value="<?php echo remove_junk($item['amount']); ?>" placeholder="Amount" style="margin-bottom:6px;">
                            <button type="button" class="btn btn-danger remove-invoice-item" style="margin-top: 10px;">Remove</button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" id="add-invoice-item" class="btn btn-success">Add Another Item</button>
                    <button type="submit" name="update_invoice" class="btn btn-primary">Update Invoice</button>
                    <a href="invoice.php" class="btn btn-default">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('add-invoice-item').addEventListener('click', function() {
    var itemDiv = document.createElement('div');
    itemDiv.className = 'form-group invoice-item';
    itemDiv.style.border = "1px solid #ddd";
    itemDiv.style.padding = "10px";
    itemDiv.innerHTML = `
        <input type="text" class="form-control" name="item-description[]" placeholder="Description" required style="margin-bottom:6px;">
        <input type="text" class="form-control" name="stock-property-no[]" placeholder="Stock/Property No." required style="margin-bottom:6px;">
        <input type="text" class="form-control" name="unit[]" placeholder="Unit" required style="margin-bottom:6px;">
        <input type="number" class="form-control" name="quantity[]" placeholder="Quantity" required style="margin-bottom:6px;">
        <input type="number" step="0.01" class="form-control" name="unit-cost[]" placeholder="Unit Cost" style="margin-bottom:6px;">
        <input type="number" step="0.01" class="form-control" name="amount[]" placeholder="Amount" style="margin-bottom:6px;">
        <button type="button" class="btn btn-danger remove-invoice-item" style="margin-top: 10px;">Remove</button>
    `;
    document.getElementById('invoice-items').appendChild(itemDiv);

    itemDiv.querySelector('.remove-invoice-item').addEventListener('click', function() {
        itemDiv.remove();
    });
});

// Remove item for existing items
document.querySelectorAll('.remove-invoice-item').forEach(function(btn) {
    btn.addEventListener('click', function() {
        btn.closest('.invoice-item').remove();
    });
});
</script>

<?php include_once('layouts/footer.php'); ?>