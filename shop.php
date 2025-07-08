<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}
include 'connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Gloati | Shop</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
<style>
:root {
  --primary: #6ca59e;
  --primary-dark: #55877f;
  --background: #fffaf7;
  --text: #333;
  --muted: #777;
  --border: #e6e6e6;
}

body, main {
  margin: 0;
  padding: 0;
  font-family: 'Inter', sans-serif;
  background-color: var(--background);
  color: var(--text);
}

.menu-toggle {
  display: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: var(--primary);
}

header {
  background: #fff;
  padding: 1rem 2rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: relative;
  z-index: 10;
}

.logo {
  padding-left: auto;
  font-size: 2rem;
  font-weight: bold;
  color: var(--primary);
}

.cart-btn {
  background-color: var(--primary-dark);
  color: #fff;
  padding: 0.4rem 1rem;
  border-radius: 20px;
  font-weight: bold;
  cursor: pointer;
}

/* SIDEBAR */
#sidebar {
  position: fixed;
  top: 0;
  left: -250px;
  width: 250px;
  height: 100vh;
  background-color: #1e2a38;
  transition: left 0.3s ease;
  z-index: 1000;
  padding-top: 1.5rem;
}

#sidebar.active {
  left: 0;
}

#sidebar h2 {
  text-align: center;
  font-size: 1.5rem;
  color: #ffffff;
  margin-bottom: 30px;
}

#sidebar ul {
  list-style-type: none;
  padding: 0;
}

#sidebar ul li {
  margin: 15px 0;
}

#sidebar ul li a {
  color: #ecf0f1;
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 15px;
  border-radius: 6px;
  transition: background-color 0.2s ease;
}

#sidebar ul li a:hover {
  background-color: #34495e;
}

#sidebar ul li a i {
  font-size: 1.2rem;
}

.sidebar-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: none;
  z-index: 999;
}

.sidebar-overlay.active {
  display: block;
}

/* MAIN CONTAINER */
main.container {
  max-width: 1200px;
  margin-left: 250px;
  margin-top: 2rem;
  padding: 0 1rem;
  transition: margin-left 0.3s ease;
}

h2 {
  margin-bottom: 1rem;
  font-size: 1.75rem;
  text-align: center;
}

.products {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1.5rem;
}

.product {
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 1rem;
  text-align: left;
  display: flex;
  flex-direction: column;
  height: 100%;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
  transition: box-shadow 0.2s ease;
}

.product:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.product img {
  width: 100%;
  height: 180px;
  object-fit: contain;
  margin-bottom: 0.8rem;
}

.product h3 {
  font-size: 1rem;
  font-weight: 600;
  min-height: 2.4rem;
  margin: 0.3rem 0;
}

.product p {
  font-size: 1.1rem;
  font-weight: bold;
  color: #B12704;
  margin-bottom: 0.5rem;
}

.product button {
  margin-top: auto;
  padding: 0.5rem 0;
  background-color: #FFD814;
  color: #111;
  border: 1px solid #FCD200;
  border-radius: 6px;
  font-size: 0.9rem;
  font-weight: bold;
  cursor: pointer;
}

.product button:hover {
  background-color: #F7CA00;
}

/* CART PANEL */
#cartItems {
  position: fixed;
  top: 5rem;
  right: 1rem;
  width: 340px;
  background: white;
  border-radius: 14px;
  padding: 1.5rem;
  border: 1px solid var(--border);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
  display: none;
  z-index: 1001;
  max-height: 80vh;
  overflow-y: auto;
}

#cartItems ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

#cartItems li {
  margin-bottom: 1rem;
  border-bottom: 1px dashed var(--border);
  padding-bottom: 0.5rem;
}

#cartItems input[type="text"] {
  width: 100%;
  padding: 0.5rem;
  margin: 0.5rem 0;
  border-radius: 6px;
  border: 1px solid var(--border);
}

#cartItems button {
  padding: 0.3rem 0.6rem;
  margin: 0.2rem;
  background-color: #f2f2f2;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.85rem;
}

.checkout {
  width: 100%;
  margin-top: 1rem;
  background-color: var(--primary);
  color: white;
  padding: 0.6rem;
  font-weight: bold;
  border-radius: 30px;
  border: none;
  cursor: pointer;
}

/* RESPONSIVE */
/* Always show the toggle button */
.menu-toggle {
  display: block;
  font-size: 1.5rem;
  cursor: pointer;
  color: var(--primary);
}

/* Sidebar: Hidden by default on all screen sizes */
/* SIDEBAR ANIMATION */
#sidebar {
  position: fixed;
  top: 0;
  left: -250px;
  width: 250px;
  height: 100vh;
  background-color: #1e2a38;
  z-index: 1000;
  padding-top: 1.5rem;
  transition: transform 0.4s ease;
  transform: translateX(-100%);
}

#sidebar.active {
  transform: translateX(0);
}

/* FADE IN OVERLAY */
.sidebar-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.0);
  z-index: 999;
  display: none;
  transition: background-color 0.3s ease;
}

.sidebar-overlay.active {
  display: block;
  background-color: rgba(0, 0, 0, 0.5);
}


/* Adjust main content when sidebar is active */
main.container {
  max-width: 1200px;
  margin-left: 0;
  margin-top: 2rem;
  padding: 0 1rem;
  transition: margin-left 0.3s ease;
}

main.container.shifted {
  margin-left: 250px; /* Shift content when sidebar is open */
}

/* RESPONSIVE */
@media (max-width: 768px) {
  header {
    flex-direction: column;
    gap: 1rem;
  }

  .products {
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  }
}

/* REMOVE old rule that forces sidebar visible on desktop */

</style>
</head>
<body>

<header>
  <div class="menu-toggle" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
  </div>
  <div class="logo">Gloati</div>
  <div class="cart-btn" onclick="toggleCart()">Cart (<span id="cartCount">0</span>)</div>
</header>

<nav id="sidebar">
  <h2><i class="fas fa-store"></i> Gloati</h2>
  <ul>
    <li><a href="homepage.html"><i class="fas fa-home"></i> Home</a></li>
    <li><a href="about.php"><i class="fas fa-info-circle"></i> About Us</a></li>
    <li><a href="contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
  </ul>
</nav>

<div class="sidebar-overlay" onclick="toggleSidebar()"></div>

<!-- Cart Panel -->
<div id="cartItems">
  <h4>Your Cart</h4>
  <ul id="cartList"></ul>

  <label><strong>Apply Coupon:</strong></label>
  <input type="text" id="couponInput" placeholder="Enter coupon" />
  <button onclick="applyCoupon()">Apply</button>
  <p id="discountMsg" style="color: green; font-size: 0.9rem;"></p>

  <p><strong>Total:</strong> ₹<span id="cartTotal">0</span></p>
  <button class="checkout" onclick="checkout()">Checkout</button>
</div>


<main class="container">
  <form method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.5rem;">
    <input type="text" name="search" placeholder="Search products..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" style="padding: 0.6rem 1rem; border: 1px solid #ccc; border-radius: 20px; font-size: 1rem;">
    <select name="sort" style="padding: 0.6rem; border-radius: 8px;">
      <option value="">Sort by</option>
      <option value="low" <?= ($_GET['sort'] ?? '') === 'low' ? 'selected' : '' ?>>Price: Low to High</option>
      <option value="high" <?= ($_GET['sort'] ?? '') === 'high' ? 'selected' : '' ?>>Price: High to Low</option>
    </select>
    <select name="category" style="padding: 0.6rem; border-radius: 8px;">
      <option value="">All Categories</option>
      <option value="skincare" <?= ($_GET['category'] ?? '') === 'skincare' ? 'selected' : '' ?>>Skincare</option>
      <option value="haircare" <?= ($_GET['category'] ?? '') === 'haircare' ? 'selected' : '' ?>>Haircare</option>
      <option value="bodycare" <?= ($_GET['category'] ?? '') === 'bodycare' ? 'selected' : '' ?>>Bodycare</option>
    </select>
    <button type="submit" style="background-color: var(--primary); color: white; border: none; padding: 0.6rem 1rem; border-radius: 8px;">Apply</button>
  </form>

  <div class="products">
    <?php
    // Build query using PDO
    $search = $_GET['search'] ?? '';
    $sort = $_GET['sort'] ?? '';
    $category = $_GET['category'] ?? '';

    $conditions = [];
    $params = [];

    if (!empty($search)) {
        $conditions[] = "name ILIKE :search";
        $params[':search'] = '%' . $search . '%';
    }
    if (!empty($category)) {
        $conditions[] = "category = :category";
        $params[':category'] = $category;
    }

    $where = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
    $orderBy = ($sort === 'low') ? 'ORDER BY price ASC' : (($sort === 'high') ? 'ORDER BY price DESC' : '');

    $stmt = $conn->prepare("SELECT * FROM products $where $orderBy");
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as $row) {
        echo '<a href="product.php?id=' . $row['id'] . '" style="text-decoration:none; color:inherit;">';
        echo '<div class="product">';
        echo '<img src="' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '" />';
        echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';

        // Rating logic
        $ratingStmt = $conn->prepare("SELECT AVG(rating) as rating, COUNT(*) as rating_count FROM reviews WHERE product_id = :id");
        $ratingStmt->execute([':id' => $row['id']]);
        $ratingData = $ratingStmt->fetch(PDO::FETCH_ASSOC);

        $averageRating = round($ratingData['rating'] ?? 0, 1);
        $ratingCount = $ratingData['rating_count'] ?? 0;

        $starsFull = floor($averageRating);
        $starsHalf = ($averageRating - $starsFull >= 0.5) ? 1 : 0;
        $starsEmpty = 5 - $starsFull - $starsHalf;

        echo '<div style="color: #FFA41C; font-size: 0.85rem; margin-bottom: 0.4rem;">';
        for ($i = 0; $i < $starsFull; $i++) echo '★';
        if ($starsHalf) echo '½';
        for ($i = 0; $i < $starsEmpty; $i++) echo '☆';
        echo " ({$ratingCount} reviews)</div>";

        echo '<p>₹' . number_format($row['price']) . '</p>';
        echo '</div>';
        echo '</a>';
    }
    ?>
  </div>
</main>
<script>
function toggleSidebar() {
  const sidebar = document.getElementById("sidebar");
  const overlay = document.querySelector(".sidebar-overlay");
  const main = document.querySelector("main.container");

  sidebar.classList.toggle("active");
  overlay.classList.toggle("active");
  main.classList.toggle("shifted");
}

  let cart = JSON.parse(localStorage.getItem("gloatiCart")) || [];
  let appliedCoupon = null;
  let discountPercent = 0;

  function updateCart() {
    const cartList = document.getElementById("cartList");
    const cartCount = document.getElementById("cartCount");
    const cartTotal = document.getElementById("cartTotal");
    const discountMsg = document.getElementById("discountMsg");

    let total = 0, count = 0;
    cartList.innerHTML = cart.map(item => {
      const itemTotal = item.price * item.qty;
      total += itemTotal;
      count += item.qty;
      return `
        <li>
          ${item.name} x ${item.qty} = ₹${itemTotal}<br />
          <button onclick="addToCart(${item.id}, '${item.name}', ${item.price})">+</button>
          <button onclick="removeFromCart(${item.id})">-</button>
        </li>
      `;
    }).join("");

    let finalTotal = total;
    if (discountPercent > 0) {
      const discount = (total * discountPercent / 100).toFixed(2);
      finalTotal = (total - discount).toFixed(2);
      discountMsg.innerHTML = `Coupon <b>${appliedCoupon}</b> applied - ₹${discount} off`;
    } else {
      discountMsg.innerHTML = "";
    }

    cartCount.textContent = count;
    cartTotal.textContent = finalTotal;
  }

  function toggleCart() {
    const cartEl = document.getElementById("cartItems");
    cartEl.style.display = cartEl.style.display === "block" ? "none" : "block";
    
  }

  function applyCoupon() {
    const code = document.getElementById("couponInput").value.trim().toUpperCase();
    if (!code) return alert("Enter a coupon");

    fetch(`validate-coupon.php?code=${code}`)
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          appliedCoupon = code;
          discountPercent = data.discount;
          updateCart();
        } else {
          alert(data.message);
        }
      })
      .catch(() => alert("Error validating coupon"));
  }

  function checkout() {
    if (cart.length === 0) {
      alert("Cart is empty");
      return;
    }

    fetch("checkout.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ cart, coupon: appliedCoupon })
    })
    .then(res => res.text())
    .then(data => {
      alert(data);
      localStorage.removeItem("gloatiCart");
      cart = [];
      appliedCoupon = null;
      discountPercent = 0;
      updateCart();
      toggleCart();
    })
    .catch(err => alert("Checkout error: " + err));
  }

  function addToCart(id, name, price) {
    const index = cart.findIndex(item => item.id === id);
    if (index > -1) {
      cart[index].qty += 1;
    } else {
      cart.push({ id, name, price, qty: 1 });
    }
    localStorage.setItem("gloatiCart", JSON.stringify(cart));
    updateCart();
  }

  function removeFromCart(id) {
    const index = cart.findIndex(item => item.id === id);
    if (index > -1) {
      cart[index].qty -= 1;
      if (cart[index].qty <= 0) cart.splice(index, 1);
      localStorage.setItem("gloatiCart", JSON.stringify(cart));
      updateCart();
    }
  }
  document.addEventListener("DOMContentLoaded", updateCart);
</script>
</body>
</html>