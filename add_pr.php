<?php
$page_title = 'Add Purchase Request';
require_once 'includes/load.php';

page_require_level(2);
?>

<?php
if (isset($_POST['add_purchase_request'])) {
    $req_fields = ['entity_name', 'fund_cluster', 'office_section', 'pr_no', 'responsibility_center_code', 'purpose', 'requestor', 'approved_by'];
    validate_fields($req_fields);

    $errors = [];
    
    // Check if quantities exist and are valid
    if (!isset($_POST['quantity']) || !is_array($_POST['quantity'])) {
        $errors[] = "Items must be added with valid quantities.";
    } else {
        foreach ($_POST['quantity'] as $quantity) {
            if (empty($quantity) || !is_numeric($quantity) || $quantity <= 0) {
                $errors[] = "Quantity must be a positive number.";
            }
        }
    }

    if (empty($errors)) {
        $entity_name = remove_junk($db->escape($_POST['entity_name']));
        $fund_cluster = remove_junk($db->escape($_POST['fund_cluster']));
        $office_section = remove_junk($db->escape($_POST['office_section']));
        $pr_no = remove_junk($db->escape($_POST['pr_no']));
        $responsibility_center_code = remove_junk($db->escape($_POST['responsibility_center_code']));
        $purpose = remove_junk($db->escape($_POST['purpose']));
        $requestor = remove_junk($db->escape($_POST['requestor']));
        $approved_by = remove_junk($db->escape($_POST['approved_by']));
        $date_requested = make_date();

        // Start transaction
        $db->query("START TRANSACTION");

        // Insert purchase request (add approved_by)
        $query = "INSERT INTO purchase_requests (entity_name, fund_cluster, office_section, pr_no, responsibility_center_code, purpose, requestor, approved_by, date_requested, status) 
                  VALUES ('{$entity_name}', '{$fund_cluster}', '{$office_section}', '{$pr_no}', '{$responsibility_center_code}', '{$purpose}', '{$requestor}', '{$approved_by}', '{$date_requested}', 'Pending')";
        
        if ($db->query($query)) {
            $purchase_request_id = $db->insert_id();

            // Insert each item only if valid data exists
            if (isset($_POST['item-name']) && is_array($_POST['item-name'])) {
                foreach ($_POST['item-name'] as $key => $item_name) {
                    if (!empty($item_name) && isset($_POST['quantity'][$key]) && isset($_POST['unit'][$key]) && isset($_POST['unit_cost'][$key])) {
                        $item_name = remove_junk($db->escape($item_name));
                        $quantity = remove_junk($db->escape($_POST['quantity'][$key]));
                        $unit = remove_junk($db->escape($_POST['unit'][$key]));
                        $unit_cost = remove_junk($db->escape($_POST['unit_cost'][$key]));
                        $total_cost = $quantity * $unit_cost;

                        $query = "INSERT INTO purchase_request_items (purchase_request_id, item_description, unit, quantity, unit_cost, total_cost) 
                                  VALUES ('{$purchase_request_id}', '{$item_name}', '{$unit}', '{$quantity}', '{$unit_cost}', '{$total_cost}')";
                        
                        if (!$db->query($query)) {
                            $db->query("ROLLBACK");
                            $session->msg('d', 'Failed to add purchase request item!');
                            redirect('purchase_requests.php', false);
                        }
                    }
                }
            }

            // Commit transaction if everything is successful
            $db->query("COMMIT");
            $session->msg('s', "Purchase request added successfully.");
            redirect('add_pr.php', false);
        } else {
            $db->query("ROLLBACK");
            $session->msg('d', 'Failed to add purchase request!');
            redirect('add_pr.php', false);
        }
    } else {
        $session->msg("d", implode("<br>", $errors));
        redirect('add_pr.php', false);
    }
}
?>

<?php include_once 'layouts/header.php'; ?>
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
                    <span class="glyphicon glyphicon-th"></span>
                    <span>Purchase Request</span>
                </strong>
            </div>
            <div class="panel-body">
                <form method="post" action="add_pr.php" class="clearfix">
                    <div class="form-group">
                        <input type="text" class="form-control" name="entity_name" placeholder="Entity Name">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="fund_cluster" placeholder="Fund Cluster">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="office_section" placeholder="Office/Section">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="pr_no" placeholder="PR Number">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="responsibility_center_code" placeholder="Responsibility Center Code">
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" name="purpose" placeholder="Purpose"></textarea>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="requestor" placeholder="Requestor">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="approved_by" placeholder="Approved By">
                    </div>
                    <div id="items">
                        <div class="form-group item" style="border: 1px solid #ddd; padding: 10px;">
                            <div class="input-group" style="margin-top: 10px;">
                                <span class="input-group-addon">
                                    <i class="glyphicon glyphicon-th-large"></i>
                                </span>
                                <input type="text" class="form-control" name="item-name[]" placeholder="Item Name">
                            </div>
                            <div class="input-group" style="margin-top: 10px;">
                                <span class="input-group-addon">
                                    <i class="glyphicon glyphicon-shopping-cart"></i>
                                </span>
                                <input type="number" class="form-control" name="quantity[]" placeholder="Quantity">
                            </div>
                            <div class="input-group" style="margin-top: 10px;">
                                <span class="input-group-addon">
                                    <i class="glyphicon glyphicon-scale"></i>
                                </span>
                                <input type="text" class="form-control" name="unit[]" placeholder="Unit">
                            </div>
                            <div class="input-group" style="margin-top: 10px;">
                                <span class="input-group-addon">
                                    <i class="glyphicon glyphicon-usd"></i>
                                </span>
                                <input type="number" step="0.01" class="form-control" name="unit_cost[]" placeholder="Unit Cost">
                            </div>
                        </div>
                    </div>
                    <button type="button" id="add-item" class="btn btn-success">Add Another Item</button>
                    <button type="submit" name="add_purchase_request" class="btn btn-danger">Add Purchase Request</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('add-item').addEventListener('click', function() {
    var itemDiv = document.createElement('div');
    itemDiv.className = 'form-group item';
    itemDiv.style.border = "1px solid #ddd";
    itemDiv.style.padding = "10px";
    itemDiv.innerHTML = `
        <div class="input-group" style="margin-top: 10px;">
            <span class="input-group-addon">
                <i class="glyphicon glyphicon-th-large"></i>
            </span>
            <input type="text" class="form-control" name="item-name[]" placeholder="Item Name">
        </div>
        <div class="input-group" style="margin-top: 10px;">
            <span class="input-group-addon">
                <i class="glyphicon glyphicon-shopping-cart"></i>
            </span>
            <input type="number" class="form-control" name="quantity[]" placeholder="Quantity">
        </div>
        <div class="input-group" style="margin-top: 10px;">
            <span class="input-group-addon">
                <i class="glyphicon glyphicon-scale"></i>
            </span>
            <input type="text" class="form-control" name="unit[]" placeholder="Unit">
        </div>
        <div class="input-group" style="margin-top: 10px;">
            <span class="input-group-addon">
                <i class="glyphicon glyphicon-usd"></i>
            </span>
            <input type="number" step="0.01" class="form-control" name="unit_cost[]" placeholder="Unit Cost">
        </div>
        <button type="button" class="btn btn-danger remove-item" style="margin-top: 10px;">Remove</button>
    `;

    document.getElementById('items').appendChild(itemDiv);

    // Add event listener to remove button
    itemDiv.querySelector('.remove-item').addEventListener('click', function() {
        itemDiv.remove();
    });
});
</script>

<?php include_once 'layouts/footer.php'; ?>