<?php
require_once('../../../tripko-backend/config/Database.php');
require_once('../../../tripko-backend/check_session.php');
checkTourismOfficerSession();

// Initialize database connection
$database = new Database();
$conn = $database->getConnection();

// Get the tourism officer's municipality info
$user_id = $_SESSION['user_id'];
$query = "SELECT t.name as town_name, t.town_id 
          FROM towns t 
          INNER JOIN users u ON u.town_id = t.town_id 
          WHERE u.user_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$town_data = $result->fetch_assoc();

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
    <title>Festivals Management - <?php echo htmlspecialchars($town_name); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Kameron:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Kameron', serif;
        }
        .image-preview {
            max-width: 200px;
            max-height: 200px;
            object-fit: cover;
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
            <a href="dashboard.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-[#1e4d70]">
                <i class="fas fa-home mr-2"></i>Dashboard
            </a>
            <a href="tourist_spots.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-[#1e4d70]">
                <i class="fas fa-umbrella-beach mr-2"></i>Tourist Spots
            </a>
            <a href="festivals.php" class="block py-2.5 px-4 rounded transition duration-200 bg-[#1e4d70]">
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
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold">Festivals Management</h1>
                <?php if ($mode === 'list'): ?>
                <a href="?action=add" class="bg-[#255D4F] text-white px-4 py-2 rounded-lg hover:bg-[#1e4d70] transition duration-200">
                    <i class="fas fa-plus-circle mr-2"></i>Add New Festival
                </a>
                <?php endif; ?>
            </div>

            <?php if ($mode === 'list'): ?>
            <!-- Festivals List -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="festivalsList">
                        <!-- Will be populated by JavaScript -->
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                <i class="fas fa-spinner fa-spin mr-2"></i>Loading festivals...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <?php else: ?>
            <!-- Add/Edit Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold mb-6"><?php echo $mode === 'add' ? 'Add New Festival' : 'Edit Festival'; ?></h2>
                
                <form id="festivalForm" class="space-y-6">
                    <input type="hidden" name="town_id" value="<?php echo $town_id; ?>">
                    <?php if ($mode === 'edit'): ?>
                    <input type="hidden" name="festival_id" value="<?php echo htmlspecialchars($_GET['id']); ?>">
                    <?php endif; ?>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Festival Name</label>
                            <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#255D4F] focus:ring focus:ring-[#255D4F] focus:ring-opacity-50">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Category</label>
                            <select name="category" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#255D4F] focus:ring focus:ring-[#255D4F] focus:ring-opacity-50">
                                <option value="">Select a category</option>
                                <option value="religious">Religious</option>
                                <option value="cultural">Cultural</option>
                                <option value="harvest">Harvest</option>
                                <option value="arts">Arts</option>
                                <option value="food">Food</option>
                                <option value="music">Music</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input type="date" name="start_date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#255D4F] focus:ring focus:ring-[#255D4F] focus:ring-opacity-50">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">End Date</label>
                            <input type="date" name="end_date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#255D4F] focus:ring focus:ring-[#255D4F] focus:ring-opacity-50">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#255D4F] focus:ring focus:ring-[#255D4F] focus:ring-opacity-50"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Location/Venue</label>
                            <input type="text" name="venue" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#255D4F] focus:ring focus:ring-[#255D4F] focus:ring-opacity-50">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Contact Information</label>
                            <input type="tel" name="contact_info" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#255D4F] focus:ring focus:ring-[#255D4F] focus:ring-opacity-50">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Activities</label>
                            <textarea name="activities" rows="3" placeholder="List the main activities and events" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#255D4F] focus:ring focus:ring-[#255D4F] focus:ring-opacity-50"></textarea>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Images</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <div class="flex flex-wrap gap-4 mb-4" id="imagePreviewContainer"></div>
                                    <div class="flex text-sm text-gray-600">
                                        <label class="relative cursor-pointer bg-white rounded-md font-medium text-[#255D4F] hover:text-[#1e4d70] focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-[#255D4F]">
                                            <span>Upload images</span>
                                            <input type="file" name="images[]" multiple accept="image/*" class="sr-only" onchange="previewImages(this)">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB each</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="festivals.php" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#255D4F]">
                            Cancel
                        </a>
                        <button type="submit" class="px-4 py-2 bg-[#255D4F] text-white rounded-md hover:bg-[#1e4d70] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#255D4F]">
                            <?php echo $mode === 'add' ? 'Create Festival' : 'Update Festival'; ?>
                        </button>
                    </div>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Function to load festivals
        async function loadFestivals() {
            try {
                const response = await fetch(`../../../tripko-backend/api/tourism_officers/festivals.php?town_id=<?php echo $town_id; ?>`);
                const data = await response.json();
                
                const tbody = document.getElementById('festivalsList');
                tbody.innerHTML = '';
                
                if (data.success && data.festivals && data.festivals.length > 0) {
                    data.festivals.forEach(festival => {
                        const startDate = new Date(festival.start_date).toLocaleDateString();
                        const endDate = new Date(festival.end_date).toLocaleDateString();
                        tbody.innerHTML += `
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full object-cover" 
                                                 src="${festival.image_url || 'default-festival.jpg'}" 
                                                 alt="${festival.name}">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">${festival.name}</div>
                                            <div class="text-sm text-gray-500">${festival.category}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    ${startDate} - ${endDate}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                                        festival.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                    }">
                                        ${festival.status}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium">
                                    <a href="?action=edit&id=${festival.id}" class="text-[#255D4F] hover:text-[#1e4d70] mr-3">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button onclick="deleteFestival(${festival.id})" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                No festivals found
                            </td>
                        </tr>
                    `;
                }
            } catch (error) {
                console.error('Error loading festivals:', error);
                document.getElementById('festivalsList').innerHTML = `
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-red-500">
                            Error loading festivals. Please try again.
                        </td>
                    </tr>
                `;
            }
        }

        // Function to preview images before upload
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
                                <img src="${e.target.result}" alt="preview" class="image-preview rounded">
                                <button type="button" onclick="this.parentElement.remove()" 
                                        class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center">
                                    ×
                                </button>
                            `;
                            container.appendChild(div);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        }

        // Function to delete a festival
        async function deleteFestival(festivalId) {
            if (confirm('Are you sure you want to delete this festival? This action cannot be undone.')) {
                try {
                    const response = await fetch('../../../tripko-backend/api/tourism_officers/festivals.php', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            festival_id: festivalId,
                            town_id: <?php echo $town_id; ?>
                        })
                    });

                    const data = await response.json();
                    if (data.success) {
                        alert('Festival deleted successfully');
                        loadFestivals();
                    } else {
                        throw new Error(data.message || 'Failed to delete festival');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error deleting festival: ' + error.message);
                }
            }
        }

        // Form submission handler
        const form = document.getElementById('festivalForm');
        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(form);
                
                try {
                    const url = '<?php echo $mode === "add" ? 
                        "../../../tripko-backend/api/tourism_officers/festivals.php" : 
                        "../../../tripko-backend/api/tourism_officers/festivals.php?id=" . $_GET["id"]; ?>';
                    
                    const response = await fetch(url, {
                        method: '<?php echo $mode === "add" ? "POST" : "PUT"; ?>',
                        body: formData
                    });

                    const data = await response.json();
                    if (data.success) {
                        alert('Festival <?php echo $mode === "add" ? "created" : "updated"; ?> successfully');
                        window.location.href = 'festivals.php';
                    } else {
                        throw new Error(data.message || 'Failed to <?php echo $mode; ?> festival');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error <?php echo $mode === "add" ? "creating" : "updating"; ?> festival: ' + error.message);
                }
            });
        }

        // Load festivals when page loads (if in list mode)
        <?php if ($mode === 'list'): ?>
        document.addEventListener('DOMContentLoaded', loadFestivals);
        <?php endif; ?>

        <?php if ($mode === 'edit'): ?>
        // Load festival data for editing
        document.addEventListener('DOMContentLoaded', async () => {
            try {
                const response = await fetch(`../../../tripko-backend/api/tourism_officers/festivals.php?id=<?php echo $_GET['id']; ?>`);
                const data = await response.json();
                
                if (data.success && data.festival) {
                    const form = document.getElementById('festivalForm');
                    Object.entries(data.festival).forEach(([key, value]) => {
                        const input = form.elements[key];
                        if (input) {
                            input.value = value;
                        }
                    });

                    // Load existing images
                    if (data.festival.images) {
                        const container = document.getElementById('imagePreviewContainer');
                        data.festival.images.forEach(imageUrl => {
                            const div = document.createElement('div');
                            div.className = 'relative';
                            div.innerHTML = `
                                <img src="${imageUrl}" alt="existing" class="image-preview rounded">
                                <button type="button" onclick="this.parentElement.remove()" 
                                        class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center">
                                    ×
                                </button>
                                <input type="hidden" name="existing_images[]" value="${imageUrl}">
                            `;
                            container.appendChild(div);
                        });
                    }
                }
            } catch (error) {
                console.error('Error loading festival:', error);
                alert('Error loading festival data: ' + error.message);
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>
