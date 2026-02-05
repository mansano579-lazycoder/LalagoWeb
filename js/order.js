// js/order.js

const cartDiv = document.getElementById("cart");
const cartData = JSON.parse(localStorage.getItem("cartData"));

if (!cartData || cartData.items.length === 0) {
  cartDiv.innerHTML = "<p>Your cart is empty</p>";
  throw new Error("Empty cart");
}

let total = 0;

// Display cart items
cartData.items.forEach(item => {
  total += item.price * item.quantity;
  cartDiv.innerHTML += `
    <p>
      ${item.name} × ${item.quantity}
      — ₱${item.price * item.quantity}
    </p>
  `;
});

cartDiv.innerHTML += `<hr><strong>Total: ₱${total}</strong>`;

// Place order
function placeOrder() {
  const user = auth.currentUser;

  if (!user) {
    alert("Please login first");
    return;
  }

  db.collection("restaurant_orders").add({
    userId: user.uid,
    restaurantId: cartData.restaurantId,
    items: cartData.items,
    total: total,
    status: "pending",
    createdAt: firebase.firestore.FieldValue.serverTimestamp()
  })
  .then(() => {
    localStorage.removeItem("cartData");
    alert("Order placed successfully!");
    window.location.href = "index.php";
  })
  .catch(err => {
    alert("Error placing order");
    console.error(err);
  });
}
