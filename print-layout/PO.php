<?php
require_once('../includes/load.php');

if (isset($_GET['id'])) {
    $order_id = (int)$_GET['id'];
    $po_data = find_by_id('purchase_orders', $order_id, 'po_id'); // Use 'po_id' as the primary key

    if (!$po_data) {
        $session->msg('d', 'Purchase order not found.');
        redirect('../manage_po.php', false);
    }

    // Fetch items associated with the purchase order
    $items = find_items_by_po_id($order_id);
} else {
    $session->msg('d', 'Missing purchase order ID.');
    redirect('../manage_po.php', false);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: Arial, sans-serif;
            margin: 0;
        }
        .container {
            border: 1px solid black;
            padding: 20px;
            width: 210mm;
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .department {
            text-align: center;
            font-size: 16px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        .signature {
            margin-top: 20px;
        }
        .funding-details {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">PURCHASE ORDER</div>
        <div class="department">DEPARTMENT OF EDUCATION</div>

        <!-- Combined Table -->
        <table>
            <!-- Supplier and PO Details -->
            <tr>
                <td colspan="3">Supplier Name: <?php echo remove_junk($po_data['supplier_name']); ?></td>
                <td colspan="3">PO No.: <?php echo remove_junk($po_data['po_number']); ?></td>
            </tr>
            <tr>
                <td colspan="3">Address: <?php echo remove_junk($po_data['address']); ?></td>
                <td colspan="3">Date: <?php echo read_date($po_data['date']); ?></td>
            </tr>
            <tr>
                <td colspan="3">TIN: <?php echo remove_junk($po_data['tin']); ?></td>
                <td colspan="3">Mode Of Procurement: <?php echo remove_junk($po_data['mode_of_procurement']); ?></td>
            </tr>

            <!-- Items Table Header -->
            <tr>
                <th>Stock/Property No.</th>
                <th>Unit</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Unit Cost</th>
                <th>Amount</th>
            </tr>

           <!-- Items List -->
<?php if (!empty($items) && count($items) > 0): ?>
    <?php foreach ($items as $item): ?>
        <tr>
            <td><?php echo remove_junk($item['stock_property_no']); ?></td>
            <td><?php echo remove_junk($item['unit']); ?></td>
            <td><?php echo remove_junk($item['description']); ?></td>
            <td><?php echo remove_junk($item['quantity']); ?></td>
            <td><?php echo remove_junk($item['unit_cost']); ?></td>
            <td><?php echo remove_junk($item['amount']); ?></td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="6">No items found for this purchase order.</td>
    </tr>
<?php endif; ?>

            <!-- Grand Total and Purpose -->
            <tr>
                <td colspan="5">Grand Total:</td>
                <td><?php echo remove_junk($po_data['total_amount']); ?></td>
            </tr>
            <tr>
                <td colspan="5">Total Amount in Words:</td>
                <td><?php echo remove_junk($po_data['total_amount_words']); ?></td>
            </tr>
            <tr>
                <td colspan="5">Purpose:</td>
                <td><?php echo remove_junk($po_data['purpose']); ?></td>
            </tr>

            <!-- Penalty Clause -->
            <tr>
                <td colspan="6">
                    <p><strong>In case of failure to make the full delivery within the time specified above, a penalty of one-tenth (1/10) of one percent for every day of delay shall be imposed.</strong></p>
                </td>
            </tr>

            <!-- Signature Section -->
            <tr>
                <td colspan="3" style="vertical-align: top;">
                    <p>Confirm:</p>
                    <p>_______________________________</p>
                    <p>Signature over Printed Name of Supplier</p>
                    <p>Date: __________________________</p>
                </td>
                <td colspan="3" style="vertical-align: top;">
                    <p>Very truly yours,</p>
                    <p>_______________________________</p>
                    <p>(Head of the Procuring Entity)</p>
                </td>
            </tr>

            <!-- Funding Details -->
            <tr>
                <td colspan="2">Fund Cluster: <?php echo remove_junk($po_data['fund_cluster']); ?></td>
                <td colspan="2">ORS/BURS No.: <?php echo remove_junk($po_data['ors_burs_no']); ?></td>
                <td colspan="2">Date of the ORS/BURS: <?php echo read_date($po_data['date_of_ors_burs']); ?></td>
            </tr>
            <tr>
                <td colspan="3">Funds Available: <?php echo remove_junk($po_data['funds_available']); ?></td>
                <td colspan="3">Amount: <?php echo remove_junk($po_data['total_amount']); ?></td>
            </tr>
            <tr>
                <td colspan="6" style="text-align: center;"><strong>(Certified Funds Available)</strong></td>
            </tr>
        </table>
    </div>
</body>
</html>