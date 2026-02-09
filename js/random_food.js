document.addEventListener("DOMContentLoaded", () => {
  const container = document.getElementById("randomFood");

  if (!container) {
    console.error("❌ randomFood container not found");
    return;
  }

  let isLoading = false;
  const LIMIT = 20;
  let loadedFoodIds = new Set();

  loadFoods();

  // Lazy load on scroll
  window.addEventListener("scroll", () => {
    if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 300) {
      loadFoods();
    }
  });

  function loadFoods() {
    if (isLoading) return;
    isLoading = true;

    db.collection("vendor_products")
      .where("publish", "==", true)
      .get()
      .then(snapshot => {
        if (snapshot.empty) {
          container.innerHTML = "<p>No food available</p>";
          isLoading = false;
          return;
        }

        // Filter out already loaded foods
        let foods = snapshot.docs
          .map(doc => ({ id: doc.id, ...doc.data() }))
          .filter(f => !loadedFoodIds.has(f.id));

        if (foods.length === 0) {
          isLoading = false;
          return;
        }

        // Shuffle foods
        foods.sort(() => 0.5 - Math.random());

        // Take LIMIT foods
        const batch = foods.slice(0, LIMIT);

        batch.forEach(food => {
          loadedFoodIds.add(food.id);

          const reviewsCount = food.reviewAttributes?.reviewsCount || 0;
          const reviewsSum   = food.reviewAttributes?.reviewsSum || 0;
          const rating       = reviewsCount > 0 ? (reviewsSum / reviewsCount).toFixed(1) : "0.0";
          const price        = food.disPrice && food.disPrice !== "0" ? food.disPrice : food.price;
          const addonsCount  = Array.isArray(food.addOnsTitle) ? food.addOnsTitle.length : 0;
          const vegBadge     = food.veg ? '<span class="veg">🟢 Veg</span>' : '';
          const nonvegBadge  = food.nonveg ? '<span class="nonveg">🔴 Non-Veg</span>' : '';

          const card = document.createElement("a");
          card.href = `foods/product.php?id=${food.id}`;
          card.className = "food-card";

          card.innerHTML = `
            <img src="${food.photo || 'https://via.placeholder.com/300'}" loading="lazy" alt="${food.name}">
            <div class="food-info">
              <div class="food-name">${food.name}</div>
              <div class="food-rating">⭐ ${rating} (${reviewsCount})</div>
              <div class="food-meta">
                ${vegBadge} ${nonvegBadge} ${addonsCount > 0 ? `<span class="addons">➕ ${addonsCount} add-ons</span>` : ''}
              </div>
              <div class="food-price">₱${price}</div>
            </div>
          `;

          container.appendChild(card);
        });

        isLoading = false;
      })
      .catch(err => {
        console.error("Firestore error:", err);
        isLoading = false;
      });
  }
});
