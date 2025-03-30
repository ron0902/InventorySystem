<?php
$page_title = 'Stock Report';
require_once('includes/load.php');
// Check user permission level
page_require_level(1);

// Fetch all stock reports
$stock_reports = find_all('stock_report'); // Fetch all rows from the stock_report table

// Fetch purchase orders
$purchase_orders = find_all('purchase_orders'); // Replace 'purchase_orders' with your actual table name

// Fetch purchase order items
$purchase_order_items = [];
if (isset($_POST['po_id']) && !empty($_POST['po_id'])) {
    $po_id = $_POST['po_id'];
    $purchase_order_items = find_by_sql("SELECT * FROM purchase_order_items WHERE po_id = '{$po_id}'");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_report'])) {
    $po_id = $_POST['po_id'];
    $order_ids = $_POST['order_id']; // This will be an array of selected order IDs
    $balance_per_card = $_POST['balance_per_card'];
    $on_hand_per_count = $_POST['on_hand_per_count'];

    // Insert each selected order item into the database
    foreach ($order_ids as $order_id) {
        $query = "INSERT INTO stock_report (po_id, order_id, balance_per_card, on_hand_per_count) 
                  VALUES ('{$po_id}', '{$order_id}', '{$balance_per_card}', '{$on_hand_per_count}')";
        if (!$db->query($query)) {
            $session->msg('d', "Failed to add stock report for order ID {$order_id}.");
        }
    }
    $session->msg('s', "Stock report added successfully.");
    redirect('stock_report.php', false);
}
?>
<?php include_once('layouts/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
</div>

<!-- Form for Submitting Stock Reports -->
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-plus"></span>
                    <span>Add New Stock Report</span>
                </strong>
            </div>
            <div class="panel-body">
                <form method="POST" action="stock_report.php">
                    <div class="form-group">
                        <label for="po_id">Purchase Order</label>
                        <select class="form-control" name="po_id" id="po_id" onchange="this.form.submit()" required>
                            <option value="">Select Purchase Order</option>
                            <?php foreach ($purchase_orders as $po): ?>
                                <option value="<?php echo $po['po_id']; ?>" <?php echo (isset($po_id) && $po_id == $po['po_id']) ? 'selected' : ''; ?>>
                                    <?php echo $po['po_id']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
                <?php if (!empty($purchase_order_items)): ?>
                    <form method="POST" action="stock_report.php">
                        <input type="hidden" name="po_id" value="<?php echo $po_id; ?>">
                        <div class="form-group">
                            <label for="order_id">Purchase Order Items</label>
                            <select class="form-control" name="order_id[]" id="order_id" multiple required>
                                <?php foreach ($purchase_order_items as $item): ?>
                                    <option value="<?php echo $item['order_id']; ?>">
                                        <?php echo $item['description']; ?> (Qty: <?php echo $item['quantity']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Hold down the Ctrl (Windows) or Command (Mac) key to select multiple items.</small>
                        </div>
                        <div class="form-group">
                            <label for="balance_per_card">Balance Per Card</label>
                            <input type="number" class="form-control" name="balance_per_card" placeholder="Enter Balance Per Card" required>
                        </div>
                        <div class="form-group">
                            <label for="on_hand_per_count">On Hand Per Count</label>
                            <input type="number" class="form-control" name="on_hand_per_count" placeholder="Enter On Hand Per Count" required>
                        </div>
                        <button type="submit" name="submit_report" class="btn btn-primary">Submit</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Stock Report Table -->
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>Stock Report</span>
                </strong>
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th>Purchase Order ID</th>
                            <th>Order ID</th>
                            <th>Balance Per Card</th>
                            <th>On Hand Per Count</th>
                            <th>Discrepancy</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($stock_reports)): ?>
                            <?php foreach ($stock_reports as $report): ?>
                                <tr>
                                    <td class="text-center"><?php echo $report['report_id']; ?></td>
                                    <td><?php echo $report['po_id']; ?></td>
                                    <td><?php echo $report['order_id']; ?></td>
                                    <td><?php echo $report['balance_per_card']; ?></td>
                                    <td><?php echo $report['on_hand_per_count']; ?></td>
                                    <td><?php echo $report['discrepancy']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No stock report data available.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>