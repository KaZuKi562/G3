<?php
include "db_connect.php"; // include database connection

// Check if ID is provided in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch student data by ID
    $sql = "SELECT * FROM phone WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc(); // fetch data
    } else {
        echo "<div class='alert alert-danger text-center'>Product not found!</div>";
        exit;
    }
}

// Handle form update
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

    // Update query
    $sql = "UPDATE phone 
            SET product_id='$product_id', name='$name', price='$price', points='$points', getpoints='$getpoints', 
            img='$img', processor='$processor', os='$os', resolution='$resolution', dimention='$dimention', 
            camera='$camera', battery='$battery', stock='$stock'
            WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "<div class='alert alert-success text-center'>Product updated successfully!</div>";
        // Optional: redirect back to index
        // header("Location: index.php"); exit;
    } else {
        echo "<div class='alert alert-danger text-center'>Error: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-warning text-dark">
            <h4 class="mb-0">Edit Product</h4>
        </div>
        <div class="card-body">
            <form method="POST">
                <!-- Student Number -->
                <div class="mb-3">
                    <label class="form-label">Prodict Id</label>
                    <input type="text" name="product_id" class="form-control" value="<?php echo $product['product_id']; ?>" required>
                </div>

                <!-- Fullname -->
                <div class="mb-3">
                    <label class="form-label">Brand</label>
                    <input type="text" name="brand" class="form-control" value="<?php echo $product['brand']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo $product['name']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="text" name="price" class="form-control" value="<?php echo $product['price']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Points</label>
                    <input type="text" name="points" class="form-control" value="<?php echo $product['points']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">P Points</label>
                    <input type="text" name="getpoints" class="form-control" value="<?php echo $product['getpoints']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Img Location</label>
                    <input type="text" name="img" class="form-control" value="<?php echo $product['img']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Processor</label>
                    <input type="text" name="processor" class="form-control" value="<?php echo $product['processor']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Os</label>
                    <input type="text" name="os" class="form-control" value="<?php echo $product['os']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Resolution</label>
                    <input type="text" name="resolution" class="form-control" value="<?php echo $product['resolution']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Dimention</label>
                    <input type="text" name="dimention" class="form-control" value="<?php echo $product['dimention']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Camera</label>
                    <input type="text" name="camera" class="form-control" value="<?php echo $product['camera']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Battery</label>
                    <input type="text" name="battery" class="form-control" value="<?php echo $product['battery']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Stock</label>
                    <input type="text" name="stock" class="form-control" value="<?php echo $product['stock']; ?>" required>
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-between">
                    <a href="admin.php" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn btn-primary">Update Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
