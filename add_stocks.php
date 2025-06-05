<?php
  $page_title = 'Add Stock';
  require_once('includes/load.php');


  // Fetch all POs for the dropdown
  $all_pos = find_all_pos(); // Function to fetch all POs

  // Handle form submission for processing PO
  if (isset($_POST['process_po'])) {
      $po_id = (int)$_POST['po_id'];

      // Fetch PO items for the selected PO
      $po_items = find_po_items_by_po_id($po_id);
      if (!$po_items || $db->num_rows($po_items) == 0) {
          $session->msg('d', "No items found for the selected PO.");
          redirect('add_stocks.php', false);
      }

      // Loop through PO items and insert or update stocks
      while ($po_item = $po_items->fetch_assoc()) {
          $stock_property_no = $po_item['stock_property_no'];
          $quantity = (int)$po_item['quantity'];
          $unit_cost = (float)$po_item['unit_cost'];
          $description = $db->escape($po_item['description']);
          $amount = (float)$po_item['amount'];

          // Fetch the categorie_id for the stock_property_no
          $categorie_query = "SELECT categorie_id FROM stocks WHERE stock_property_no = '{$stock_property_no}' LIMIT 1";
          $categorie_result = $db->query($categorie_query);
          if ($categorie_result && $db->num_rows($categorie_result) > 0) {
              $categorie_row = $categorie_result->fetch_assoc();
              $categorie_id = (int)$categorie_row['categorie_id'];
          } else {
              $categorie_id = "NULL"; // Set to NULL if no category is found
          }

          // Insert or update stock
          $query = "INSERT INTO stocks (stock_property_no, quantity, unit_cost, description, amount, categorie_id) 
                    VALUES ('{$stock_property_no}', '{$quantity}', '{$unit_cost}', '{$description}', '{$amount}', {$categorie_id})
                    ON DUPLICATE KEY UPDATE 
                        quantity = quantity + VALUES(quantity), 
                        unit_cost = VALUES(unit_cost), 
                        description = VALUES(description)";
          if (!$db->query($query)) {
              $session->msg('d', "Failed to process PO item: " . $db->error);
              redirect('add_stocks.php', false);
          }
      }

      $session->msg('s', "Stock updated successfully from PO.");
      redirect('add_stocks.php', false);
  }

  // Handle adding new products
  if (isset($_POST['add_product'])) {
      $stock_property_no = remove_junk($db->escape($_POST['stock_property_no']));
      $quantity = (int)$_POST['quantity'];
      $unit_cost = (float)$_POST['unit_cost'];
      $description = remove_junk($db->escape($_POST['description']));
      $categorie_id = (int)$_POST['categorie_id'];

      $query = "INSERT INTO stocks (stock_property_no, quantity, unit_cost, description, categorie_id) 
                VALUES ('{$stock_property_no}', '{$quantity}', '{$unit_cost}', '{$description}', '{$categorie_id}')
                ON DUPLICATE KEY UPDATE 
                    quantity = quantity + VALUES(quantity), 
                    unit_cost = VALUES(unit_cost), 
                    description = VALUES(description)";
      if ($db->query($query)) {
          $session->msg('s', "Product added successfully.");
      } else {
          $session->msg('d', "Failed to add product: " . $db->error);
      }
      redirect('add_stocks.php', false);
  }
?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<!-- Form to process PO -->
<div class="row">
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Process Purchase Order</span>
        </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="add_stocks.php">
          <div class="form-group">
            <label for="po_id">Select Purchase Order</label>
            <select class="form-control" name="po_id" required>
              <option value="">Select PO</option>
              <?php foreach ($all_pos as $po): ?>
                <option value="<?php echo (int)$po['po_id']; ?>">
                  <?php echo $po['po_number'] . ' - ' . $po['supplier_name']; ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <button type="submit" name="process_po" class="btn btn-primary">Process PO</button>
        </form>
      </div>
    </div>
  </div>

  <!-- Form to add new product -->
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Add New Product</span>
        </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="add_stocks.php">
          <div class="form-group">
            <label for="stock_property_no">Stock Property No</label>
            <input type="text" class="form-control" name="stock_property_no" required>
          </div>
          <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="number" class="form-control" name="quantity" required>
          </div>
          <div class="form-group">
            <label for="unit_cost">Unit Cost</label>
            <input type="number" step="0.01" class="form-control" name="unit_cost" required>
          </div>
          <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" name="description" required></textarea>
          </div>
          <div class="form-group">
            <label for="categorie_id">Category</label>
            <select class="form-control" name="categorie_id">
              <option value="">Select Category</option>
              <?php foreach (find_all('categories') as $category): ?>
                <option value="<?php echo (int)$category['id']; ?>">
                  <?php echo $category['name']; ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <button type="submit" name="add_product" class="btn btn-success">Add Product</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>