<?php
$page_title = 'Add Inspection and Acceptance Report';
require_once 'includes/load.php';

// Insert Logic
if (isset($_POST['add_inspection'])) {
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
        $complete = isset($_POST['complete']) ? 1 : 0;
        $quantity = isset($_POST['quantity']) ? 1 : 0;

        $db->query("START TRANSACTION");

        $query = "INSERT INTO inspection (supplier, iar_no, po_no, date, invoice_no, purpose, date_inspected, date_received, complete, quantity)
                  VALUES ('{$supplier}', '{$iar_no}', '{$po_no}', '{$date}', '{$invoice_no}', '{$purpose}', '{$date_inspected}', '{$date_received}', '{$complete}', '{$quantity}')";

        if ($db->query($query)) {
            $inspection_id = $db->insert_id();

            if (isset($_POST['stock_no']) && is_array($_POST['stock_no'])) {
                foreach ($_POST['stock_no'] as $key => $stock_no) {
                    $unit = remove_junk($db->escape($_POST['unit'][$key]));
                    $description = remove_junk($db->escape($_POST['description'][$key]));
                    $qty = remove_junk($db->escape($_POST['quantity'][$key]));

                    $query = "INSERT INTO inspection_items (inspection_id, stock_no, unit, description, quantity)
                              VALUES ('{$inspection_id}', '{$stock_no}', '{$unit}', '{$description}', '{$qty}')";
                    if (!$db->query($query)) {
                        $db->query("ROLLBACK");
                        $session->msg('d', 'Failed to add inspection item!');
                        redirect('add_inspection.php', false);
                    }
                }
            }

            $db->query("COMMIT");
            $session->msg('s', "Inspection report added successfully.");
            redirect('add_inspection.php', false);
        } else {
            $db->query("ROLLBACK");
            $session->msg('d', 'Failed to add inspection report!');
            redirect('add_inspection.php', false);
        }
    } else {
        $session->msg("d", implode("<br>", $errors));
        redirect('add_inspection.php', false);
    }
}
?>

<?php include_once 'layouts/header.php'; ?>
<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
</div>

<!-- INSPECTION FORM -->
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-list-alt"></span>
                    <span>Add Inspection and Acceptance Report</span>
                </strong>
            </div>
            <div class="panel-body">
                <form method="post" action="add_inspection.php" class="clearfix">
                    <div class="form-group">
                    <select class="form-control" name="supplier" required>
                        <option value="">Select Supplier</option>
                        <?php
                        $suppliers = find_all('suppliers'); // Adjust table name if needed
                        foreach ($suppliers as $supplier):
                        ?>
                            <option value="<?php echo remove_junk($supplier['name']); ?>">
                                <?php echo remove_junk($supplier['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                     </div>      
                     <div class="form-group">
                        <input type="text" class="form-control" name="iar_no" placeholder="IAR No." required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="po_no" placeholder="PO No." required>
                    </div>
                    <div class="form-group">
                        <input type="date" class="form-control" name="date" placeholder="Date" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="invoice_no" placeholder="Invoice No." required>
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" name="purpose" placeholder="Purpose" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Date Inspected</label>
                        <input type="date" class="form-control" name="date_inspected" required>
                    </div>
                    <div class="form-group">
                        <label>Date Received</label>
                        <input type="date" class="form-control" name="date_received" required>
                    </div>
                    <div class="form-group">
                    <label><input type="radio" name="status" value="complete" required> Complete</label>
                    <label style="margin-left:20px;"><input type="radio" name="status" value="quantity" required> Quantity</label>
                    </div>
                    <div id="inspection-items">
                        <div class="form-group inspection-item" style="border: 1px solid #ddd; padding: 10px;">
                            <input type="text" class="form-control" name="stock_no[]" placeholder="Stock No." required style="margin-bottom:6px;">
                            <input type="text" class="form-control" name="unit[]" placeholder="Unit" required style="margin-bottom:6px;">
                            <input type="text" class="form-control" name="description[]" placeholder="Description" required style="margin-bottom:6px;">
                            <input type="number" class="form-control" name="quantity[]" placeholder="Quantity" required style="margin-bottom:6px;">
                        </div>
                    </div>
                    <button type="button" id="add-inspection-item" class="btn btn-success">Add Another Item</button>
                    <button type="submit" name="add_inspection" class="btn btn-primary">Add Inspection</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MANAGE INSPECTION SECTION -->
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-cog"></span>
                    <span>Manage Inspection</span>
                </strong>
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>IAR No</th>
                            <th>Supplier</th>
                            <th>PO No</th>
                            <th>Date</th>
                            <th>Invoice No</th>
                            <th>Status</th>
                            <th style="width:120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $inspections = find_all('inspection');
                        foreach ($inspections as $insp):
                        ?>
                        <tr>
                            <td><?php echo remove_junk($insp['iar_no']); ?></td>
                            <td><?php echo remove_junk($insp['supplier']); ?></td>
                            <td><?php echo remove_junk($insp['po_no']); ?></td>
                            <td><?php echo remove_junk($insp['date']); ?></td>
                            <td><?php echo remove_junk($insp['invoice_no']); ?></td>
                            <td>
                                <?php
                                $status = [];
                                if ($insp['complete']) $status[] = 'Complete';
                                if ($insp['quantity']) $status[] = 'Quantity';
                                echo implode(', ', $status);
                                ?>
                            </td>
                            <td>
                                <a href="edit_inspection.php?id=<?php echo (int)$insp['id']; ?>"
                                   class="btn btn-warning btn-xs"
                                   style="color:white; margin-right:2px;"
                                   title="Edit">
                                    <span class="glyphicon glyphicon-pencil"></span>
                                </a>
                                <a href="delete_inspection.php?id=<?php echo (int)$insp['id']; ?>"
                                   class="btn btn-danger btn-xs"
                                   style="color:white; margin-right:2px;"
                                   title="Delete"
                                   onclick="return confirm('Are you sure you want to delete this inspection?');">
                                    <span class="glyphicon glyphicon-remove"></span>
                                </a>
                                <a href="print-layout/INSPECTION.php?id=<?php echo (int)$insp['id']; ?>"
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
// Add Inspection item dynamically
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

    // Remove item
    itemDiv.querySelector('.remove-inspection-item').addEventListener('click', function() {
        itemDiv.remove();
    });
});
</script>

<?php include_once 'layouts/footer.php'; ?>