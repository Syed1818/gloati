<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Contact Us | Gloati</title>
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

    .contact-container {
      max-width: 700px;
      margin: 3rem auto;
      padding: 2rem;
      background-color: #ffffff;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
    }

    h1 {
      font-size: 2rem;
      color: #6ca59e;
      margin-bottom: 1rem;
    }

    form label {
      display: block;
      margin-top: 1rem;
      font-weight: 500;
    }

    input, textarea {
      width: 100%;
      padding: 0.75rem;
      margin-top: 0.3rem;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 1rem;
      transition: border-color 0.2s;
    }

    input:focus, textarea:focus {
      border-color: #6ca59e;
      outline: none;
    }

    button {
      margin-top: 1.5rem;
      background-color: #6ca59e;
      color: white;
      border: none;
      padding: 0.75rem 1.5rem;
      font-size: 1rem;
      border-radius: 25px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #55877f;
    }

    .contact-info {
      margin-top: 2rem;
      font-size: 0.95rem;
      color: #666;
    }

    @media (max-width: 600px) {
      .contact-container {
        margin: 1rem;
        padding: 1.5rem;
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

<div class="contact-container">
  <h1>Contact Us</h1>
  <form action="contact-submit.php" method="POST">
    <label for="name">Your Name</label>
    <input type="text" name="name" id="name" required />

    <label for="email">Email Address</label>
    <input type="email" name="email" id="email" required />

    <label for="message">Your Message</label>
    <textarea name="message" id="message" rows="5" required></textarea>

    <button type="submit">Send Message</button>
  </form>

  <div class="contact-info">
    <p><strong>Email:</strong> support@gloati.com</p>
    <p><strong>Phone:</strong> +91 98765 43210</p>
    <p><strong>Address:</strong> Gloati HQ, 123 Wellness Lane, Mumbai, India</p>
  </div>
</div>

</body>
</html>
