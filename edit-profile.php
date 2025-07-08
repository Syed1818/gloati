<?php
session_start();
if (!isset($_SESSION['user'])) {
  header("Location: login.html");
  exit();
}

include 'connect.php'; // optional: use this instead of raw mysqli

$username = $_SESSION['user'];
$success = $error = "";

// Get current user info
$stmt = $conn->prepare("SELECT profile_image, email, phone, address FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$currentImage = $row['profile_image'] ?? 'default-avatar.png';
$email = $row['email'] ?? '';
$phone = $row['phone'] ?? '';
$address = $row['address'] ?? '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $newEmail = trim($_POST['email'] ?? '');
  $newPhone = trim($_POST['phone'] ?? '');
  $newAddress = trim($_POST['address'] ?? '');

  if ($newEmail && $newPhone && $newAddress) {
    $stmt = $conn->prepare("UPDATE users SET email = ?, phone = ?, address = ? WHERE username = ?");
    $stmt->bind_param("ssss", $newEmail, $newPhone, $newAddress, $username);
    $stmt->execute();
    $stmt->close();
    $email = $newEmail;
    $phone = $newPhone;
    $address = $newAddress;
    $success = "Profile updated successfully!";
  } else {
    $error = "All fields are required.";
  }

  // Profile image upload
  if (!empty($_FILES['profile_image']['name'])) {
    $file = $_FILES['profile_image'];
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (in_array($ext, $allowed)) {
      $filename = uniqid("img_", true) . '.' . $ext;
      $targetPath = "uploads/" . $filename;

      if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE username = ?");
        $stmt->bind_param("ss", $filename, $username);
        $stmt->execute();
        $stmt->close();
        $currentImage = $filename;
        $success .= "<br>Profile picture updated!";
      } else {
        $error = "Failed to upload image.";
      }
    } else {
      $error = "Invalid file type. Only JPG, PNG, GIF, WEBP allowed.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit Profile - Gloati</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #fffaf7;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .edit-container {
      background: #fff;
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
      text-align: center;
    }
    h2 {
      color: #6ca59e;
      margin-bottom: 1.5rem;
    }
    img {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 1rem;
      border: 2px solid #6ca59e44;
    }
    input[type="file"], input[type="email"], input[type="text"], textarea {
      width: 100%;
      padding: 0.6rem;
      margin: 0.3rem 0 1rem;
      border: 1px solid #ccc;
      border-radius: 8px;
    }
    textarea {
      resize: vertical;
      min-height: 60px;
    }
    button {
      padding: 0.7rem 1.2rem;
      background-color: #6ca59e;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 1rem;
    }
    button:hover {
      background-color: #55877f;
    }
    .message {
      margin-top: 1rem;
      font-size: 0.95rem;
    }
    .success { color: green; }
    .error { color: red; }
    a.back-link {
      display: inline-block;
      margin-top: 1.5rem;
      text-decoration: none;
      color: #6ca59e;
    }
    a.back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="edit-container">
    <h2>Edit Profile</h2>
    <img src="uploads/<?php echo htmlspecialchars($currentImage); ?>" alt="Profile" />
    <form method="POST" enctype="multipart/form-data">
      <input type="file" name="profile_image" accept="image/*" />
      <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Email" required />
      <input type="text" name="phone" value="<?php echo htmlspecialchars($phone); ?>" placeholder="Phone" required />
      <textarea name="address" placeholder="Address" required><?php echo htmlspecialchars($address); ?></textarea>
      <button type="submit">Update Profile</button>
    </form>
    <?php if (!empty($success)): ?>
      <div class="message success"><?php echo $success; ?></div>
    <?php elseif (!empty($error)): ?>
      <div class="message error"><?php echo $error; ?></div>
    <?php endif; ?>
    <a class="back-link" href="my account.php">← Back to Account</a>
  </div>
</body>
</html>
