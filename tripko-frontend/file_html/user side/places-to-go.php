<?php
session_start();

// Database connection configuration
$host = "localhost";     // Your database host
$username = "root";      // Your database username        @media (max-width: 768px) {
 // Your database password
$database = "tripko_db"; // Your database name

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch tourist spots with town information - only beaches
$query = "SELECT ts.*, t.name as town_name, t.image_path as town_image_path 
          FROM tourist_spots ts 
          LEFT JOIN towns t ON ts.town_id = t.town_id 
          WHERE ts.status = 'active' AND ts.category = 'Beach'
          ORDER BY ts.name ASC";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$result = $stmt->get_result();

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TripKo Pangasinan - Places to Go</title>
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

        .nav-dropdown-content.show {
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
            transition: transform 0.3s;
        }

        .nav-dropdown.active > a::after {
            transform: rotate(180deg);
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
        
        .hero_content {
            margin-top: 9px;
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
            padding-top: 20px;
        }

        .hero_title {
            color: #255D8A;
            margin: 0;
            font-size: 2.0em;
            font-family: 'Cedarville Cursive', cursive;
            text-align: center;
        }        .scroll-container {
            display: flex;
            overflow-x: auto;
            gap: 20px;
            padding: 20px;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
            position: relative;
            scroll-snap-type: x mandatory;
        }
        
        .scroll-container .card {
            scroll-snap-align: start;
        }
        
        /* Custom scrollbar styling */
        .scroll-container::-webkit-scrollbar {
            height: 8px;
            background-color: #f5f5f5;
            border-radius: 4px;
        }
        
        .scroll-container::-webkit-scrollbar-thumb {
            background-color: #255D8A;
            border-radius: 4px;
        }
        
        .scroll-container::-webkit-scrollbar-track {
            background-color: #f5f5f5;
            border-radius: 4px;
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
        }        .card {
            min-width: 300px;
            height: 500px;
            perspective: 1000px;
            border: 2px solid #255D8A;
            border-radius: 17px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(37, 93, 138, 0.2);
        }

        .card-inner {
            position: relative;
            width: 100%;
            height: 100%;
            transition: transform 0.8s;
            transform-style: preserve-3d;
        }

        /* Remove hover and add clicked state */
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
        }        .card-front img {
            width: 100%;
            height: 80%;
            object-fit: cover;
            object-position: center;
            transition: transform 0.3s ease;
        }
        
        .card-front img:hover {
            transform: scale(1.05);
        }

        .card-front .content {
            padding: 15px;
            background: white;
            height: 30%;
        }        .card-back {
            transform: rotateY(180deg);
            background: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background-color: #f8f9fa;
            border: 2px solid #255D8A;
        }

        .card-back .spot-name {
            color: #255D8A;
            font-size: 1.4em;
            margin-bottom: 15px;
            text-align: center;
        }

        .card-back .spot-description {
            color: #444;
            line-height: 1.6;
            text-align: justify;
            margin-bottom: 20px;
            flex-grow: 1;
            overflow-y: auto;
            padding-right: 10px;
        }

        .card-back .contact-info {
            color: #666;
            font-size: 0.9em;
            text-align: center;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
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

        .no-results {
            text-align: center;
            padding: 40px;
            color: #255D8A;
            font-size: 1.2em;
        }
        
        .no-results i {
            display: block;
            margin-bottom: 20px;
        }
        
        /* Loading animation */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
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

        .scroll-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #255D8A;
            color: white;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            z-index: 10;
        }

        .scroll-btn:hover {
            background: #1e4d70;
            transform: translateY(-50%) scale(1.1);
        }

        .scroll-btn.scroll-left {
            left: 10px;
        }

        .scroll-btn.scroll-right {
            right: 10px;
        }

        .scroll-btn i {
            font-size: 1.5rem;
        }

        @media (max-width: 768px) {
            .scroll-btn {
                width: 35px;
                height: 35px;
            }

            .scroll-btn i {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-content">
            <div class="logo">
                TripKo Pangasinan
            </div>
            <div class="nav-links">
                <a href="../homepage.php"><i class='bx bxs-home'></i>Home</a>
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
                <a href="../routeFinder.php"><i class='bx bxs-bus'></i> Route Finder</a>
                <a href="../"><i class='bx bxs-book-content'></i> Directory</a>
                <a href="#"><i class='bx bxs-check-circle'></i> Tourist Spot Status</a>
                <a href="#"><i class='bx bxs-info-circle'></i> About Us</a>
                <a href="#"><i class='bx bxs-phone'></i> Contact Us</a>
                <a href="../../../tripko-backend/logout.php"><i class='bx bx-log-out'></i> Logout</a>
            </div>
            <div class="menu-btn">
                <i class='bx bx-menu'></i>
            </div>
        </div>
    </nav>    <!-- Main Content -->    
    <section class="hero_content">
        <div class="title-row">
            <a href="javascript:history.back()" class="back-button">
                <i class='bx bx-arrow-back'></i> Back
            </a>
            <h1 class="hero_title">Where the Waves Meet Your Soul</h1>
        </div>
        
        <?php if ($result->num_rows === 0): ?>
            <div class="no-results">
                <i class='bx bx-water' style="font-size: 48px; color: #255D8A;"></i>
                <p>No beaches found at the moment. Check back later!</p>
            </div>
        <?php else: ?>
            <div class="scroll-container">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="card">
                        <div class="card-inner">
                            <div class="card-front">                            <img src="<?php 
                                    $spotImagePath = $row['image_path'];
                                    $townImagePath = $row['town_image_path'];
                                    
                                    // First try spot image, then town image, then fallback
                                    if (!empty($spotImagePath) && file_exists('../../../uploads/' . $spotImagePath)) {
                                        echo '../../../uploads/' . htmlspecialchars($spotImagePath);
                                    } else if (!empty($townImagePath) && file_exists('../../../uploads/' . $townImagePath)) {
                                        echo '../../../uploads/' . htmlspecialchars($townImagePath);
                                    } else {
                                        echo '../../assets/images/placeholder.jpg';
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
                            <div class="card-back">                            <h3 class="spot-name"><?php echo htmlspecialchars($row['name']); ?></h3>
                                <p class="spot-description"><?php echo htmlspecialchars($row['description'] ?: 'No description available.'); ?></p>
                                <?php if (!empty($row['contact_info'])): ?>
                                    <div class="contact-info">
                                        <i class='bx bxs-phone'></i> <?php echo htmlspecialchars($row['contact_info']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const menuBtn = document.querySelector('.menu-btn');
            const navLinks = document.querySelector('.nav-links');
            const dropdownBtn = document.querySelector('.nav-dropdown > a');
            const dropdownContent = document.querySelector('.nav-dropdown-content');
            const dropdown = document.querySelector('.nav-dropdown');
            
            menuBtn?.addEventListener('click', () => {
                navLinks?.classList.toggle('active');
            });

            // Handle dropdown click
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
            });            // Show loading overlay
            const loadingOverlay = document.createElement('div');
            loadingOverlay.className = 'loading-overlay';
            loadingOverlay.innerHTML = '<div class="loading-spinner"></div>';
            document.body.appendChild(loadingOverlay);

            // Hide loading overlay when all images are loaded
            Promise.all(
                Array.from(document.images)
                    .map(img => {
                        if (img.complete) return Promise.resolve();
                        return new Promise((resolve, reject) => {
                            img.addEventListener('load', resolve);
                            img.addEventListener('error', resolve); // Resolve on error too
                        });
                    })
            ).then(() => {
                loadingOverlay.style.display = 'none';
            });

            // Add click handlers for cards
            const cards = document.querySelectorAll('.card');
            cards.forEach(card => {
                card.addEventListener('click', () => {
                    // Remove flipped class from all other cards
                    cards.forEach(c => {
                        if (c !== card) c.classList.remove('flipped');
                    });
                    // Toggle flipped class on clicked card
                    card.classList.toggle('flipped');
                });
            });

            // Touch interaction for cards
            cards.forEach(card => {
                let touchStartX = 0;
                let touchEndX = 0;

                card.addEventListener('touchstart', e => {
                    touchStartX = e.changedTouches[0].screenX;
                }, false);

                card.addEventListener('touchend', e => {
                    touchEndX = e.changedTouches[0].screenX;
                    handleSwipe(card, touchStartX, touchEndX);
                }, false);
            });

            function handleSwipe(card, startX, endX) {
                const SWIPE_THRESHOLD = 50;
                const diffX = endX - startX;

                if (Math.abs(diffX) > SWIPE_THRESHOLD) {
                    if (diffX > 0 && !card.classList.contains('flipped')) {
                        // Swipe right - flip card
                        card.classList.add('flipped');
                    } else if (diffX < 0 && card.classList.contains('flipped')) {
                        // Swipe left - unflip card
                        card.classList.remove('flipped');
                    }
                }
            }

            // Add scroll buttons
            const scrollContainer = document.querySelector('.scroll-container');
            const scrollLeftBtn = document.createElement('button');
            const scrollRightBtn = document.createElement('button');
            
            scrollLeftBtn.className = 'scroll-btn scroll-left';
            scrollRightBtn.className = 'scroll-btn scroll-right';
            scrollLeftBtn.innerHTML = '<i class="bx bx-chevron-left"></i>';
            scrollRightBtn.innerHTML = '<i class="bx bx-chevron-right"></i>';

            document.querySelector('.hero_content').appendChild(scrollLeftBtn);
            document.querySelector('.hero_content').appendChild(scrollRightBtn);

            // Scroll button functionality
            scrollLeftBtn.addEventListener('click', () => {
                scrollContainer.scrollBy({
                    left: -320,
                    behavior: 'smooth'
                });
            });

            scrollRightBtn.addEventListener('click', () => {
                scrollContainer.scrollBy({
                    left: 320,
                    behavior: 'smooth'
                });
            });

            // Show/hide scroll buttons based on scroll position
            scrollContainer.addEventListener('scroll', () => {
                scrollLeftBtn.style.display = 
                    scrollContainer.scrollLeft > 0 ? 'flex' : 'none';
                scrollRightBtn.style.display = 
                    scrollContainer.scrollLeft < (scrollContainer.scrollWidth - scrollContainer.clientWidth) 
                    ? 'flex' : 'none';
            });

            // Initial check for scroll buttons
            setTimeout(() => {
                scrollLeftBtn.style.display = 'none';
                scrollRightBtn.style.display = 
                    scrollContainer.scrollWidth > scrollContainer.clientWidth 
                    ? 'flex' : 'none';
            }, 100);
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>