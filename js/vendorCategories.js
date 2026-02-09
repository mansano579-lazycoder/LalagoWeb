function initVendorCategories() {
  const categoriesContainer = document.getElementById("categoriesContainer");
  if (!categoriesContainer) return;
  if (typeof db === "undefined") return;

  const categoryCounts = {};
  const categoryPhotos = {};

  db.collection("vendors")
    .where("reststatus", "==", true)
    .get()
    .then(vendorSnap => {

      if (vendorSnap.empty) {
        categoriesContainer.innerHTML = "<p>No vendors found.</p>";
        return;
      }

      vendorSnap.forEach(doc => {
        const v = doc.data();
        if (!v.categoryID || !v.categoryTitle) return;

        if (!categoryCounts[v.categoryID]) {
          categoryCounts[v.categoryID] = {
            count: 1,
            title: v.categoryTitle.trim()
          };
        } else {
          categoryCounts[v.categoryID].count++;
        }
      });

      // 2️⃣ LOAD CATEGORY PHOTOS (SOURCE OF IMAGES)
      return db.collection("vendor_categories")
        .where("publish", "==", true)
        .get();
    })
    .then(catSnap => {

      if (!catSnap || catSnap.empty) {
        categoriesContainer.innerHTML = "<p>No categories found.</p>";
        return;
      }

      catSnap.forEach(doc => {
        const c = doc.data();
        const catID = c.id || doc.id;

        // ❌ Skip categories WITHOUT restaurants
        if (!categoryCounts[catID]) return;

        const title = (c.title || c.name || "").trim() || categoryCounts[catID].title;
        categoryPhotos[catID] = {
          title,
          photo: c.photo || "https://via.placeholder.com/100",
          count: categoryCounts[catID].count
        };
      });

      // 3️⃣ SORT BY TOP CATEGORIES
      const sortedCategories = Object.entries(categoryPhotos)
        .map(([id, data]) => ({ id, ...data }))
        .sort((a, b) => b.count - a.count);

      // 4️⃣ SHOW TOP N
      const topN = 5;

      sortedCategories.slice(0, topN).forEach(cat => {
        const card = document.createElement("div");
        card.className = "category-card";

        card.innerHTML = `
          <img src="${cat.photo}" alt="${cat.title}">
          <span>${cat.title}</span>
        `;

        card.onclick = () => {
          window.location.href =
            "foods/categories.php?category=" +
            encodeURIComponent(cat.id);
        };

        categoriesContainer.appendChild(card);
      });

      if (categoriesContainer.innerHTML === "") {
        categoriesContainer.innerHTML = "<p>No categories available.</p>";
      }
    })
    .catch(err => {
      console.error(err);
      categoriesContainer.innerHTML = "<p>Error loading categories.</p>";
    });
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initVendorCategories);
} else {
  initVendorCategories();
}
