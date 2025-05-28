<?php
require_once('../includes/load.php');

if (isset($_GET['id'])) {
    $request_id = (int)$_GET['id'];
    $pr_data = find_by_id('purchase_requests', $request_id, 'request_id');

    if (!$pr_data) {
        $session->msg('d', 'Purchase request not found.');
        redirect('../mange_pr.php', false);
    }

    $items = find_items_by_pr_id($request_id);
} else {
    $session->msg('d', 'Missing purchase request ID.');
    redirect('../mange_pr.php', false);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Request</title>
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
        .content {
            margin-left: 20px;
        }
        .content div {
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        .signature {
            margin-top: 40px;
        }
        .signature div {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">PURCHASE REQUEST</div>
        <div class="department">DEPARTMENT OF EDUCATION</div>
        <div class="content">
            <table>
                <tr>
                    <td colspan="2">Entity Name: <?php echo remove_junk($pr_data['entity_name']); ?></td>
                    <td>Fund Cluster: <?php echo remove_junk($pr_data['fund_cluster']); ?></td>
                </tr>
                <tr>
                    <td>Office/Section: <?php echo remove_junk($pr_data['office_section']); ?></td>
                    <td>PR No.: <?php echo remove_junk($pr_data['pr_no']); ?></td>
                    <td>Date: <?php echo read_date($pr_data['date_requested']); ?></td>
                </tr>
                <tr>
                    <td colspan="3">Responsibility Center Code: <?php echo remove_junk($pr_data['responsibility_center_code']); ?></td>
                </tr>
            </table>

            <table>
                <thead>
                    <tr>
                        <th>Stock/Property No.</th>
                        <th>Unit</th>
                        <th>Item Description</th>
                        <th>Unit Cost</th>
                        <th>Total Cost</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                    <td><?php echo remove_junk($item['id']); ?></td>
                        <td><?php echo remove_junk($item['unit']); ?></td>
                        <td><?php echo remove_junk($item['item_description']); ?></td>
                        <td><?php echo remove_junk($item['unit_cost']); ?></td>
                        <td><?php echo remove_junk($item['total_cost']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="4">Grand Total:</td>
                        <td><?php echo array_sum(array_column($items, 'total_cost')); ?></td>
                    </tr>
                    <tr>
                        <td colspan="4">Purpose:</td>
                        <td><?php echo remove_junk($pr_data['purpose']); ?></td>
                    </tr>
                    <tr>
                    <td></td>
                    <td colspan="2">Requested by:</td>
                    <td colspan="2">Approved by:</td>
                </tr>
                <tr>
                    <td>Signature:</td>
                    <td colspan="2" rowspan="3" style="text-align:center;"><?php echo remove_junk($pr_data['requestor']); ?></td>
                    <td colspan="2" rowspan="3" style="text-align:center;"><?php echo remove_junk($pr_data['approved_by']); ?></td>
                </tr>
                <tr>
                    <td>Printed Name:</td>
                    
                </tr>
                </tr>
                <tr>
                    <td>Designation:</td>
                </tr>
                    
                </tbody>
            </table>

           
        </div>
    </div>
</body>
</html>