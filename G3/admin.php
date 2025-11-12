<?php
// Include the database connection file (db_connect.php contains $conn = new mysqli(...) )
include "db_connect.php";

// Default SQL query: this will select all rows from the 'students' table
$sql = "SELECT * FROM phone";

// Initialize variables for search input (text box) and filter option (dropdown)
$search = "";
$filter = "";

// Check if the user submitted the search form (using GET method)
if (isset($_GET['search']) && isset($_GET['filter'])) {
    // Get the search keyword from the input field
    $search = $_GET['search'];
    // Get the selected column to filter by (student_no, fullname, course, year_level)
    $filter = $_GET['filter'];

    // If both the keyword and filter are not empty
    if (!empty($search) && !empty($filter)) {
        // Modify the SQL query to include a WHERE condition
        // Example: SELECT * FROM students WHERE fullname LIKE '%Juan%'
        $sql = "SELECT * FROM phone WHERE $filter LIKE '%$search%'";
    }
}

// Execute the final SQL query and store the result set in $result
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        table {
            table-layout: fixed; 
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            vertical-align: middle !important;
            text-align: center;
            word-wrap: break-word;
        }
        thead th {
            white-space: nowrap;
        }
        td img {
            width: 60px;
            height: auto;
            border-radius: 5px;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .table-dark th{
            font-size: 12px;
        }
    </style>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white text-center">
            <h3>Product List</h3>
        </div>
        <div class="card-body">
            
            <!-- Search Form -->
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-4">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="Enter search keyword">
                </div>

                <div class="col-md-3">
                    <select name="filter" class="form-select">
                        <option value="">-- Select Filter --</option>
                        <option value="brand" <?= ($filter=="brand")?"selected":"" ?>>Brand</option>
                        <option value="name" <?= ($filter=="name")?"selected":"" ?>>Name</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <button type="submit" class="btn btn-success w-100">Search</button>
                </div>
                <div class="col-md-2">
                    <a href="admin.php" class="btn btn-secondary w-100">Reset</a>
                </div>
            </form>
            <div class="mb-3">
                <a href="create.php" class="btn btn-success">+ Add New Product</a>
            </div>
            <!-- Student Table -->
            <table class="table table-bordered table-striped text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Brand</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Points</th>
                        <th>P Points</th>
                        <th>img</th>
                        <th>Processor</th>
                        <th>Os</th>
                        <th>Resolution</th>
                        <th>Dimention</th>
                        <th>Camera</th>
                        <th>Battery</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>".$row["id"]."</td>
                                    <td>".$row["brand"]."</td>
                                    <td>".$row["name"]."</td>
                                    <td>".$row["price"]."</td>
                                    <td>".$row["points"]."</td>
                                    <td>".$row["getpoints"]."</td>
                                    <td>".$row["img"]."</td>
                                    <td>".$row["processor"]."</td>
                                    <td>".$row["os"]."</td>
                                    <td>".$row["resolution"]."</td>
                                    <td>".$row["dimention"]."</td>
                                    <td>".$row["camera"]."</td>
                                    <td>".$row["battery"]."</td>
                                    <td>".$row["stock"]."</td>
                                    <td>
                                        <a href='edit.php?id=".$row["id"]."' class='btn btn-sm btn-warning'>Edit</a>
                                        <a href='deleted.php?id=".$row["id"]."' class='btn btn-sm btn-danger' onclick=\"return confirm('Are you sure you want to delete this student?');\">Delete</a>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-muted'>No records found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
