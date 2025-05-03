<?php
$page_title = 'Accounts Payable';
require_once('includes/load.php');
// Check user permission level
page_require_level(1);

// Fetch all suppliers, invoices, and purchase orders
$all_suppliers = find_all('suppliers');
$all_invoices = find_all('invoices');
$all_pos = find_all('purchase_orders');

// Handle Add Accounts Payable
if (isset($_POST['add_ap'])) {
    $supplier_id   = (int)$_POST['supplier-id'];
    $po_id         = (int)$_POST['po-id'];
    $invoice_id    = (int)$_POST['invoice-id'];
    $amount        = remove_junk($db->escape($_POST['amount']));
    $due_date      = remove_junk($db->escape($_POST['due-date']));

    $invoice = find_by_id('invoices', $invoice_id, 'invoice_id');
    
    if (!$invoice) {
        $session->msg("d", "Invoice not found.");
        redirect('accountspayable.php', false);
    }

    if ($supplier_id && $po_id && $invoice_id && $amount && $due_date) {
        $query = "INSERT INTO accounts_payable (supplier_id, po_id, invoice_id, amount, balance, due_date, status) 
                  VALUES ('{$supplier_id}', '{$po_id}', '{$invoice_id}', '{$amount}', '{$amount}', '{$due_date}', 'Pending')";
        
        if ($db->query($query)) {
            $new_ap_id = $db->insert_id;

            // Log the initial entry in the ledger
            $log_sql = "INSERT INTO ledger (ap_id, transaction_type, amount, balance) 
                        VALUES ('{$new_ap_id}', 'Initial Entry', '{$amount}', '{$amount}')";
            $db->query($log_sql);

            $session->msg("s", "Accounts Payable added successfully!");
            redirect('accountspayable.php', false);
        } else {
            $session->msg("d", "Failed to add Accounts Payable.");
            redirect('accountspayable.php', false);
        }
    } else {
        $session->msg("d", "All fields are required.");
        redirect('accountspayable.php', false);
    }
}

// Handle Delete
if (isset($_GET['delete_ap'])) {
    $ap_id = (int)$_GET['delete_ap'];
    $ap = find_by_id('accounts_payable', $ap_id, 'ap_id');

    if ($ap) {
        $delete_sql = "DELETE FROM accounts_payable WHERE ap_id = '{$ap_id}'";
        if ($db->query($delete_sql)) {
            $session->msg("s", "Accounts Payable entry deleted successfully!");
        } else {
            $session->msg("d", "Failed to delete Accounts Payable entry.");
        }
    } else {
        $session->msg("d", "Accounts Payable entry not found.");
    }
    redirect('accountspayable.php', false);
}

// Handle Payments
if (isset($_POST['make_payment'])) {
    $ap_id = (int)$_POST['ap_id'];
    $payment_amount = (float)$_POST['payment-amount'];

    $ap = find_by_id('accounts_payable', $ap_id, 'ap_id');
    if ($ap) {
        $new_balance = $ap['balance'] - $payment_amount;
        if ($new_balance < 0) {
            $session->msg("d", "Payment exceeds the remaining balance.");
            redirect('accountspayable.php', false);
        }

        $sql = "UPDATE accounts_payable SET balance = '{$new_balance}' WHERE ap_id = '{$ap_id}'";
        if ($db->query($sql)) {
            // Log the payment in the ledger
            $log_sql = "INSERT INTO ledger (ap_id, transaction_type, amount, balance) 
                        VALUES ('{$ap_id}', 'Payment', '{$payment_amount}', '{$new_balance}')";
            $db->query($log_sql);

            $session->msg("s", "Payment applied successfully!");
        } else {
            $session->msg("d", "Failed to apply payment.");
        }
    } else {
        $session->msg("d", "Accounts Payable entry not found.");
    }
    redirect('accountspayable.php', false);
}

// Handle Status Check
if (isset($_GET['check_status'])) {
    $ap_id = (int)$_GET['check_status'];
    $ap = find_by_id('accounts_payable', $ap_id, 'ap_id');

    if ($ap) {
        // Determine the new status based on the current state
        $new_status = ($ap['balance'] == 0) ? 'Paid' : 'Pending';

        // Update the status if it's different
        if ($new_status !== $ap['status']) {
            $update_status_sql = "UPDATE accounts_payable SET status = '{$new_status}' WHERE ap_id = {$ap_id}";
            if ($db->query($update_status_sql)) {
                $session->msg("s", "Accounts Payable status updated to {$new_status}!");
            } else {
                $session->msg("d", "Failed to update status.");
            }
        } else {
            $session->msg("s", "Accounts Payable status is already {$new_status}.");
        }
    } else {
        $session->msg("d", "Accounts Payable entry not found.");
    }
    redirect('accountspayable.php', false);
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
                            <?php foreach ($all_pos as $po): ?>
                                <option value="<?php echo (int)$po['po_id']; ?>">
                                    <?php echo remove_junk($po['po_number']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="invoice-id">Invoice Number</label>
                        <select class="form-control" name="invoice-id" required>
                            <option value="">Select Invoice</option>
                            <?php foreach ($all_invoices as $invoice): ?>
                                <option value="<?php echo (int)$invoice['invoice_id']; ?>">
                                    <?php echo remove_junk($invoice['invoice_number']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
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
                            <th>Balance</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th class="text-center" style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
    <?php
    $sql = "SELECT ap.ap_id, ap.amount, ap.balance, ap.due_date, ap.status, s.name AS supplier_name, i.invoice_number
            FROM accounts_payable AS ap
            INNER JOIN suppliers AS s ON ap.supplier_id = s.supplier_id
            INNER JOIN invoices AS i ON ap.invoice_id = i.invoice_id
            ORDER BY ap.ap_id DESC";
    
    $result = $db->query($sql);

    while ($ap = $result->fetch_assoc()):
    ?>
        <tr>
            <td class="text-center"><?php echo count_id(); ?></td>
            <td><?php echo remove_junk(ucfirst($ap['supplier_name'])); ?></td>
            <td><?php echo remove_junk($ap['invoice_number']); ?></td>
            <td><?php echo remove_junk($ap['amount']); ?></td>
            <td><?php echo remove_junk($ap['balance']); ?></td>
            <td><?php echo remove_junk($ap['due_date']); ?></td>
            <td><?php echo remove_junk($ap['status']); ?></td>
            <td class="text-center">
                <div class="btn-group">
                    <a href="edit_ap.php?ap_id=<?php echo (int)$ap['ap_id']; ?>" class="btn btn-warning btn-sm" data-toggle="tooltip" >
                        <span class="glyphicon glyphicon-edit"></span>
                    </a>
                    <a href="?delete_ap=<?php echo (int)$ap['ap_id']; ?>" class="btn btn-danger btn-sm" data-toggle="tooltip" >
                        <span class="glyphicon glyphicon-trash"></span>
                    </a>
                    <a href="ledger.php?ap_id=<?php echo (int)$ap['ap_id']; ?>" class="btn btn-info btn-sm" data-toggle="tooltip" >
                        <span class="glyphicon glyphicon-book"></span>
                    </a>
                    <a href="?check_status=<?php echo (int)$ap['ap_id']; ?>" class="btn btn-success btn-sm" data-toggle="tooltip">
                        <span class="glyphicon glyphicon-check"></span>
                    </a>
                </div>
            </td>
        </tr>
    <?php endwhile; ?>
</tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>