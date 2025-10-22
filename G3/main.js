function showTab(tabName) {
    // Highlight active tab
    document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
    document.querySelector(`.tab[onclick="showTab('${tabName}')"]`).classList.add('active');
    // For demo: In a real app, filter products by tab/category here
}
// Optionally: Add filter logic in JS for checkboxes/radios if desired

// ====== ADS BANNER SLIDER ======
const track = document.querySelector('.ads-track');
const dotsContainer = document.querySelector('.ads-dots');
const slides = document.querySelectorAll('.ads-track img');
let currentIndex = 0;

// Create dots dynamically
slides.forEach((_, i) => {
  const dot = document.createElement('span');
  dot.addEventListener('click', () => goToSlide(i));
  dotsContainer.appendChild(dot);
});

const dots = dotsContainer.querySelectorAll('span');
updateDots();

// Function to move the track
function goToSlide(index) {
  currentIndex = index;
  track.style.transform = `translateX(-${index * 100}%)`;
  updateDots();
}

// Highlight the active dot
function updateDots() {
  dots.forEach((dot, i) => {
    dot.classList.toggle('active', i === currentIndex);
  });
}

// Swipe support (mobile gesture)
let startX = 0;
track.addEventListener('touchstart', e => startX = e.touches[0].clientX);
track.addEventListener('touchend', e => {
  let endX = e.changedTouches[0].clientX;
  if (endX < startX - 50) nextSlide(); // swipe left
  if (endX > startX + 50) prevSlide(); // swipe right
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

//page reload for filtering
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
    let totalPrice = 0, totalPoints = 0;
    let totalGetPoints = 0;
    cart.forEach((item, idx) => {
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
                <div class="cart-item-title">${item.name} <br><small>${item.brand}</small></div>
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
    itemsDiv.innerHTML = html;
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
        // If already in cart, just increment qty
        let found = cart.find(item => item.name === prod.name);
        if (found) found.qty++;
        else cart.push(prod);
        setCart(cart);
        updateCartBadge();
    });
});

// Open/close cart modal
document.getElementById('cartIcon').addEventListener('click', showCartModal);
document.getElementById('closeCart').addEventListener('click', hideCartModal);
document.getElementById('cartModal').addEventListener('click', function(e){
    if (e.target === this) hideCartModal();
});

// Cart popup button events (event delegation)
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

// On page load
updateCartBadge();