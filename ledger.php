<?php
$page_title = 'Accounts Payable Ledger';
require_once('includes/load.php');
page_require_level(1);

// Get the Accounts Payable ID from the URL
if (!isset($_GET['ap_id']) || empty($_GET['ap_id'])) {
    die("Error: Missing or invalid 'ap_id' parameter in the URL.");
}
$ap_id = (int)$_GET['ap_id'];

// Fetch Accounts Payable details
$ap = find_by_id('accounts_payable', $ap_id, 'ap_id');
if (!$ap) {
    $session->msg("d", "Accounts Payable entry not found.");
    redirect('accountspayable.php');
}

// Fetch ledger entries for the specified Accounts Payable ID
$ledger_entries = find_by_sql("SELECT * FROM ledger WHERE ap_id = '{$ap_id}' ORDER BY transaction_date DESC");

// Debugging: Log the query results
error_log("Ledger entries for AP ID {$ap_id}: " . json_encode($ledger_entries));
?>

<?php include_once('layouts/header.php'); ?>

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
                    <span>Ledger for Accounts Payable #<?php echo $ap_id; ?></span>
                </strong>
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th>Transaction Type</th>
                            <th>Amount</th>
                            <th>Balance</th>
                            <th>Transaction Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($ledger_entries)): ?>
                            <?php foreach ($ledger_entries as $entry): ?>
                                <tr>
                                    <td class="text-center"><?php echo (int)$entry['ledger_id']; ?></td>
                                    <td><?php echo remove_junk(ucfirst($entry['transaction_type'])); ?></td>
                                    <td><?php echo remove_junk($entry['amount']); ?></td>
                                    <td><?php echo remove_junk($entry['balance']); ?></td>
                                    <td><?php echo remove_junk($entry['transaction_date']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No transactions found for this Accounts Payable entry.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <a href="accountspayable.php" class="btn btn-default">Back to Accounts Payable</a>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>