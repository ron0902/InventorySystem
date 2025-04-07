    <?php
    $page_title = 'Accounts Payable';
    require_once('includes/load.php');
    // Check user permission level
    page_require_level(1);

    // Fetch all suppliers
    $all_suppliers = find_all('suppliers');

    // Add new accounts payable entry
    if (isset($_POST['add_ap'])) {
        $supplier_id = (int)$_POST['supplier-id'];
        $invoice_number = remove_junk($db->escape($_POST['invoice-number']));
        $amount = (float)$_POST['amount'];
        $due_date = remove_junk($db->escape($_POST['due-date']));
        $po_id = (int)$_POST['po-id']; // Fetch the selected Purchase Order ID
    
        // Validate the data
        if (empty($supplier_id) || empty($invoice_number) || empty($amount) || empty($due_date) || empty($po_id)) {
            $session->msg("d", "All fields are required!");
            redirect('accountspayable.php', false);
        }
    
        // Insert into the database
        $sql = "INSERT INTO accounts_payable (supplier_id, invoice_number, amount, due_date, order_id) 
        VALUES ('{$supplier_id}', '{$invoice_number}', '{$amount}', '{$due_date}', '{$po_id}')";
        if ($db->query($sql)) {
            $session->msg("s", "New Accounts Payable added successfully!");
            redirect('accountspayable.php', false);
        } else {
            $session->msg("d", "Failed to add Accounts Payable!");
            redirect('accountspayable.php', false);
        }
    }
    // Delete an accounts payable entry
    if (isset($_GET['delete_ap'])) {
        $ap_id = (int)$_GET['delete_ap'];

        // Check if the entry exists
        $ap_sql = "SELECT * FROM accounts_payable WHERE id = '{$ap_id}' LIMIT 1";
        $ap_result = $db->query($ap_sql);

        if ($ap_result && $ap_result->num_rows > 0) {
            // Proceed with deletion
            $delete_sql = "DELETE FROM accounts_payable WHERE id = '{$ap_id}'";
            if ($db->query($delete_sql)) {
                $session->msg("s", "Accounts Payable entry deleted successfully!");
                redirect('accountspayable.php', false);
            } else {
                $session->msg("d", "Failed to delete Accounts Payable entry!");
                redirect('accountspayable.php', false);
            }
        } else {
            $session->msg("d", "Accounts Payable entry not found!");
            redirect('accountspayable.php', false);
        }
    }

    // Handle check order status for accounts payable
if (isset($_GET['check_status'])) {
    $ap_id = (int)$_GET['check_status'];
    // Get the details of the accounts payable entry
    $ap_sql = "SELECT * FROM accounts_payable WHERE id = '{$ap_id}'";
    $ap_result = $db->query($ap_sql);

    if ($ap_result && $ap_result->num_rows > 0) {
        $ap_details = $ap_result->fetch_assoc();
        $po_id = $ap_details['po_id'];  // Assuming you have a PO ID in your accounts payable table
        $invoice_id = $ap_details['invoice_id'];  // Assuming you have an invoice ID in your accounts payable table

        // Call the function to check if the order is complete
        $order_status = check_order_completeness($po_id, $invoice_id);

        // Store the status message in session and redirect to show the message
        $session->msg("s", $order_status);
        redirect('accountspayable.php', false);
    } else {
        $session->msg("d", "Accounts payable entry not found.");
        redirect('accountspayable.php', false);
    }
}

    ?>

    <?php include_once('layouts/header.php'); ?>

    <div class="row">
        <div class="col-md-12">
            <?php echo display_msg($msg); ?>
        </div>
    </div>

    <!-- Add Accounts Payable Form -->
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong>
                        <span class="glyphicon glyphicon-th"></span>
                        <span>Add New Accounts Payable</span>
                    </strong>
                </div>
                <div class="panel-body">
                    <form method="post" action="accountspayable.php">
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
        <?php 
        // Fetch all Purchase Orders
        $all_pos = find_all('purchase_orders');
        foreach ($all_pos as $po): ?>
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
                            <input type="number" class="form-control" name="amount" placeholder="Amount" required>
                        </div>
                        <div class="form-group">
                            <input type="date" class="form-control" name="due-date" placeholder="Due Date" required>
                        </div>
                        
                        <button type="submit" name="add_ap" class="btn btn-primary">Add Accounts Payable</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- List of Accounts Payable -->
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong>
                        <span class="glyphicon glyphicon-th"></span>
                        <span>List of Accounts Payable</span>
                    </strong>
                </div>
                <div class="panel-body">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;">#</th>
                                <th>Supplier</th>
                                <th>Invoice Number</th>
                                <th>Amount</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th class="text-center" style="width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            $all_ap = find_all('accounts_payable');
                            foreach ($all_ap as $ap):
                                // Fetch supplier name for each entry
                                $supplier = find_by_id('suppliers', (int)$ap['supplier_id']);
                                $supplier_name = $supplier ? $supplier['name'] : 'Unknown';
                            ?>
                                <tr>
                                    <td class="text-center"><?php echo count_id(); ?></td>
                                    <td><?php echo remove_junk(ucfirst($supplier_name)); ?></td>
                                    <td><?php echo remove_junk($ap['invoice_number']); ?></td>
                                    <td><?php echo remove_junk($ap['amount']); ?></td>
                                    <td><?php echo remove_junk($ap['due_date']); ?></td>
                                    <td><?php echo remove_junk($ap['status']); ?></td>
                                    <td class="text-center">
        <div class="btn-group">
            <a href="edit_ap.php?ap_id=<?php echo (int)$ap['ap_id']; ?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">
                <span class="glyphicon glyphicon-edit"></span>
            </a>
            <a href="?delete_ap=<?php echo (int)$ap['ap_id']; ?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete">
                <span class="glyphicon glyphicon-trash"></span>
            </a>
            <a href="?check_status=<?php echo (int)$ap['ap_id']; ?>" class="btn btn-xs btn-info" data-toggle="tooltip" title="Check Order Status">
                <span class="glyphicon glyphicon-check"></span>
            </a>
        </div>
    </td>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php include_once('layouts/footer.php'); ?>
