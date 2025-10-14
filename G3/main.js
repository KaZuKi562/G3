function showTab(tabName) {
    // Highlight active tab
    document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
    document.querySelector(`.tab[onclick="showTab('${tabName}')"]`).classList.add('active');
    // For demo: In a real app, filter products by tab/category here
}
// Optionally: Add filter logic in JS for checkboxes/radios if desired