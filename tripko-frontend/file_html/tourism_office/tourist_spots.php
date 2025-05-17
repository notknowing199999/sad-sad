<?php
session_start();
require_once '../../../tripko-backend/config/Database.php';
require_once '../../../tripko-backend/models/TouristSpot.php';

try {
    // Verify user is logged in and is a tourism officer
    if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'tourism_officer') {
        throw new Exception('Unauthorized access. Please log in as a tourism officer.');
    }

    if (!isset($_SESSION['town_id'])) {
        throw new Exception('No municipality assigned. Please contact an administrator.');
    }

    // Initialize database connection
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    // Get tourism officer's town name
    $town_query = "SELECT name FROM towns WHERE town_id = ?";
    $town_stmt = $conn->prepare($town_query);
    if (!$town_stmt) {
        throw new Exception("Failed to prepare town query");
    }
    
    $town_stmt->bind_param("i", $_SESSION['town_id']);
    if (!$town_stmt->execute()) {
        throw new Exception("Failed to fetch town details");
    }
    
    $town_result = $town_stmt->get_result();
    $town_name = $town_result->fetch_assoc()['name'] ?? 'Unknown Municipality';

    // Initialize tourist spot object
    $tourist_spot = new TouristSpot($conn);
    
    // Get tourist spots for this town
    $query = "SELECT ts.*, COUNT(r.review_id) as review_count, AVG(r.rating) as avg_rating
              FROM tourist_spots ts
              LEFT JOIN reviews r ON ts.spot_id = r.spot_id
              WHERE ts.town_id = ?
              GROUP BY ts.spot_id
              ORDER BY ts.name ASC";
              
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Failed to prepare spots query");
    }
    
    $stmt->bind_param("i", $_SESSION['town_id']);
    if (!$stmt->execute()) {
        throw new Exception("Failed to fetch tourist spots");
    }
    
    $result = $stmt->get_result();

} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tourist Spots - <?php echo htmlspecialchars($town_name); ?></title>
    <link rel="stylesheet" href="../../file_css/tourism_spots.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php include 'components/sidebar.php'; ?>

    <div class="content">
        <?php if (isset($error)): ?>
            <div class="error-message">
                <i class='bx bx-error-circle'></i>
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php else: ?>
            <div class="header">
                <h2>Tourist Spots in <?php echo htmlspecialchars($town_name); ?></h2>
                <button class="add-spot-btn" onclick="showAddSpotModal()">
                    <i class='bx bx-plus'></i> Add New Spot
                </button>
            </div>

            <div class="filters">
                <div class="search-box">
                    <i class='bx bx-search'></i>
                    <input type="text" id="searchSpot" placeholder="Search tourist spots...">
                </div>
                <div class="filter-box">
                    <select id="categoryFilter">
                        <option value="">All Categories</option>
                        <option value="Beaches">Beaches</option>
                        <option value="Islands">Islands</option>
                        <option value="Waterfalls">Waterfalls</option>
                        <option value="Caves">Caves</option>
                        <option value="Churches">Churches</option>
                        <option value="Festivals">Festivals</option>
                    </select>
                    <select id="statusFilter">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <?php if ($result && $result->num_rows > 0): ?>
                <div class="spots-grid">
                    <?php while ($spot = $result->fetch_assoc()): ?>
                        <div class="spot-card" data-category="<?php echo htmlspecialchars($spot['category']); ?>" 
                             data-status="<?php echo htmlspecialchars($spot['status'] ?? 'active'); ?>">
                            <div class="spot-image">
                                <img src="<?php 
                                    echo $spot['image_path'] ? 
                                        '../../../uploads/' . htmlspecialchars($spot['image_path']) : 
                                        '../../../assets/images/placeholder.jpg'; 
                                ?>" alt="<?php echo htmlspecialchars($spot['name']); ?>"
                                onerror="this.src='../../../assets/images/placeholder.jpg'">
                                <div class="spot-status <?php echo htmlspecialchars($spot['status'] ?? 'active'); ?>">
                                    <?php echo ucfirst(htmlspecialchars($spot['status'] ?? 'active')); ?>
                                </div>
                            </div>
                            <div class="spot-info">
                                <h3><?php echo htmlspecialchars($spot['name']); ?></h3>
                                <p class="category"><?php echo htmlspecialchars($spot['category']); ?></p>
                                <div class="spot-stats">
                                    <span class="reviews">
                                        <i class='bx bx-comment'></i>
                                        <?php echo $spot['review_count']; ?> reviews
                                    </span>
                                    <span class="rating">
                                        <i class='bx bx-star'></i>
                                        <?php echo $spot['avg_rating'] ? 
                                            number_format($spot['avg_rating'], 1) : 'No ratings'; ?>
                                    </span>
                                </div>
                                <div class="spot-actions">
                                    <button onclick="viewSpotDetails(<?php echo $spot['spot_id']; ?>)" 
                                            class="view-btn">
                                        <i class='bx bx-show'></i>
                                    </button>
                                    <button onclick="editSpot(<?php echo $spot['spot_id']; ?>)" 
                                            class="edit-btn">
                                        <i class='bx bx-edit'></i>
                                    </button>
                                    <button onclick="toggleSpotStatus(<?php echo $spot['spot_id']; ?>, 
                                        '<?php echo $spot['status'] ?? 'active'; ?>')" 
                                            class="toggle-btn">
                                        <i class='bx bx-power-off'></i>
                                    </button>
                                    <button onclick="deleteSpot(<?php echo $spot['spot_id']; ?>)" 
                                            class="delete-btn">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="no-spots">
                    <i class='bx bx-map-alt'></i>
                    <p>No tourist spots found in your municipality.</p>
                    <p>Click the "Add New Spot" button to add one.</p>
                </div>
            <?php endif; ?>

            <!-- Add/Edit Tourist Spot Modal -->
            <div id="spotModal" class="modal">
                <!-- Modal content will be dynamically populated -->
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Search and filter functionality
        const searchInput = document.getElementById('searchSpot');
        const categoryFilter = document.getElementById('categoryFilter');
        const statusFilter = document.getElementById('statusFilter');
        const spotCards = document.querySelectorAll('.spot-card');

        function filterSpots() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedCategory = categoryFilter.value.toLowerCase();
            const selectedStatus = statusFilter.value.toLowerCase();

            spotCards.forEach(card => {
                const spotName = card.querySelector('h3').textContent.toLowerCase();
                const category = card.dataset.category.toLowerCase();
                const status = card.dataset.status.toLowerCase();
                
                const matchesSearch = spotName.includes(searchTerm);
                const matchesCategory = !selectedCategory || category === selectedCategory;
                const matchesStatus = !selectedStatus || status === selectedStatus;

                card.style.display = 
                    matchesSearch && matchesCategory && matchesStatus ? 'block' : 'none';
            });

            // Show/hide no results message
            const visibleCards = Array.from(spotCards)
                .filter(card => card.style.display !== 'none').length;
            
            let noResults = document.querySelector('.no-results');
            if (!noResults) {
                noResults = document.createElement('div');
                noResults.className = 'no-results';
                noResults.innerHTML = `
                    <i class='bx bx-search'></i>
                    <p>No tourist spots found matching your criteria.</p>
                `;
                document.querySelector('.spots-grid').after(noResults);
            }
            noResults.style.display = visibleCards === 0 ? 'block' : 'none';
        }

        searchInput.addEventListener('input', filterSpots);
        categoryFilter.addEventListener('change', filterSpots);
        statusFilter.addEventListener('change', filterSpots);

        // Spot status toggle
        function toggleSpotStatus(spotId, currentStatus) {
            const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
            const action = newStatus === 'active' ? 'activate' : 'deactivate';

            Swal.fire({
                title: 'Confirm Status Change',
                text: `Are you sure you want to ${action} this tourist spot?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`../../../tripko-backend/api/tourist_spot/update_status.php`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            spot_id: spotId,
                            status: newStatus
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: `Tourist spot ${action}d successfully`,
                                icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            throw new Error(data.message || 'Failed to update status');
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error!',
                            text: error.message,
                            icon: 'error'
                        });
                    });
                }
            });
        }

        // Delete spot
        function deleteSpot(spotId) {
            Swal.fire({
                title: 'Delete Tourist Spot',
                text: 'Are you sure you want to delete this tourist spot? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'No, keep it'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`../../../tripko-backend/api/tourist_spot/delete.php?spot_id=${spotId}`, {
                        method: 'DELETE'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Tourist spot has been deleted.',
                                icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            throw new Error(data.message || 'Failed to delete tourist spot');
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error!',
                            text: error.message,
                            icon: 'error'
                        });
                    });
                }
            });
        }
    </script>
</body>
</html>