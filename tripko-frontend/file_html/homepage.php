<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: SignUp_LogIn_Form.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TripKo Pangasinan - Home</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Basic reset and common styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            line-height: 1.6;
        }

        /* Navigation */
        .navbar {
            background-color: #255D8A;
            padding: 1rem;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .nav-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
        }

        /* Hero Section */
        .hero {
            height: 100vh;
            background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), 
                            url('../images/hundred-islands.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }

        .hero-content {
            max-width: 800px;
            padding: 2rem;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }

        .cta-button {
            background-color: #255D8A;
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .cta-button:hover {
            background-color: #1e4d70;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-content">
            <div class="logo">TripKo</div>
            <div class="nav-links">
                <a href="#">Home</a>
                <a href="#">Tourist Spots</a>
                <a href="#">Transportation</a>
                <a href="#">Festivals</a>
                <a href="#">About</a>
                <a href="../../tripko-backend/logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-content">
            <h1>Welcome to TripKo Pangasinan</h1>
            <p>Discover the beauty of Pangasinan's tourist spots, festivals, and plan your journey with our transportation guides.</p>
            <a href="#" class="cta-button">Explore Now</a>
        </div>
    </section>
</body>
</html>