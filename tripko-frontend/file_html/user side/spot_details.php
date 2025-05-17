<?php
session_start();
require_once '../../../tripko-backend/config/Database.php';
require_once '../../../tripko-backend/models/TouristSpot.php';

try {
    if (!isset($_GET['id'])) {
        throw new Exception('Missing spot ID');
    }

    $spot_id = intval($_GET['id']);
    
    // Initialize database connection
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    // Get user type from session
    $userType = $_SESSION['user_type'] ?? 'guest';
    $userTownId = $_SESSION['town_id'] ?? null;

    // Get spot details with town information
    $query = "SELECT ts.*, t.name as town_name 
              FROM tourist_spots ts
              JOIN towns t ON ts.town_id = t.town_id
              WHERE ts.spot_id = ?";
              
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Failed to prepare query");
    }
    
    $stmt->bind_param("i", $spot_id);
    if (!$stmt->execute()) {
        throw new Exception("Failed to fetch tourist spot details");
    }
    
    $result = $stmt->get_result();
    $spot = $result->fetch_assoc();

    if (!$spot) {
        throw new Exception('Tourist spot not found', 404);
    }

    // Check if spot is inactive and user is not authorized
    if (($spot['status'] === 'inactive') && 
        !in_array($userType, ['admin', 'tourism_officer']) && 
        ($userType === 'tourism_officer' && $spot['town_id'] != $userTownId)) {
        throw new Exception('This tourist spot is currently unavailable', 403);
    }

} catch (Exception $e) {
    $error = $e->getMessage();
    $errorCode = $e->getCode();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($spot) ? htmlspecialchars($spot['name']) : 'Tourist Spot Details'; ?></title>
    <link rel="stylesheet" href="../../file_css/spot_details.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php include 'components/navbar.php'; ?>

    <div class="content">
        <?php if (isset($error)): ?>
            <div class="error-container">
                <div class="error-message">
                    <i class='bx bx-error-circle'></i>
                    <h2><?php 
                        echo $errorCode === 404 ? 'Not Found' : 
                            ($errorCode === 403 ? 'Access Denied' : 'Error');
                    ?></h2>
                    <p><?php echo htmlspecialchars($error); ?></p>
                    <a href="javascript:history.back()" class="back-button">
                        <i class='bx bx-arrow-back'></i> Go Back
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="spot-details-content">
                <div class="spot-header">
                    <?php if ($spot['status'] === 'inactive'): ?>
                        <div class="status-banner inactive">
                            <i class='bx bx-info-circle'></i>
                            This tourist spot is currently inactive
                        </div>
                    <?php endif; ?>
                    
                    <div class="spot-image">
                        <img src="<?php 
                            echo $spot['image_path'] ? 
                                '../../../uploads/' . htmlspecialchars($spot['image_path']) : 
                                '../../../assets/images/placeholder.jpg'; 
                        ?>" alt="<?php echo htmlspecialchars($spot['name']); ?>"
                        onerror="this.src='../../../assets/images/placeholder.jpg'">
                    </div>

                    <div class="spot-info">
                        <h1><?php echo htmlspecialchars($spot['name']); ?></h1>
                        <div class="spot-meta">
                            <span class="location">
                                <i class='bx bx-map'></i>
                                <?php echo htmlspecialchars($spot['town_name']); ?>
                            </span>
                            <span class="category">
                                <i class='bx bx-category'></i>
                                <?php echo htmlspecialchars($spot['category']); ?>
                            </span>
                            <?php if ($spot['contact_info']): ?>
                                <span class="contact">
                                    <i class='bx bx-phone'></i>
                                    <?php echo htmlspecialchars($spot['contact_info']); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="rating-section">
                            <div class="star-rating">
                                <?php for ($i = 1; $i <= 5; $i++) : ?>
                                    <span class="star <?php echo ($i <= $spot['rating']) ? 'filled' : ''; ?>">★</span>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <div class="spot-description">
                            <?php echo nl2br(htmlspecialchars($spot['description'])); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>