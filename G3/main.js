function showTab(tabName) {
    document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
    document.querySelector(`.tab[onclick="showTab('${tabName}')"]`).classList.add('active');
}


//ads sliddder
const track = document.querySelector('.ads-track');
const dotsContainer = document.querySelector('.ads-dots');

if (track && dotsContainer) {
    const slides = document.querySelectorAll('.ads-track img');
    let currentIndex = 0;

    slides.forEach((_, i) => {
      const dot = document.createElement('span');
      dot.addEventListener('click', () => goToSlide(i));
      dotsContainer.appendChild(dot);
    });

    const dots = dotsContainer.querySelectorAll('span');
    updateDots();

    function goToSlide(index) {
      currentIndex = index;
      track.style.transform = `translateX(-${index * 100}%)`;
      updateDots();
    }

    function updateDots() {
      dots.forEach((dot, i) => {
        dot.classList.toggle('active', i === currentIndex);
      });
    }

    let startX = 0;
    track.addEventListener('touchstart', e => startX = e.touches[0].clientX);
    track.addEventListener('touchend', e => {
      let endX = e.changedTouches[0].clientX;
      if (endX < startX - 50) nextSlide(); 
      if (endX > startX + 50) prevSlide(); 
    });

    function nextSlide() {
      currentIndex = (currentIndex + 1) % slides.length;
      goToSlide(currentIndex);
    }
    function prevSlide() {
      currentIndex = (currentIndex - 1 + slides.length) % slides.length;
      goToSlide(currentIndex);
    }

    // Auto-slide every 4s
    setInterval(nextSlide, 4000);
}

//filtering
document.querySelectorAll('#filterForm input[type="checkbox"]').forEach(cb => {
  cb.addEventListener('change', () => {
    document.getElementById('filterForm').submit();
  });
});

function getCart() {
    return JSON.parse(localStorage.getItem('cart') || '[]');
}
function setCart(cart) {
    localStorage.setItem('cart', JSON.stringify(cart));
}
function updateCartBadge() {
    const cart = getCart();
    const badge = document.getElementById('cartBadge');
    const totalQty = cart.reduce((sum, item) => sum + item.qty, 0);
    badge.textContent = totalQty;
    badge.style.display = totalQty > 0 ? 'inline-block' : 'none';
}
function showCartModal() {
    renderCartPopup();
    document.getElementById('cartModal').classList.add('open');
}
function hideCartModal() {
    document.getElementById('cartModal').classList.remove('open');
}
function renderCartPopup() {
    const cart = getCart();
    const itemsDiv = document.getElementById('cartItems');
    if (!cart.length) {
        itemsDiv.innerHTML = "<p>Your cart is empty.</p>";
        document.getElementById('cartSummary').innerHTML = '';
        return;
    }

    let html = '';
    let totalPrice = 0, totalPoints = 0, totalGetPoints = 0;

    cart.forEach((item, idx) => {
        // ✅ Add this guard
        if (!item.price || !item.points || !item.getpoints) return;

        const price = parseInt(item.price.replace(/[₱,]/g, '')) * item.qty;
        const points = parseInt(item.points.replace(/[, P]/g, '')) * item.qty;
        const getpoints = parseInt(item.getpoints.replace(/[GET ,P]/g, '')) * item.qty;

        totalPrice += price;
        totalPoints += points;
        totalGetPoints += getpoints;

        html += `
        <div class="cart-item" data-index="${idx}">
            <img src="${item.img}" alt="${item.name}">
            <div style="flex:1">
                <div class="cart-item-title">${item.name} (${item.memory}GB)<br><small>${item.brand}</small></div>
                <div>${item.price} &nbsp; ${item.points}</div>
                <div>${item.getpoints}</div>
                <div class="cart-qty-group">
                    <button class="cart-qty-btn minus">-</button>
                    <span style="margin:0 8px;">${item.qty}</span>
                    <button class="cart-qty-btn plus">+</button>
                    <button class="remove-btn">Remove</button>
                </div>
            </div>
        </div>`;
    });

    itemsDiv.innerHTML = html || "<p>Your cart is empty.</p>";
    document.getElementById('cartSummary').innerHTML = `
        <div><strong>Total</strong></div>
        <div>₱${totalPrice.toLocaleString()}</div>
        <div>${totalPoints.toLocaleString()} P</div>
        <div>GET ${totalGetPoints.toLocaleString()} P</div>
    `;
}


// Handle Add to Cart
document.querySelectorAll('.cart-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const card = btn.closest('.product-card');
        const prod = {
            brand: card.getAttribute('data-brand'),
            name: card.getAttribute('data-name'),
            price: card.getAttribute('data-price'),
            img: card.getAttribute('data-img'),
            points: card.getAttribute('data-points'),
            getpoints: card.getAttribute('data-getpoints'),
            qty: 1
        };
        let cart = getCart();
        let found = cart.find(item => item.name === prod.name);
        if (found) found.qty++;
        else cart.push(prod);
        setCart(cart);
        updateCartBadge();
    });
});

document.getElementById('cartIcon').addEventListener('click', showCartModal);
document.getElementById('closeCart').addEventListener('click', hideCartModal);
document.getElementById('cartModal').addEventListener('click', function(e){
    if (e.target === this) hideCartModal();
});

document.getElementById('cartItems').addEventListener('click', function(e){
    const cart = getCart();
    const idx = e.target.closest('.cart-item') ? parseInt(e.target.closest('.cart-item').getAttribute('data-index')) : -1;
    if (idx === -1) return;
    if (e.target.classList.contains('plus')) {
        cart[idx].qty++;
    } else if (e.target.classList.contains('minus')) {
        if (cart[idx].qty > 1) cart[idx].qty--;
    } else if (e.target.classList.contains('remove-btn')) {
        cart.splice(idx, 1);
    }
    setCart(cart);
    updateCartBadge();
    renderCartPopup();
});

updateCartBadge();


let qty = 1;
let basePrice = 0;
let basePoints = 0;
let baseGetPoints = 0;
let currentProductId = null;

document.querySelectorAll('.product-card > .buy-btn').forEach(button => {
    button.addEventListener('click', function() {
        const productCard = button.closest('.product-card');

        // Store product ID
        currentProductId = productCard.dataset.productId;

        // ✅ Initialize Buy Now link right away
        const buyNowLink = document.getElementById('buyNowLink');
        buyNowLink.href = "BuyNow.php?product_id=" + encodeURIComponent(currentProductId);

        document.getElementById('productImage').src = productCard.dataset.img;
        document.getElementById('productBrand').textContent = productCard.dataset.brand;
        document.getElementById('productPrice').textContent = `${productCard.dataset.price}`;
        document.getElementById('productPoints').textContent = `${productCard.dataset.points} `;
        document.getElementById('productGetPoints').textContent = `GET ${productCard.dataset.getpoints} `;
        document.getElementById('specProcessor').textContent = productCard.dataset.processor;
        document.getElementById('specOS').textContent = productCard.dataset.os;
        document.getElementById('specResolution').textContent = productCard.dataset.resolution;
        document.getElementById('specDimension').textContent = productCard.dataset.dimension;
        document.getElementById('specCamera').textContent = productCard.dataset.camera;
        document.getElementById('specBattery').textContent = productCard.dataset.battery;

        // Base values
        basePrice = parseInt(productCard.dataset.price.replace(/[₱,]/g, '')) || 0;
        basePoints = parseInt(productCard.dataset.points.replace(/[^\d]/g, '')) || 0;
        baseGetPoints = parseInt(productCard.dataset.getpoints.replace(/[^\d]/g, '')) || 0;

        qty = 1;
        document.getElementById('qtyValue').textContent = '1';

        const memorySelect = document.getElementById('memorySelect');
        memorySelect.value = '128';

        updateTotals();

        document.getElementById('buyModal').style.display = 'flex';
    });
});





document.getElementById('qtyMinus').addEventListener('click', () => {
    if (qty > 1) qty--;
    document.getElementById('qtyValue').textContent = qty;
    updateTotals();
});

document.getElementById('qtyPlus').addEventListener('click', () => {
    qty++;
    document.getElementById('qtyValue').textContent = qty;
    updateTotals();
});

document.getElementById('memorySelect').addEventListener('change', updateTotals);


function updateTotals() {
    const memory = document.getElementById('memorySelect').value;

    let price = basePrice;
    let points = basePoints;
    let getpoints = baseGetPoints;

    if (memory === '256') {
        price += 10000;
        points += 7000;
        getpoints += 4500;
    }

    const totalPrice = price * qty;
    const totalPoints = points * qty;
    const totalGetPoints = getpoints * qty;

    document.getElementById('totalPrice').textContent = '₱' + totalPrice.toLocaleString();
    document.getElementById('totalPoints').textContent = totalPoints.toLocaleString() + ' P';
    document.getElementById('totalGetPoints').textContent = 'GET ' + totalGetPoints.toLocaleString() + ' P';

    if (currentProductId) {
        const buyNowLink = document.getElementById('buyNowLink');
        buyNowLink.href = `BuyNow.php?` +
                           `product_id=${currentProductId}` +
                           `&final_price=${totalPrice}` +
                           `&final_points=${totalPoints}` +
                           `&final_getpoints=${totalGetPoints}` +
                           `&qty=${qty}` +
                           `&memory=${memory}`;
    }
}


// Add to Cart button
document.getElementById('addToCartBtn').addEventListener('click', function() {
    const currentQty = parseInt(document.getElementById('qtyValue').textContent);
    const selectedMemory = document.getElementById('memorySelect').value;

    const finalPrice = document.getElementById('totalPrice').textContent;
    const finalPoints = document.getElementById('totalPoints').textContent;
    const finalGetPoints = document.getElementById('totalGetPoints').textContent;

    const name = document.getElementById('productName').textContent.trim();
    const brand = document.getElementById('productBrand').textContent.trim();
    const img = document.getElementById('productImage').src;

    const productCard = document.querySelector(`.product-card[data-name="${name}"][data-brand="${brand}"]`);

    const unitPriceText = finalPrice.replace(/[₱,]/g, '');
    const unitPointsText = finalPoints.replace(/[^\d]/g, '');
    const unitGetPointsText = finalGetPoints.replace(/[^\d]/g, '');

    const unitPrice = (parseInt(unitPriceText) / currentQty) || 0;
    const unitPoints = (parseInt(unitPointsText) / currentQty) || 0;
    const unitGetPoints = (parseInt(unitGetPointsText) / currentQty) || 0;

    if (unitPrice === 0) {
        alert("Please select a valid configuration before adding to cart.");
        return;
    }

    const prod = {
        id: currentProductId,           
        name: name,
        brand: brand,
        memory: selectedMemory,  
        price: '₱' + unitPrice.toLocaleString(),
        points: unitPoints.toLocaleString() + ' P',
        getpoints: 'GET ' + unitGetPoints.toLocaleString() + ' P',
        img: img,
        qty: currentQty
    };

    let cart = getCart();

    let found = cart.find(item => item.id === prod.id && item.memory === prod.memory);

    if (found) {
        found.qty += currentQty;
    } else {
        cart.push(prod);
    }

    setCart(cart);
    updateCartBadge();

    alert(`${currentQty} × ${name} (${selectedMemory}GB) added to cart!`);
    document.getElementById('buyModal').style.display = 'none';
});




document.getElementById('closeBuyModal').addEventListener('click', function() {
    document.getElementById('buyModal').style.display = 'none';
});


const checkoutBtn = document.getElementById('checkoutBtn');
if (checkoutBtn) {
  checkoutBtn.addEventListener('click', function() {
      window.location.href = 'checkout.php'; // Replace with your checkout page
  });
}

