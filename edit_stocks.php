<?php
$page_title = 'Edit Stock';
require_once('includes/load.php');
// Checkin What level user has permission to view this page
page_require_level(2);

// Check if the 'stocks_id' parameter exists in the URL
if (!isset($_GET['stocks_id']) || empty($_GET['stocks_id'])) {
    $session->msg("d", "Missing stock ID.");
    redirect('stocks.php');
}

// Fetch the stock details
$stocks_id = (int)$_GET['stocks_id'];
$stock = find_by_id('stocks', $stocks_id, 'stocks_id');
if (!$stock) {
    $session->msg("d", "Stock not found.");
    redirect('stocks.php');
}

// Fetch all categories
$all_categories = find_all('categories');

if (isset($_POST['edit_stock'])) {
    $req_fields = array('stock_property_no', 'quantity', 'unit_cost', 'description', 'categorie_id');
    validate_fields($req_fields);

    $stock_property_no = remove_junk($db->escape($_POST['stock_property_no']));
    $quantity = (int)$_POST['quantity'];
    $unit_cost = (float)$_POST['unit_cost'];
    $description = remove_junk($db->escape($_POST['description']));
    $categorie_id = (int)$_POST['categorie_id'];

    if (empty($errors)) {
        // SQL Update query
        $sql = "UPDATE stocks SET 
                    stock_property_no='{$stock_property_no}', 
                    quantity='{$quantity}', 
                    unit_cost='{$unit_cost}', 
                    description='{$description}', 
                    categorie_id='{$categorie_id}' 
                WHERE stocks_id='{$stock['stocks_id']}'";
        $result = $db->query($sql);

        if ($result && $db->affected_rows() === 1) {
            $session->msg("s", "Stock updated successfully.");
            redirect('stocks.php', false);
        } else {
            $session->msg("d", "Sorry! Failed to update the stock.");
            redirect('edit_stocks.php?stocks_id=' . $stock['stocks_id'], false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('edit_stocks.php?stocks_id=' . $stock['stocks_id'], false);
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
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong>
                <span class="glyphicon glyphicon-th"></span>
                <span>Edit Stock</span>
            </strong>
        </div>
        <div class="panel-body">
            <div class="col-md-7">
                <form method="post" action="edit_stocks.php?stocks_id=<?php echo (int)$stock['stocks_id']; ?>">
                    <div class="form-group">
                        <label for="stock_property_no">Stock Property No</label>
                        <input type="text" class="form-control" name="stock_property_no" value="<?php echo remove_junk($stock['stock_property_no']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input type="number" class="form-control" name="quantity" value="<?php echo remove_junk($stock['quantity']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="unit_cost">Unit Cost</label>
                        <input type="number" step="0.01" class="form-control" name="unit_cost" value="<?php echo remove_junk($stock['unit_cost']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" name="description" required><?php echo remove_junk($stock['description']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="categorie_id">Category</label>
                        <select class="form-control" name="categorie_id" required>
                            <option value="">Select a category</option>
                            <?php foreach ($all_categories as $cat): ?>
                                <option value="<?php echo (int)$cat['id']; ?>" <?php if ($stock['categorie_id'] === $cat['id']) echo "selected"; ?>>
                                    <?php echo remove_junk($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="edit_stock" class="btn btn-danger">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>