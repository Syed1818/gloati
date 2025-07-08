<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>About Us | Gloati</title>
<style>
  body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background-color: #fefefe;
    color: #333;
    line-height: 1.6;
  }

  header {
    background: #ffffff;
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    position: sticky;
    top: 0;
    z-index: 1000;
  }

  .logo {
    font-size: 2rem;
    font-weight: bold;
    color: #6ca59e;
    letter-spacing: 1px;
  }

  nav a {
    text-decoration: none;
    margin-left: 20px;
    color: #6ca59e;
    font-weight: 500;
    font-size: 1rem;
    transition: color 0.3s ease;
  }

  nav a:hover {
    color: #4a837b;
  }

  .about-container {
    max-width: 1000px;
    margin: 3rem auto;
    padding: 0 1.5rem;
  }

  h1 {
    font-size: 2.8rem;
    color: #6ca59e;
    margin-bottom: 1rem;
  }

  p {
    font-size: 1.1rem;
    color: #555;
    margin-bottom: 1rem;
  }

  .highlight {
    background-color: #e7faf7;
    border-left: 4px solid #6ca59e;
    padding: 1.25rem;
    border-radius: 8px;
    margin: 2rem 0;
    font-weight: 500;
  }

  .team {
    margin-top: 3rem;
  }

  .team h2 {
    font-size: 2rem;
    color: #4a837b;
    margin-bottom: 1.5rem;
  }

  .team-member {
    display: flex;
    align-items: center;
    background: #ffffff;
    border: 1px solid #ddd;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    transition: transform 0.3s ease;
  }

  .team-member:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 14px rgba(0, 0, 0, 0.08);
  }

  .team-member img {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 1rem;
    border: 2px solid #6ca59e;
  }

  .team-member div {
    font-size: 1rem;
    color: #444;
  }

  @media (max-width: 600px) {
    .team-member {
      flex-direction: column;
      text-align: center;
    }

    .team-member img {
      margin-bottom: 0.75rem;
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

<div class="about-container">
  <h1>About Gloati</h1>
  <p>Gloati is more than just skincare. We are a movement dedicated to redefining beauty through clean, conscious, and empowering self-care. Founded with love and backed by science, our mission is to help you feel confident in your skin, every single day.</p>

  <p>We believe skincare should be effective, safe, and inclusive. Every product we create is cruelty-free, sustainably sourced, and dermatologist-approved—crafted with nature and innovation hand-in-hand.</p>

  <div class="highlight">
    🌿 <strong>Our Promise:</strong> No harsh chemicals, no false promises—only glowing results.
  </div>

  <div class="team">
    <h2>Meet the Team</h2>

    <div class="team-member">
      <img src="images/zaid.png" alt="Founder">
      <div>
        <strong>Zaid Khan</strong><br />
        Founder Of Gloati & Skincare Visionary
      </div>
    </div>
    <div class="team-member">
        <img src="images/mohit.png" alt="Founder">
        <div>
            <strong>Mohit Sharma</strong><br />
            Co-Founder Of Gloati & Skincare Visionary
        </div>
    </div>

    <div class="team-member">
        <img src="images/team2.jpg" alt="Scientist">
        <div>
            <strong>Dr. Neil Kapoor</strong><br />
            Head of Product Formulation
        </div>
    </div>
</div>
</div>

</body>
</html>
