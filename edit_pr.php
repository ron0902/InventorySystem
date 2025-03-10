<?php
$page_title = 'Edit Purchase Request';
require_once('includes/load.php');
// Checkin What level user has permission to view this page
page_require_level(2);
?>

<?php
if (isset($_GET['id'])) {
  $request_id = (int)$_GET['id'];
  $pr_data = find_by_id('purchase_requests', $request_id, 'request_id');

  if (!$pr_data) {
    $session->msg('d', 'Purchase request not found.');
    redirect('manage_pr.php', false);
  }

  $items = find_items_by_pr_id($request_id);
} else {
  $session->msg('d', 'Missing purchase request ID.');
  redirect('manage_pr.php', false);
}

$pr = find_by_id('purchase_requests', (int)$_GET['id'], 'request_id');
$all_categories = find_all('categories');
$all_departments = find_all('departments');
$all_users = find_all('users');

if (!$pr) {
  $session->msg("d", "Missing purchase request id.");
  redirect('purchase_requests.php');
}
?>

<?php
if (isset($_POST['purchase_request'])) {
  $req_fields = array('user_id', 'entity_name', 'fund_cluster', 'office_section', 'pr_no', 'responsibility_center_code', 'purpose');
  validate_fields($req_fields);

  $errors = array();
  if (empty($_POST['item-name'])) {
    $errors[] = "At least one item is required.";
  } else {
    foreach ($_POST['quantity'] as $quantity) {
      if (empty($quantity) || !is_numeric($quantity) || $quantity <= 0) {
        $errors[] = "Quantity must be a positive number.";
      }
    }
  }

  if (empty($errors)) {
    $user_id = remove_junk($db->escape($_POST['user_id']));
    $entity_name = remove_junk($db->escape($_POST['entity_name']));
    $fund_cluster = remove_junk($db->escape($_POST['fund_cluster']));
    $office_section = remove_junk($db->escape($_POST['office_section']));
    $pr_no = remove_junk($db->escape($_POST['pr_no']));
    $responsibility_center_code = remove_junk($db->escape($_POST['responsibility_center_code']));
    $purpose = remove_junk($db->escape($_POST['purpose']));
    $date_requested = make_date();

    // Update main purchase request
    $query  = "UPDATE purchase_requests SET";
    $query .= " user_id = '{$user_id}', entity_name = '{$entity_name}', fund_cluster = '{$fund_cluster}',";
    $query .= " office_section = '{$office_section}', pr_no = '{$pr_no}', responsibility_center_code = '{$responsibility_center_code}',";
    $query .= " purpose = '{$purpose}', date_requested = '{$date_requested}'";
    $query .= " WHERE request_id = '{$pr['request_id']}'";

    if ($db->query($query)) {
      $db->query("DELETE FROM purchase_request_items WHERE purchase_request_id = '{$pr['request_id']}'");

      // Insert each item
      foreach ($_POST['item-name'] as $key => $item_name) {
        $item_name = remove_junk($db->escape($item_name));
        $quantity = remove_junk($db->escape($_POST['quantity'][$key]));
        $unit = remove_junk($db->escape($_POST['unit'][$key]));
        $unit_cost = remove_junk($db->escape($_POST['unit_cost'][$key]));
        $total_cost = $quantity * $unit_cost;

        $query  = "INSERT INTO purchase_request_items (";
        $query .= "purchase_request_id, item_description, unit, quantity, unit_cost, total_cost";
        $query .= ") VALUES (";
        $query .= " '{$pr['request_id']}', '{$item_name}', '{$unit}', '{$quantity}', '{$unit_cost}', '{$total_cost}'";
        $query .= ")";

        if (!$db->query($query)) {
          $session->msg('d', 'Sorry, failed to add the purchase request item!');
          redirect('edit_pr.php?id=' . $pr['request_id'], false);
        }
      }
      $session->msg('s', "Purchase request updated successfully.");
      redirect('mange_pr.php', false);
    } else {
      $session->msg('d', 'Sorry, failed to update the purchase request!');
      redirect('edit_pr.php?id=' . $pr['request_id'], false);
    }
  } else {
    $session->msg("d", implode(", ", $errors));
    redirect('edit_pr.php?id=' . $pr['request_id'], false);
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
          <span>Edit Purchase Request</span>
        </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="edit_pr.php?id=<?php echo (int)$pr['request_id'] ?>" class="clearfix">
          <div class="form-group">
            <select class="form-control" name="user_id">
              <option value="">Select User</option>
              <?php foreach ($all_users as $user): ?>
                <option value="<?php echo (int)$user['id']; ?>" <?php if($pr['user_id'] === $user['id']): echo "selected"; endif; ?>>
                  <?php echo remove_junk($user['name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <input type="text" class="form-control" name="entity_name" value="<?php echo remove_junk($pr['entity_name']); ?>" placeholder="Entity Name">
          </div>
          <div class="form-group">
            <input type="text" class="form-control" name="fund_cluster" value="<?php echo remove_junk($pr['fund_cluster']); ?>" placeholder="Fund Cluster">
          </div>
          <div class="form-group">
            <input type="text" class="form-control" name="office_section" value="<?php echo remove_junk($pr['office_section']); ?>" placeholder="Office/Section">
          </div>
          <div class="form-group">
            <input type="text" class="form-control" name="pr_no" value="<?php echo remove_junk($pr['pr_no']); ?>" placeholder="PR Number">
          </div>
          <div class="form-group">
            <input type="text" class="form-control" name="responsibility_center_code" value="<?php echo remove_junk($pr['responsibility_center_code']); ?>" placeholder="Responsibility Center Code">
          </div>
          <div class="form-group">
            <textarea class="form-control" name="purpose" placeholder="Purpose"><?php echo remove_junk($pr['purpose']); ?></textarea>
          </div>
          <div id="items">
            <?php foreach ($items as $item): ?>
            <div class="form-group item" style="border: 1px solid #ddd; padding: 10px;">
              <div class="input-group" style="margin-top: 10px;">
                <span class="input-group-addon">
                  <i class="glyphicon glyphicon-th-large"></i>
                </span>
                <input type="text" class="form-control" name="item-name[]" value="<?php echo remove_junk($item['item_description']); ?>" placeholder="Item Name">
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
          <button type="submit" name="purchase_request" class="btn btn-danger">Update</button>
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
});

document.getElementById('items').addEventListener('click', function(e) {
  if (e.target && e.target.classList.contains('remove-item')) {
    e.target.closest('.item').remove();
  }
});
</script>

<?php include_once('layouts/footer.php'); ?>