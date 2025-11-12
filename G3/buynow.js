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

    // Function to parse points from display (e.g., "50,000 P" -> 50000)
    function parsePoints(displayText) {
        return parseInt(displayText.replace(/[^\d]/g, '')) || 0;  // Remove non-digits
    }

    // Update final_points when payment method changes
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'point') {
                const pointsDisplay = document.getElementById('totalPointsDisplay');
                if (pointsDisplay) {
                    const requiredPoints = parsePoints(pointsDisplay.textContent);
                    document.getElementById('finalPointsInput').value = requiredPoints;
                }
            }
        });
    });

    // Validate points on form submit
    function validatePoints(event) {
        const pointOption = document.getElementById('point');
        if (pointOption && pointOption.checked) {
            const requiredPointsInput = document.getElementById('requiredPoints');
            const requiredPoints = parseInt(requiredPointsInput.value) || 0;
            const userPointsInput = document.getElementById('userPoints');
            const userPoints = parseInt(userPointsInput.value) || 0;

            if (userPoints < requiredPoints) {
                event.preventDefault();  // Stop form submission
                alert('You do not have enough points (' + requiredPoints.toLocaleString() + ' P required) to purchase this item.');
                return false;
            }
        }
        return true;
    }

    // Attach validation to form submit
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', validatePoints);
    }

    // Handle success/error messages from PHP redirect
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    const message = urlParams.get('message');

    if (status && message) {
        alert(status.toUpperCase() + ": " + decodeURIComponent(message)); 
        history.replaceState(null, '', window.location.pathname + window.location.search.replace(/&status=[^&]*&message=[^&]*/, ''));
    }

    // Update display when memory is selected
    document.querySelectorAll('input[name="memory"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const selectedMemory = this.value;
            const productNameElement = document.querySelector('.product-name strong');
            if (productNameElement) {
                // Extract base name from current text (remove existing memory suffix)
                const currentText = productNameElement.textContent;
                const baseName = currentText.replace(/ \$(128|256)GB\$$/, ''); // Remove " (128GB)" or " (256GB)"
                productNameElement.textContent = baseName + (selectedMemory === '256' ? ' (256GB)' : ' (128GB)');
            }
        });
    });
});