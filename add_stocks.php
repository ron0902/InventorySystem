<?php
  $page_title = 'Add Product';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);
  $all_categories = find_all('categories');
?>
<?php
 if(isset($_POST['add_stocks'])){
   $req_fields = array('product-title', 'product-categorie', 'product-quantity', 'unit-cost');
   validate_fields($req_fields);
   
   if(empty($errors)){
     $p_name   = remove_junk($db->escape($_POST['product-title']));
     $p_cat    = remove_junk($db->escape($_POST['product-categorie']));
     $p_qty    = remove_junk($db->escape($_POST['product-quantity']));
     $unit_cost = remove_junk($db->escape($_POST['unit-cost']));
     $amount   = $p_qty * $unit_cost; // Calculate the amount
     $date     = make_date();

     // Insert the updated data into the new database schema
     $query  = "INSERT INTO stocks (";
     $query .= "name, quantity, unit_cost, amount, categorie_id, date";
     $query .= ") VALUES (";
     $query .= " '{$p_name}', '{$p_qty}', '{$unit_cost}', '{$amount}', '{$p_cat}', '{$date}'";
     $query .= ")";
     $query .= " ON DUPLICATE KEY UPDATE name='{$p_name}', quantity='{$p_qty}', unit_cost='{$unit_cost}', amount='{$amount}'";
     
     if($db->query($query)){
       $session->msg('s',"Stock added successfully.");
       redirect('add_stocks.php', false);
     } else {
       $session->msg('d',' Sorry, failed to add the Stock!');
       redirect('product.php', false);
     }
   } else {
     $session->msg("d", $errors);
     redirect('add_stocks.php', false);
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
  <div class="col-md-8">
      <div class="panel panel-default">
        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>Add New Stocks</span>
         </strong>
        </div>
        <div class="panel-body">
         <div class="col-md-12">
          <form method="post" action="add_stocks.php" class="clearfix">
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon">
                   <i class="glyphicon glyphicon-th-large"></i>
                  </span>
                  <input type="text" class="form-control" name="product-title" placeholder="Name">
               </div>
              </div>
              <div class="form-group">
                <div class="row">
                  <div class="col-md-6">
                    <select class="form-control" name="product-categorie">
                      <option value="">Select Category</option>
                    <?php  foreach ($all_categories as $cat): ?>
                      <option value="<?php echo (int)$cat['id'] ?>">
                        <?php echo $cat['name'] ?></option>
                    <?php endforeach; ?>
                    </select>
                  </div>
                </div>
              </div>

              <div class="form-group">
               <div class="row">
                 <div class="col-md-4">
                   <div class="input-group">
                     <span class="input-group-addon">
                      <i class="glyphicon glyphicon-shopping-cart"></i>
                     </span>
                     <input type="number" class="form-control" name="product-quantity" placeholder="Quantity">
                  </div>
                 </div>
                 <div class="col-md-4">
                   <div class="input-group">
                     <span class="input-group-addon">
                       <i class="glyphicon glyphicon-usd"></i>
                     </span>
                     <input type="number" class="form-control" name="unit-cost" placeholder="Unit Cost" required>
                     <span class="input-group-addon">.00</span>
                  </div>
                 </div>
               </div>
              </div>
              <button type="submit" name="add_stocks" class="btn btn-danger">Add product</button>
          </form>
         </div>
        </div>
      </div>
    </div>
  </div>

<?php include_once('layouts/footer.php'); ?>
