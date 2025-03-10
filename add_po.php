<?php
$page_title = 'Add Purchase Order';
require_once 'includes/load.php';
page_require_level(2); // Ensure only users with level 2 or higher can access this page

// Fetch all suppliers from the database
$suppliers = find_all('suppliers'); // Assuming 'suppliers' is the table name
?>

<?php
// Function to generate a unique stock/property number
function generate_stock_property_no($po_id, $index) {
  return "{$po_id}" . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
}

// Function to convert number to words
function convert_number_to_words($number) {
    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'Zero',
        1                   => 'One',
        2                   => 'Two',
        3                   => 'Three',
        4                   => 'Four',
        5                   => 'Five',
        6                   => 'Six',
        7                   => 'Seven',
        8                   => 'Eight',
        9                   => 'Nine',
        10                  => 'Ten',
        11                  => 'Eleven',
        12                  => 'Twelve',
        13                  => 'Thirteen',
        14                  => 'Fourteen',
        15                  => 'Fifteen',
        16                  => 'Sixteen',
        17                  => 'Seventeen',
        18                  => 'Eighteen',
        19                  => 'Nineteen',
        20                  => 'Twenty',
        30                  => 'Thirty',
        40                  => 'Forty',
        50                  => 'Fifty',
        60                  => 'Sixty',
        70                  => 'Seventy',
        80                  => 'Eighty',
        90                  => 'Ninety',
        100                 => 'Hundred',
        1000                => 'Thousand',
        1000000             => 'Million',
        1000000000          => 'Billion',
        1000000000000       => 'Trillion',
        1000000000000000    => 'Quadrillion',
        1000000000000000000 => 'Quintillion'
    );

    if (!is_numeric($number)) {
        return false;
    }

    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    return $string;
}

if (isset($_POST['add_purchase_order'])) {
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

    // Insert purchase order
    $query = "INSERT INTO purchase_orders (
              supplier_name, address, tin, po_number, date, mode_of_procurement, purpose, 
              place_of_delivery, delivery_term, payment_term, total_amount, total_amount_words, 
              confirm_supplier, confirm_date, head_of_procurement, fund_cluster, funds_available, 
              ors_burs_no, date_of_ors_burs
              ) VALUES (
              '{$supplier_name}', '{$address}', '{$tin}', '{$po_number}', '{$date}', '{$mode_of_procurement}', 
              '{$purpose}', '{$place_of_delivery}', '{$delivery_term}', '{$payment_term}', '{$total_amount}', 
              '{$total_amount_words}', '{$confirm_supplier}', '{$confirm_date}', '{$head_of_procurement}', 
              '{$fund_cluster}', '{$funds_available}', '{$ors_burs_no}', '{$date_of_ors_burs}'
              )";
    
    if ($db->query($query)) {
      $po_id = $db->insert_id(); // Get the ID of the newly inserted purchase order

      // Insert items
      if (isset($_POST['description']) && is_array($_POST['description'])) {
        foreach ($_POST['description'] as $key => $description) {
          if (!empty($description) && isset($_POST['quantity'][$key]) && isset($_POST['unit'][$key]) && isset($_POST['unit_cost'][$key])) {
            $description = remove_junk($db->escape($description));
            $quantity = remove_junk($db->escape($_POST['quantity'][$key]));
            $unit = remove_junk($db->escape($_POST['unit'][$key]));
            $unit_cost = remove_junk($db->escape($_POST['unit_cost'][$key]));
            $amount = $quantity * $unit_cost;

            // Generate stock/property number
            $stock_property_no = generate_stock_property_no($po_id, $key);

            $query = "INSERT INTO purchase_order_items (po_id, stock_property_no, unit, description, quantity, unit_cost, amount) 
                      VALUES ('{$po_id}', '{$stock_property_no}', '{$unit}', '{$description}', '{$quantity}', '{$unit_cost}', '{$amount}')";
            
            if (!$db->query($query)) {
              $db->query("ROLLBACK");
              $session->msg('d', 'Failed to add purchase order item!');
              redirect('add_po.php', false);
            }
          }
        }
      }

      // Commit transaction if everything is successful
      $db->query("COMMIT");
      $session->msg('s', "Purchase order added successfully.");
      redirect('purchase_orders.php', false);
    } else {
      $db->query("ROLLBACK");
      $session->msg('d', 'Failed to add purchase order!');
      redirect('add_po.php', false);
    }
  } else {
    $session->msg("d", implode("<br>", $errors));
    redirect('add_po.php', false);
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
          <span>Add Purchase Order</span>
        </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="add_po.php" class="clearfix">
          <div class="form-group">
            <label for="supplier_name">Supplier Name</label>
            <select class="form-control" name="supplier_name" required>
              <option value="">Select Supplier</option>
              <?php foreach ($suppliers as $supplier): ?>
                <option value="<?php echo remove_junk($supplier['name']); ?>">
                  <?php echo remove_junk($supplier['name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="address">Address</label>
            <input type="text" class="form-control" name="address" required>
          </div>
          <div class="form-group">
            <label for="tin">TIN</label>
            <input type="text" class="form-control" name="tin" required>
          </div>
          <div class="form-group">
            <label for="po_number">PO Number</label>
            <input type="text" class="form-control" name="po_number" required>
          </div>
          <div class="form-group">
            <label for="date">Date</label>
            <input type="date" class="form-control" name="date" required>
          </div>
          <div class="form-group">
            <label for="mode_of_procurement">Mode of Procurement</label>
            <input type="text" class="form-control" name="mode_of_procurement" required>
          </div>
          <div class="form-group">
            <label for="purpose">Purpose</label>
            <textarea class="form-control" name="purpose" required></textarea>
          </div>
          <div class="form-group">
            <label for="place_of_delivery">Place of Delivery</label>
            <input type="text" class="form-control" name="place_of_delivery">
          </div>
          <div class="form-group">
            <label for="delivery_term">Delivery Term</label>
            <input type="text" class="form-control" name="delivery_term">
          </div>
          <div class="form-group">
            <label for="payment_term">Payment Term</label>
            <input type="text" class="form-control" name="payment_term">
          </div>
          <div class="form-group">
            <label for="confirm_supplier">Confirmed by Supplier</label>
            <input type="text" class="form-control" name="confirm_supplier">
          </div>
          <div class="form-group">
            <label for="confirm_date">Confirmation Date</label>
            <input type="date" class="form-control" name="confirm_date">
          </div>
          <div class="form-group">
            <label for="head_of_procurement">Head of Procurement</label>
            <input type="text" class="form-control" name="head_of_procurement">
          </div>
          <div class="form-group">
            <label for="fund_cluster">Fund Cluster</label>
            <input type="text" class="form-control" name="fund_cluster">
          </div>
          <div class="form-group">
            <label for="funds_available">Funds Available</label>
            <input type="text" class="form-control" name="funds_available">
          </div>
          <div class="form-group">
            <label for="ors_burs_no">ORS/BURS No.</label>
            <input type="text" class="form-control" name="ors_burs_no">
          </div>
          <div class="form-group">
            <label for="date_of_ors_burs">Date of ORS/BURS</label>
            <input type="date" class="form-control" name="date_of_ors_burs">
          </div>
          <div id="items">
            <div class="form-group item" style="border: 1px solid #ddd; padding: 10px;">
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
            </div>
          </div>
          <div class="form-group">
            <label for="total_amount_words">Total Amount in Words</label>
            <div id="total_amount_words" class="form-control" style="background-color: #f8f9fa; padding: 10px;">
              <!-- The total amount in words will appear here -->
            </div>
          </div>
          </div>
          <button type="button" id="add-item" class="btn btn-success">Add Another Item</button>
          <button type="submit" name="add_purchase_order" class="btn btn-primary">Add Purchase Order</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
// JavaScript to convert number to words
function convertNumberToWords(number) {
  const units = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine"];
  const teens = ["Ten", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"];
  const tens = ["", "Ten", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];

  if (number === 0) return "Zero";

  let words = "";

  if (Math.floor(number / 1000000) > 0) {
    words += convertNumberToWords(Math.floor(number / 1000000)) + " Million ";
    number %= 1000000;
  }

  if (Math.floor(number / 1000) > 0) {
    words += convertNumberToWords(Math.floor(number / 1000)) + " Thousand ";
    number %= 1000;
  }

  if (Math.floor(number / 100) > 0) {
    words += convertNumberToWords(Math.floor(number / 100)) + " Hundred ";
    number %= 100;
  }

  if (number > 0) {
    if (number < 10) {
      words += units[number];
    } else if (number < 20) {
      words += teens[number - 10];
    } else {
      words += tens[Math.floor(number / 10)];
      if (number % 10 > 0) {
        words += "-" + units[number % 10];
      }
    }
  }

  return words.trim();
}

// Update total amount in words dynamically
document.addEventListener('input', function (e) {
  if (e.target.name === 'quantity[]' || e.target.name === 'unit_cost[]') {
    updateTotalAmountInWords();
  }
});

function updateTotalAmountInWords() {
  let totalAmount = 0;

  // Calculate the total amount
  document.querySelectorAll('.item').forEach((item, index) => {
    const quantity = parseFloat(item.querySelector('input[name="quantity[]"]').value) || 0;
    const unitCost = parseFloat(item.querySelector('input[name="unit_cost[]"]').value) || 0;
    totalAmount += quantity * unitCost;
  });

  // Convert the total amount to words
  const totalAmountWords = convertNumberToWords(totalAmount);

  // Display the total amount in words
  document.getElementById('total_amount_words').textContent = totalAmountWords || "Zero";
}

// Add item dynamically
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

// Remove item
document.getElementById('items').addEventListener('click', function(e) {
  if (e.target && e.target.classList.contains('remove-item')) {
    e.target.closest('.item').remove();
    updateTotalAmountInWords(); // Update total amount in words after removing an item
  }
});
</script>


<?php include_once('layouts/footer.php'); ?>