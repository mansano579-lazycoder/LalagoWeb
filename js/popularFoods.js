const carouselTrack = document.getElementById('popularFoodsCarousel');
let carouselIndex = 0;

// Fetch popular foods from Firebase
db.collection("popular_foods")
  .orderBy("sold", "desc") // most sold first
  .limit(12) // get top 12
  .get()
  .then(snapshot => {
    if (snapshot.empty) {
      carouselTrack.innerHTML = '<p style="padding:20px;">No popular foods available.</p>';
      return;
    }

    snapshot.forEach(doc => {
      const food = doc.data();
      const item = document.createElement('div');
      item.className = 'carousel-item';
      item.innerHTML = `
        <img src="${food.photo || 'https://via.placeholder.com/250x150?text=Food'}" alt="${food.name}">
        <h4>${food.name}</h4>
      `;
      carouselTrack.appendChild(item);
    });
  })
  .catch(err => {
    console.error("Error fetching foods:", err);
    carouselTrack.innerHTML = '<p style="padding:20px;">Failed to load popular foods.</p>';
  });

// Carousel buttons
const prevBtn = document.querySelector('.carousel-btn.prev');
const nextBtn = document.querySelector('.carousel-btn.next');

prevBtn.addEventListener('click', () => {
  if (!carouselTrack.children.length) return;
  const itemWidth = carouselTrack.children[0].offsetWidth + 10; // margin-right
  carouselIndex = Math.max(carouselIndex - 1, 0);
  carouselTrack.style.transform = `translateX(-${carouselIndex * itemWidth}px)`;
});

nextBtn.addEventListener('click', () => {
  if (!carouselTrack.children.length) return;
  const itemWidth = carouselTrack.children[0].offsetWidth + 10;
  const maxIndex = carouselTrack.children.length - 6; // show 6 cards at once
  carouselIndex = Math.min(carouselIndex + 1, maxIndex);
  carouselTrack.style.transform = `translateX(-${carouselIndex * itemWidth}px)`;
});
