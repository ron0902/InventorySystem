<?php
  $page_title = 'All Product';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page


  // Fetch all products from the stocks table
  $products = find_all('stocks'); // Updated to fetch directly from the stocks table
?>
<?php include_once('layouts/header.php'); ?>
  <div class="row">
     <div class="col-md-12">
       <?php echo display_msg($msg); ?>
     </div>
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading clearfix">
        <div class="pull-right">
           <!-- Updated link to add_stocks.php -->
           <a href="add_stocks.php" class="btn btn-primary">Add New</a>
         </div>
        </div>
        <div class="panel-body">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th class="text-center" style="width: 50px;">#</th>
                <th> Stock Property No</th>
                <th class="text-center" style="width: 10%;"> Categories </th>
                <th class="text-center" style="width: 10%;"> Quantity </th>
                <th class="text-center" style="width: 10%;"> Unit Cost </th>
                <th class="text-center" style="width: 20%;"> Description </th>
                <th class="text-center" style="width: 100px;"> Actions </th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($products as $product): ?>
              <tr>
                <td class="text-center"><?php echo count_id(); ?></td>
                <td> <?php echo remove_junk($product['stock_property_no']); ?></td>
                <td class="text-center"> 
                  <?php 
                    $category = find_by_id('categories', $product['categorie_id']); 
                    echo $category ? remove_junk($category['name']) : 'N/A'; 
                  ?>
                </td>
                <td class="text-center"> <?php echo remove_junk($product['quantity']); ?></td>
                <td class="text-center"> <?php echo remove_junk($product['unit_cost']); ?></td>
                <td class="text-center"> <?php echo remove_junk($product['description']); ?></td>
                <td class="text-center">
                  <div class="btn-group">
                    <a href="edit_stocks.php?stocks_id=<?php echo (int)$product['stocks_id']; ?>" class="btn btn-info btn-xs" title="Edit" data-toggle="tooltip">
                      <span class="glyphicon glyphicon-edit"></span>
                    </a>
                    <a href="delete_stocks.php?stocks_id=<?php echo (int)$product['stocks_id']; ?>" class="btn btn-danger btn-xs" title="Delete" data-toggle="tooltip">
                      <span class="glyphicon glyphicon-trash"></span>
                    </a>  
                  </div>
                </td>
              </tr>
             <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
<?php include_once('layouts/footer.php'); ?>