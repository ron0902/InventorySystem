<?php
$page_title = 'Add RFQ';
require_once 'includes/load.php';


// Handle status change
if (isset($_GET['action']) && $_GET['action'] === 'receive' && isset($_GET['id'])) {
    $rfq_id = (int)$_GET['id'];
    $sql = "UPDATE rfq SET status='Received' WHERE id='{$rfq_id}'";
    if ($db->query($sql)) {
        $session->msg('s', 'RFQ marked as Received.');
    } else {
        $session->msg('d', 'Failed to update RFQ status.');
    }
    redirect('add_rfq.php');
}

// RFQ Insert Logic
if (isset($_POST['add_rfq'])) {
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

        $query = "INSERT INTO rfq (rfq_no, rfq_date, company_name, company_address, status)
                  VALUES ('{$rfq_no}', '{$rfq_date}', '{$company_name}', '{$company_address}', 'Pending')";

        if ($db->query($query)) {
            $rfq_id = $db->insert_id();

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
                            $session->msg('d', 'Failed to add RFQ item!');
                            redirect('add_rfq.php', false);
                        }
                    }
                }
            }

            $db->query("COMMIT");
            $session->msg('s', "RFQ added successfully.");
            redirect('add_rfq.php', false);
        } else {
            $db->query("ROLLBACK");
            $session->msg('d', 'Failed to add RFQ!');
            redirect('add_rfq.php', false);
        }
    } else {
        $session->msg("d", implode("<br>", $rfq_errors));
        redirect('add_rfq.php', false);
    }
}
?>

<?php include_once 'layouts/header.php'; ?>
<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
</div>

<!-- RFQ FORM -->
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-list-alt"></span>
                    <span>Add RFQ</span>
                </strong>
            </div>
            <div class="panel-body">
                <form method="post" action="add_rfq.php" class="clearfix">
                    <div class="form-group">
                        <input type="text" class="form-control" name="rfq_no" placeholder="RFQ Number" required>
                    </div>
                    <div class="form-group">
                        <input type="date" class="form-control" name="rfq_date" placeholder="RFQ Date" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="company_name" placeholder="Company Name" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="company_address" placeholder="Company Address" required>
                    </div>
                    <div id="rfq-items">
                        <div class="form-group rfq-item" style="border: 1px solid #ddd; padding: 10px;">
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
                        </div>
                    </div>
                    <button type="button" id="add-rfq-item" class="btn btn-success">Add Another Item</button>
                    <button type="submit" name="add_rfq" class="btn btn-primary">Add RFQ</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MANAGE RFQ SECTION -->
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-cog"></span>
                    <span>Manage RFQ</span>
                </strong>
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>RFQ No</th>
                            <th>Date</th>
                            <th>Company Name</th>
                            <th>Address</th>
                            <th>Status</th>
                            <th style="width:150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch all RFQs
                        $rfqs = find_all('rfq');
                        foreach ($rfqs as $rfq):
                        ?>
                        <tr>
                            <td><?php echo remove_junk($rfq['rfq_no']); ?></td>
                            <td><?php echo remove_junk($rfq['rfq_date']); ?></td>
                            <td><?php echo remove_junk($rfq['company_name']); ?></td>
                            <td><?php echo remove_junk($rfq['company_address']); ?></td>
                            <td><?php echo remove_junk($rfq['status']); ?></td>
                            <td>
                            <a href="edit_rfq.php?id=<?php echo (int)$rfq['id']; ?>" 
                            class="btn btn-warning btn-xs" 
                            style="color:white; margin-right:2px;" 
                            title="Edit">
                                <span class="glyphicon glyphicon-pencil"></span>
                            </a>
                            <a href="delete_rfq.php?id=<?php echo (int)$rfq['id']; ?>" 
                            class="btn btn-danger btn-xs" 
                            style="color:white; margin-right:2px;" 
                            title="Delete"
                            onclick="return confirm('Are you sure you want to delete this RFQ?');">
                                <span class="glyphicon glyphicon-remove"></span>
                            </a>
                            <a href="print-layout/RFQ.php?id=<?php echo (int)$rfq['id']; ?>" 
                            class="btn btn-info btn-xs" 
                            style="color:white;" 
                            title="Print" 
                            target="_blank">
                                <span class="glyphicon glyphicon-print"></span>
                            </a>
                            <a href="add_rfq.php?action=receive&id=<?php echo (int)$rfq['id']; ?>"
                            class="btn btn-success btn-xs"
                            style="color:white; margin-right:2px;"
                            title="Mark as Received"
                            onclick="return confirm('Mark this RFQ as Received?');">
                                <span class="glyphicon glyphicon-ok"></span>
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
// Add RFQ item dynamically
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

    // Remove item
    itemDiv.querySelector('.remove-rfq-item').addEventListener('click', function() {
        itemDiv.remove();
    });
});
</script>

<?php include_once 'layouts/footer.php'; ?>