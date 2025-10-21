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
