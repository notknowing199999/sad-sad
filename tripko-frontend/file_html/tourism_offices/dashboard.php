<?php
require_once('../../../tripko-backend/config/Database.php');
require_once('../../../tripko-backend/config/check_session.php');
checkTourismOfficerSession();

// Establish database connection
$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die("Database connection failed");
}

// Get the tourism officer's municipality info
$user_id = $_SESSION['user_id'];
$query = "SELECT t.name as town_name, t.town_id 
          FROM towns t 
          INNER JOIN user u ON u.town_id = t.town_id 
          WHERE u.user_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$town_data = $result->fetch_assoc();

$town_name = $town_data['town_name'];
$town_id = $town_data['town_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tourism Officer Dashboard - <?php echo htmlspecialchars($town_name); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Kameron:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Kameron', serif;
        }
    </style>
</head>
<body class="bg-[#F3F1E8]">
    <!-- Navigation -->
    <nav class="bg-[#255D4F] text-white p-4 fixed w-full top-0 z-50">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <span class="text-2xl font-bold">TripKo Tourism Office</span>
                <span class="text-lg">| <?php echo htmlspecialchars($town_name); ?></span>
            </div>
            <div class="flex items-center space-x-4">
                <a href="../../../tripko-backend/config/confirm_logout.php" class="hover:text-gray-300">
                    <i class="fas fa-sign-out-alt mr-2"></i>Sign Out
                </a>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="fixed left-0 top-16 h-full w-64 bg-[#255D4F] text-white p-4">
        <div class="space-y-4">
            <a href="dashboard.php" class="block py-2.5 px-4 rounded transition duration-200 bg-[#1e4d70] hover:bg-[#1e4d70]">
                <i class="fas fa-home mr-2"></i>Dashboard
            </a>
            <a href="tourist_spots.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-[#1e4d70]">
                <i class="fas fa-umbrella-beach mr-2"></i>Tourist Spots
            </a>
            <a href="festivals.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-[#1e4d70]">
                <i class="fas fa-calendar-alt mr-2"></i>Festivals
            </a>
            <a href="itineraries.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-[#1e4d70]">
                <i class="fas fa-map-marked-alt mr-2"></i>Itineraries
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="ml-64 mt-16 p-8">
        <div class="container mx-auto">
            <h1 class="text-3xl font-bold mb-8">Welcome, Tourism Officer of <?php echo htmlspecialchars($town_name); ?></h1>
            
            <!-- Statistics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Tourist Spots Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">Tourist Spots</h3>
                        <i class="fas fa-umbrella-beach text-2xl text-[#255D4F]"></i>
                    </div>
                    <p class="text-3xl font-bold text-[#255D4F]" id="touristSpotCount">0</p>
                    <p class="text-sm text-gray-600">Total locations</p>
                </div>

                <!-- Festivals Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">Festivals</h3>
                        <i class="fas fa-calendar-alt text-2xl text-[#255D4F]"></i>
                    </div>
                    <p class="text-3xl font-bold text-[#255D4F]" id="festivalCount">0</p>
                    <p class="text-sm text-gray-600">Annual events</p>
                </div>

                <!-- Itineraries Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">Itineraries</h3>
                        <i class="fas fa-map-marked-alt text-2xl text-[#255D4F]"></i>
                    </div>
                    <p class="text-3xl font-bold text-[#255D4F]" id="itineraryCount">0</p>
                    <p class="text-sm text-gray-600">Travel routes</p>
                </div>

                <!-- Visitor Stats Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">Monthly Visitors</h3>
                        <i class="fas fa-users text-2xl text-[#255D4F]"></i>
                    </div>
                    <p class="text-3xl font-bold text-[#255D4F]" id="visitorCount">0</p>
                    <p class="text-sm text-gray-600">Website views</p>
                </div>
            </div>

            <!-- Recent Activity & Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Activity -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold mb-4">Recent Activity</h2>
                    <div class="space-y-4" id="recentActivity">
                        <!-- Activity items will be populated by JavaScript -->
                        <div class="animate-pulse">
                            <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                            <div class="space-y-3 mt-4">
                                <div class="h-4 bg-gray-200 rounded"></div>
                                <div class="h-4 bg-gray-200 rounded"></div>
                                <div class="h-4 bg-gray-200 rounded"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold mb-4">Quick Actions</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <a href="tourist_spots.php?action=add" class="flex items-center p-4 bg-[#255D4F] text-white rounded-lg hover:bg-[#1e4d70] transition duration-200">
                            <i class="fas fa-plus-circle mr-2"></i>
                            Add Tourist Spot
                        </a>
                        <a href="festivals.php?action=add" class="flex items-center p-4 bg-[#255D4F] text-white rounded-lg hover:bg-[#1e4d70] transition duration-200">
                            <i class="fas fa-plus-circle mr-2"></i>
                            Add Festival
                        </a>
                        <a href="itineraries.php?action=add" class="flex items-center p-4 bg-[#255D4F] text-white rounded-lg hover:bg-[#1e4d70] transition duration-200">
                            <i class="fas fa-plus-circle mr-2"></i>
                            Create Itinerary
                        </a>
                        <a href="#" onclick="generateReport()" class="flex items-center p-4 bg-[#255D4F] text-white rounded-lg hover:bg-[#1e4d70] transition duration-200">
                            <i class="fas fa-chart-bar mr-2"></i>
                            Generate Report
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Function to load dashboard statistics
        async function loadDashboardStats() {
            try {
                const response = await fetch(`../../../tripko-backend/api/tourism_officers/dashboard_stats.php?town_id=<?php echo $town_id; ?>`);
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('touristSpotCount').textContent = data.stats.tourist_spots || 0;
                    document.getElementById('festivalCount').textContent = data.stats.festivals || 0;
                    document.getElementById('itineraryCount').textContent = data.stats.itineraries || 0;
                    document.getElementById('visitorCount').textContent = data.stats.monthly_visitors || 0;
                }
            } catch (error) {
                console.error('Error loading dashboard stats:', error);
            }
        }

        // Function to load recent activity
        async function loadRecentActivity() {
            try {
                const response = await fetch(`../../../tripko-backend/api/tourism_officers/recent_activity.php?town_id=<?php echo $town_id; ?>`);
                const data = await response.json();
                
                if (data.success) {
                    const activityContainer = document.getElementById('recentActivity');
                    activityContainer.innerHTML = '';
                    
                    data.activities.forEach(activity => {
                        const activityElement = document.createElement('div');
                        activityElement.className = 'border-b border-gray-200 pb-3 mb-3';
                        activityElement.innerHTML = `
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-semibold">${activity.title}</p>
                                    <p class="text-sm text-gray-600">${activity.description}</p>
                                </div>
                                <span class="text-sm text-gray-500">${activity.timestamp}</span>
                            </div>
                        `;
                        activityContainer.appendChild(activityElement);
                    });
                }
            } catch (error) {
                console.error('Error loading recent activity:', error);
            }
        }

        // Function to generate report
        function generateReport() {
            window.location.href = `report.php?town_id=<?php echo $town_id; ?>`;
        }

        // Load data when page loads
        document.addEventListener('DOMContentLoaded', () => {
            loadDashboardStats();
            loadRecentActivity();
        });
    </script>
</body>
</html>