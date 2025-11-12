<?php
include "db_connect.php";

function parsePrice($price) {
    return (int) str_replace(['₱', ',', ' '], '', $price);
}

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
$sql = "SELECT * FROM phone $whereSql";
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
    <title>Swastecha Points Redemption Store</title>
    <link rel="stylesheet" href="styles.css?v=2">
</head>
<body>
    <div class="top-bar">
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

<!-- BUY NOW MODAL -->
<div class="buy-modal" id="buyModal">
  <div class="buy-content">
    <button class="close-btn" id="closeBuyModal">&times;</button>

    <!-- PRODUCT INFO SECTION -->
    <div class="buy-header">
      <img id="productImage" src="" alt="Product Image" class="buy-img">
      <div class="buy-info">
        <h3 id="productName"></h3>
        <p id="productBrand"></p>
        <p id="productPrice"></p>
        <p id="productPoints"></p>
        <p id="productGetPoints"></p>

        <div class="quantity-control">
          <button id="qtyMinus">−</button>
          <span id="qtyValue">1</span>
          <button id="qtyPlus">+</button>
        </div>
      </div>
    </div>

    <hr>

    <!-- SPECS SECTION -->
    <ul class="specs">
      <li><strong>Processor:</strong> <span id="specProcessor"></span></li>
      <li><strong>OS:</strong> <span id="specOS"></span></li>
      <li><strong>Resolution:</strong> <span id="specResolution"></span></li>
      <li><strong>Dimension:</strong> <span id="specDimension"></span></li>
      <li><strong>Camera:</strong> <span id="specCamera"></span></li>
      <li><strong>Battery:</strong> <span id="specBattery"></span></li>
    </ul>

<!-- OPTIONS -->
<div class="option-group">
    <label for="memorySelect"><strong>Memory:</strong></label>
    <select id="memorySelect">
        <option value="128" selected>128GB</option> 
        <option value="256">256GB</option>
    </select>
</div>


<!-- TOTALS SECTION -->
    <div class="total-section">
        <p><strong>Total Price:</strong> <span id="totalPrice">₱0</span></p>
        <p><strong>Total Points:</strong> <span id="totalPoints">0 P</span></p>
        <p><strong>Total Get Points:</strong> <span id="totalGetPoints">GET 0 P</span></p>
    </div>

<!-- ACTION BUTTONS -->
    <div class="modal-actions product-cards">
        <button id="addToCartBtn" class="buy-btn">Add to Cart</button>
        <a id="buyNowLink" href="#">
            <button class="buyNowBtn">Buy now</button>
        </a>
    </div>
  </div>
</div>




    <div class="ads-banner">
        <div class="ads-track">
            <img src="ads/ad.png" alt="Ad 0">
            <img src="ads/ad1.png" alt="Ad 1">          
        </div>
        <div class="ads-dots"></div>
    </div>

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
                <div class="product-card" data-brand="<?= htmlspecialchars($p['brand']) ?>" data-price="<?= htmlspecialchars($p['price']) ?>" class="product-card" 
                     data-brand="<?= htmlspecialchars($p['brand']) ?>" 
                     data-price="<?= htmlspecialchars($p['price']) ?>"
                     data-name="<?= htmlspecialchars($p['name']) ?>"
                     data-img="<?= htmlspecialchars($p['img']) ?>"
                     data-points="<?= htmlspecialchars($p['points']) ?>"
                     data-getpoints="<?= htmlspecialchars($p['getpoints']) ?>"
                     data-processor="<?= htmlspecialchars($p['processor']) ?>"
                     data-os="<?= htmlspecialchars($p['os']) ?>"
                     data-resolution="<?= htmlspecialchars($p['resolution']) ?>"
                     data-dimension="<?= htmlspecialchars($p['dimention']) ?>"
                     data-camera="<?= htmlspecialchars($p['camera']) ?>"
                     data-battery="<?= htmlspecialchars($p['battery']) ?>"
                     data-product-id="<?= htmlspecialchars($p['product_id']) ?>">
                    <img src="<?= htmlspecialchars($p['img']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                    <div class="product-title"><?= htmlspecialchars($p['name']) ?></div>
                    <div class="product-prices">
                        <span><?= htmlspecialchars($p['price']) ?></span> 
                        <span><?= htmlspecialchars($p['points']) ?></span>
                    </div>
                    <div class="product-getpoints"><?= htmlspecialchars($p['getpoints']) ?></div>
                    <button class="buy-btn">Buy now</button>
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
                <strong>Swastecha</strong>
                <ul>
                    <li>Reliable online shop to purchase phones.</li>
                </ul>
            </div>
            <div>
                <strong>Contact</strong>
                <ul>
                    <li>Email: support@swastecha.com</li>
                    <li>Phone: +63 1234-567-7898</li>
                </ul>
            </div>
            <div>
                <strong>Follow Us</strong>
                <ul>
                    <li>Facebook</li>
                    <li>Instagram</li>
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