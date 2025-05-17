<?php
require_once('../../../tripko-backend/config/Database.php');
require_once('../../../tripko-backend/config/check_session.php');
checkTourismOfficerSession();

// Initialize database connection
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

if (!$town_data) {
    die("Error: No town assigned to this tourism officer");
}

$town_name = $town_data['town_name'];
$town_id = $town_data['town_id'];

// Check if we're in add/edit mode
$mode = isset($_GET['action']) ? $_GET['action'] : 'list';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tourist Spots Management - <?php echo htmlspecialchars($town_name); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kameron:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Kameron', serif;
            background-color: #F3F1E8;
        }

        .form-container {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            width: 95%;
            max-width: 1200px;
            margin: auto;
            position: relative;
            z-index: 50;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-size: 15px;
            font-family: 'Kameron';
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .form-control {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 1rem;
            line-height: 1.5;
            transition: all 0.15s ease-in-out;
        }

        .form-control:focus {
            outline: none;
            border-color: #255D4F;
            box-shadow: 0 0 0 2px rgba(37, 93, 79, 0.2);
        }

        textarea.form-control {
            min-height: 120px;
            resize: none;
        }

        .upload-area {
            padding: 2rem;
            border: 2px dashed #e2e8f0;
            border-radius: 0.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
        }

        .upload-area:hover {
            border-color: #255D8A;
        }

        .image-preview {
            max-width: 200px;
            max-height: 200px;
            object-fit: cover;
            border-radius: 0.375rem;
        }

        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 0.375rem;
        }
    </style>
</head>
<body>
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
            <a href="dashboard.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-[#1e4d70]">
                <i class="fas fa-home mr-2"></i>Dashboard
            </a>
            <a href="tourist_spots.php" class="block py-2.5 px-4 rounded transition duration-200 bg-[#1e4d70]">
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
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold">Tourist Spots Management</h1>
                <?php if ($mode === 'list'): ?>
                <a href="?action=add" class="bg-[#255D4F] text-white px-4 py-2 rounded-lg hover:bg-[#1e4d70] transition duration-200">
                    <i class="fas fa-plus-circle mr-2"></i>Add New Tourist Spot
                </a>
                <?php endif; ?>
            </div>

            <?php if ($mode === 'list'): ?>
            <!-- Tourist Spots List -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="touristSpotsList">
                        <!-- Loading state -->
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                <i class="fas fa-spinner fa-spin mr-2"></i>Loading tourist spots...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <?php if ($mode === 'edit' || $mode === 'add'): ?>
            <!-- Add/Edit Form -->
            <div class="form-container bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold mb-6"><?php echo $mode === 'add' ? 'Add New Tourist Spot' : 'Edit Tourist Spot'; ?></h2>
                
                <form id="touristSpotForm" enctype="multipart/form-data">
                    <input type="hidden" name="town_id" value="<?php echo htmlspecialchars($town_id); ?>">
                    <?php if ($mode === 'edit'): ?>
                    <input type="hidden" name="spot_id" value="<?php echo htmlspecialchars($_GET['id']); ?>">
                    <?php endif; ?>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Tourist Spot Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required class="form-control w-full rounded-md border-gray-300 shadow-sm focus:border-[#255D4F] focus:ring focus:ring-[#255D4F] focus:ring-opacity-50">
                        </div>
                        <div class="form-group">
                            <label>Category <span class="text-red-500">*</span></label>
                            <select name="category" required class="form-control w-full rounded-md border-gray-300 shadow-sm focus:border-[#255D4F] focus:ring focus:ring-[#255D4F] focus:ring-opacity-50">
                                <option value="" selected disabled>Select category</option>
                                <option value="Beach">Beach</option>
                                <option value="Islands">Islands</option>
                                <option value="Waterfalls">Waterfalls</option>
                                <option value="Caves">Caves</option>
                                <option value="Churches">Churches and Cathedrals</option>
                                <option value="Festivals">Festivals</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Description <span class="text-red-500">*</span></label>
                        <textarea name="description" rows="4" required class="form-control w-full rounded-md border-gray-300 shadow-sm focus:border-[#255D4F] focus:ring focus:ring-[#255D4F] focus:ring-opacity-50 resize-none"></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Location <span class="text-red-500">*</span></label>
                            <input type="text" name="location" required class="form-control w-full rounded-md border-gray-300 shadow-sm focus:border-[#255D4F] focus:ring focus:ring-[#255D4F] focus:ring-opacity-50">
                        </div>
                        <div class="form-group">
                            <label>Contact Info <span class="text-gray-500">(Optional)</span></label>
                            <input type="text" name="contact_info" class="form-control w-full rounded-md border-gray-300 shadow-sm focus:border-[#255D4F] focus:ring focus:ring-[#255D4F] focus:ring-opacity-50">
                        </div>
                    </div>

                    <div class="form-group mb-6">
                        <div class="upload-area border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-[#255D8A] transition-colors">
                            <i class="fas fa-cloud-upload-alt text-3xl text-[#255D8A] mb-2"></i>
                            <p class="text-sm font-medium mb-1">Upload Images</p>
                            <span class="text-xs text-gray-500">PNG, JPG or JPEG (max. 5MB each)</span>
                            <input type="file" name="images[]" accept="image/png, image/jpeg" multiple class="hidden" onchange="previewImages(this)">
                            <div id="imagePreviewContainer" class="mt-4 grid grid-cols-4 gap-2"></div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="tourist_spots.php" class="px-4 py-2 rounded-md bg-gray-200 hover:bg-gray-300 transition-colors text-[15px]">Cancel</a>
                        <button type="submit" class="px-4 py-2 rounded-md bg-[#255D8A] text-white hover:bg-[#1e4d70] transition-colors text-[15px]">
                            <?php echo $mode === 'add' ? 'Create Tourist Spot' : 'Update Tourist Spot'; ?>
                        </button>
                    </div>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Load tourist spots
        async function loadTouristSpots() {
            const tbody = document.getElementById('touristSpotsList');
            tbody.innerHTML = `<tr>
                <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                    <i class="fas fa-spinner fa-spin mr-2"></i>Loading...
                </td>
            </tr>`;
            
            try {
                const response = await fetch('../../../tripko-backend/api/tourism_officers/tourist_spots.php', {
                    method: 'GET',
                    headers: { 
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('Fetched data:', data);
                
                if (!data.success || !data.spots || data.spots.length === 0) {
                    tbody.innerHTML = `<tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                            <div class="flex flex-col items-center py-4">
                                <i class="fas fa-folder-open text-4xl mb-2"></i>
                                <p class="text-lg">No tourist spots found</p>
                                <p class="text-sm">Click "Add New Tourist Spot" to create one</p>
                            </div>
                        </td>
                    </tr>`;
                    return;
                }
                
                tbody.innerHTML = data.spots.map(spot => {
                    const statusClass = spot.status === 'inactive' ? 'bg-red-50' : '';
                    const imageSrc = spot.image_path ? 
                        '../../../uploads/' + spot.image_path : 
                        '../../../assets/images/placeholder.jpg';
                    
                    return `<tr class="${statusClass}">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full object-cover" 
                                         src="${imageSrc}" 
                                         alt="${spot.name || ''}"
                                         onerror="this.src='../../../assets/images/placeholder.jpg'">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">${spot.name || ''}</div>
                                    <div class="text-sm text-gray-500">${spot.location || spot.town_name || ''}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">${spot.category || ''}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                                spot.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                            }">${spot.status || 'inactive'}</span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <a href="?action=edit&id=${spot.spot_id}" class="text-[#255D4F] hover:text-[#1e4d70] mr-3">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <button onclick="deleteSpot(${spot.spot_id})" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </td>
                    </tr>`;
                }).join('');
            } catch (error) {
                console.error('Error loading tourist spots:', error);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-red-500">
                            <div class="mb-1">Error loading tourist spots: ${error.message}</div>
                            <div class="text-sm">Please try refreshing the page or contact support if the issue persists.</div>
                        </td>
                    </tr>
                `;
            }
        }

        // Image preview handling
        function previewImages(input) {
            const container = document.getElementById('imagePreviewContainer');
            container.innerHTML = '';

            if (input.files) {
                [...input.files].forEach(file => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = e => {
                            const div = document.createElement('div');
                            div.className = 'relative';
                            div.innerHTML = `
                                <img src="${e.target.result}" alt="preview" class="image-preview rounded shadow-sm">
                                <button type="button" onclick="this.closest('.relative').remove()" 
                                        class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center 
                                               opacity-0 group-hover:opacity-100 transition-opacity duration-200">×</button>
                            `;
                            container.appendChild(div);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        }

        // Form submission handler
        const form = document.getElementById('touristSpotForm');
        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(form);
                
                try {
                    const response = await fetch('../../../tripko-backend/api/tourism_officers/tourist_spots.php', {
                        method: '<?php echo $mode === "add" ? "POST" : "PUT"; ?>',
                        body: formData,
                        credentials: 'same-origin'
                    });

                    if (!response.ok) {
                        const errorText = await response.text();
                        throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
                    }

                    const data = await response.json();
                    if (data.success) {
                        alert('Tourist spot <?php echo $mode === "add" ? "created" : "updated"; ?> successfully');
                        window.location.href = 'tourist_spots.php';
                    } else {
                        throw new Error(data.message || 'Failed to <?php echo $mode; ?> tourist spot');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error <?php echo $mode === "add" ? "creating" : "updating"; ?> tourist spot: ' + error.message);
                }
            });
        }

        // Delete tourist spot handler
        async function deleteSpot(spotId) {
            if (!confirm('Are you sure you want to delete this tourist spot? This action cannot be undone.')) {
                return;
            }

            try {
                const response = await fetch('../../../tripko-backend/api/tourism_officers/tourist_spots.php', {
                    method: 'DELETE',
                    headers: { 
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ spot_id: spotId, town_id: <?php echo $town_id; ?> }),
                    credentials: 'same-origin'
                });

                const data = await response.json();
                if (data.success) {
                    alert('Tourist spot deleted successfully');
                    loadTouristSpots();
                } else {
                    throw new Error(data.message || 'Failed to delete tourist spot');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error deleting tourist spot: ' + error.message);
            }
        }

        // Initialize page
        <?php if ($mode === 'list'): ?>
        document.addEventListener('DOMContentLoaded', loadTouristSpots);
        <?php endif; ?>

        <?php if ($mode === 'edit'): ?>
        document.addEventListener('DOMContentLoaded', async () => {
            try {
                const response = await fetch(`../../../tripko-backend/api/tourism_officers/tourist_spots.php?id=<?php echo $_GET['id']; ?>`);
                const data = await response.json();
                
                if (data.success && data.spot) {
                    const form = document.getElementById('touristSpotForm');
                    Object.entries(data.spot).forEach(([key, value]) => {
                        const input = form.elements[key];
                        if (input) input.value = value;
                    });

                    if (data.spot.images) {
                        const container = document.getElementById('imagePreviewContainer');
                        data.spot.images.forEach(imageUrl => {
                            const div = document.createElement('div');
                            div.className = 'relative';
                            div.innerHTML = `
                                <img src="${imageUrl}" alt="existing" class="image-preview rounded">
                                <button type="button" onclick="this.parentElement.remove()" 
                                        class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center">×</button>
                                <input type="hidden" name="existing_images[]" value="${imageUrl}">
                            `;
                            container.appendChild(div);
                        });
                    }
                }
            } catch (error) {
                console.error('Error loading tourist spot:', error);
                alert('Error loading tourist spot data: ' + error.message);
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>
