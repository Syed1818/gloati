<<?php
include 'connect.php';

if (!isset($_GET['id'])) {
    echo "Product not found.";
    exit;
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute([':id' => $id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "Product not found.";
    exit;
}

$reviewStmt = $conn->prepare("SELECT * FROM reviews WHERE product_id = :id ORDER BY created_at DESC");
$reviewStmt->execute([':id' => $id]);
$reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($product['name']); ?> | Gloati</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #6ca59e;
      --primary-dark: #55877f;
      --text: #333;
      --price: #B12704;
      --bg: #fefefe;
      --border: #e0e0e0;
    }

    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text);
    }

    header {
      background: white;
      padding: 1rem 2rem;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .logo {
      font-size: 1.8rem;
      font-weight: bold;
      color: var(--primary);
    }

    .cart-btn {
      background-color: var(--primary);
      color: white;
      padding: 0.5rem 1rem;
      border-radius: 30px;
      cursor: pointer;
    }

    .container {
      max-width: 1200px;
      margin: 2rem auto;
      padding: 0 1rem;
      display: flex;
      gap: 2rem;
      flex-wrap: wrap;
    }

    .review-form {
        margin-top: 2rem;
        padding: 1rem;
        background: #f9f9f9;
        border: 1px solid var(--border);
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .review-form h3 {
      margin-bottom: 1rem;
      color: var(--primary);
    }
    .review-form textarea {
      width: 100%;
      padding: 0.5rem;
      border-radius: 6px;
      border: 1px solid var(--border);
      margin-bottom: 0.5rem;
    }

    .review-form select {
      width: 100%;
      padding: 0.5rem;
      border-radius: 6px;
      border: 1px solid var(--border);
      margin-bottom: 0.5rem;
    }
    .review-form button {
      background-color: var(--primary);
      color: white;
      padding: 0.5rem 1rem;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
    }
    .review-form button:hover {
      background-color: var(--primary-dark);
    }
    .product-image {
      flex: 1 1 300px;
      text-align: center;
    }

    .product-image img {
      width: 100%;
      max-width: 400px;
      object-fit: contain;
      border-radius: 12px;
      border: 1px solid var(--border);
      padding: 10px;
      background-color: #fff;
    }

    .product-info {
      flex: 1 1 400px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .product-info h1 {
      font-size: 1.8rem;
      margin-bottom: 0.6rem;
    }
    .product-info .rating {
      color: #FFA41C;
      font-size: 1.1rem;
      margin-bottom: 0.6rem;
    }
    .product-info p.description {
      font-size: 1rem;
      margin-bottom: 1rem;
      line-height: 1.6;
    }

    .product-info .price {
      font-size: 1.4rem;
      color: var(--price);
      margin-bottom: 1rem;
    }

    .product-info button {
      background-color: #FFD814;
      border: 1px solid #FCD200;
      padding: 0.75rem 1.5rem;
      border-radius: 8px;
      font-weight: 600;
      font-size: 1rem;
      cursor: pointer;
      transition: background-color 0.2s ease;
      width: fit-content;
    }

    .product-info button:hover {
      background-color: #F7CA00;
    }

    #cartItems {
      position: fixed;
      top: 4.5rem;
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
      padding: 0;
      list-style: none;
    }

    #cartItems li {
      margin-bottom: 1rem;
      border-bottom: 1px dashed var(--border);
      padding-bottom: 0.5rem;
    }

    #cartItems input[type="text"] {
      width: 65%;
      padding: 0.4rem;
      margin-top: 0.5rem;
      margin-bottom: 0.5rem;
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
      background: var(--primary);
      color: white;
      padding: 0.6rem;
      font-weight: bold;
      border-radius: 30px;
      border: none;
    }

    @media (max-width: 768px) {
      .container {
        flex-direction: column;
        align-items: center;
      }
      .product-info {
        text-align: center;
      }
    }
  </style>
</head>
<body>

<header>
  <div class="logo">Gloati</div>
  <div class="cart-btn" onclick="toggleCart()">Cart (<span id="cartCount">0</span>)</div>
</header>

<div id="cartItems">
  <h4>Your Cart</h4>
  <ul id="cartList"></ul>
  <label><strong>Apply Coupon:</strong></label><br />
  <input type="text" id="couponInput" placeholder="Enter coupon" />
  <button onclick="applyCoupon()">Apply</button>
  <p id="discountMsg" style="color: green; font-size: 0.9rem;"></p>
  <p><strong>Total:</strong> ₹<span id="cartTotal">0</span></p>
  <button class="checkout" onclick="checkout()">Checkout</button>
</div>

<div class="container">
  <div class="product-image">
    <img src="<?= htmlspecialchars($product['image']) ?>" alt="Product Image">
  </div>
  <div class="product-info">
    <h1><?= htmlspecialchars($product['name']) ?></h1>
    <p class="price">₹<?= number_format($product['price']) ?></p>
    <p class="description"><?= nl2br(htmlspecialchars($product['decription'])) ?></p>
    <button onclick="addToCart(<?= $product['id'] ?>, '<?= addslashes($product['name']) ?>', <?= $product['price'] ?>)">Add to Cart</button>

    <hr style="margin:2rem 0;">
    <h2 style="margin-bottom:1rem;">Customer Reviews</h2>
    <?php if (count($reviews) > 0): ?>
      <?php foreach ($reviews as $review): ?>
        <div style="margin-bottom:1.5rem; padding:1rem; border:1px solid #eee; border-radius:8px;">
          <strong><?= htmlspecialchars($review['user_name']) ?></strong> -
          <span style="color:#FFA41C;">
            <?= str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']) ?>
          </span>
          <p style="margin: 0.5rem 0;"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
          <small style="color: #888;"><?= date("F j, Y", strtotime($review['created_at'])) ?></small>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p style="color: #888;">No reviews yet. Be the first to review this product!</p>
    <?php endif; ?>

    <div class="review-form">
      <h3>Leave a Review</h3>
      <form method="POST" action="submit-review.php">
        <input type="hidden" name="product_id" value="<?= $id ?>">
        <textarea name="comment" placeholder="Your Review" rows="4" required></textarea>
        <label for="rating">Rating:</label>
        <select name="rating" id="rating" required>
          <option value="5">★★★★★</option>
          <option value="4">★★★★☆</option>
          <option value="3">★★★☆☆</option>
          <option value="2">★★☆☆☆</option>
          <option value="1">★☆☆☆☆</option>
        </select>
        <button type="submit">Submit Review</button>
      </form>
    </div>
  </div>
</div>

<script>
  let cart = JSON.parse(localStorage.getItem("gloatiCart")) || [];
  let appliedCoupon = null;
  let discountPercent = 0;

  function saveCart() {
    localStorage.setItem("gloatiCart", JSON.stringify(cart));
  }

  function addToCart(id, name, price) {
    const index = cart.findIndex(item => item.id === id);
    if (index > -1) {
      cart[index].qty += 1;
    } else {
      cart.push({ id, name, price, qty: 1 });
    }
    saveCart();
    updateCart();
    alert("Product added to cart!");
  }

  function removeFromCart(id) {
    const index = cart.findIndex(item => item.id === id);
    if (index > -1) {
      cart[index].qty -= 1;
      if (cart[index].qty <= 0) cart.splice(index, 1);
      saveCart();
      updateCart();
    }
  }

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

  document.addEventListener("DOMContentLoaded", updateCart);
</script>
</body>
</html>
