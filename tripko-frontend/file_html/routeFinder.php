<?php
session_start();
require_once '../../tripko-backend/check_session.php';
require_once '../../tripko-backend/config/Database.php';

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['query'])) {
    $query = htmlspecialchars($_GET['query']);

    // Search for terminal locations
    $sql = "SELECT location_name FROM terminal_locations WHERE location_name LIKE ?";
    $stmt = $conn->prepare($sql);
    $queryParam = "%$query%";
    $stmt->bind_param("s", $queryParam);
    $stmt->execute();
    $result = $stmt->get_result();
    $terminalResults = [];
    while ($row = $result->fetch_assoc()) {
        $terminalResults[] = $row;
    }

    // Search for tourist spots
    $sql = "SELECT name AS location_name, latitude, longitude FROM tourist_spots WHERE name LIKE ? AND status = 'active' AND (category = 'beach' OR name LIKE '%beach%')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $queryParam);
    $stmt->execute();
    $result = $stmt->get_result();
    $touristSpotResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Merge results
    $results = array_merge($terminalResults, $touristSpotResults);

    header('Content-Type: application/json');
    echo json_encode($results);
    exit();
}

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: SignUp_LogIn_Form.php");
    exit();
}

// Default map center coordinates for Pangasinan (Lingayen)
$default_lat = 16.0292;
$default_lng = 120.2229;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Route Finder - TripKo</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../file_css/navbar.css">
    <link rel="stylesheet" href="../file_css/userpage.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.min.js"></script>
    

</head>
<body>
    <?php include_once 'navbar.php'; renderNavbar(); ?>

    <div class="main-container">
        <div class="form-container">
            <h1>Route Finder</h1>
            <form id="route-form">
              
                    <div class="form-group">
                        <label for="starting-point">Starting Point</label>
                        <input type="text" id="starting-point" name="starting_point" placeholder="Enter starting point" required oninput="searchLocations(this.value, 'starting-suggestions')">
                        <div id="starting-suggestions" class="suggestions-box"></div>
                    </div>
                    <div class="form-group">
                        <label for="ending-point">Ending Point</label>
                        <input type="text" id="ending-point" name="ending_point" placeholder="Enter ending point" required oninput="searchLocations(this.value, 'ending-suggestions')">
                        <div id="ending-suggestions" class="suggestions-box"></div>
                    </div>
               
                <button type="submit" class="btn">Find Route</button>
            </form>
        </div>

        <div id="map-container">
            <div id="map"></div>
        </div>
        
        <!-- Added container for route details -->
        <div id="route-details"></div>
    </div>

    <script>
        // Search for locations as user types
        async function searchLocations(query, suggestionsId) {
            if (query.length < 2) {
                document.getElementById(suggestionsId).style.display = 'none';
                return;
            }

            const response = await fetch(`routeFinder.php?query=${encodeURIComponent(query)}`);
            const results = await response.json();
            const suggestions = document.getElementById(suggestionsId);
            
            suggestions.innerHTML = '';
            suggestions.style.display = results.length > 0 ? 'block' : 'none';

            results.forEach(location => {
                const option = document.createElement('div');
                option.textContent = location.location_name;
                option.classList.add('suggestion-item');
                option.addEventListener('click', () => {
                    const inputId = suggestionsId === 'starting-suggestions' ? 'starting-point' : 'ending-point';
                    document.getElementById(inputId).value = location.location_name;
                    suggestions.style.display = 'none';
                });
                suggestions.appendChild(option);
            });
        }

        // Close suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.matches('#starting-point') && !e.target.matches('#ending-point')) {
                document.getElementById('starting-suggestions').style.display = 'none';
                document.getElementById('ending-suggestions').style.display = 'none';
            }
        });

        // Initialize Leaflet map
        document.addEventListener('DOMContentLoaded', () => {
            const map = L.map('map').setView([<?php echo $default_lat; ?>, <?php echo $default_lng; ?>], 10);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            const startingPointInput = document.getElementById('starting-point');
            const endingPointInput = document.getElementById('ending-point');

            let routingControl = null;

            document.getElementById('route-form').addEventListener('submit', async (e) => {
                e.preventDefault();

                const startingPoint = startingPointInput.value;
                const endingPoint = endingPointInput.value;

                if (!startingPoint || !endingPoint) {
                    alert('Please enter both starting and ending points.');
                    return;
                }

                // Geocode starting and ending points
                const geocode = async (address) => {
                    // Include Pangasinan in the search for better results
                    const searchAddress = address + ", Pangasinan, Philippines";
                    const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(searchAddress)}`);
                    const data = await response.json();
                    if (data.length > 0) {
                        return { lat: parseFloat(data[0].lat), lng: parseFloat(data[0].lon) };
                    } else {
                        throw new Error('Location not found');
                    }
                };

                try {
                    const startCoords = await geocode(startingPoint);
                    const endCoords = await geocode(endingPoint);

                    if (routingControl) {
                        map.removeControl(routingControl);
                    }

                    routingControl = L.Routing.control({
                        waypoints: [
                            L.latLng(startCoords.lat, startCoords.lng),
                            L.latLng(endCoords.lat, endCoords.lng)
                        ],
                        routeWhileDragging: true,
                        lineOptions: {
                            styles: [{ color: '#255D8A', weight: 5 }]
                        }
                    }).addTo(map);

                    // Listen for route calculation complete to show route details
                    routingControl.on('routesfound', function(e) {
                        const routes = e.routes;
                        const summary = routes[0].summary;
                        
                        // Update route details
                        document.getElementById('route-details').innerHTML = `
                            <h2>Route Information</h2>
                            <p><strong>From:</strong> ${startingPoint}</p>
                            <p><strong>To:</strong> ${endingPoint}</p>
                            <p><strong>Distance:</strong> ${(summary.totalDistance / 1000).toFixed(2)} km</p>
                            <p><strong>Estimated Travel Time:</strong> ${Math.round(summary.totalTime / 60)} minutes</p>
                        `;
                    });
                    
                    // Also submit to backend if needed (for logging or future features)
                    const formData = new FormData();
                    formData.append('starting_point', startingPoint);
                    formData.append('ending_point', endingPoint);
                    
                    fetch('../../tripko-backend/api/routes/find_route.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Route data from server:', data);
                        // Handle backend response if needed
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                    
                } catch (error) {
                    alert('Error: ' + error.message);
                }
            });
            
            // Add user location functionality if needed
            if (navigator.geolocation) {
                const locationBtn = document.createElement('button');
                locationBtn.textContent = 'Use My Location';
                locationBtn.className = 'btn';
                locationBtn.style.marginTop = '10px';
                locationBtn.addEventListener('click', function() {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const userLat = position.coords.latitude;
                            const userLng = position.coords.longitude;
                            
                            // Reverse geocode to get address
                            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${userLat}&lon=${userLng}`)
                                .then(response => response.json())
                                .then(data => {
                                    const address = data.display_name;
                                    document.getElementById('starting-point').value = address;
                                    
                                    // Add marker and center map
                                    L.marker([userLat, userLng]).addTo(map)
                                        .bindPopup('Your Location')
                                        .openPopup();
                                    
                                    map.setView([userLat, userLng], 14);
                                });
                        },
                        () => {
                            alert("Error: The Geolocation service failed.");
                        }
                    );
                });
                
                // Add button after the form
                document.getElementById('route-form').appendChild(locationBtn);
            }
        });
    </script>
</body>
</html>