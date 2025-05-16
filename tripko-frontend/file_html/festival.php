<?php
require_once('../../tripko-backend/config/check_session.php');
checkAdminSession();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>TripKo Pangasinan - Festivals</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Merriweather&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Kameron:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../file_css/dashboard.css" />
  <style>
    body {
        font-family: 'Kameron', serif;
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

    canvas#transportChart {
      width: 100% !important;
      height: 100% !important;
      position: absolute !important;
      top: 0;
      left: 0;
    }
  </style>
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
          <p class="px-4 text-xs font-semibold text-blue-300 uppercase">Tourism</p>
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
          <p class="px-4 text-xs font-semibold text-blue-300 uppercase">Transportation</p>
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
          <p class="px-4 text-xs font-semibold text-blue-300 uppercase">Management</p>
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
    </aside>

    <!-- Main content -->
    <main class="flex-1 bg-[#F3F1E7] p-6">
      <header class="mb-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3 text-gray-900 font-normal text-base">
            <button aria-label="Menu" class="focus:outline-none">
              <i class="fas fa-bars text-lg"></i>
            </button>
            <span>Festivals</span>
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
        <div class="flex justify-end mt-4">
          <div class="flex gap-3">
            <button onclick="openModal()" class="bg-[#255D8A] text-white px-4 py-2 rounded-md hover:bg-[#1e4d70] transition-colors">
              + Add New Festival
            </button>
            <button onclick="toggleView()" id="viewToggleBtn" class="bg-[#255D8A] text-white px-4 py-2 rounded-md hover:bg-[#1e4d70] transition-colors">
              <i class="fas fa-table"></i> Table View
            </button>
          </div>
        </div>
      </header>

      <!-- Festivals grid -->
      <div id="gridView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Festival cards will be dynamically added here -->
      </div>

      <!-- Festivals table -->
      <div id="tableView" class="hidden">
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <table class="w-full border-collapse">
            <thead class="bg-gray-50">
              <tr>
                <th class="border border-gray-300 px-4 py-2 text-left">Name</th>
                <th class="border border-gray-300 px-4 py-2 text-left">Description</th>
                <th class="border border-gray-300 px-4 py-2 text-left">Date</th>
                <th class="border border-gray-300 px-4 py-2 text-left">Municipality</th>
                <th class="border border-gray-300 px-4 py-2 text-center">Status</th>
                <th class="border border-gray-300 px-4 py-2 text-center">Actions</th>
              </tr>
            </thead>
            <tbody id="festivalTableBody">
              <!-- Festival rows will be dynamically added here -->
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <!-- Add New Festival Modal -->
  <div id="addFestivalModal" class="fixed inset-0 hidden z-50">
    <div class="bg-black bg-opacity-50 absolute inset-0"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
      <div class="form-container bg-white relative z-10 p-8 rounded shadow-lg w-full max-w-2xl">
        <button type="button" class="absolute right-4 top-4 text-gray-500 hover:text-gray-700" onclick="closeModal()">
          <i class="fas fa-times text-xl"></i>
        </button>
       
        <!-- Replace the existing festival form with this updated version -->
<form id="festivalForm" enctype="multipart/form-data">
  <!-- Festival Name and Municipality -->
  <div class="form-row grid grid-cols-2 gap-6 mb-6">
    <div class="form-group">
      <label for="festival-name" class="block text-[15px] font-medium text-gray-700 mb-2">
        Festival Name <span class="text-red-500">*</span>
      </label>
      <input type="text" id="festival-name" name="name" required 
             class="w-full border rounded-md px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-[#255D8A]" />
    </div>
    
    <div class="form-group">
      <label for="municipality" class="block text-[15px] font-medium text-gray-700 mb-2">
        Municipality <span class="text-red-500">*</span>
      </label>
      <select id="municipality" name="municipality" required 
              class="w-full border rounded-md px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-[#255D8A]">
        <option value="" disabled selected>Select municipality</option>
      </select>
    </div>
  </div>

  <!-- Description -->
  <div class="form-group mb-6">
    <label for="festival-description" class="block text-[15px] font-medium text-gray-700 mb-2">
      Description <span class="text-red-500">*</span>
    </label>
    <textarea id="festival-description" name="description" rows="4" required 
              class="w-full border rounded-md px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-[#255D8A]"></textarea>
  </div>

  <!-- Date -->
  <div class="form-group mb-6">
    <label for="festival-date" class="block text-[15px] font-medium text-gray-700 mb-2">
      Date <span class="text-red-500">*</span>
    </label>
    <input type="date" id="festival-date" name="date" required 
           class="w-full border rounded-md px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-[#255D8A]" />
  </div>

  <!-- Image Upload -->
  <div class="form-group mb-6">
    <div class="upload-area border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-[#255D8A] transition-colors">
      <i class="fas fa-cloud-upload-alt text-3xl text-[#255D8A] mb-2"></i>
      <p class="text-[15px] font-medium mb-1">Upload Image</p>
      <span class="text-[13px] text-gray-500">PNG, JPG or JPEG (max. 5MB)</span>
      <input type="file" id="festivalImage" name="image" accept="image/png, image/jpeg" class="hidden" />
      <div id="selectedFile" class="mt-2 text-[13px] text-gray-600"></div>
      <div class="image-preview mt-4"></div>
    </div>
  </div>

  <!-- Form Buttons -->
  <div class="flex justify-end space-x-2 pt-4 border-t">
    <button type="button" 
            class="px-4 py-2 rounded-md bg-gray-200 hover:bg-gray-300 transition-colors text-[15px]" 
            onclick="closeModal()">Cancel</button>
    <button type="submit" 
            class="px-4 py-2 rounded-md bg-[#255D8A] text-white hover:bg-[#1e4d70] transition-colors text-[15px]">
      Save
    </button>
  </div>
</form>
      </div>
    </div>
  </div>

  <!-- Status Change Modal -->
  <div id="statusModal" class="fixed inset-0 hidden z-50">
    <div class="bg-black bg-opacity-50 absolute inset-0"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
      <div class="bg-white relative z-10 p-6 rounded-lg shadow-lg w-full max-w-md">
        <h3 class="text-xl font-bold mb-4">Change Status</h3>
        <p class="mb-4">Current status: <span id="currentStatusText" class="font-semibold"></span></p>
        <div class="space-y-3">
          <button onclick="updateFestivalStatus('active')" 
                  class="w-full py-2 px-4 rounded bg-green-600 text-white hover:bg-green-700 transition-colors">
            Set Active
          </button>
          <button onclick="updateFestivalStatus('inactive')" 
                  class="w-full py-2 px-4 rounded bg-red-600 text-white hover:bg-red-700 transition-colors">
            Set Inactive
          </button>
          <button onclick="closeStatusModal()" 
                  class="w-full py-2 px-4 rounded bg-gray-300 hover:bg-gray-400 transition-colors">
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Mobile menu toggle
    const mobileMenuButton = document.getElementById('mobileMenuButton');
    const mobileMenu = document.getElementById('mobileMenu');
    
    mobileMenuButton?.addEventListener('click', () => {
      mobileMenu?.classList.toggle('hidden');
    });

    // File upload handling
    const uploadArea = document.querySelector('.upload-area');
    if (uploadArea) {
      uploadArea.addEventListener('click', () => {
        document.getElementById('festivalImage')?.click();
      });
    }

    // Helper function for image URLs
    function getImageUrl(imagePath) {
      if (!imagePath || imagePath === 'placeholder.jpg') {
        return '../images/placeholder.jpg';
      }
      return `/TripKo-System/uploads/${imagePath}`;
    }

    // All the rest of our functions
    function toggleView() {
      const gridView = document.getElementById('gridView');
      const tableView = document.getElementById('tableView');
      const viewToggleBtn = document.getElementById('viewToggleBtn');

      if (gridView.classList.contains('hidden')) {
        gridView.classList.remove('hidden');
        tableView.classList.add('hidden');
        viewToggleBtn.innerHTML = '<i class="fas fa-table"></i> Table View';
      } else {
        gridView.classList.add('hidden');
        tableView.classList.remove('hidden');
        viewToggleBtn.innerHTML = '<i class="fas fa-th"></i> Grid View';
      }
    }

    async function loadFestivals() {
    const grid = document.getElementById('gridView');
    const tableBody = document.getElementById('festivalTableBody');

    try {
        console.log('Fetching festivals...');
        
        // Show loading state
        grid.innerHTML = '<div class="col-span-full flex justify-center items-center py-8"><div class="loader"></div></div>';
        tableBody.innerHTML = '<tr><td colspan="6" class="text-center py-4">Loading...</td></tr>';
        
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 30000);
        
        const res = await fetch('../../tripko-backend/api/festival/read.php', {
            signal: controller.signal,
            credentials: 'include',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        
        clearTimeout(timeoutId);
        
        if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
        }
        
        const data = await res.json();
        console.log('Festivals data:', data);

        // Check if data.records exists and is an array
        const festivals = data.records || [];
        if (!Array.isArray(festivals)) {
            throw new Error('Invalid response format from server');
        }

        if (festivals.length === 0) {
            console.log('No festivals found');
            const noDataMessage = '<div class="text-center py-8 text-gray-500"><i class="fas fa-inbox text-4xl mb-3 block"></i><p>No festivals found</p></div>';
            grid.innerHTML = noDataMessage;
            tableBody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-gray-500">No festivals found</td></tr>';
            return;
        }

        // Update grid and table views with the festival data
        updateViews(festivals);
        
    } catch (err) {
        console.error('Failed to load festivals:', err);
        grid.innerHTML = '<div class="col-span-full text-center py-8 text-gray-500">Error loading festivals</div>';
        tableBody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-gray-500">Error loading festivals</td></tr>';
    }
}

// Add this helper function to update views
function updateViews(festivals) {
    const grid = document.getElementById('gridView');
    const tableBody = document.getElementById('festivalTableBody');
    
    // Update grid view
    grid.innerHTML = festivals.map(f => `
        <div class="bg-white rounded-lg shadow-md border border-gray-200 flex flex-col h-full group hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="relative w-full h-56 bg-gray-100 flex items-center justify-center overflow-hidden">
                <img src="${getImageUrl(f.image_path)}" 
                     alt="${f.name}" 
                     class="w-full h-full object-cover transition-all duration-500 group-hover:scale-110"
                     onerror="this.onerror=null; this.src='../images/placeholder.jpg';" />
            </div>
            <div class="flex-1 flex flex-col p-5">
                <div class="mb-3">
                    <div class="flex items-center justify-between mb-3">
                        <span class="bg-[#255D8A] text-white text-[14px] px-3 py-1 rounded-full font-medium">
                            ${f.town_name || 'Unknown'}
                        </span>
                        ${f.status === 'inactive' ? 
                            '<span class="bg-red-500 text-white font-medium text-[14px] px-2 py-1 rounded">Inactive</span>' 
                            : ''}
                    </div>
                </div>
                <h3 class="text-[17px] font-medium mb-2 text-[#255D8A] line-clamp-2">${f.name}</h3>
                <p class="text-[15px] text-gray-700 mb-3 line-clamp-3">${f.description}</p>
                <p class="text-[15px] text-gray-500 mt-auto flex items-center">
                    <i class="fas fa-calendar-alt mr-2"></i>${f.date}
                </p>
            </div>
        </div>
    `).join('');

    // Update table view
    tableBody.innerHTML = festivals.map(f => `
        <tr class="hover:bg-gray-50">
            <td class="border border-gray-300 px-4 py-2">${f.name}</td>
            <td class="border border-gray-300 px-4 py-2">
                <div class="max-w-xs overflow-hidden text-ellipsis">${f.description}</div>
            </td>
            <td class="border border-gray-300 px-4 py-2">${f.date}</td>
            <td class="border border-gray-300 px-4 py-2">${f.town_name || 'Unknown'}</td>
            <td class="border border-gray-300 px-4 py-2 text-center">
                <span class="px-2 py-1 rounded-full text-xs font-medium ${
                    f.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                }">
                    ${f.status || 'Unknown'}
                </span>
            </td>
            <td class="border border-gray-300 px-4 py-2 text-center">
                <div class="flex justify-center gap-2">
                    <button onclick="editFestival(${f.festival_id})" 
                            class="bg-[#255d8a] text-white px-3 py-1 rounded text-sm hover:bg-[#1e4d70]">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </button>
                    <button onclick="openStatusModal(${f.festival_id}, '${f.status || 'active'}')"
                            class="${f.status === 'inactive' ? 'bg-red-600' : 'bg-green-600'} text-white px-3 py-1 rounded text-sm hover:bg-opacity-90">
                        Status
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

    let currentFestivalId = null;

    function openStatusModal(festivalId, currentStatus) {
      currentFestivalId = festivalId;
      const modal = document.getElementById('statusModal');
      const statusText = document.getElementById('currentStatusText');
      statusText.textContent = currentStatus;
      statusText.className = 'font-medium ' + 
        (currentStatus === 'active' ? 'text-green-600' : 'text-red-600');
      modal.classList.remove('hidden');
    }

    function closeStatusModal() {
      document.getElementById('statusModal').classList.add('hidden');
      currentFestivalId = null;
    }

    async function updateFestivalStatus(newStatus) {
      if (!currentFestivalId) return;

      try {
        const res = await fetch('../../tripko-backend/api/festival/toggle_status.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ 
            festival_id: currentFestivalId,
            status: newStatus 
          })
        });
        
        if (!res.ok) {
          throw new Error(`HTTP error! status: ${res.status}`);
        }
        const data = await res.json();
        if (data.success) {
          alert(`Festival status updated to ${newStatus}`);
          closeStatusModal();
          loadFestivals();
        } else {
          throw new Error(data.message || 'Failed to update status');
        }
      } catch (err) {
        console.error('Error updating status:', err);
        alert('Error: ' + err.message);
      }
    }

    // Update the existing toggleStatus function to use the modal
    function toggleStatus(id, currentStatus) {
      openStatusModal(id, currentStatus);
    }

    function openModal() {
      document.getElementById('addFestivalModal').classList.remove('hidden');
    }

    function closeModal() {
      document.getElementById('addFestivalModal').classList.add('hidden');
      document.getElementById('festivalForm').reset();
      document.getElementById('selectedFile').textContent = '';
      const preview = document.querySelector('.upload-area .image-preview');
      if (preview) {
        preview.remove();
      }
    }

    async function editFestival(id) {
      try {
        const response = await fetch(`../../tripko-backend/api/festival/read_single.php?id=${id}`);
        const data = await response.json();
        
        if (data.success && data.festival) {
          const festival = data.festival;
          
          // Populate form fields
          const form = document.getElementById('festivalForm');
          form.name.value = festival.name || '';
          form.description.value = festival.description || '';
          form.date.value = festival.date || '';
          form.municipality.value = festival.town_id || '';
          
          // Add festival ID to form for update
          let idInput = form.querySelector('input[name="festival_id"]');
          if (!idInput) {
            idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'festival_id';
            form.appendChild(idInput);
          }
          idInput.value = id;
          
          // Show image preview if available
          if (festival.image_path) {
            const preview = document.createElement('div');
            preview.className = 'image-preview mt-4';
            preview.innerHTML = `
              <img src="${getImageUrl(festival.image_path)}" 
                   alt="Preview" 
                   class="w-full h-48 object-cover rounded"
                   onerror="this.onerror=null; this.src='../images/placeholder.jpg';" />
            `;
            
            const uploadArea = document.querySelector('.upload-area');
            const existingPreview = uploadArea.querySelector('.image-preview');
            if (existingPreview) {
              existingPreview.remove();
            }
            uploadArea.appendChild(preview);
          }
          
          openModal();
        } else {
          throw new Error(data.message || 'Failed to load festival details');
        }
      } catch (error) {
        console.error('Error loading festival:', error);
        alert('Failed to load festival details');
      }
    }

    // Update form submission to handle both create and edit
    document.getElementById('festivalForm')?.addEventListener('submit', async e => {
      e.preventDefault();
      const formData = new FormData(e.target);
      const isEdit = formData.has('festival_id');
      
      try {
        const url = isEdit ? 
          '../../tripko-backend/api/festival/update.php' : 
          '../../tripko-backend/api/festival/create.php';

        console.log('Form data being sent:', Object.fromEntries(formData));
        const res = await fetch(url, {
          method: 'POST',
          body: formData
        });
        
        const result = await res.json();
        console.log('Server response:', result);
        
        if (result.success) {
          alert(isEdit ? 'Festival updated successfully!' : 'Festival added successfully!');
          closeModal();
          loadFestivals();
        } else {
          throw new Error(result.message || `Failed to ${isEdit ? 'update' : 'add'} festival`);
        }
      } catch (err) {
        console.error(isEdit ? 'Update error:' : 'Error submitting form:', err);
        alert('Error: ' + err.message);
      }
    });

    // Initialize everything when the DOM is ready
    document.addEventListener('DOMContentLoaded', () => {
      // Set up file input handling
      const fileInput = document.getElementById('festivalImage');
      const selectedFile = document.getElementById('selectedFile');
      const uploadArea = document.querySelector('.upload-area');

      fileInput?.addEventListener('change', function() {
        const file = this.files?.[0];
        if (file) {
          selectedFile.textContent = `Selected: ${file.name}`;
          
          // Create image preview
          const reader = new FileReader();
          reader.onload = (e) => {
            const preview = document.createElement('div');
            preview.className = 'image-preview mt-4';
            preview.innerHTML = `
              <img src="${e.target.result}" 
                   alt="Preview" 
                   class="w-full h-48 object-cover rounded" />
            `;
            
            // Remove any existing preview
            const existingPreview = uploadArea.querySelector('.image-preview');
            if (existingPreview) {
              existingPreview.remove();
            }
            
            uploadArea.appendChild(preview);
          };
          reader.readAsDataURL(file);
        } else {
          selectedFile.textContent = '';
          const preview = uploadArea.querySelector('.image-preview');
          if (preview) {
            preview.remove();
          }
        }
      });

      // Load municipalities for the select dropdown
      async function loadMunicipalities() {
        try {
          const response = await fetch('../../tripko-backend/api/towns/read.php');
          const data = await response.json();
          const municipalitySelect = document.getElementById('municipality');
          
          municipalitySelect.innerHTML = '<option value="" selected disabled>Select municipality</option>';
          
          if (data.success && data.records && Array.isArray(data.records)) {
            data.records.forEach(town => {
              const option = document.createElement('option');
              option.value = town.town_id;
              option.textContent = town.name;
              municipalitySelect.appendChild(option);
            });
          } else {
            throw new Error('Invalid data format received from server');
          }
        } catch (error) {
          console.error('Failed to load municipalities:', error);
          document.getElementById('municipality').innerHTML = 
            '<option value="" disabled selected>Error loading municipalities</option>';
        }
      }
      
      // Initialize page
      loadMunicipalities();

      // Load initial data
      loadFestivals();
    });

    // Load municipalities when the page loads
    document.addEventListener('DOMContentLoaded', async () => {
      try {
        await loadMunicipalities();
        await loadFestivals();
      } catch (error) {
        console.error('Error during initialization:', error);
      }
    });

    // Load municipalities for the select dropdown
    async function loadMunicipalities() {
      const select = document.getElementById('municipality');
      if (!select) {
        console.error('Municipality select element not found');
        return;
      }

      try {
        console.log('Fetching municipalities...');
        const response = await fetch('../../tripko-backend/api/towns/read.php', {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'Cache-Control': 'no-cache'
          }
        });

        console.log('Response status:', response.status);
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        let text;
        try {
          text = await response.text();
          console.log('Raw response:', text);
        } catch (e) {
          throw new Error(`Failed to read response: ${e.message}`);
        }

        if (!text.trim()) {
          throw new Error('Empty response received from server');
        }

        let data;
        try {
          data = JSON.parse(text);
          console.log('Parsed data:', data);
        } catch (e) {
          console.error('JSON parse error:', e);
          throw new Error(`Invalid JSON response: ${e.message}`);
        }

        select.innerHTML = '<option value="" disabled selected>Select municipality</option>';
        
        if (!data.success) {
          throw new Error(data.message || 'Server returned unsuccessful response');
        }

        if (!data.records || !Array.isArray(data.records)) {
          throw new Error('Invalid data format: missing or invalid records array');
        }

        console.log('Found', data.records.length, 'municipalities');
        
        // Sort municipalities by name
        const sortedRecords = [...data.records].sort((a, b) => 
          a.name.localeCompare(b.name, undefined, {sensitivity: 'base'})
        );

        sortedRecords.forEach(town => {
          if (!town.town_id || !town.name) {
            console.warn('Invalid town data:', town);
            return;
          }
          const option = document.createElement('option');
          option.value = town.town_id;
          option.textContent = town.name;
          select.appendChild(option);
        });

        if (select.children.length <= 1) {
          throw new Error('No valid municipalities loaded');
        }
      } catch (error) {
        console.error('Failed to load municipalities:', error);
        
        const errorMessage = error.message.includes('Failed to fetch') ? 
          'Network error - please check your connection' :
          'Error loading municipalities';
          
        select.innerHTML = `<option value="" disabled selected>${errorMessage}</option>`;
      }
    }

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
    icon.style.transform = dropdown.classList.contains('hidden') ? 'rotate(180deg)' : 'rotate(0deg)';
}
  </script>
</body>
</html>