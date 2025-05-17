<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>TripKo Pangasinan - Tourist Spots</title>  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Merriweather&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Kameron:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../file_css/dashboard.css" />
  <style>
    body {
        font-family: 'Kameron',serif;
        font-size: 17px;
    }

    .nav-links a,
    .font-medium,
    button,
    select,
    input,
    p,
    h1, h2, h3, h4, h5, h6 {
        font-family: 'Kameron';
    }

    #municipalityFilter option {
    background-color: white;
    color: #1a202c;
    }

    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: 40;
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
    }

    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
    }


    canvas#transportChart {
      width: 100% !important;
      height: 100% !important;
      position: absolute !important;
      top: 0;
      left: 0;
    }
  </style>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
        const transportDropdown = document.getElementById('transportDropdown');
        const transportDropdownIcon = document.getElementById('transportDropdownIcon');

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#transportDropdown') && !e.target.closest('[onclick*="toggleTransportDropdown"]')) {
                transportDropdown?.classList.add('hidden');
                if (transportDropdownIcon) {
                    transportDropdownIcon.style.transform = 'rotate(0deg)';
                }
            }
        });
    });

    function toggleTransportDropdown(event) {
        event.preventDefault();
        const dropdown = document.getElementById('transportDropdown');
        const icon = document.getElementById('transportDropdownIcon');
        dropdown.classList.toggle('hidden');
        icon.style.transform = dropdown.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
    }
  </script>
</head>
<body class="bg-white text-gray-900">
  <div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="flex flex-col w-72 bg-[#255d8a] text-white">
      <!-- Logo and Brand -->
      <div class="p-6 border-b border-[#1e4d70]">
        <div class="flex items-center space-x-4">
          <div class="p-2 bg-white bg-opacity-10 rounded-lg">
            <i class="fas fa-compass text-3xl"></i>
          </div>
          <div>
            <h1 class="text-2xl font-medium">TripKo</h1>
            <p class="text-sm text-blue-200">Pangasinan Tourism</p>
          </div>
        </div>
      </div>

      <!-- Navigation Menu -->
      <nav class="flex-1 p-6 space-y-2 overflow-y-auto">
        <!-- Dashboard -->
        <a href="dashboard.php" class="flex items-center px-4 py-3 text-white hover:bg-[#1e4d70] rounded-lg transition-colors group">
          <i class="fas fa-home w-6"></i>
          <span class="ml-3">Dashboard</span>
        </a>

        <!-- Tourism Section -->
        <div class="mt-6">
          <p class="px-4 text-xs font-medium text-blue-300 uppercase">Tourism</p>
          <div class="mt-3 space-y-2">
            <a href="tourist_spot.php" class="flex items-center px-4 py-3 text-white hover:bg-[#1e4d70] rounded-lg transition-colors group">
              <i class="fas fa-umbrella-beach w-6"></i>
              <span class="ml-3">Tourist Spots</span>
            </a>
            <a href="itineraries.php" class="flex items-center px-4 py-3 text-white hover:bg-[#1e4d70] rounded-lg transition-colors group">
              <i class="fas fa-map-marked-alt w-6"></i>
              <span class="ml-3">Itineraries</span>
            </a>
            <a href="festival.php" class="flex items-center px-4 py-3 text-white hover:bg-[#1e4d70] rounded-lg transition-colors group">
              <i class="fas fa-calendar-alt w-6"></i>
              <span class="ml-3">Festivals</span>
            </a>
          </div>
        </div>

        <!-- Transportation Section -->
        <div class="mt-6">
          <p class="px-4 text-xs font-medium text-blue-300 uppercase">Transportation</p>
          <div class="mt-3 space-y-2">
             <button onclick="toggleTransportDropdown(event)" class="w-full flex items-center justify-between px-4 py-3 text-white hover:bg-[#1e4d70] rounded-lg transition-colors group">
                <div class="flex items-center">
                    <i class="fas fa-bus w-6"></i>
                    <span class="ml-3">Transport Info</span>
                </div>
                <i class="fas fa-chevron-down text-sm transition-transform duration-200 rotate-180" id="transportDropdownIcon"></i>
            </button>
            <div id="transportDropdown" class="pl-4 space-y-2">
              <a href="terminal-locations.html" class="flex items-center px-4 py-2 text-blue-200 hover:text-white hover:bg-[#1e4d70] rounded-lg transition-colors group">
                <i class="fas fa-map-marker-alt w-6"></i>
                <span class="ml-3">Terminals</span>
              </a>
              <a href="terminal-routes.html" class="flex items-center px-4 py-2 text-blue-200 hover:text-white hover:bg-[#1e4d70] rounded-lg transition-colors group">
                <i class="fas fa-route w-6"></i>
                <span class="ml-3">Routes & Types</span>
              </a>
              <a href="fare.html" class="flex items-center px-4 py-2 text-blue-200 hover:text-white hover:bg-[#1e4d70] rounded-lg transition-colors group">
                <i class="fas fa-money-bill-wave w-6"></i>
                <span class="ml-3">Fare Rates</span>
              </a>
            </div>
          </div>
        </div>

        <!-- Management Section -->
        <div class="mt-6">
          <p class="px-4 text-xs font-medium text-blue-300 uppercase">Management</p>
          <div class="mt-3 space-y-2">
           <a href="users.php" class="flex items-center px-4 py-3 text-white hover:bg-[#1e4d70] rounded-lg transition-colors group">
              <i class="fas fa-users w-6"></i>
              <span class="ml-3">Users</span>

            </a>
            <a href="reports.php" class="flex items-center px-4 py-3 text-white hover:bg-[#1e4d70] rounded-lg transition-colors group">
              <i class="fas fa-chart-bar w-6"></i>
              <span class="ml-3">Reports</span>
            </a>
          </div>
        </div>
      </nav>

      <!-- User Profile -->
      <div class="p-6 border-t border-[#1e4d70]">
        <div class="flex items-center space-x-4">
          <div class="p-2 bg-white bg-opacity-10 rounded-full">
            <i class="fas fa-user-circle text-2xl"></i>
          </div>
          <div>
            <h3 class="font-medium">Administrator</h3>
            <a href="../../tripko-backend/config/confirm_logout.php" class="text-sm text-blue-200 hover:text-white group flex items-center mt-1">
              <i class="fas fa-sign-out-alt mr-2"></i>
              <span>Sign Out</span>
            </a>
          </div>
        </div>
      </div>
    </aside>    <!-- Main content -->
    <main class="flex-1 bg-[#F3F1E7] p-6">
      <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center gap-3 text-gray-900 font-normal text-base">
              <button aria-label="Menu" class="focus:outline-none">
                  <i class="fas fa-bars text-lg"></i>
              </button>
              <span>Tourist Spots</span>
          </div>
          
          <div class="flex items-center gap-4">
            <div>
              <input type="search" placeholder="Search" class="w-48 md:w-64 rounded-full border border-gray-400 bg-[#F3F1E7] py-1.5 px-4 text-gray-600 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#255D8A]" />
            </div>
            <button aria-label="Notifications" class="text-black text-xl focus:outline-none">
              <i class="fas fa-bell"></i>
            </button>
          </div>
        </div>

        <div class="flex justify-end gap-3">
          <button onclick="openModal()" class="bg-[#255D8A] text-white px-4 py-2 rounded-md hover:bg-[#1e4d70] transition-colors">
            + Add new spot
          </button>
          <button onclick="toggleView()" id="viewToggleBtn" class="bg-[#255D8A] text-white px-4 py-2 rounded-md hover:bg-[#1e4d70] transition-colors">
            <i class="fas fa-table"></i> Table View
          </button>
          <div class="relative">
            <select id="municipalityFilter" onchange="filterTouristSpots()" class="bg-[#255D8A] text-white px-4 py-2 rounded-md hover:bg-[#1e4d70] transition-colors cursor-pointer">
          <option value="" class="bg-white text-gray-900 hover:bg-[#255D8A] hover:text-white">All Municipalities</option>
        </select>
          </div>
        </div>
      </div>

      <!-- Tourist spots grid -->
      <div id="gridView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Tourist spot cards will be dynamically added here -->
      </div>

      <!-- Tourist spots table -->
      <div id="tableView" class="hidden">
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <table class="w-full border-collapse">
            <thead class="bg-gray-50">
              <tr>
                <th class="border border-gray-300 px-4 py-2 text-left font-bold">Name</th>
                <th class="border border-gray-300 px-4 py-2 text-left font-bold">Category</th>
                <th class="border border-gray-300 px-4 py-2 text-left font-bold">Municipality</th>
                <th class="border border-gray-300 px-4 py-2 text-left font-bold">Contact Info</th>
                <th class="border border-gray-300 px-4 py-2 text-left font-bold">Description</th>
                <th class="border border-gray-300 px-4 py-2 text-center font-bold">Actions</th>
              </tr>
            </thead>
            <tbody id="spotTableBody">
              <!-- Tourist spot rows will be dynamically added here -->
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <!-- Add New Spot Modal -->
  <div id="addSpotModal" class="fixed inset-0 hidden">
    <div class="modal-overlay"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
      <div class="form-container">
        <button type="button" class="absolute right-4 top-4 text-gray-500 hover:text-gray-700" onclick="closeModal()">
          <i class="fas fa-times text-xl"></i>
        </button>
        
        <form enctype="multipart/form-data">
          <div class="form-row">
            <div class="form-group">
              <label>Tourist Spot Name <span class="required">*</span></label>
              <input type="text" name="name" required class="form-control">
            </div>
            <div class="form-group">
              <label>Category <span class="required">*</span></label>
              <select name="category" required class="form-control">
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
            <label>Description <span class="required">*</span></label>
            <textarea name="description" rows="4" required class="form-control"></textarea>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Municipality <span class="required">*</span></label>
              <select name="town_id" id="townSelect" required class="form-control">
                <option value="" selected disabled>Select municipality</option>
              </select>
            </div>
            <div class="form-group">
              <label>Contact Info <span class="optional">(Optional)</span></label>
              <input type="text" name="contact_info" class="form-control">
            </div>
          </div>

          <div class="form-group mb-6">
            <div class="upload-area border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-[#255D8A] transition-colors">
              <i class="fas fa-cloud-upload-alt text-3xl text-[#255D8A] mb-2"></i>
              <p class="text-sm font-medium mb-1">Upload Images</p>
              <span class="text-xs text-gray-500">PNG, JPG or JPEG (max. 5MB each)</span>
              <input type="file" name="images[]" accept="image/png, image/jpeg" multiple class="hidden" />
              <div class="image-preview mt-4 grid grid-cols-4 gap-2"></div>
            </div>
          </div>

          <div class="form-buttons">
            <button type="button" class="px-4 py-2 rounded-md bg-gray-200 hover:bg-gray-300 transition-colors text-[15px]" onclick="closeModal()">Cancel</button>
            <button type="submit" class="px-4 py-2 rounded-md bg-[#255D8A] text-white hover:bg-[#1e4d70] transition-colors text-[15px]">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    // Modal functionality and form handling
    const modal = document.getElementById('addSpotModal');
    const form = document.querySelector('form');
    const fileInput = document.querySelector('input[type="file"]');
    const uploadArea = document.querySelector('.upload-area');

    function openModal() {
      modal.classList.remove('hidden');
      
      // Remove any existing spot_id input to ensure we're in create mode
      const existingSpotId = form.querySelector('input[name="spot_id"]');
      if (existingSpotId) {
        existingSpotId.remove();
      }
      
      // Reset form and clear preview
      form.reset();
      const preview = uploadArea.querySelector('.image-preview');
      if (preview) preview.remove();
    }

    function closeModal() {
      modal.classList.add('hidden');
    }

    // Form submission handler - handles both create and edit
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(form);
      const files = fileInput.files;
      
      // Only append files if new ones were selected
      if (files.length > 0) {
        for(let i = 0; i < files.length; i++) {
          formData.append('images[]', files[i]);
        }
      }

      // Determine if this is an edit or create based on presence of spot_id
      const isEdit = formData.has('spot_id');
      const url = isEdit ? 
        '../../tripko-backend/api/tourist_spot/update.php' : 
        '../../tripko-backend/api/tourist_spot/create.php';

      try {
        const response = await fetch(url, {
          method: 'POST',
          body: formData
        });

        const data = await response.json();
        if(data.success) {
          alert(isEdit ? 'Tourist spot updated successfully!' : 'Tourist spot added successfully!');
          closeModal();
          loadTouristSpots();
        } else {
          throw new Error(data.message || `Failed to ${isEdit ? 'update' : 'save'} tourist spot`);
        }
      } catch (error) {
        console.error(isEdit ? 'Update error:' : 'Save error:', error);
        alert('Error: ' + error.message);
      }
    });

    // Transport dropdown toggle
    function toggleTransportDropdown(event) {
      event.preventDefault();
      const dropdown = document.getElementById('transportDropdown');
      const icon = document.getElementById('transportDropdownIcon');
      dropdown.classList.toggle('hidden');
      icon.style.transform = dropdown.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
    }

    // File upload handling
    uploadArea.addEventListener('click', () => fileInput.click());

    fileInput.addEventListener('change', (e) => {
      const files = Array.from(e.target.files);
      const preview = document.createElement('div');
      preview.className = 'image-preview grid grid-cols-3 gap-2 mt-2';
      
      files.forEach(file => {
        const reader = new FileReader();
        reader.onload = (e) => {
          const img = document.createElement('img');
          img.src = e.target.result;
          img.className = 'w-full h-24 object-cover rounded';
          preview.appendChild(img);
        };
        reader.readAsDataURL(file);
      });

      const existingPreview = uploadArea.querySelector('.image-preview');
      if(existingPreview) existingPreview.remove();
      uploadArea.appendChild(preview);
    });

    // Helper functions
    function getImageUrl(imagePath) {
      if (!imagePath || imagePath === 'placeholder.jpg') {
        return 'https://placehold.co/400x300?text=No+Image';
      }
      return `/TripKo-System/uploads/${imagePath}`;
    }

    // Load and display tourist spots
    async function loadTouristSpots() {
      const container = document.querySelector('.grid');
      try {
        container.innerHTML = `
          <div class="col-span-full flex justify-center items-center py-8">
            <div class="text-center">
              <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-[#255D8A] mb-4"></div>
              <p class="text-gray-600">Loading tourist spots...</p>
            </div>
          </div>
        `;

        const response = await fetch('../../tripko-backend/api/tourist_spot/read.php');
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status} - ${await response.text()}`);
        }

        const data = await response.json();
        container.innerHTML = '';
        
        const selectedMunicipality = document.getElementById('municipalityFilter').value;
        
        if (!data || !data.records || !Array.isArray(data.records)) {
          throw new Error('Invalid data format received from server');
        }

        // Continue with existing spot display logic
        const filteredSpots = selectedMunicipality ? 
          data.records.filter(spot => spot.town_id === selectedMunicipality) : 
          data.records;

        if (filteredSpots.length === 0) {            container.innerHTML = `
            <div class="col-span-full text-center py-8 text-gray-500">
              <i class="fas fa-filter text-4xl mb-3 block"></i>
              <p class="text-[17px] font-medium" style="font-family: 'Kameron'">No tourist spots found for the selected municipality</p>
            </div>
          `;
          return;
        }

        filteredSpots.forEach(spot => {
          const statusClass = spot.status === 'inactive' ? 'bg-red-100 border-red-300' : '';            container.innerHTML += `
              <div class="rounded-lg overflow-hidden border border-gray-200 shadow-md bg-white flex flex-col h-full transition-transform hover:scale-105 hover:shadow-lg ${statusClass}">
                <div class="relative w-full h-48 bg-gray-100">
                  <img src="${getImageUrl(spot.image_path)}" 
                       alt="${spot.name || 'Tourist Spot'}"                           class="w-full h-full object-cover transition-all duration-300" 
                           onerror="this.src='../images/placeholder.jpg'" />
                  ${spot.status === 'inactive' ? '<div class="absolute top-2 left-2 bg-red-500 text-white font-medium text-[17px] px-2 py-1 rounded" style="font-family: \'Kameron\'">Inactive</div>' : ''}
                </div>
                <div class="flex-1 flex flex-col p-4" style="font-family: 'Kameron'">
                  <div class="text-[16px] font-regular">
                    <div class="bg-[#255D8A] text-white w-full px-2 py-1 rounded mb-3 text-center">${spot.category || 'Uncategorized'}</div>
                    <h3 class="text-[#255D8A] mb-1">${spot.name}</h3>
                    <p class="text-gray-700 mb-2 line-clamp-3">${spot.description}</p>
                  </div>
                  <div class="mt-auto text-[14px] font-regular">
                    <p class="text-gray-500 flex items-center spot-municipality" data-town-id="${spot.town_id}">
                      <i class="fas fa-map-marker-alt mr-1"></i>${spot.town_name || 'Unknown Location'}
                    </p>
                    <p class="text-gray-500 mt-1 flex items-center">
                      <i class="fas fa-phone-alt mr-1"></i>${spot.contact_info || 'No contact info'}
                    </p>
                  </div>
                </div>
              </div>
            `;
        });
      } catch (error) {
        console.error('Fetch Error:', error);
        document.querySelector('.grid').innerHTML = `
          <div class="col-span-full text-center py-8 text-red-500">
            <i class="fas fa-exclamation-circle text-4xl mb-3 block"></i>
            <p>Failed to load tourist spots. Please try again later.</p>
            <p class="text-sm mt-2">Error details: ${error.message}</p>
          </div>
        `;
      }
    }    // Load municipalities for the select dropdown
    async function loadMunicipalities() {
      try {
        console.log('Fetching municipalities...');
        const response = await fetch('../../tripko-backend/api/towns/read.php', {
          headers: {
            'Accept': 'application/json',
            'Cache-Control': 'no-cache'
          },
          credentials: 'same-origin'
        });
        
        console.log('Response status:', response.status);
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const text = await response.text();
        console.log('Raw response:', text);
        
        let data;
        try {
          data = JSON.parse(text);
          console.log('Parsed data:', data);
        } catch (e) {
          console.error('JSON parse error:', e);
          throw new Error('Invalid JSON response from server');
        }
        
        const townSelect = document.querySelector('select[name="town_id"]');
        const municipalityFilter = document.getElementById('municipalityFilter');
        
        if (!townSelect || !municipalityFilter) {
          console.error('Could not find select elements');
          throw new Error('Required elements not found');
        }
        
        townSelect.innerHTML = '<option value="" selected disabled>Select municipality</option>';
        municipalityFilter.innerHTML = '<option value="">All Municipalities</option>';
        
        if (data.success && data.records && Array.isArray(data.records)) {
          console.log('Found', data.records.length, 'municipalities');
          data.records.sort((a, b) => a.name.localeCompare(b.name)).forEach(town => {
            const option = document.createElement('option');
            option.value = town.town_id;
            option.textContent = town.name;
            townSelect.appendChild(option);
            
            const filterOption = option.cloneNode(true);
            municipalityFilter.appendChild(filterOption);
          });
        } else {
          console.error('Invalid data format:', data);
          throw new Error('Invalid data format received from server');
        }
      } catch (error) {
        console.error('Failed to load municipalities:', error);
        // Show error in both dropdowns
        const elements = [
          document.querySelector('select[name="town_id"]'),
          document.getElementById('municipalityFilter')
        ];
        
        elements.forEach(el => {
          if (el) {
            el.innerHTML = '<option value="" disabled selected>Error loading municipalities</option>';
          }
        });
        
        // Show error message to user
        alert('Failed to load municipalities. Please refresh the page and try again.');
      }
    }

    // Initialize page
    document.addEventListener('DOMContentLoaded', () => {
      loadTouristSpots();
      loadMunicipalities();
    });

    // Edit spot functionality
    async function editSpot(spot) {
      openModal();
      
      // Set form values
      form.name.value = spot.name || '';
      form.category.value = spot.category || '';
      form.description.value = spot.description || '';
      form.contact_info.value = spot.contact_info || '';
      
      // Set town_id and ensure the select option exists
      const townSelect = form.querySelector('select[name="town_id"]');
      if (spot.town_id) {
        // Wait for municipalities to load if they haven't yet
        if (townSelect.options.length <= 1) {
          await loadMunicipalities();
        }
        townSelect.value = spot.town_id;
      }
      
      // Add spot_id to form for update
      let spotIdInput = form.querySelector('input[name="spot_id"]');
      if (!spotIdInput) {
        spotIdInput = document.createElement('input');
        spotIdInput.type = 'hidden';
        spotIdInput.name = 'spot_id';
        form.appendChild(spotIdInput);
      }
      spotIdInput.value = spot.spot_id;

      // Add existing image preview if available
      if (spot.image_path) {
        const preview = document.createElement('div');
        preview.className = 'image-preview grid grid-cols-3 gap-2 mt-2';
        const img = document.createElement('img');
        img.src = getImageUrl(spot.image_path);
        img.className = 'w-full h-24 object-cover rounded';
        preview.appendChild(img);
        
        const existingPreview = uploadArea.querySelector('.image-preview');
        if(existingPreview) existingPreview.remove();
        uploadArea.appendChild(preview);
      }
    }

    // Delete spot functionality
    async function deleteSpot(spotId, spotName) {
      if (confirm(`Are you sure you want to delete the tourist spot "${spotName}"?`)) {
        try {
          const response = await fetch(`../../tripko-backend/api/tourist_spot/delete.php?spot_id=${spotId}`, {
            method: 'DELETE'
          });
          const data = await response.json();
          if (data.success) {
            alert('Tourist spot deleted successfully!');
            loadTouristSpots();
          } else {
            throw new Error(data.message || 'Failed to delete tourist spot');
          }
        } catch (error) {
          console.error('Delete error:', error);
          alert('Error: ' + error.message);
        }
      }
    }

    let currentSpotId = null;

    function toggleSpotStatus(spotId, currentStatus) {
      const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
      updateSpotStatus(spotId, newStatus);
    }

    async function updateSpotStatus(spotId, newStatus) {
      try {
        const response = await fetch('../../tripko-backend/api/tourist_spot/update_status.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            spot_id: spotId,
            status: newStatus
          })
        });

        const data = await response.json();
        if (data.success) {
          loadTouristSpots();
          if (!document.getElementById('tableView').classList.contains('hidden')) {
            await loadTableView();
          }
        } else {
          throw new Error(data.message || 'Failed to update status');
        }
      } catch (error) {
        console.error('Error updating status:', error);
        alert('Failed to update tourist spot status: ' + error.message);
      }
    }

    // Toggle view function
    function toggleView() {
      const gridView = document.getElementById('gridView');
      const tableView = document.getElementById('tableView');
      const viewToggleBtn = document.getElementById('viewToggleBtn');
      
      const isGridView = !gridView.classList.contains('hidden');
      
      gridView.classList.toggle('hidden', isGridView);
      tableView.classList.toggle('hidden', !isGridView);
      viewToggleBtn.innerHTML = isGridView ? '<i class="fas fa-th"></i> Grid View' : '<i class="fas fa-table"></i> Table View';
      
      if (!isGridView) {
        loadTableView(); // Load table data
      } else {
        loadTouristSpots(); // Load grid data
      }
    }

    
    // Load table view data
    async function loadTableView() {
      try {
        const response = await fetch('../../tripko-backend/api/tourist_spot/read.php');
        const data = await response.json();
        const tableBody = document.getElementById('spotTableBody');
        tableBody.innerHTML = '';
        
        if (data && data.records && Array.isArray(data.records)) {
          data.records.forEach(spot => {
            const statusClass = spot.status === 'inactive' ? 'bg-red-50' : '';
            tableBody.innerHTML += `
              <tr class="hover:bg-gray-100 transition-colors ${statusClass}">
                <td class="border border-gray-300 px-4 py-2">${spot.name}</td>
                <td class="border border-gray-300 px-4 py-2">${spot.category || 'N/A'}</td>
                <td class="border border-gray-300 px-4 py-2">${spot.town_name || 'N/A'}</td>
                <td class="border border-gray-300 px-4 py-2">${spot.contact_info || 'N/A'}</td>
                <td class="border border-gray-300 px-4 py-2 line-clamp-2">${spot.description}</td>
                <td class="border border-gray-300 px-4 py-2 text-center">
                  <span class="inline-block px-2 py-1 text-xs rounded-full ${
                    spot.status === 'inactive' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'
                  }">${spot.status || 'active'}</span>
                </td>
                <td class="border border-gray-300 px-4 py-2 text-center">
                  <div class="flex justify-center gap-2">
                    <button onclick='editSpot(${JSON.stringify(spot).replace(/'/g, "&#39;")})'  
                            class="bg-[#255d8a] text-white px-3 py-1 rounded text-sm hover:bg-[#1e4d70]"> 
                      Edit
                    </button>
                    <button onclick="deleteSpot(${spot.spot_id}, '${spot.name}')" 
                            class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700">
                      Delete
                    </button>
                    <button onclick="openStatusModal(${spot.spot_id}, '${spot.status || 'active'}')"
                            class="${spot.status === 'inactive' ? 'bg-red-600' : 'bg-green-600'} text-white px-3 py-1 rounded text-sm hover:bg-opacity-90">
                      Status
                    </button>
                  </div>
                </td>
              </tr>
            `;
          });
        } else {
          tableBody.innerHTML = `
            <tr>
              <td colspan="7" class="text-center py-8">
                <div class="flex flex-col items-center justify-center text-gray-500">
                  <i class="fas fa-inbox text-4xl mb-3"></i>
                  <p>No tourist spots found</p>
                </div>
              </td>
            </tr>
          `;
        }
      } catch (error) {
        console.error('Fetch Error:', error);
        document.getElementById('spotTableBody').innerHTML = `
          <tr>
            <td colspan="7" class="text-center py-8">
              <div class="flex flex-col items-center justify-center text-red-500">
                <i class="fas fa-exclamation-circle text-4xl mb-3"></i>
                <p>Failed to load tourist spots</p>
                <p class="text-sm mt-2">Please try again later</p>
              </div>
            </td>
          </tr>
        `;
      }
    }

    // Filter tourist spots by municipality
    function filterTouristSpots() {
      const selectedMunicipality = document.getElementById('municipalityFilter').value;
      const gridView = document.getElementById('gridView');
      const tableView = document.getElementById('tableView');
      
      try {
        // Show loading state
        const container = !tableView.classList.contains('hidden') ? 
          document.getElementById('spotTableBody') : 
          document.getElementById('gridView');
          
        container.innerHTML = `
          <div class="col-span-full flex justify-center items-center py-8">
            <div class="text-center">
              <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-[#255D8A] mb-4"></div>
              <p class="text-gray-600">Filtering tourist spots...</p>
            </div>
          </div>
        `;

        // Reload the tourist spots with the filter
        loadTouristSpots();
        
        // If we're in table view, reload that too
        if (!tableView.classList.contains('hidden')) {
          loadTableView();
        }
      } catch (error) {
        console.error('Error filtering tourist spots:', error);
        showNotification('Failed to filter tourist spots. Please try again.', 'error');
      }
    }
  </script>
</body>
</html>