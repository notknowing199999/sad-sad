<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/tripko-system/tripko-backend/check_session.php');

// Redirect logic
if (!isLoggedIn()) {
    header("Location: SignUp_LogIn_Form.php");
    exit();
} elseif (isAdmin()) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>TripKo Pangasinan</title>

  <!-- Icons and Stylesheets -->
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="../file_css/userpage.css" />
  <link rel="stylesheet" href="../file_css/navbar.css" />

<style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #fff;
    }

    h1 {
      color: #255D84;
      font-size: 32px;
      font-weight: bold;
      margin-bottom: 20px;
    }

    .contact-container {
      max-width: 900px;
      margin: 40px auto;
      padding: 20px 30px;
      background: #ffffff;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
      color: #333;
    }

    .required {
      color: red;
      margin-left: 2px;
    }

    input[type="text"],
    input[type="email"],
    textarea {
      width: 100%;
      padding: 12px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 6px;
      box-sizing: border-box;
      font-size: 16px;
    }

    textarea {
      height: 150px;
      resize: none;
    }

    .submit-btn {
      background-color: #255D84;
      color: white;
      border: none;
      padding: 15px 30px;
      font-size: 16px;
      font-weight: bold;
      border-radius: 30px;
      cursor: pointer;
      width: 150px;
    }

    .submit-btn:hover {
      background-color: #255D84;
    }
  </style>

</head>
<body>

  <?php include_once 'navbar.php'; renderNavbar(); ?>

  <section class="hero_content">
  <div class="contact-container">
    <h1>Feedback & Concerns</h1>

  <form action="#" method="post">
    <label for="name">Name <span class="required">*</span></label>
    <input type="text" id="name" name="name" required>

    <label for="email">Email Address <span class="required">*</span></label>
    <input type="email" id="email" name="email" required>

    <label for="message">Message <span class="required">*</span></label>
    <textarea id="message" name="message" required></textarea>

    <button type="submit" class="submit-btn">SUBMIT</button>
  </form>
  </div>
  </section>
  
</body>
</html>
