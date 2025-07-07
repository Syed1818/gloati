<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Terms & Conditions | Gloati</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #fffaf7;
      color: #333;
    }
    header {
      background: #fff;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .logo {
      font-size: 1.8rem;
      color: #6ca59e;
      font-weight: bold;
    }
    nav a {
      text-decoration: none;
      margin-left: 20px;
      color: #6ca59e;
      font-weight: 500;
    }
    nav a:hover {
      text-decoration: underline;
    }

    .content {
      max-width: 900px;
      margin: 3rem auto;
      padding: 0 2rem;
    }
    h1 {
      font-size: 2.2rem;
      color: #6ca59e;
      margin-bottom: 1.5rem;
    }
    h2 {
      color: #55877f;
      margin-top: 2rem;
      font-size: 1.4rem;
    }
    p {
      line-height: 1.8;
      margin-bottom: 1rem;
      font-size: 1.05rem;
    }
    ul {
      margin-left: 1.2rem;
      line-height: 1.6;
    }
    footer {
      margin-top: 4rem;
      text-align: center;
      font-size: 0.9rem;
      color: #999;
      padding: 2rem;
    }

    @media (max-width: 600px) {
      .content {
        padding: 1rem;
      }
    }
  </style>
</head>
<body>

<header>
  <div class="logo">Gloati</div>
  <nav>
    <a href="homepage.html">Home</a>
    <a href="shop.php">Shop</a>
    <a href="about.php">About</a>
    <a href="contact.php">Contact</a>
  </nav>
</header>

<div class="content">
  <h1>Terms & Conditions</h1>

  <p>Welcome to Gloati! By accessing or using our website, you agree to comply with and be bound by the following terms and conditions. Please read them carefully.</p>

  <h2>1. Use of Website</h2>
  <p>This website is intended for users who are at least 18 years old. By using this site, you confirm that you meet this requirement or are supervised by an adult.</p>

  <h2>2. Products & Orders</h2>
  <ul>
    <li>All products are subject to availability.</li>
    <li>We reserve the right to limit quantities or cancel orders at our discretion.</li>
    <li>Prices are subject to change without notice.</li>
  </ul>

  <h2>3. Payment</h2>
  <p>All transactions are processed securely. By providing payment information, you authorize us to charge the specified amount for your order.</p>

  <h2>4. Returns & Refunds</h2>
  <p>We accept returns for damaged or defective products within 7 days of delivery. For full details, please refer to our Return Policy (coming soon).</p>

  <h2>5. User Accounts</h2>
  <ul>
    <li>You are responsible for maintaining the confidentiality of your account credentials.</li>
    <li>We reserve the right to terminate accounts that violate our policies or misuse the platform.</li>
  </ul>

  <h2>6. Intellectual Property</h2>
  <p>All content on this website—including text, images, and branding—is the property of Gloati and may not be copied, distributed, or used without permission.</p>

  <h2>7. Limitation of Liability</h2>
  <p>We are not liable for any damages resulting from your use of the website or products. Always patch test skincare before full use.</p>

  <h2>8. Changes to Terms</h2>
  <p>We may update these terms from time to time. Changes will be posted on this page and will take effect immediately upon publication.</p>

  <p>Last updated: June 2025</p>
</div>

<footer>
  &copy; <?php echo date("Y"); ?> Gloati. All rights reserved.
</footer>

</body>
</html>
