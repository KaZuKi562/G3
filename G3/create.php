<?php
include "db_connect.php"; // include database connection

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id']; 
    $brand = $_POST['brand']; 
    $name = $_POST['name']; 
    $price = $_POST['price']; 
    $points = $_POST['points']; 
    $getpoints = $_POST['getpoints']; 
    $img = $_POST['img']; 
    $processor = $_POST['processor']; 
    $os = $_POST['os']; 
    $resolution = $_POST['resolution']; 
    $dimention = $_POST['dimention']; 
    $camera = $_POST['camera']; 
    $battery = $_POST['battery']; 
    $stock = $_POST['stock']; 

     // ✅ Check if student number already exists
     $check_sql = "SELECT product_id FROM phone WHERE product_id = '$product_id'";
     $check_result = mysqli_query($conn, $check_sql);

     if (mysqli_num_rows($check_result) > 0) {
        echo "<div class='alert alert-warning text-center'>
                Product number <b>$product_id</b> already exists!
              </div>";
    } else {
        // ✅ Insert query
        $sql = "INSERT INTO phone (product_id, brand, name, price, points, getpoints, img, processor, os, resolution, dimention, camera, battery, stock) 
                VALUES ('$product_id', '$brand', '$name', '$price', '$points', '$getpoints', '$img', '$processor', '$os', '$resolution', '$dimention', '$camera', '$battery', '$stock')";

        if (mysqli_query($conn, $sql)) {
            echo "<div class='alert alert-success text-center'>New product added successfully!</div>";
        } else {
            echo "<div class='alert alert-danger text-center'>Error: " . mysqli_error($conn) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Student</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Add New Product</h4>
        </div>
        <div class="card-body">
            <form method="POST">
                <!-- Student Number -->
                <div class="mb-3">
                    <label class="form-label">Prodict Id</label>
                    <input type="text" name="product_id" class="form-control" required>
                </div>

                <!-- Fullname -->
                <div class="mb-3">
                    <label class="form-label">Brand</label>
                    <input type="text" name="brand" class="form-control" required>
                </div>

                <!-- Course (Dropdown) -->
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <!-- Year Level -->
                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="text" name="price" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Points</label>
                    <input type="text" name="points" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">P Points</label>
                    <input type="text" name="getpoints" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">img location</label>
                    <input type="text" name="img" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Processor</label>
                    <input type="text" name="processor" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Os</label>
                    <input type="text" name="os" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Resolution</label>
                    <input type="text" name="resolution" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Dimention</label>
                    <input type="text" name="dimention" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Camera</label>
                    <input type="text" name="camera" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Battery</label>
                    <input type="text" name="battery" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Stock</label>
                    <input type="text" name="stock" class="form-control" required>
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-between">
                    <a href="admin.php" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn btn-success">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
