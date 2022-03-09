<?php
session_start();

$products = '';

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cart</title>
  <link rel="stylesheet" href="css/output.css">

</head>

<body class="bg-gray-900 text-white p-8">


  <?php
  $connection = mysqli_connect("localhost", "root", "", "e-shoes");

  $arr = [];
  if (isset($_POST['id'])) {
    // get product by id 
    $rows = mysqli_query($connection, "select * from products where id=" . $_POST['id']);

    // add item to the session
    while ($row = mysqli_fetch_assoc($rows)) {
      // check if the product on the cart
      $is_product_in_session = false;
      foreach ($_SESSION['cart'] as $key) {
        if (isset($key['id'])) {

          if ($key['id'] == $row['id'])  $is_product_in_session = true;
        }
      }

      if ($is_product_in_session) {
        $new_products_array = array_map(function ($item) use (&$row) {
          if (
            isset($item['id'])  && $item['id'] ===
            $row['id']
          ) {
            $item['quantity']++;
            return $item;
          }

          return $item;
        }, $_SESSION['cart']);

        $_SESSION['cart'] = $new_products_array;
      } else {
        array_push($_SESSION['cart'], array_merge($row, ['quantity' => 1]));
      }
    }
  }

  // remove from session
  else if (isset($_POST['delete_id'])) {
    // check if the product on the cart
    $is_product_in_session = false;
    foreach ($_SESSION['cart'] as $key) {
      if (isset($key['id'])) {
        if ($key['id'] == $_POST['delete_id'])  $is_product_in_session = true;
      }
    }

    if ($is_product_in_session) {
      $new_products_array = array_filter($_SESSION['cart'], fn ($item) => $item['id'] === $_POST['delete_id']);

      // echo json_encode($new_products_array);
      $_SESSION['cart'] = $new_products_array;
    } else {
      array_push($_SESSION['cart'], array_merge($row, ['quantity' => 1]));
    }
  }
  // 
  else {
    // get request
    header('Location: index.php');
  }

  foreach ($_SESSION['cart'] as $item) {
    $products .= '
    <div class="overflow-hidden bg-white rounded-lg shadow-lg dark:bg-gray-800">
    <div class="px-4 py-2">
      <h1 class="text-3xl font-bold text-gray-700 uppercase dark:text-white">
      ' . $item['title'] . '
      </h1>
      <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
      ' . $item['description'] . '
      </p>
    </div>
  
    <img class="object-cover w-full h-48 mt-2" 
      
      src="' . $item['image'] . '"
      alt="NIKE AIR">
  
    <div class="flex items-center justify-between px-4 py-2 bg-gray-800">
      <h5 class="text-lg font-bold text-white">$' . $item['price'] . '</h5>
  
      <form action="cart.php" method="POST">
        <input type="hidden" name="id" value="' . $item['id'] . '">
        
        <button class="px-2 py-1 text-xs font-semibold text-gray-800 uppercase transition-colors duration-200 transform bg-white rounded hover:bg-gray-200 focus:bg-gray-400 focus:outline-none">Add</button>
        </form>
        
      <form action="cart.php" method="POST">
        <input type="hidden" name="delete_id" value="' . $item['id'] . '">
        <button class="px-2 py-1 text-xs font-semibold text-gray-100 uppercase transition-colors duration-200 transform bg-red-500 rounded hover:bg-gray-200 focus:bg-gray-400 focus:outline-none">remove</button>
      </form>
      <div class="">' . $item['quantity'] . '</div>
    </div>

  </div>
  ';
  }
  ?>


  <div class="border border-dashed p-4 rounded">
    <h1 class="text-3xl my-4">
      Cart page
    </h1>

    <section class="p-6 mx-auto bg-white rounded-md shadow-md bg-inherit">

      <!-- products list -->
      <div class="grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
        <!-- render the products -->
        <?php echo $products; ?>
      </div>
    </section>
  </div>

  <!-- reload -->
  <button id="reload" class="mt-8 px-2 py-3 bg-gray-600 text-white">
    Reload
  </button>
  <script>
    document.querySelector("#reload").onclick = () => window.location.reload()
  </script>

</body>

</html>