const carouselTrack = document.getElementById('vendorProductsCarousel');
let carouselIndex = 0;
const visibleItems = 6;
let itemWidth = 0;

// ================== LOAD PRODUCTS ==================
function loadProducts(categoryID = null) {
  let query = db.collection("vendor_products");
  if (categoryID) query = query.where("categoryID", "==", categoryID);

  query.get().then(snapshot => {
    carouselTrack.innerHTML = "";

    if (snapshot.empty) {
      carouselTrack.innerHTML = "<p style='padding:20px;'>No products found.</p>";
      return;
    }

    snapshot.docs.forEach(doc => {
      const data = doc.data();
      const photo = data.photo || (data.photos && data.photos[0]) || 'https://via.placeholder.com/250x150';
      const productId = doc.id;

      const item = document.createElement('div');
      item.className = 'carousel-item';
      item.dataset.id = productId; // attach product ID

      // ONLY IMAGE, no title
      item.innerHTML = `<img src="${photo}" alt="Product" style="cursor:pointer;">`;

      carouselTrack.appendChild(item);
    });

    // Calculate item width including margin
    if (carouselTrack.children.length) {
      const style = getComputedStyle(carouselTrack.children[0]);
      const marginRight = parseInt(style.marginRight) || 0;
      itemWidth = carouselTrack.children[0].offsetWidth + marginRight;
    }
  }).catch(err => {
    console.error("Error loading products:", err);
    carouselTrack.innerHTML = "<p style='padding:20px;'>Error loading products.</p>";
  });
}

// ================== CAROUSEL NAVIGATION ==================
const prevBtn = document.querySelector('.carousel-btn.prev');
const nextBtn = document.querySelector('.carousel-btn.next');

prevBtn.addEventListener('click', () => {
  carouselIndex = Math.max(carouselIndex - 1, 0);
  carouselTrack.style.transform = `translateX(-${carouselIndex * itemWidth}px)`;
});

nextBtn.addEventListener('click', () => {
  const maxIndex = Math.max(carouselTrack.children.length - visibleItems, 0);
  carouselIndex = Math.min(carouselIndex + 1, maxIndex);
  carouselTrack.style.transform = `translateX(-${carouselIndex * itemWidth}px)`;
});

// ================== AUTO SLIDE ==================
setInterval(() => {
  if (!carouselTrack.children.length) return;
  const maxIndex = Math.max(carouselTrack.children.length - visibleItems, 0);
  carouselIndex = carouselIndex < maxIndex ? carouselIndex + 1 : 0;
  carouselTrack.style.transform = `translateX(-${carouselIndex * itemWidth}px)`;
}, 3000);

// ================== CLICK DELEGATION ==================
carouselTrack.addEventListener('click', function(e) {
  const item = e.target.closest('.carousel-item');
  if (!item) return;
  const productId = item.dataset.id;
  if (!productId) return;

  // Go to product page
  window.location.href = `foods/product.php?id=${productId}`;
});

// ================== INITIAL LOAD ==================
loadProducts();
