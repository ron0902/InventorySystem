<?php
$page_title = 'Edit Purchase Order';
require_once 'includes/load.php';
?>

<?php
if (isset($_GET['id'])) {
  $po_id = (int)$_GET['id']; 
  $po_data = find_by_id('purchase_orders', $po_id, 'po_id'); 

  if (!$po_data) {
    $session->msg('d', 'Purchase order not found.'); 
    redirect('manage_po.php', false);
  }

  $items = find_items_by_po_id($po_id);
} else {
  $session->msg('d', 'Missing purchase order ID.'); 
  redirect('manage_po.php', false); 
}

$po = find_by_id('purchase_orders', (int)$_GET['id'], 'po_id'); 
$all_categories = find_all('categories');
$all_departments = find_all('departments');
$all_users = find_all('users');

if (!$po) {
  $session->msg("d", "Missing purchase order id."); 
  redirect('purchase_orders.php'); 
}
?>

<?php
if (isset($_POST['edit_purchase_order'])) {
  $req_fields = ['supplier_name', 'address', 'tin', 'po_number', 'date', 'mode_of_procurement', 'purpose'];
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
    $supplier_name = remove_junk($db->escape($_POST['supplier_name']));
    $address = remove_junk($db->escape($_POST['address']));
    $tin = remove_junk($db->escape($_POST['tin']));
    $po_number = remove_junk($db->escape($_POST['po_number']));
    $date = remove_junk($db->escape($_POST['date']));
    $mode_of_procurement = remove_junk($db->escape($_POST['mode_of_procurement']));
    $purpose = remove_junk($db->escape($_POST['purpose']));
    $place_of_delivery = remove_junk($db->escape($_POST['place_of_delivery']));
    $delivery_term = remove_junk($db->escape($_POST['delivery_term']));
    $payment_term = remove_junk($db->escape($_POST['payment_term']));
    $confirm_supplier = remove_junk($db->escape($_POST['confirm_supplier']));
    $confirm_date = remove_junk($db->escape($_POST['confirm_date']));
    $head_of_procurement = remove_junk($db->escape($_POST['head_of_procurement']));
    $fund_cluster = remove_junk($db->escape($_POST['fund_cluster']));
    $funds_available = remove_junk($db->escape($_POST['funds_available']));
    $ors_burs_no = remove_junk($db->escape($_POST['ors_burs_no']));
    $date_of_ors_burs = remove_junk($db->escape($_POST['date_of_ors_burs']));

    // Calculate total amount
    $total_amount = 0;
    if (isset($_POST['quantity']) && is_array($_POST['quantity'])) {
      foreach ($_POST['quantity'] as $key => $quantity) {
        $unit_cost = $_POST['unit_cost'][$key];
        $total_amount += $quantity * $unit_cost;
      }
    }

    // Convert total amount to words
    $total_amount_words = convert_number_to_words($total_amount);

    // Start transaction
    $db->query("START TRANSACTION");

    // Update purchase order
    $query = "UPDATE purchase_orders SET
              supplier_name = '{$supplier_name}',
              address = '{$address}',
              tin = '{$tin}',
              po_number = '{$po_number}',
              date = '{$date}',
              mode_of_procurement = '{$mode_of_procurement}',
              purpose = '{$purpose}',
              place_of_delivery = '{$place_of_delivery}',
              delivery_term = '{$delivery_term}',
              payment_term = '{$payment_term}',
              total_amount = '{$total_amount}',
              total_amount_words = '{$total_amount_words}',
              confirm_supplier = '{$confirm_supplier}',
              confirm_date = '{$confirm_date}',
              head_of_procurement = '{$head_of_procurement}',
              fund_cluster = '{$fund_cluster}',
              funds_available = '{$funds_available}',
              ors_burs_no = '{$ors_burs_no}',
              date_of_ors_burs = '{$date_of_ors_burs}'
              WHERE id = '{$po_id}'";
    
    if ($db->query($query)) {
      // Delete existing items for this PO
      $db->query("DELETE FROM purchase_order_items WHERE po_id = '{$po_id}'");

      // Insert updated items
      if (isset($_POST['description']) && is_array($_POST['description'])) {
        foreach ($_POST['description'] as $key => $description) {
          if (!empty($description) && isset($_POST['quantity'][$key]) && isset($_POST['unit'][$key]) && isset($_POST['unit_cost'][$key])) {
            $description = remove_junk($db->escape($description));
            $quantity = remove_junk($db->escape($_POST['quantity'][$key]));
            $unit = remove_junk($db->escape($_POST['unit'][$key]));
            $unit_cost = remove_junk($db->escape($_POST['unit_cost'][$key]));
            $amount = $quantity * $unit_cost;

            $query = "INSERT INTO purchase_order_items (po_id, stock_property_no, unit, description, quantity, unit_cost, amount) 
                      VALUES ('{$po_id}', '{$stock_property_no}', '{$unit}', '{$description}', '{$quantity}', '{$unit_cost}', '{$amount}')";
            
            if (!$db->query($query)) {
              $db->query("ROLLBACK");
              $session->msg('d', 'Failed to update purchase order item!');
              redirect('edit_po.php?id=' . $po_id, false);
            }
          }
        }
      }

      // Commit transaction if everything is successful
      $db->query("COMMIT");
      $session->msg('s', "Purchase order updated successfully.");
      redirect('purchase_orders.php', false);
    } else {
      $db->query("ROLLBACK");
      $session->msg('d', 'Failed to update purchase order!');
      redirect('edit_po.php?id=' . $po_id, false);
    }
  } else {
    $session->msg("d", implode("<br>", $errors));
    redirect('edit_po.php?id=' . $po_id, false);
  }
}
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
          <span>Edit Purchase Order</span>
        </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="edit_po.php?id=<?php echo (int)$po_data['po_id']; ?>" class="clearfix">
          <div class="form-group">
            <label for="supplier_name">Supplier Name</label>
            <input type="text" class="form-control" name="supplier_name" value="<?php echo remove_junk($po_data['supplier_name']); ?>" required>
          </div>
          <div class="form-group">
            <label for="address">Address</label>
            <input type="text" class="form-control" name="address" value="<?php echo remove_junk($po_data['address']); ?>" required>
          </div>
          <div class="form-group">
            <label for="tin">TIN</label>
            <input type="text" class="form-control" name="tin" value="<?php echo remove_junk($po_data['tin']); ?>" required>
          </div>
          <div class="form-group">
            <label for="po_number">PO Number</label>
            <input type="text" class="form-control" name="po_number" value="<?php echo remove_junk($po_data['po_number']); ?>" required>
          </div>
          <div class="form-group">
            <label for="date">Date</label>
            <input type="date" class="form-control" name="date" value="<?php echo remove_junk($po_data['date']); ?>" required>
          </div>
          <div class="form-group">
            <label for="mode_of_procurement">Mode of Procurement</label>
            <input type="text" class="form-control" name="mode_of_procurement" value="<?php echo remove_junk($po_data['mode_of_procurement']); ?>" required>
          </div>
          <div class="form-group">
            <label for="purpose">Purpose</label>
            <textarea class="form-control" name="purpose" required><?php echo remove_junk($po_data['purpose']); ?></textarea>
          </div>
          <div class="form-group">
            <label for="place_of_delivery">Place of Delivery</label>
            <input type="text" class="form-control" name="place_of_delivery" value="<?php echo remove_junk($po_data['place_of_delivery']); ?>">
          </div>
          <div class="form-group">
            <label for="delivery_term">Delivery Term</label>
            <input type="text" class="form-control" name="delivery_term" value="<?php echo remove_junk($po_data['delivery_term']); ?>">
          </div>
          <div class="form-group">
            <label for="payment_term">Payment Term</label>
            <input type="text" class="form-control" name="payment_term" value="<?php echo remove_junk($po_data['payment_term']); ?>">
          </div>
          <div class="form-group">
            <label for="confirm_supplier">Confirmed by Supplier</label>
            <input type="text" class="form-control" name="confirm_supplier" value="<?php echo remove_junk($po_data['confirm_supplier']); ?>">
          </div>
          <div class="form-group">
            <label for="confirm_date">Confirmation Date</label>
            <input type="date" class="form-control" name="confirm_date" value="<?php echo remove_junk($po_data['confirm_date']); ?>">
          </div>
          <div class="form-group">
            <label for="head_of_procurement">Head of Procurement</label>
            <input type="text" class="form-control" name="head_of_procurement" value="<?php echo remove_junk($po_data['head_of_procurement']); ?>">
          </div>
          <div class="form-group">
            <label for="fund_cluster">Fund Cluster</label>
            <input type="text" class="form-control" name="fund_cluster" value="<?php echo remove_junk($po_data['fund_cluster']); ?>">
          </div>
          <div class="form-group">
            <label for="funds_available">Funds Available</label>
            <input type="text" class="form-control" name="funds_available" value="<?php echo remove_junk($po_data['funds_available']); ?>">
          </div>
          <div class="form-group">
            <label for="ors_burs_no">ORS/BURS No.</label>
            <input type="text" class="form-control" name="ors_burs_no" value="<?php echo remove_junk($po_data['ors_burs_no']); ?>">
          </div>
          <div class="form-group">
            <label for="date_of_ors_burs">Date of ORS/BURS</label>
            <input type="date" class="form-control" name="date_of_ors_burs" value="<?php echo remove_junk($po_data['date_of_ors_burs']); ?>">
          </div>
          <div id="items">
            <?php foreach ($items as $item): ?>
            <div class="form-group item" style="border: 1px solid #ddd; padding: 10px;">
              <div class="input-group" style="margin-top: 10px;">
                <span class="input-group-addon">
                  <i class="glyphicon glyphicon-th-large"></i>
                </span>
                <input type="text" class="form-control" name="description[]" value="<?php echo remove_junk($item['description']); ?>" placeholder="Item Description">
              </div>
              <div class="input-group" style="margin-top: 10px;">
                <span class="input-group-addon">
                  <i class="glyphicon glyphicon-shopping-cart"></i>
                </span>
                <input type="number" class="form-control" name="quantity[]" value="<?php echo remove_junk($item['quantity']); ?>" placeholder="Quantity">
              </div>
              <div class="input-group" style="margin-top: 10px;">
                <span class="input-group-addon">
                  <i class="glyphicon glyphicon-scale"></i>
                </span>
                <input type="text" class="form-control" name="unit[]" value="<?php echo remove_junk($item['unit']); ?>" placeholder="Unit">
              </div>
              <div class="input-group" style="margin-top: 10px;">
                <span class="input-group-addon">
                  <i class="glyphicon glyphicon-usd"></i>
                </span>
                <input type="number" step="0.01" class="form-control" name="unit_cost[]" value="<?php echo remove_junk($item['unit_cost']); ?>" placeholder="Unit Cost">
              </div>
              <button type="button" class="btn btn-danger remove-item" style="margin-top: 10px;">Remove</button>
            </div>
            <?php endforeach; ?>
          </div>
          <button type="button" id="add-item" class="btn btn-success">Add Another Item</button>
          <button type="submit" name="edit_purchase_order" class="btn btn-primary">Update Purchase Order</button>
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
      <input type="text" class="form-control" name="description[]" placeholder="Item Description">
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
});

document.getElementById('items').addEventListener('click', function(e) {
  if (e.target && e.target.classList.contains('remove-item')) {
    e.target.closest('.item').remove();
  }
});
</script>

<?php include_once('layouts/footer.php'); ?>