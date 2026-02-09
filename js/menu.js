// Get restaurant ID from URL
const params = new URLSearchParams(window.location.search);
const restaurantId = params.get("id");

if (!restaurantId) {
  alert("Restaurant not found");
  window.location.href = "index.php";
}

const menuDiv = document.getElementById("menu");

// Initialize cart
let cartData = JSON.parse(localStorage.getItem("cartData")) || {
  restaurantId: restaurantId,
  items: []
};

// Placeholder image if product has no image
const placeholderImage = "assets/default-product.jpg"; // make sure this exists

// Load menu items from Firestore
db.collection("vendor_products")
  .where("vendorID", "==", restaurantId)
  .get()
  .then(snapshot => {
    if (snapshot.empty) {
      menuDiv.innerHTML = "<p style='text-align:center;'>No menu items available.</p>";
      return;
    }

    snapshot.forEach(doc => {
      const item = doc.data();

      // Check if image exists in Firestore; use placeholder if not
      const imageUrl = item.image ? item.image : placeholderImage;

      // Add product card
      const cardHTML = `
        <div class="product-card">
          <img src="${imageUrl}" alt="${item.name}">
          <div class="product-details">
            <h3>${item.name}</h3>
            <p>₱${item.price}</p>
            <button class="add-to-cart" onclick="addToCart('${doc.id}', '${item.name}', ${item.price})">
              Add to Cart
            </button>
          </div>
        </div>
      `;
      menuDiv.innerHTML += cardHTML;
    });
  })
  .catch(error => {
    console.error("Error fetching menu items:", error);
    menuDiv.innerHTML = "<p style='text-align:center;'>Failed to load menu.</p>";
  });

// Add item to cart
function addToCart(id, name, price) {
  // Prevent multiple restaurant orders
  if (cartData.restaurantId !== restaurantId) {
    alert("You can only order from one restaurant at a time.");
    return;
  }

  // Check if item already in cart
  const existingItem = cartData.items.find(item => item.productId === id);
  if (existingItem) {
    existingItem.quantity += 1;
  } else {
    cartData.items.push({
      productId: id,
      name: name,
      price: price,
      quantity: 1
    });
  }

  localStorage.setItem("cartData", JSON.stringify(cartData));
  alert(`${name} added to cart`);
}
