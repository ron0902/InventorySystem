<?php
$page_title = 'Add Invoice';
require_once('includes/load.php');
page_require_level(1);

// Fetch suppliers and POs
$all_suppliers = find_all('suppliers');
$all_pos = find_all('purchase_orders');

// Handle Delete Invoice
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $invoice_id = (int)$_GET['delete_id'];

    // Delete related rows in the invoice_items table
    $delete_items = "DELETE FROM invoice_items WHERE invoice_id = '{$invoice_id}'";
    if ($db->query($delete_items)) {
        // Delete the invoice
        $delete_invoice = "DELETE FROM invoices WHERE invoice_id = '{$invoice_id}'";
        if ($db->query($delete_invoice)) {
            $session->msg("s", "Invoice deleted successfully.");
        } else {
            $session->msg("d", "Failed to delete invoice.");
        }
    } else {
        $session->msg("d", "Failed to delete related invoice items.");
    }
    redirect('invoice.php');
}

// Handle Add Invoice
if (isset($_POST['add_invoice'])) {
    $req_fields = ['supplier-id', 'po-id', 'invoice-number', 'invoice-amount', 'due-date'];
    validate_fields($req_fields);

    if (empty($errors)) {
        // Initialize arrays to avoid undefined array key warnings
        $item_descriptions = isset($_POST['item-description']) ? $_POST['item-description'] : [];
        $stock_property_nos = isset($_POST['stock-property-no']) ? $_POST['stock-property-no'] : [];
        $units = isset($_POST['unit']) ? $_POST['unit'] : [];
        $quantities = isset($_POST['quantity']) ? $_POST['quantity'] : [];
        $unit_costs = isset($_POST['unit-cost']) ? $_POST['unit-cost'] : [];
        $amounts = isset($_POST['amount']) ? $_POST['amount'] : [];

        $invalid_amounts = false;

        // Validate amounts
        foreach ($amounts as $index => $amt) {
            if (trim($amt) === '' || !is_numeric($amt)) {
                $invalid_amounts = true;
                $errors[] = "Amount for item " . ($index + 1) . " is required and must be a number.";
            }
        }

        if (!$invalid_amounts) {
            $supplier_id = (int)$_POST['supplier-id'];
            $po_id = (int)$_POST['po-id'];
            $invoice_number = remove_junk($db->escape($_POST['invoice-number']));
            $amount = remove_junk($db->escape($_POST['invoice-amount']));
            $due_date = remove_junk($db->escape($_POST['due-date']));

            $sql = "INSERT INTO invoices (supplier_id, po_id, invoice_number, amount, due_date) 
                    VALUES ('{$supplier_id}', '{$po_id}', '{$invoice_number}', '{$amount}', '{$due_date}')";

            if ($db->query($sql)) {
                $invoice_id = $db->insert_id;

                // Insert items only if arrays are not empty
                if (!empty($item_descriptions)) {
                    for ($i = 0; $i < count($item_descriptions); $i++) {
                        $item_description = remove_junk($db->escape($item_descriptions[$i]));
                        $stock_property_no = remove_junk($db->escape($stock_property_nos[$i]));
                        $unit = remove_junk($db->escape($units[$i]));
                        $quantity = (int)$quantities[$i];
                        $unit_cost = (float)$unit_costs[$i];
                        $amount = (float)$amounts[$i];

                        $sql_item = "INSERT INTO invoice_items (invoice_id, po_id, stock_property_no, unit, description, quantity, unit_cost, amount) 
                                     VALUES ('{$invoice_id}', '{$po_id}', '{$stock_property_no}', '{$unit}', '{$item_description}', '{$quantity}', '{$unit_cost}', '{$amount}')";
                        $db->query($sql_item);
                    }
                }

                $session->msg("s", "Invoice and items added successfully.");
                redirect('invoice.php');
            } else {
                $session->msg("d", "Failed to add invoice.");
                redirect('invoice.php');
            }
        } else {
            $session->msg("d", implode("<br>", $errors));
            redirect('invoice.php');
        }
    } else {
        $session->msg("d", implode("<br>", $errors));
        redirect('invoice.php');
    }
}

// Fetch invoices
$all_invoices = find_by_sql("
    SELECT invoices.*, suppliers.name AS supplier_name, purchase_orders.po_number 
    FROM invoices 
    LEFT JOIN suppliers ON invoices.supplier_id = suppliers.supplier_id 
    LEFT JOIN purchase_orders ON invoices.po_id = purchase_orders.po_id 
    ORDER BY invoices.invoice_id DESC
");
?>

<?php include_once('layouts/header.php'); ?>
<div class="row">
    <div class="col-md-12"><?php echo display_msg($msg); ?></div>
</div>

<!-- Add Invoice Form -->
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong><span class="glyphicon glyphicon-th"></span> Add New Invoice</strong>
            </div>
            <div class="panel-body">
                <form method="post" action="invoice.php">
                    <div class="form-group">
                        <label for="supplier-id">Supplier</label>
                        <select class="form-control" name="supplier-id" required>
                            <option value="">Select Supplier</option>
                            <?php foreach ($all_suppliers as $supplier): ?>
                                <option value="<?php echo (int)$supplier['supplier_id']; ?>">
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
                                <option value="<?php echo (int)$po['po_id']; ?>">
                                    <?php echo remove_junk($po['po_number']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <input type="text" class="form-control" name="invoice-number" placeholder="Invoice Number" required>
                    </div>
                    <div class="form-group">
                        <input type="number" class="form-control" name="invoice-amount" placeholder="Total Amount" required>
                    </div>
                    <div class="form-group">
                        <input type="date" class="form-control" name="due-date" placeholder="Due Date" required>
                    </div>

                    <button type="submit" name="add_invoice" class="btn btn-primary">Add Invoice</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Invoice Table -->
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong><span class="glyphicon glyphicon-list-alt"></span> All Invoices</strong>
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>Invoice Number</th>
                            <th>Supplier</th>
                            <th>Purchase Order</th>
                            <th>Amount</th>
                            <th>Due Date</th>
                            <th class="text-center" style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_invoices as $i => $invoice): ?>
                            <tr>
                                <td class="text-center"><?php echo count_id() + $i; ?></td>
                                <td><?php echo remove_junk($invoice['invoice_number']); ?></td>
                                <td><?php echo remove_junk($invoice['supplier_name']); ?></td>
                                <td><?php echo remove_junk($invoice['po_number']); ?></td>
                                <td><?php echo number_format($invoice['amount'], 2); ?></td>
                                <td><?php echo remove_junk($invoice['due_date']); ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="edit_invoice.php?id=<?php echo (int)$invoice['invoice_id']; ?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">
                                            <i class="glyphicon glyphicon-pencil"></i>
                                        </a>
                                        <a href="invoice.php?delete_id=<?php echo (int)$invoice['invoice_id']; ?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete">
                                            <i class="glyphicon glyphicon-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($all_invoices)): ?>
                            <tr><td colspan="7" class="text-center">No invoices found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>