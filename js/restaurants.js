// restaurants.js

const container = document.getElementById("restaurants");
const searchInput = document.getElementById("searchInput"); // optional search input
const categoryItems = document.querySelectorAll(".category-item"); // optional aside categories

let vendors = [];
let selectedCategoryID = null;

// 1️⃣ Load all vendors
db.collection("vendors")
  .where("reststatus", "==", true)
  .get()
  .then(snapshot => {
    if(snapshot.empty){
      container.innerHTML = "<p>No restaurants available.</p>";
      return;
    }

    snapshot.forEach(doc => {
      const v = doc.data();
      v.id = doc.id; // store doc id
      vendors.push(v);
    });

    renderVendors(); // initial render
  })
  .catch(error => {
    console.error("Error fetching vendors:", error);
    container.innerHTML = "<p>Failed to load restaurants. Please try again later.</p>";
  });

// 2️⃣ Render vendors with optional category & search filter
function renderVendors() {
  container.innerHTML = "";
  const searchTerm = searchInput ? searchInput.value.toLowerCase() : "";

  let found = false;

  vendors.forEach(v => {
    const matchesCategory = !selectedCategoryID || v.categoryID === selectedCategoryID;
    const matchesSearch = !searchTerm || v.title.toLowerCase().includes(searchTerm);

    if(matchesCategory && matchesSearch){
      found = true;

      const image = v.logo || v.photo || 'https://via.placeholder.com/150?text=Store';

      const card = document.createElement("div");
      card.className = "restaurant-card";
      if(!v.isOpen) card.classList.add("closed");
      card.onclick = () => openVendor(v.id);

      // Image wrapper
      const imgWrapper = document.createElement("div");
      imgWrapper.className = "restaurant-image-wrapper";

      const img = document.createElement("img");
      img.className = "restaurant-profile-pic";
      img.src = image;
      img.alt = v.title;

      imgWrapper.appendChild(img);

      // Card body
      const body = document.createElement("div");
      body.className = "restaurant-body";

      const name = document.createElement("div");
      name.className = "restaurant-name";
      name.innerText = v.title;

      const meta = document.createElement("div");
      meta.className = "restaurant-meta";
      meta.innerText = `${v.location || 'Nearby'} • ${v.category || 'Food'}`;

      const badge = document.createElement("span");
      badge.className = `open-badge ${v.isOpen ? 'open' : 'closed'}`;
      badge.innerText = v.isOpen ? "Open" : "Closed";

      // Optional rating
      const rating = document.createElement("div");
      rating.className = "restaurant-rating";
      const stars = v.rating || 0;
      rating.innerHTML = "★".repeat(stars) + "☆".repeat(5 - stars);

      body.appendChild(name);
      body.appendChild(meta);
      body.appendChild(badge);
      body.appendChild(rating);

      card.appendChild(imgWrapper);
      card.appendChild(body);

      container.appendChild(card);
    }
  });

  if(!found){
    container.innerHTML = "<p>No restaurants found.</p>";
  }
}

// 3️⃣ Open vendor page (absolute path ensures correct page)
function openVendor(id){
  window.location.href = `/users/vendor.php?id=${id}`;
}

// 4️⃣ Search functionality
if(searchInput){
  searchInput.addEventListener("keyup", e => {
    if(e.key === "Enter"){
      renderVendors();
    } else {
      renderVendors(); // optional: live search
    }
  });
}

// 5️⃣ Category filtering
if(categoryItems){
  categoryItems.forEach(item => {
    item.addEventListener("click", () => {
      selectedCategoryID = item.dataset.catId || null;

      // Highlight active
      categoryItems.forEach(i => i.classList.remove("active"));
      item.classList.add("active");

      renderVendors();
    });
  });
}
