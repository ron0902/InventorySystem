<?php
$page_title = 'Edit Accounts Payable';
require_once('includes/load.php');
// Check user permission level
page_require_level(1);

// Get AP ID
$ap_id = (int)$_GET['ap_id'];
$ap = find_by_id('accounts_payable', $ap_id);
if (!$ap) {
    $session->msg("d", "Missing Accounts Payable ID.");
    redirect('accountspayable.php');
}

// Fetch suppliers and purchase orders
$all_suppliers = find_all('suppliers');
$all_pos = find_all('purchase_orders');

// Handle form update
if (isset($_POST['update_ap'])) {
    $supplier_id = (int)$_POST['supplier-id'];
    $invoice_number = remove_junk($db->escape($_POST['invoice-number']));
    $amount = (float)$_POST['amount'];
    $due_date = remove_junk($db->escape($_POST['due-date']));
    $po_id = (int)$_POST['po-id'];
    
    if (empty($supplier_id) || empty($invoice_number) || empty($amount) || empty($due_date) || empty($po_id)) {
        $session->msg("d", "All fields are required.");
        redirect("edit_ap.php?ap_id={$ap_id}", false);
    }

    $sql = "UPDATE accounts_payable SET 
                supplier_id = '{$supplier_id}', 
                invoice_number = '{$invoice_number}', 
                amount = '{$amount}', 
                due_date = '{$due_date}', 
                order_id = '{$po_id}' 
            WHERE ap_id = '{$ap_id}'";

    if ($db->query($sql)) {
        $session->msg("s", "Accounts Payable updated.");
        redirect('accountspayable.php', false);
    } else {
        $session->msg("d", "Update failed.");
        redirect("edit_ap.php?ap_id={$ap_id}", false);
    }
}
?>

<?php include_once('layouts/header.php'); ?>

<div class="row">
    <div class="col-md-6">
        <?php echo display_msg($msg); ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong><span class="glyphicon glyphicon-pencil"></span> Edit Accounts Payable</strong>
            </div>
            <div class="panel-body">
                <form method="post" action="edit_ap.php?ap_id=<?php echo (int)$ap['ap_id']; ?>">
                    <div class="form-group">
                        <label for="supplier-id">Supplier</label>
                        <select class="form-control" name="supplier-id">
                            <?php foreach ($all_suppliers as $supplier): ?>
                                <option value="<?php echo (int)$supplier['supplier_id']; ?>" <?php if ($ap['supplier_id'] == $supplier['supplier_id']) echo 'selected'; ?>>
                                    <?php echo remove_junk(ucfirst($supplier['name'])); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="po-id">Purchase Order</label>
                        <select class="form-control" name="po-id">
                            <?php foreach ($all_pos as $po): ?>
                                <option value="<?php echo (int)$po['po_id']; ?>" <?php if ($ap['order_id'] == $po['po_id']) echo 'selected'; ?>>
                                    <?php echo remove_junk($po['po_number']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Invoice Number</label>
                        <input type="text" class="form-control" name="invoice-number" value="<?php echo remove_junk($ap['invoice_number']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Amount</label>
                        <input type="number" class="form-control" name="amount" value="<?php echo remove_junk($ap['amount']); ?>" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Due Date</label>
                        <input type="date" class="form-control" name="due-date" value="<?php echo remove_junk($ap['due_date']); ?>" required>
                    </div>
                    <button type="submit" name="update_ap" class="btn btn-success">Update</button>
                    <a href="accountspayable.php" class="btn btn-default">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>
