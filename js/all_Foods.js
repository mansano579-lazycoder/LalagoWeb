const foodsGrid = document.getElementById("foodsGrid");
const carouselTrack = document.getElementById("foodsCarousel");
const prevBtn = document.querySelector(".carousel-btn.prev");
const nextBtn = document.querySelector(".carousel-btn.next");

let allFoods = [];
let currentIndex = 0;
let autoScrollInterval = null;

// Fetch foods from Firebase
db.collection("vendor_products")
  .get()
  .then(snapshot => {
    snapshot.forEach(doc => {
      allFoods.push(doc.data());
    });

    // RANDOMIZE
    allFoods.sort(() => 0.5 - Math.random());

    // Render Grid and Carousel
    renderFoodsGrid();
    renderCarousel();

    // Start auto-scroll for carousel
    startAutoScroll();
  });

// Render 4-per-row grid
function renderFoodsGrid() {
  foodsGrid.innerHTML = "";
  allFoods.forEach(food => {
    const div = document.createElement("div");
    div.className = "food-card";
    div.innerHTML = `
      <img src="${food.photo || 'https://via.placeholder.com/300'}" loading="lazy">
      <div class="food-info">
        <div class="food-name">${food.name || 'Unnamed Food'}</div>
        <div class="food-price">₱${food.price || '0.00'}</div>
      </div>
    `;
    foodsGrid.appendChild(div);
  });
}

// Render top carousel
function renderCarousel() {
  if (!carouselTrack) return;
  carouselTrack.innerHTML = "";
  allFoods.forEach(food => {
    const cdiv = document.createElement("div");
    cdiv.className = "carousel-item";
    cdiv.innerHTML = `<img src="${food.photo || 'https://via.placeholder.com/300'}" loading="lazy">`;
    carouselTrack.appendChild(cdiv);
  });
}

// Auto-scroll carousel
function startAutoScroll() {
  if (!carouselTrack) return;

  const slide = () => {
    const itemWidth = carouselTrack.querySelector(".carousel-item").offsetWidth + 12; // include margin
    const visibleCount = Math.floor(carouselTrack.parentElement.offsetWidth / itemWidth);
    const maxIndex = carouselTrack.children.length - visibleCount;

    currentIndex++;
    if (currentIndex > maxIndex) currentIndex = 0;

    carouselTrack.style.transform = `translateX(-${currentIndex * itemWidth}px)`;
  };

  autoScrollInterval = setInterval(slide, 3000);

  // Pause on hover
  carouselTrack.addEventListener("mouseenter", () => clearInterval(autoScrollInterval));
  carouselTrack.addEventListener("mouseleave", () => autoScrollInterval = setInterval(slide, 3000));
}

// Prev/Next buttons
if (prevBtn && nextBtn && carouselTrack) {
  const getItemWidth = () => carouselTrack.querySelector(".carousel-item").offsetWidth + 12;

  nextBtn.addEventListener("click", () => {
    const visibleCount = Math.floor(carouselTrack.parentElement.offsetWidth / getItemWidth());
    const maxIndex = carouselTrack.children.length - visibleCount;

    if (currentIndex < maxIndex) currentIndex++;
    else currentIndex = 0;

    carouselTrack.style.transform = `translateX(-${currentIndex * getItemWidth()}px)`;
  });

  prevBtn.addEventListener("click", () => {
    const visibleCount = Math.floor(carouselTrack.parentElement.offsetWidth / getItemWidth());
    const maxIndex = carouselTrack.children.length - visibleCount;

    if (currentIndex > 0) currentIndex--;
    else currentIndex = maxIndex;

    carouselTrack.style.transform = `translateX(-${currentIndex * getItemWidth()}px)`;
  });
}
