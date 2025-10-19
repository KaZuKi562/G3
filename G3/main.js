function showTab(tabName) {
    // Highlight active tab
    document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
    document.querySelector(`.tab[onclick="showTab('${tabName}')"]`).classList.add('active');
    // For demo: In a real app, filter products by tab/category here
}
// Optionally: Add filter logic in JS for checkboxes/radios if desired

window.addEventListener('resize', adjustAdHeight);

function adjustAdHeight() {
    const adBanner = document.querySelector('.ads-banner');
    const width = adBanner.offsetWidth;
    
    // Maintain 16:9 aspect ratio
    const height = width * (9 / 16);
    adBanner.style.height = `${height}px`;
}

// Call the function initially to set the height
adjustAdHeight();
