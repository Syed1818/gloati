<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Privacy Policy | Gloati</title>
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
  <h1>Privacy Policy</h1>

  <p>At Gloati, your privacy is important to us. This Privacy Policy explains how we collect, use, and protect your personal information when you interact with our website or use our services.</p>

  <h2>1. Information We Collect</h2>
  <p>We may collect the following types of information:</p>
  <ul>
    <li>Personal details (name, email, contact number)</li>
    <li>Order and payment information</li>
    <li>Usage data from website visits (IP, browser type, device info)</li>
  </ul>

  <h2>2. How We Use Your Information</h2>
  <p>We use your data to:</p>
  <ul>
    <li>Process and deliver orders</li>
    <li>Send updates and promotional emails (only with your consent)</li>
    <li>Improve our website and customer experience</li>
    <li>Comply with legal requirements</li>
  </ul>

  <h2>3. Data Protection</h2>
  <p>We implement security measures to protect your personal data from unauthorized access, alteration, or disclosure.</p>

  <h2>4. Cookies</h2>
  <p>We use cookies to personalize content, analyze traffic, and improve user experience. You may disable cookies through your browser settings.</p>

  <h2>5. Sharing of Information</h2>
  <p>We do not sell or share your personal information with third parties, except when required by law or necessary for service delivery (e.g., payment processing).</p>

  <h2>6. Your Rights</h2>
  <p>You have the right to access, update, or delete your personal information. To do so, please contact us at <strong>privacy@gloati.com</strong>.</p>

  <h2>7. Changes to This Policy</h2>
  <p>We may update this Privacy Policy from time to time. Changes will be posted on this page with the date of revision.</p>

  <p>Last updated: June 2025</p>
</div>

<footer>
  &copy; <?php echo date("Y"); ?> Gloati. All rights reserved.
</footer>

</body>
</html>
