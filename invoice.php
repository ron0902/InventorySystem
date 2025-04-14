<?php
$page_title = 'Add Invoice';
require_once('includes/load.php');
page_require_level(1);

// Fetch suppliers and POs
$all_suppliers = find_all('suppliers');
$all_pos = find_all('purchase_orders');

if (isset($_POST['add_invoice'])) {
    $req_fields = ['supplier-id', 'po-id', 'invoice-number', 'invoice-amount', 'due-date'];
    validate_fields($req_fields);

    if (empty($errors)) {
        $item_descriptions = $_POST['item-description'];
        $stock_property_nos = $_POST['stock-property-no'];
        $units = $_POST['unit'];
        $quantities = $_POST['quantity'];
        $unit_costs = $_POST['unit-cost'];
        $amounts = $_POST['amount'];

        $invalid_amounts = false;
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
                $invoice_id = $db->insert_id();

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

                $session->msg("s", "Invoice and items added successfully.");
                redirect('invoice.php', false);
            } else {
                $session->msg("d", "Failed to add invoice.");
                redirect('invoice.php', false);
            }
        } else {
            $session->msg("d", $errors);
            redirect('invoice.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('invoice.php', false);
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

                    <!-- Items section -->
                    <div id="items-section">
                        <div class="item-entry">
                            <h4>Product 1</h4>
                            <div class="form-group"><input type="text" class="form-control" name="item-description[]" placeholder="Item Description" required></div>
                            <div class="form-group"><input type="text" class="form-control" name="stock-property-no[]" placeholder="Stock/Property No." required></div>
                            <div class="form-group"><input type="text" class="form-control" name="unit[]" placeholder="Unit" required></div>
                            <div class="form-group"><input type="number" class="form-control" name="quantity[]" placeholder="Quantity" required></div>
                            <div class="form-group"><input type="number" class="form-control" name="unit-cost[]" placeholder="Unit Cost" required></div>
                            <div class="form-group"><input type="number" class="form-control" name="amount[]" placeholder="Amount" required></div>
                            <div class="form-group d-flex justify-content-end">
                                <button type="button" class="btn btn-danger remove-item">Remove</button>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-secondary" id="add-item">Add Another Item</button>
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
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_invoices as $i => $invoice): ?>
                            <?php
                                $due_date = strtotime($invoice['due_date']);
                                $today = strtotime(date('Y-m-d'));
                                $css_class = ($due_date < $today) ? 'danger' : '';
                            ?>
                            <tr class="<?php echo $css_class; ?>">
                                <td class="text-center"><?php echo count_id() + $i; ?></td>
                                <td><?php echo remove_junk($invoice['invoice_number']); ?></td>
                                <td><?php echo remove_junk($invoice['supplier_name']); ?></td>
                                <td><?php echo remove_junk($invoice['po_number']); ?></td>
                                <td><?php echo number_format($invoice['amount'], 2); ?></td>
                                <td><?php echo remove_junk($invoice['due_date']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($all_invoices)): ?>
                            <tr><td colspan="6" class="text-center">No invoices found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function createItemEntry(count) {
        const div = document.createElement('div');
        div.classList.add('item-entry');
        div.innerHTML = `
            <h4>Product ${count}</h4>
            <div class="form-group"><input type="text" class="form-control" name="item-description[]" placeholder="Item Description" required></div>
            <div class="form-group"><input type="text" class="form-control" name="stock-property-no[]" placeholder="Stock/Property No." required></div>
            <div class="form-group"><input type="text" class="form-control" name="unit[]" placeholder="Unit" required></div>
            <div class="form-group"><input type="number" class="form-control" name="quantity[]" placeholder="Quantity" required></div>
            <div class="form-group"><input type="number" class="form-control" name="unit-cost[]" placeholder="Unit Cost" required></div>
            <div class="form-group"><input type="number" class="form-control" name="amount[]" placeholder="Amount" required></div>
            <div class="form-group d-flex justify-content-end">
                <button type="button" class="btn btn-danger remove-item">Remove</button>
            </div>
        `;
        return div;
    }

    function addRemoveHandlers() {
        const removeButtons = document.querySelectorAll('.remove-item');
        removeButtons.forEach(btn => {
            btn.onclick = function () {
                btn.closest('.item-entry').remove();
                updateProductTitles();
            };
        });
    }

    function updateProductTitles() {
        const entries = document.querySelectorAll('#items-section .item-entry');
        entries.forEach((entry, index) => {
            const title = entry.querySelector('h4');
            title.textContent = `Product ${index + 1}`;
        });
    }

    document.getElementById('add-item').addEventListener('click', function () {
        const itemsSection = document.getElementById('items-section');
        const itemCount = itemsSection.querySelectorAll('.item-entry').length + 1;
        const newItem = createItemEntry(itemCount);
        itemsSection.appendChild(newItem);
        addRemoveHandlers();
    });

    document.addEventListener('DOMContentLoaded', function () {
        addRemoveHandlers();
    });
</script>

<?php include_once('layouts/footer.php'); ?>
