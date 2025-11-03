document.addEventListener('DOMContentLoaded', function() {
    const shippingBtn = document.getElementById('shippingBtn');
    const pickupBtn = document.getElementById('pickupBtn');
    const pickupInfoDiv = document.getElementById('pickupInfo');
    const shippingFieldsDiv = document.getElementById('shippingFields');

    if (shippingBtn && pickupBtn) {
        function toggleDeliveryOption(selectedOption) {
            if (selectedOption === 'shipping') {
                shippingBtn.classList.add('active');
                pickupBtn.classList.remove('active');
                
                pickupInfoDiv.style.display = 'none';
                shippingFieldsDiv.style.display = 'block'; 
            } else { 
                pickupBtn.classList.add('active');
                shippingBtn.classList.remove('active');


                shippingFieldsDiv.style.display = 'none';
                pickupInfoDiv.style.display = 'block'; 
            }
        }

        shippingBtn.addEventListener('click', () => toggleDeliveryOption('shipping'));
        pickupBtn.addEventListener('click', () => toggleDeliveryOption('pickup'));
    }
    function getCart() { return JSON.parse(localStorage.getItem('cart') || '[]'); }
    function updateCartBadge() {
      const cart = getCart();
      const badge = document.getElementById('cartBadge');
      const totalQty = cart.reduce((sum, item) => sum + item.qty, 0);
      if (badge) { 
          badge.textContent = totalQty;
          badge.style.display = totalQty > 0 ? 'inline-block' : 'none';
      }
    }
    updateCartBadge();
});

function validatePoints(event) {
    const pointOption = document.getElementById('point');
    if (pointOption && pointOption.checked) {
        const requiredPointsInput = document.getElementById('requiredPoints');
        const requiredPoints = parseInt(requiredPointsInput.value);
        const userPointsInput = document.getElementById('userPoints');
        const userPoints = parseInt(userPointsInput.value);

        if (userPoints < requiredPoints) {
            event.preventDefault(); 
            alert('You do not have enough points (' + requiredPoints.toLocaleString() + ' P required) to purchase this item.');
            
            return false;
        }
    }
    return true; 
}
document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    const message = urlParams.get('message');

    if (status && message) {
        alert(status.toUpperCase() + ": " + decodeURIComponent(message)); 
        history.replaceState(null, '', window.location.pathname + window.location.search.replace(/&status=[^&]*&message=[^&]*/, ''));
    }
});