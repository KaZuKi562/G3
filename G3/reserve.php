<?php
// pre_order.php
include "db_connect.php"; // your database connection
session_start();

$where = [];
$params = [];
$types = "";

//  Brand filter 
if (!empty($_GET['brand'])) {
    $brands = $_GET['brand'];
    $in = implode(',', array_fill(0, count($brands), '?'));
    $where[] = "brand IN ($in)";
    $types .= str_repeat('s', count($brands));
    $params = array_merge($params, $brands);
}

// price filter 
$priceFilterSql = [];
if (!empty($_GET['price'])) {
    foreach ($_GET['price'] as $filter) {
        if ($filter === 'under10k') {
            $priceFilterSql[] = "(CAST(REPLACE(REPLACE(REPLACE(price, '₱', ''), ',', ''), ' ', '') AS UNSIGNED) < 10000)";
        } elseif ($filter === '10k30k') {
            $priceFilterSql[] = "(CAST(REPLACE(REPLACE(REPLACE(price, '₱', ''), ',', ''), ' ', '') AS UNSIGNED) BETWEEN 10000 AND 30000)";
        } elseif ($filter === 'above50k') {
            $priceFilterSql[] = "(CAST(REPLACE(REPLACE(REPLACE(price, '₱', ''), ',', ''), ' ', '') AS UNSIGNED) > 50000)";
        }
    }
    if ($priceFilterSql) {
        $where[] = '(' . implode(' OR ', $priceFilterSql) . ')';
    }
}

// --- search filter ---
$search = '';
if (isset($_GET['search']) && trim($_GET['search']) !== '') {
    $search = trim($_GET['search']);
    $where[] = "(brand LIKE ? OR name LIKE ?)";
    $types .= "ss";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$whereSql = $where ? "WHERE " . implode(" AND ", $where) : "";
$sql = "SELECT * FROM products $whereSql";
$stmt = $conn->prepare($sql);

if ($params) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$filteredProducts = $result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Swastecha - Pre Order</title>
<link rel="stylesheet" href="styles.css">
<style>
/* Base Reset */
* { margin:0; padding:0; box-sizing:border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
body { background:#f0f0f0; color:#1d1d1f; line-height:1.5; }

/* Navbar */
.navbar {
    display:flex; justify-content:space-between; align-items:center; padding:16px 32px; background: #e6e6e6ff;
    border-bottom:1px solid #e5e5e7; position:sticky; top:0; z-index:100;   
}
.navbar .logo { font-weight:700; font-size:24px; }
.navbar .nav-links { display:flex; gap:24px; }
.navbar .nav-links a { font-weight:500; font-size:16px; transition:0.3s; }
.navbar .nav-links a:hover { color:#0071e3; }

/* Page Title */
.page-title { text-align:center; font-size:36px; font-weight:700; margin:60px 0 40px; }

/* Products Grid */
.products {
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
    justify-content: center;
    padding: 0 32px 80px;
}

/* Product Card */
.product-cards {
    background: #fff;
    border-radius: 16px;
    padding: 20px;
    width: 260px;
    text-align:center;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    transition: transform 0.3s, box-shadow 0.3s;
}
.product-cards:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 30px rgba(0,0,0,0.12);
}
.product-cards img {
    width: 100%;
    height: 220px;
    object-fit: cover;
    border-radius: 12px;
    margin-bottom: 16px;
}
.product-cards .price { font-size:18px; font-weight:600; margin-bottom:8px; color:#0071e3; }
.product-cards .points { font-size:16px; color:#111; margin-bottom:8px; }
.product-cards .getpoints { font-size:14px; color:#555; margin-bottom:16px; }

.product-cards button {
    padding:10px 20px;
    border:none;
    border-radius:8px;
    background:#0071e3;
    color:#fff;
    font-weight:600;
    cursor:pointer;
    transition: background 0.3s, transform 0.3s;
}
.product-cards button:hover {
    background:#005bb5;
    transform: translateY(-2px);
}

/* Responsive */
@media(max-width:768px) {
    .products { flex-direction: column; align-items: center; }
}
.tabs {
    position: sticky;
    top: 70px;
    z-index: 100; 
    background: #fff; 
    width: 100%; 
    padding: 10px 0; 
}
.tabs {
    display: flex;
    background: #fff;
    padding-left: 32px;
    border-bottom: 2px solid #eee;
    font-size: 21px;
    gap: 50px;
    margin-bottom: 0;
}
.tab {
    background: none;
    border: none;
    padding: 22px 0 20px 0;
    cursor: pointer;
    color: #222;
    font-size: 21px;
    font-family: inherit;
    outline: none;
    transition: color 0.2s;
}
.tab.active {
    border-bottom: 3px solid #0751d9;
    color: #0751d9;
    font-weight: 700;
}
.hero {
        width: 100%;
        height: 100vh; /* full viewport height */
        background-size: cover; /* image fills the hero */
        background-position: center; /* center the image */
        background-repeat: no-repeat;
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        }

    .hero::before {
    content: "";
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.3); /* dark overlay for text readability */
    }

    .hero::after {
    content:""; position:absolute; inset:0; background:rgba(0,0,0,0.3);
    }
    .hero-content {
    position: relative;
    text-align: center;
    color: #ffffffff;
    z-index: 1;
    max-width: 90%;
    }

    .hero-content h1 {
    font-size: 64px;
    font-weight: 700;
    margin-bottom: 16px;
    color: #fff;
    }

    .hero-content p {
    font-size: 24px;
    margin-bottom: 32px;
    }
    .hero-content button {
    padding: 14px 36px;
    font-size: 18px;
    border: none;
    border-radius: 12px;
    background: #fff;
    color: #111;
    cursor: pointer;
    transition: background 0.3s, transform 0.3s;
    }

    .hero-content img{
        width: 100%;
        height: 500px;
        flex-shrink: 0;
    }
    .hero-content button:hover {
    background: #e0e0e0;
    transform: translateY(-2px);
    }

    @media(max-width:768px) {
    .hero-content h1 { font-size: 36px; }
    .hero-content p { font-size: 18px; }
    .hero-content button { font-size: 16px; padding: 10px 24px; }
    }
</style>

</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <div class="brand">Swastecha</div>
            <div class="search-bar">
            <form method="get" action="">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search">
                <?php
                if (!empty($_GET['brand'])) {
                    foreach ($_GET['brand'] as $b) {
                        echo '<input type="hidden" name="brand[]" value="' . htmlspecialchars($b) . '">';
                    }
                } 
                if (!empty($_GET['price'])) {
                    foreach ($_GET['price'] as $p) {
                        echo '<input type="hidden" name="price[]" value="' . htmlspecialchars($p) . '">';
                    }
                }
                ?>
                <button type="submit">Search</button>
            </form>
        </div>
            <div class="user-cart" style="position:relative;">
                <a href="javascript:void(0);" id="cartIcon">
                    <img src="icon/cart.png" alt="Cart" class="icon">
                    <span class="cart-badge2" id="cartBadge">0</span>
                    <a href="user.php">
                    <img src="icon/user.png" alt="User" class="icon">
                    </a>
                </a>
            </div>
        </div>

     <!-- Cart Modal -->
    <div class="cart-modal" id="cartModal">
        <div class="cart-popup">
            <button class="close-btn" id="closeCart">&times;</button>
            <div id="cartItems"></div>
            <div class="cart-summary" id="cartSummary"></div>
            <button class="checkout-btn"  onclick="window.location.href='checkout.php';">Checkout</button>
        </div>
    </div>
</div>

<!-- Hero Section -->
    <section class="hero" style="background-image:url('img/iphone1.webp');">
        <div class="hero-content">
            <h1>Pre order the Latest Phones</h1>
            <p>Experience top-notch devices with Swastecha points redemption deals.</p>
        </div>
    </section>

    <nav class="tabs active">
        <a href="main_home.php">
            <button class="tab">Cellphone</button>
        </a>
        <a href="reserve.php">
            <button class="tab active">Pre-Order</button>
         </a>
    </nav>

<h1 class="page-title">Pre-Order Products</h1>

<div class="products">
<?php
$sql = "SELECT product_id, name, img, price, points, getpoints FROM products";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo '<div class="product-cards">';
        echo '<img src="'.$row['img'].'" alt="Product Image">';
        echo '<div class="points">'.$row['name'].'</div>';
        echo '<div class="price">'.$row['price'].'</div>';
        echo '<div class="points">Points: '.$row['points'].'</div>';
        echo '<div class="getpoints">'.$row['getpoints'].'</div>';
        echo '<button onclick="window.location.href=\'preorder_checkout.php?product_id='.$row['product_id'].'\'">Pre Order Now</button>';
        echo '</div>';
    }
} else {
    echo '<p>No products available for pre-order.</p>';
}
?>
</div>

</body>
</html>
