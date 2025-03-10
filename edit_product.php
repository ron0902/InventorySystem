<?php
  $page_title = 'Edit Product';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);
?>
<?php
$product = find_by_id('products', (int)$_GET['id']);
$all_categories = find_all('categories');

if (!$product) {
  $session->msg("d", "Missing product id.");
  redirect('product.php');
}
?>
<?php
 if (isset($_POST['product'])) {
    $req_fields = array('product-title', 'product-categorie', 'product-quantity', 'unit-cost');
    validate_fields($req_fields);

   if (empty($errors)) {
       $p_name    = remove_junk($db->escape($_POST['product-title']));
       $p_cat     = (int)$_POST['product-categorie'];
       $p_qty     = remove_junk($db->escape($_POST['product-quantity']));
       $unit_cost = remove_junk($db->escape($_POST['unit-cost']));
       
       // Ensure quantity and unit cost are greater than 0
       if ($p_qty <= 0 || $unit_cost <= 0) {
           $session->msg('d', 'Quantity and Unit Cost must be greater than 0.');
           redirect('edit_product.php?id=' . $product['id'], false);
           exit;
       }

       // Calculate amount as quantity * unit cost
       $amount = $p_qty * $unit_cost;

       // SQL Update query
       $query = "UPDATE products SET";
       $query .= " name = '{$p_name}', quantity = '{$p_qty}',";
       $query .= " unit_cost = '{$unit_cost}', amount = '{$amount}', categorie_id = '{$p_cat}'";
       $query .= " WHERE id = '{$product['id']}'";
       
       // Execute the query
       $result = $db->query($query);
       
       if ($result && $db->affected_rows() === 1) {
         $session->msg('s', "Product updated successfully.");
         redirect('product.php', false);
       } else {
         $session->msg('d', 'Sorry, failed to update the product!');
         redirect('edit_product.php?id=' . $product['id'], false);
       }

   } else {
       // If there are validation errors
       $session->msg("d", $errors);
       redirect('edit_product.php?id=' . $product['id'], false);
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
            <span>Add New Product</span>
         </strong>
        </div>
        <div class="panel-body">
         <div class="col-md-7">
           <form method="post" action="edit_product.php?id=<?php echo (int)$product['id'] ?>">
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon">
                   <i class="glyphicon glyphicon-th-large"></i>
                  </span>
                  <input type="text" class="form-control" name="product-title" value="<?php echo remove_junk($product['name']);?>">
               </div>
              </div>
              <div class="form-group">
                <div class="row">
                  <div class="col-md-6">
                    <select class="form-control" name="product-categorie">
                    <option value=""> Select a categorie</option>
                   <?php  foreach ($all_categories as $cat): ?>
                     <option value="<?php echo (int)$cat['id']; ?>" <?php if($product['categorie_id'] === $cat['id']): echo "selected"; endif; ?> >
                       <?php echo remove_junk($cat['name']); ?></option>
                   <?php endforeach; ?>
                 </select>
                  </div>
                </div>
              </div>
              <div class="form-group">
               <div class="row">
                 <div class="col-md-4">
                  <div class="form-group">
                    <label for="qty">Quantity</label>
                    <div class="input-group">
                      <span class="input-group-addon">
                       <i class="glyphicon glyphicon-shopping-cart"></i>
                      </span>
                      <input type="number" class="form-control" name="product-quantity" value="<?php echo remove_junk($product['quantity']); ?>">
                   </div>
                  </div>
                 </div>
                 <div class="col-md-4">
                  <div class="form-group">
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
                  <div class="col-md-4">
                  </div>
               </div>
              </div>
              <button type="submit" name="product" class="btn btn-danger">Update</button>
          </form>
         </div>
        </div>
      </div>
  </div>

<?php include_once('layouts/footer.php'); ?>
