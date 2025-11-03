<?php
$mysqli = new mysqli("localhost", "root", "", "swastecha_db");
if ($mysqli->connect_errno) {
    die("Failed to connect: " . $mysqli->connect_error);
}

// Convert price to numeric for filter matching
function parsePrice($price) {
    return (int) str_replace(['₱', ',', ' '], '', $price);
}

$where = [];
$params = [];
$types = "";

// --- Brand filter (checkbox) ---
if (!empty($_GET['brand'])) {
    $brands = $_GET['brand'];
    $in = implode(',', array_fill(0, count($brands), '?'));
    $where[] = "brand IN ($in)";
    $types .= str_repeat('s', count($brands));
    $params = array_merge($params, $brands);
}

// --- Price filter (checkbox) ---
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

// --- Search filter ---
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
$stmt = $mysqli->prepare($sql);

// Bind params if any
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
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta charset="UTF-8">
    <title>Swastecha Points Redemption Store</title>
    <link rel="stylesheet" href="styles.css?v=2">
</head>
<body>
   <div class="top-bar">
        <div class="brand">Swastecha</div>
       <div class="search-bar">
            <form method="get" action="">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search">
                <!-- Keep all filter values in the search form so they stay checked when searching -->
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
       <div class="user-cart">
            <a href="cart.html">
                <img src="icon/cart.png" alt="Cart" class="icon">
            </a>
            <span><a href="login.php">Log In</a></span>
            <span>|</span>
            <span><a href="signup.php">Sign Up</a></span>
        </div>
    </div>

    <nav class="tabs">
        <a href="index.php">
            <button class="tab">Home</button>
        </a>
        <a href="phone.php">
            <button class="tab active">Cellphone</button>
        </a>
        <a href="tablet.php">
            <button class="tab">Tablet</button>
         </a>
        <a href="laptop.php">
             <button class="tab">Laptop</button>
        </a>
    </nav>
    
    <div class="main-container">
        <header>
        </header>
    <div class="content">
    <aside class="filters">
    <h2>Filters</h2>
    <form method="get" id="filterForm">
        <!-- Keep search term in filter form so it stays in the search bar when filtering -->
        <?php if ($search !== ''): ?>
            <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
        <?php endif; ?>
        <div class="filter-group">
            <strong>Brands</strong>
                <div> 
                    <input type="checkbox" name="brand[]" value="Apple" id="apple"
                        <?= (isset($_GET['brand']) && in_array('Apple', $_GET['brand'])) ? 'checked' : '' ?>>
                    <label for="apple">Apple</label>
                </div>
                <div>
                    <input type="checkbox" name="brand[]" value="Infinix" id="infinix"
                        <?= (isset($_GET['brand']) && in_array('Infinix', $_GET['brand'])) ? 'checked' : '' ?>>
                    <label for="infinix">Infinix</label>
                </div>
                <div>
                    <input type="checkbox" name="brand[]" value="Realme" id="realme"
                        <?= (isset($_GET['brand']) && in_array('Realme', $_GET['brand'])) ? 'checked' : '' ?>>
                    <label for="realme">Realme</label>
                </div>
        </div>
        <hr>
        <div class="filter-group">
            <strong>Price</strong>
                <div>
                    <input type="checkbox" name="price[]" value="under10k" id="under10k"
                        <?= (isset($_GET['price']) && in_array('under10k', $_GET['price'])) ? 'checked' : '' ?>>
                    <label for="under10k">Under ₱10,000</label>
                </div>
                <div>
                    <input type="checkbox" name="price[]" value="10k30k" id="10k30k"
                        <?= (isset($_GET['price']) && in_array('10k30k', $_GET['price'])) ? 'checked' : '' ?>>
                    <label for="10k30k">₱10,000 to ₱30,000</label>
                </div>
                <div>
                    <input type="checkbox" name="price[]" value="above50k" id="above50k"
                        <?= (isset($_GET['price']) && in_array('above50k', $_GET['price'])) ? 'checked' : '' ?>>
                    <label for="above50k">Above ₱50,000</label>
                </div>
        </div>
    </form>
    </aside>

        <section class="product-list" id="products">
            <?php foreach($filteredProducts as $p): ?>
            <div class="product-card" data-brand="<?= htmlspecialchars($p['brand']) ?>" data-price="<?= htmlspecialchars($p['price']) ?>">
                <img src="<?= htmlspecialchars($p['img']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                <div class="product-title"><?= htmlspecialchars($p['name']) ?></div>
                <div class="product-prices">
                    <span><?= htmlspecialchars($p['price']) ?></span> <span><?= htmlspecialchars($p['points']) ?></span>
                </div>
                <div class="product-getpoints"><?= htmlspecialchars($p['getpoints']) ?></div>
                <button class="buy-btn">Buy now</button>
                <button class="cart-btn">Add to cart</button>
            </div>
            <?php endforeach; ?>

            <?php if (empty($filteredProducts)): ?>
            <p style="font-size:18px; color:#666;">No products found matching your filters.</p>
            <?php endif; ?>
        </section>
    </div>
</div>
<section class="features">
        <div>
            <img src="delivery.png" alt="">
            <span>Delivery</span>
            <small>Delivery coverage around Pampanga</small>
        </div>
        <div>
            <img src="payment.png" alt="">
            <span>Secured Payment</span>
            <small>Your payment information is processed securely</small>
        </div>
        <div>
            <img src="support.png" alt="">
            <span>Customer Support</span>
            <small>Customer support to help you all the way</small>
        </div>
    </section>
    <footer>
        <div class="footer-columns">
            <div>
                <strong>3 Group</strong>
                <ul>
                    <li>Customer Support</li>
                    <li>Store Locations</li>
                    <li>Terms of Service</li>
                    <li>Refund Policy</li>
                    <li>Corporate Sales</li>
                    <li>Contact Us</li>
                </ul>
            </div>
            <div>
                <strong>Policies</strong>
                <ul>
                    <li>Privacy Policy</li>
                    <li>Terms and Condition</li>
                    <li>Return and Exchange Policy</li>
                    <li>FAQs</li>
                    <li>Do not sell my personal information</li>
                </ul>
            </div>
            <div>
                <strong>Product Categories</strong>
                <ul>
                    <li>Cellphone</li>
                    <li>Tablet</li>
                    <li>Headset</li>
                    <li>Laptop</li>
                    <li>Corporate Sales</li>
                </ul>
            </div>
        </div>
    </footer>
<script>
document.querySelectorAll('#filterForm input[type="checkbox"]').forEach(cb => {
  cb.addEventListener('change', () => {
    document.getElementById('filterForm').submit();
  });
});
</script>

<script src="main.js"></script>
</body>
</html>