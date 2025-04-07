<?php
$page_title = 'Edit Product';
require_once('includes/load.php');
// Checkin What level user has permission to view this page
page_require_level(2);

// Check if the 'product_id' parameter exists in the URL
if (!isset($_GET['product_id']) || empty($_GET['product_id'])) {
    $session->msg("d", "Missing product id.");
    redirect('product.php');
}

// Display the product details
$product = find_by_id('products', (int)$_GET['product_id']);
if (!$product) {
    $session->msg("d", "Product not found.");
    redirect('product.php');
}

// Fetch all categories
$all_categories = find_all('categories'); // Ensure categories are fetched

if (isset($_POST['edit_product'])) {
    $req_fields = array('product-name', 'product-category', 'product-quantity', 'unit-cost');
    validate_fields($req_fields);

    $product_name = remove_junk($db->escape($_POST['product-name']));
    $product_category = (int)$_POST['product-category'];  // Ensure category is an integer
    $product_quantity = remove_junk($db->escape($_POST['product-quantity']));
    $unit_cost = remove_junk($db->escape($_POST['unit-cost']));

    if (empty($errors)) {
        // SQL Update query
        $sql = "UPDATE products SET name='{$product_name}', quantity='{$product_quantity}', unit_cost='{$unit_cost}', categorie_id='{$product_category}'";
        $sql .= " WHERE product_id='{$product['product_id']}'";
        $result = $db->query($sql);

        if ($result && $db->affected_rows() === 1) {
            $session->msg("s", "Product updated successfully.");
            redirect('product.php', false);
        } else {
            $session->msg("d", "Sorry! Failed to update the product.");
            redirect('edit_product.php?product_id=' . $product['product_id'], false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('edit_product.php?product_id=' . $product['product_id'], false);
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
                <span>Edit Product</span>
            </strong>
        </div>
        <div class="panel-body">
            <div class="col-md-7">
                <form method="post" action="edit_product.php?product_id=<?php echo (int)$product['product_id']; ?>">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="glyphicon glyphicon-th-large"></i>
                            </span>
                            <input type="text" class="form-control" name="product-name" value="<?php echo remove_junk($product['name']);?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <select class="form-control" name="product-category">
                                    <option value=""> Select a category</option>
                                    <?php 
                                    // Loop through all categories
                                    foreach ($all_categories as $cat): ?>
                                        <option value="<?php echo (int)$cat['id']; ?>" 
                                            <?php if($product['categorie_id'] === $cat['id']) echo "selected"; ?>>
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
                                    <input type="number" class="form-control" name="product-quantity" value="<?php echo remove_junk($product['quantity']); ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="qty">Buying price</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="glyphicon glyphicon-usd"></i>
                                    </span>
                                    <input type="number" class="form-control" name="unit-cost" value="<?php echo remove_junk($product['unit_cost']); ?>" required>
                                    <span class="input-group-addon">.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="edit_product" class="btn btn-danger">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>
