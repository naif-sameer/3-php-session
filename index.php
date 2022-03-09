<?php
session_start();
$_SESSION['cart'] = [];


$connection = mysqli_connect("localhost", "root", "", "e-shoes");


// get the products
$rows = mysqli_query($connection, "select * from products");

$products = '';

while ($row = mysqli_fetch_assoc($rows)) {
  $products .= '
  <div class="overflow-hidden bg-white rounded-lg shadow-lg dark:bg-gray-800">
  <div class="px-4 py-2">
    <h1 class="text-3xl font-bold text-gray-700 uppercase dark:text-white">
    ' . $row['title'] . '
    </h1>
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
    ' . $row['description'] . '
    </p>
  </div>

  <img class="object-cover w-full h-48 mt-2" 
    
    src="' . $row['image'] . '"
    alt="NIKE AIR">

  <div class="flex items-center justify-between px-4 py-2 bg-gray-800">
    <h5 class="text-lg font-bold text-white">$' . $row['price'] . '</h5>

    <form action="cart.php" method="POST">
      <input type="hidden" name="id" value="' . $row['id'] . '">
      <button class="px-2 py-1 text-xs font-semibold text-gray-800 uppercase transition-colors duration-200 transform bg-white rounded hover:bg-gray-200 focus:bg-gray-400 focus:outline-none">Add to cart</button>
    </form>
  </div>
</div>
';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/output.css">
  <title>products</title>
</head>

<body class="bg-gray-900 text-white p-8">


  <div class="border border-dashed p-4 rounded">
    <h1 class="text-3xl my-4">
      Add to Cart page
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

  <?php


  if (isset($_POST['zip-file-btn'])) {

    $output = '';
    // check if file upload
    if ($_FILES['zip_file']['name'] !== '') {
      $file_name = $_FILES['zip_file']['name'];
      $file_type = $_FILES['zip_file']['type'];

      // check if the file is zip
      if ($file_type !== 'application/x-zip-compressed') {
        echo 'please upload only zip file';
        return;
      }

      $name = explode('.', $file_name)[0];
      $upload_path = 'uploads/';
      $file_dir = $upload_path . $file_name;

      if (move_uploaded_file($_FILES['zip_file']['tmp_name'], $file_dir)) {
        $zip = new ZipArchive;

        // open zip file
        if ($zip->open($file_dir)) {
          $zip->extractTo($upload_path);
          $zip->close();
        }

        //  zip file
        $files = scandir($upload_path);
        foreach ($files as $file) {
          $file_ext = explode('.', $file)[1];

          if ($file_ext === 'zip') {
            $new_file_name = md5(rand() . '.' . $file_ext);

            $output .= "<img src='upload/" . $new_file_name . "/>";
            copy($upload_path . $name . '/' . $file, $upload_path . $new_file_name);

            // remove zip
            unlink($upload_path . '/' . $file);
          }
          echo '<br>';
          echo $file;
        }

        unlink($file_dir);
      }


      // print_r($array[1]);
      echo '<br>';
      print_r($file_type);

      echo '<br>';
      print_r($_FILES['zip_file']);
    }
  }


  // if (isset($_FILES['file'])) {
  //   $errors = array();
  //   $file_name = $_FILES['file']['name'];
  //   $file_size = $_FILES['file']['size'];
  //   $file_tmp = $_FILES['file']['tmp_name'];
  //   $file_type = $_FILES['file']['type'];

  //   $file_ext = strtolower(end(explode('.', $file_name)));

  //   $extensions = array("zip");

  //   if (in_array($file_ext, $extensions) === false) {
  //     $errors[] = "extension not allowed, please choose a JPEG or PNG file.";
  //   }

  //   if ($file_size > 2097152) {
  //     $errors[] = 'File size must be excately 2 MB';
  //   }

  //   if (empty($errors) == true) {
  //     move_uploaded_file($file_tmp, "uploads/" . $file_name);
  //     echo "Success";
  //   } else {
  //     print_r($errors);
  //   }
  // }
  ?>

  <?php
  if (isset($output)) {
    echo $output;
  }
  ?>

</body>

</html>