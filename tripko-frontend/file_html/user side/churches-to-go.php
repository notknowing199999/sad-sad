<?php
session_start();

// Database connection configuration
$host = "localhost";     
$username = "root";      
$password = "";         
$database = "tripko_db"; 

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch tourist spots with town information - only churches
$query = "SELECT ts.*, t.name as town_name 
          FROM tourist_spots ts 
          LEFT JOIN towns t ON ts.town_id = t.town_id 
          WHERE ts.status = 'active' AND ts.category = 'Churches'
          ORDER BY ts.name ASC";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TripKo Pangasinan - Churches</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cedarville+Cursive&display=swap');
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'incolosoLata';
        }

        body {
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Navigation */
        .navbar {
            background-color: rgba(37, 93, 138, 0.95);
            padding: 0%;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            backdrop-filter: blur(5px);
        }

        .nav-content {
            max-width: 1500px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo i {
            font-size: 1.8rem;
        }

        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
            flex-wrap: nowrap;
            padding: 0 2rem;
            margin-right: 15px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            padding: 0.5rem 0.75rem;
            border-radius: 4px;
            white-space: nowrap;
        }

        .nav-links a:hover {
            color: #ffd700;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .menu-btn {
            display: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }

        @media (max-width: 1024px) {
            .nav-links {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: #255D8A;
                flex-direction: column;
                padding: 1rem;
                text-align: center;
                gap: 0.5rem;
            }

            .nav-links.active {
                display: flex;
            }

            .nav-links a {
                width: 100%;
                padding: 0.75rem;
            }

            .menu-btn {
                display: block;
            }
        }

        /* Dropdown styles */
        .nav-dropdown {
            position: relative;
            display: inline-block;
        }

        .nav-dropdown-content {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: rgba(37, 93, 138, 0.95);
            min-width: 200px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            border-radius: 4px;
            z-index: 1001;
        }

        .nav-dropdown:hover .nav-dropdown-content {
            display: block;
        }

        .nav-dropdown-content a {
            color: white;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            transition: all 0.3s;
        }

        .nav-dropdown-content a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffd700;
        }

        /* Dropdown arrow */
        .nav-dropdown > a::after {
            content: '▼';
            font-size: 0.8em;
            margin-left: 5px;
            display: inline-block;
        }

        .hero_content {
            margin-top: 80px;
            padding: 20px;
            text-align: center;
            color: #255D8A;
        }

        .title-row {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            margin-bottom: 20px;
        }

        .hero_title {
            color: #255D8A;
            margin: 0;
            font-size: 2.0em;
            font-family: 'Cedarville Cursive', cursive;
            text-align: center;
        }

        .back-button {
            position: absolute;
            left: 40px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 10px 20px;
            background-color: #255D8A;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background-color: #1e4d70;
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .hero_content {
                padding: 20px 10px;
            }

            .back-button {
                left: 10px;
                padding: 8px 15px;
                font-size: 0.9em;
            }
        }

        .scroll-container {
            display: flex;
            overflow-x: auto;
            gap: 20px;
            padding: 20px;
            scroll-behavior: smooth;
        }

        .scroll-container::-webkit-scrollbar {
            height: 8px;
        }

        .scroll-container::-webkit-scrollbar-track {
            background: transparent;
        }

        .scroll-container::-webkit-scrollbar-thumb {
            background-color: #255D8A;
            border-radius: 20px;
        }

        .card {
            min-width: 300px;
            height: 500px;
            perspective: 1000px;
            border: 2px solid #255D8A;
            border-radius: 17px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }

        .card-inner {
            position: relative;
            width: 100%;
            height: 100%;
            transition: transform 0.8s;
            transform-style: preserve-3d;
        }

        .card.flipped .card-inner {
            transform: rotateY(180deg);
        }

        .card-front, .card-back {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .card-front img {
            width: 100%;
            height: 80%;
            object-fit: cover;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .card-front img.loaded {
            opacity: 1;
        }

        .card-front .content {
            padding: 15px;
            background: white;
            height: 30%;
        }

        .card-back {
            transform: rotateY(180deg);
            background: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .spot-name {
            font-size: 1.2em;
            font-weight: bold;
            color: #255D8A;
            margin-bottom: 5px;
        }

        .spot-location {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 10px;
        }

        .spot-description {
            font-size: 0.9em;
            line-height: 1.6;
            color: #444;
        }

        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 2000;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #255D8A;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Scroll Navigation */
        .scroll-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(37, 93, 138, 0.8);
            color: white;
            border: none;
            padding: 15px;
            cursor: pointer;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            z-index: 100;
        }

        .scroll-nav:hover {
            background: rgba(37, 93, 138, 1);
        }

        .scroll-left {
            left: 20px;
        }

        .scroll-right {
            right: 20px;
        }

        .scroll-nav i {
            font-size: 1.5rem;
        }

        /* No Results Message */
        .no-results {
            text-align: center;
            padding: 40px;
            color: #666;
            font-size: 1.2em;
            width: 100%;
        }

        /* Mobile Improvements */
        @media (max-width: 768px) {
            .scroll-nav {
                display: none;
            }

            .card {
                min-width: 250px;
                height: 450px;
            }

            .spot-name {
                font-size: 1.1em;
            }

            .spot-location {
                font-size: 0.85em;
            }

            .spot-description {
                font-size: 0.85em;
            }

            .hero_title {
                font-size: 1.8em;
                padding: 0 40px;
            }
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay">
        <div class="loading-spinner"></div>
    </div>

    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-content">
            <div class="logo">
                TripKo Pangasinan
            </div>
            <div class="nav-links">
                <a href="../homepage.php"><i class='bx bxs-home'></i> Home</a>
                <div class="nav-dropdown">
                    <a href="#"><i class='bx bxs-map-alt'></i> Places to Go</a>
                    <div class="nav-dropdown-content">
                        <a href="places-to-go.php">Beaches</a>
                        <a href="islands-to-go.php">Islands</a>
                        <a href="waterfalls-to-go.php">Waterfalls</a>
                        <a href="caves-to-go.php">Caves</a>
                        <a href="churches-to-go.php">Churches</a>
                        <a href="festivals-to-go.php">Festivals</a>
                    </div>
                </div>
                <a href="../things-to-do.php"><i class='bx bxs-calendar-star'></i> Things to Do</a>
                <a href="../terminal-routes.html"><i class='bx bxs-bus'></i> Route Finder</a>
                <a href="#"><i class='bx bxs-book-content'></i> Directory</a>
                <a href="#"><i class='bx bxs-check-circle'></i> Tourist Spot Status</a>
                <a href="#"><i class='bx bxs-info-circle'></i> About Us</a>
                <a href="#"><i class='bx bxs-phone'></i> Contact Us</a>
                <a href="../../../tripko-backend/logout.php"><i class='bx bx-log-out'></i> Logout</a>
            </div>
            <div class="menu-btn">
                <i class='bx bx-menu'></i>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <section class="hero_content">
        <div class="title-row">
            <h1 class="hero_title">Where Faith and Wonder Meet</h1>
            <a href="javascript:history.back()" class="back-button">
                <i class='bx bx-arrow-back'></i> Back
            </a>
        </div>

        <!-- Scroll Navigation Buttons -->
        <button class="scroll-nav scroll-left" aria-label="Scroll left">
            <i class='bx bx-chevron-left'></i>
        </button>
        <button class="scroll-nav scroll-right" aria-label="Scroll right">
            <i class='bx bx-chevron-right'></i>
        </button>

        <div class="scroll-container">
            <?php 
            if ($result->num_rows > 0):
                while ($row = $result->fetch_assoc()): 
            ?>
                <div class="card" data-name="<?php echo htmlspecialchars($row['name']); ?>">
                    <div class="card-inner">
                        <div class="card-front">
                            <img src="<?php 
                                $imagePath = $row['image_path'];
                                if (!$imagePath || $imagePath === 'placeholder.jpg') {
                                    echo '../../assets/images/placeholder.jpg';
                                } else {
                                    echo '../../../uploads/' . htmlspecialchars($imagePath);
                                }
                            ?>" 
                                 alt="<?php echo htmlspecialchars($row['name']); ?>"
                                 loading="lazy"
                                 onerror="this.src='../../assets/images/placeholder.jpg'">
                            <div class="content">
                                <div class="spot-name"><?php echo htmlspecialchars($row['name']); ?></div>
                                <div class="spot-location"><?php echo htmlspecialchars($row['town_name']); ?></div>
                            </div>
                        </div>
                        <div class="card-back">
                            <h3 class="spot-name"><?php echo htmlspecialchars($row['name']); ?></h3>
                            <p class="spot-description"><?php echo htmlspecialchars($row['description']); ?></p>
                        </div>
                    </div>
                </div>
            <?php 
                endwhile;
            else:
            ?>
                <div class="no-results">
                    <i class='bx bx-church' style="font-size: 2em; margin-bottom: 10px;"></i>
                    <p>No churches found. Please check back later.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Navigation Menu
            const menuBtn = document.querySelector('.menu-btn');
            const navLinks = document.querySelector('.nav-links');
            const dropdownBtn = document.querySelector('.nav-dropdown > a');
            const dropdownContent = document.querySelector('.nav-dropdown-content');
            const dropdown = document.querySelector('.nav-dropdown');
            
            // Loading Overlay
            const loadingOverlay = document.querySelector('.loading-overlay');
            let loadedImages = 0;
            const totalImages = document.querySelectorAll('.card-front img').length;

            // Handle image loading
            document.querySelectorAll('.card-front img').forEach(img => {
                if (img.complete) {
                    imageLoaded(img);
                } else {
                    img.addEventListener('load', () => imageLoaded(img));
                    img.addEventListener('error', () => imageLoaded(img));
                }
            });

            function imageLoaded(img) {
                img.classList.add('loaded');
                loadedImages++;
                if (loadedImages >= totalImages) {
                    loadingOverlay.style.display = 'none';
                }
            }

            // Menu Interactions
            menuBtn?.addEventListener('click', () => {
                navLinks?.classList.toggle('active');
            });

            dropdownBtn?.addEventListener('click', (e) => {
                e.preventDefault();
                dropdownContent?.classList.toggle('show');
                dropdown?.classList.toggle('active');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.nav-dropdown')) {
                    dropdownContent?.classList.remove('show');
                    dropdown?.classList.remove('active');
                }
                if (!e.target.closest('.nav-content')) {
                    navLinks?.classList.remove('active');
                }
            });
            
            // Card Interactions
            const cards = document.querySelectorAll('.card');
            let touchStartX = 0;
            let touchEndX = 0;

            cards.forEach(card => {
                // Click to flip
                card.addEventListener('click', () => {
                    if (Math.abs(touchEndX - touchStartX) < 5) { // Only flip if it's a tap, not a swipe
                        cards.forEach(c => {
                            if (c !== card) c.classList.remove('flipped');
                        });
                        card.classList.toggle('flipped');
                    }
                });

                // Touch interactions
                card.addEventListener('touchstart', e => {
                    touchStartX = e.changedTouches[0].screenX;
                });

                card.addEventListener('touchend', e => {
                    touchEndX = e.changedTouches[0].screenX;
                    const diffX = touchEndX - touchStartX;

                    if (Math.abs(diffX) > 50) { // Swipe threshold
                        card.classList.remove('flipped');
                    }
                });
            });

            // Scroll Navigation
            const scrollContainer = document.querySelector('.scroll-container');
            const scrollLeftBtn = document.querySelector('.scroll-left');
            const scrollRightBtn = document.querySelector('.scroll-right');
            const cardWidth = 320; // Width + gap

            scrollLeftBtn.addEventListener('click', () => {
                scrollContainer.scrollBy({
                    left: -cardWidth,
                    behavior: 'smooth'
                });
            });

            scrollRightBtn.addEventListener('click', () => {
                scrollContainer.scrollBy({
                    left: cardWidth,
                    behavior: 'smooth'
                });
            });

            // Show/hide scroll buttons based on scroll position
            const updateScrollButtons = () => {
                const { scrollLeft, scrollWidth, clientWidth } = scrollContainer;
                scrollLeftBtn.style.display = scrollLeft > 0 ? 'flex' : 'none';
                scrollRightBtn.style.display = 
                    scrollLeft < (scrollWidth - clientWidth - 10) ? 'flex' : 'none';
            };

            scrollContainer.addEventListener('scroll', updateScrollButtons);
            window.addEventListener('resize', updateScrollButtons);
            updateScrollButtons(); // Initial check

            // Keyboard Navigation
            document.addEventListener('keydown', e => {
                if (e.key === 'ArrowLeft') {
                    scrollContainer.scrollBy({
                        left: -cardWidth,
                        behavior: 'smooth'
                    });
                } else if (e.key === 'ArrowRight') {
                    scrollContainer.scrollBy({
                        left: cardWidth,
                        behavior: 'smooth'
                    });
                }
            });

            // Handle errors gracefully
            window.addEventListener('error', function(e) {
                console.error('Page error:', e.error);
                loadingOverlay.style.display = 'none';
            });
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>