<?php
$page_title = 'Edit stocks';
require_once('includes/load.php');
// Checkin What level user has permission to view this page
page_require_level(2);

// Check if the 'stocks_id' parameter exists in the URL
if (!isset($_GET['stocks_id']) || empty($_GET['stocks_id'])) {
    $session->msg("d", "Missing stocks id.");
    redirect('stocks.php');
}

// Display the stocks details
$stocks_id = (int)$_GET['stocks_id'];
$stocks = find_by_id('stocks', $stocks_id, 'stocks_id');
if (!$stocks) {
    $session->msg("d", "stocks not found.");
    redirect('stocks.php');
}

// Fetch all categories
$all_categories = find_all('categories'); // Ensure categories are fetched

if (isset($_POST['edit_stocks'])) {
    $req_fields = array('stocks-name', 'stocks-category', 'stocks-quantity', 'unit-cost');
    validate_fields($req_fields);

    $stocks_name = remove_junk($db->escape($_POST['stocks-name']));
    $stocks_category = (int)$_POST['stocks-category'];  // Ensure category is an integer
    $stocks_quantity = remove_junk($db->escape($_POST['stocks-quantity']));
    $unit_cost = remove_junk($db->escape($_POST['unit-cost']));

    if (empty($errors)) {
        // SQL Update query
        $sql = "UPDATE stocks SET name='{$stocks_name}', quantity='{$stocks_quantity}', unit_cost='{$unit_cost}', categorie_id='{$stocks_category}'";
        $sql .= " WHERE stocks_id='{$stocks['stocks_id']}'";
        $result = $db->query($sql);

        if ($result && $db->affected_rows() === 1) {
            $session->msg("s", "Stocks updated successfully.");
            redirect('stocks.php', false);
        } else {
            $session->msg("d", "Sorry! Failed to update the Stocks.");
            redirect('edit_stocks.php?stocks_id=' . $stocks['stocks_id'], false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('edit_stocks.php?stocks_id=' . $stocks['stocks_id'], false);
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
                <span>Edit stocks</span>
            </strong>
        </div>
        <div class="panel-body">
            <div class="col-md-7">
                <form method="post" action="edit_stocks.php?stocks_id=<?php echo (int)$stocks['stocks_id']; ?>">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="glyphicon glyphicon-th-large"></i>
                            </span>
                            <input type="text" class="form-control" name="stocks-name" value="<?php echo remove_junk($stocks['name']);?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <select class="form-control" name="stocks-category">
                                    <option value=""> Select a category</option>
                                    <?php 
                                    // Loop through all categories
                                    foreach ($all_categories as $cat): ?>
                                        <option value="<?php echo (int)$cat['id']; ?>" 
                                            <?php if($stocks['categorie_id'] === $cat['id']) echo "selected"; ?>>
                                            <?php echo remove_junk($cat['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="qty">Quantity</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="glyphicon glyphicon-shopping-cart"></i>
                                    </span>
                                    <input type="number" class="form-control" name="stocks-quantity" value="<?php echo remove_junk($stocks['quantity']); ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="qty">Buying price</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="glyphicon glyphicon-usd"></i>
                                    </span>
                                    <input type="number" class="form-control" name="unit-cost" value="<?php echo remove_junk($stocks['unit_cost']); ?>" required>
                                    <span class="input-group-addon">.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="edit_stocks" class="btn btn-danger">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>
