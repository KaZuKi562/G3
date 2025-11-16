    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Swastecha - Home</title>
    <style>
    /* Base Reset */
    * { margin:0; padding:0; box-sizing:border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
    body { background:#fff; color:#1d1d1f; line-height:1.5; }
    a { text-decoration:none; color:inherit; }

    /* Top Navigation */
    .navbar {
    display:flex; justify-content:space-between; align-items:center; padding:16px 32px; position:sticky; top:0; background:#fff; z-index:100; border-bottom:1px solid #e5e5e7;
    }
    .navbar .logo { font-weight:700; font-size:24px; color:#000; }
    .navbar .nav-links { display:flex; gap:24px; }
    .navbar .nav-links a { font-weight:500; font-size:16px; transition:0.3s; }
    .navbar .nav-links a:hover { color:#0071e3; }

    /* Hero Section */
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
    color: #fff;
    z-index: 1;
    max-width: 90%;
    }

    .hero-content h1 {
    font-size: 64px;
    font-weight: 700;
    margin-bottom: 16px;
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

    /* Section Title */
    .section-title { text-align:center; margin:80px 0 40px; font-size:32px; font-weight:700; }

    /* Product Showcase */
    .products {
    display:grid; grid-template-columns:repeat(auto-fit, minmax(280px,1fr)); gap:40px; padding:0 32px 80px;
    }
    .product-card {
    display:flex; flex-direction:column; align-items:center; text-align:center; padding:24px; transition:0.3s;
    }
    .product-card img { width:220px; border-radius:16px; margin-bottom:16px; }
    .product-card h3 { font-size:20px; margin-bottom:8px; }
    .product-card p { font-size:16px; color:#0071e3; margin-bottom:16px; }
    .product-card button {
    padding:10px 20px; border:none; border-radius:10px; background:#000; color:#fff; font-weight:600; cursor:pointer; transition:0.3s;
    }
    .product-card button:hover { background:#333; transform:translateY(-2px); }

    /* Full Width Promo Section */
    .promo {
    display:flex; justify-content:center; align-items:center; text-align:center;
    background:#f5f5f7; padding:80px 32px;
    }
    .promo h2 { font-size:36px; margin-bottom:16px; }
    .promo p { font-size:18px; max-width:600px; margin:0 auto 24px; }
    .promo button { padding:12px 28px; border:none; border-radius:8px; background:#0071e3; color:#fff; font-weight:600; cursor:pointer; }

    /* Footer */
    footer { text-align:center; padding:32px; background:#f5f5f7; color:#6e6e73; font-size:14px; }

    /* Responsive */
    @media(max-width:768px){
    .hero h1 { font-size:36px; }
    .hero p { font-size:16px; }
    }
    </style>
    <link rel="stylesheet" href="nav.css">
    </head>
    <body>
    
    <!-- Navbar -->
    <div class="navbar">
    <div class="logo">Swastecha</div>
    <div class="user-cart" style="position:relative;">
            <a href="javascript:void(0);" id="cartIcon">
                <img src="icon/cart.png" alt="Cart" class="icon">
                <span class="cart-badge" id="cartBadge">0</span>
            </a>
            <span><a href="login.php">Log In</a></span>
            <span>|</span>
            <span><a href="signup.php">Sign Up</a></span>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="hero" style="background-image:url('img/iphone1.webp');">
        <div class="hero-content">
            <h1>Discover the Latest Phones</h1>
            <p>Experience top-notch devices with Swastecha points redemption deals.</p>
            <button onclick="window.location.href='login.php'">Login</button>
        </div>
    </section>

    <!-- Product Section -->
<h2 class="section-title">Featured Products</h2>
<section class="products">
  <div class="product-card">
    <img src="img/iphone1.webp" alt="iPhone">
    <h3>iPhone</h3>
  </div>
  <div class="product-card">
    <img src="img/infinix1.gif" alt="Infinix">
    <h3>Infinix</h3>
  </div>
  <div class="product-card">
    <img src="img/realme.gif" alt="Realme">
    <h3>Realme</h3>
  </div>
</section>

<style>

/* Products Grid */
.products {
  display: flex;
  gap: 20px;           /* space between products */
  justify-content: center; /* center the row */
  flex-wrap: wrap;      /* wrap on small screens */
  padding: 0 32px 80px;
}

/* Product Card */
.product-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: 24px;
  background: #8b8b8bff;
  border-radius: 20px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.08);
  transition: transform 0.3s, box-shadow 0.3s;
  min-width: 220px;  /* ensures cards are visible in one line */
}
.product-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 16px 30px rgba(0,0,0,0.12);
}

/* Product Image */
.product-card img {
  width: 200px;
  border-radius: 16px;
  margin-bottom: 16px;
  transition: transform 0.3s;
}
.product-card:hover img {
  transform: scale(1.05);
}

/* Product Name */
.product-card h3 {
  font-size: 20px;
  margin-bottom: 8px;
}

/* Responsive */
@media(max-width:768px){
  .products {
    flex-direction: column;
    align-items: center;
  }
}
</style>


    <!-- Promo Section -->
    <section class="promo">
    <div>
        <h2>Upgrade & Earn Points</h2>
        <p>Redeem points and enjoy amazing deals on the latest phones. Shop smarter and save more today.</p>
        <button onclick="window.location.href='BuyNow.php'">Learn More</button>
    </div>
    </section>

    <!-- Footer -->
    <footer>
    &copy; 2025 Swastecha. All rights reserved.
    </footer>

    </body>
    </html>
