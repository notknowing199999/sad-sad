<?php
session_start();
require_once('../../tripko-backend/config/check_session.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>TripKo Pangasinan - Itineraries</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Merriweather&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../file_css/dashboard.css" />
  <style>
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
        // Transport dropdown
        const transportDropdown = document.getElementById('transportDropdown');
        const transportDropdownIcon = document.getElementById('transportDropdownIcon');

        // Close dropdown
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#transportDropdown') && !e.target.closest('[onclick*="toggleTransportDropdown"]')) {
                transportDropdown?.classList.add('hidden');
                if (transportDropdownIcon) {
                    transportDropdownIcon.style.transform = 'rotate(0deg)';
                }
            }
        });
    });
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
      <header class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3 text-gray-900 font-normal text-base">
          <button aria-label="Menu" class="focus:outline-none">
            <i class="fas fa-bars text-lg"></i>
          </button>
          <span>Itineraries</span>
        </div>
        <div class="flex items-center gap-4">
          <div>
            <input type="search" placeholder="Search" class="w-48 md:w-64 rounded-full border border-gray-400 bg-[#F3F1E7] py-1.5 px-4 text-gray-600 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#255D8A]" />
          </div>
          <button aria-label="Notifications" class="text-black text-xl focus:outline-none">
            <i class="fas fa-bell"></i>
          </button>
        </div>
      </header>

      <div class="flex justify-between items-center mb-6">
        <h2 class="font-semibold text-xl">Itineraries</h2>
        <div class="flex gap-3">
          <button onclick="openModal()" class="bg-[#255D8A] text-white px-4 py-2 rounded-md hover:bg-[#1e4d70] transition-colors">
            + Create Itinerary
          </button>
          <button onclick="toggleView()" id="viewToggleBtn" class="bg-[#255D8A] text-white px-4 py-2 rounded-md hover:bg-[#1e4d70] transition-colors">
            <i class="fas fa-table"></i> Table View
          </button>
        </div>
      </div>

      <!-- Itineraries grid -->
      <div id="gridView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Itinerary cards will be dynamically added here -->
      </div>

      <!-- Itineraries table -->
      <div id="tableView" class="hidden">
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <table class="w-full border-collapse">
            <thead class="bg-gray-50">
              <tr>
                <th class="border border-gray-300 px-4 py-2 text-left">Title</th>
                <th class="border border-gray-300 px-4 py-2 text-left">Description</th>
                <th class="border border-gray-300 px-4 py-2 text-left">Municipality</th>
                <th class="border border-gray-300 px-4 py-2 text-left">Environmental Fee</th>
                <th class="border border-gray-300 px-4 py-2 text-center">Status</th>
                <th class="border border-gray-300 px-4 py-2 text-center">Actions</th>
              </tr>
            </thead>
            <tbody id="itineraryTableBody">
              <!-- Itinerary rows will be dynamically added here -->
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <!-- Add New Itinerary Modal -->
  <div id="addItineraryModal" class="fixed inset-0 hidden z-50">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
      <div class="form-container bg-white w-full max-w-4xl rounded-lg shadow-xl">
        <button type="button" class="absolute right-4 top-4 text-gray-500 hover:text-gray-700" onclick="closeModal()">
          <i class="fas fa-times text-xl"></i>
        </button>
  
        <h2 class="form-title text-2xl font-bold mb-6">Add New Itinerary</h2>
  
        <form id="itineraryForm" enctype="multipart/form-data">
          <div class="form-row grid grid-cols-2 gap-6 mb-6">
            <div class="form-group">
              <label for="destination_id" class="block text-sm font-medium text-gray-700 mb-2">
                Municipality <span class="text-red-500">*</span>
              </label>
              <select id="destination_id" name="destination_id" required 
                      class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#255D8A]">
                <option value="" selected disabled>Select municipality</option>
              </select>
            </div>
            
            <div class="form-group">
              <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                Itinerary Name <span class="text-red-500">*</span>
              </label>
              <input type="text" id="name" name="name" required 
                     class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#255D8A]">
            </div>
          </div>
  
          <div class="form-group mb-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
              Description <span class="text-red-500">*</span>
            </label>
            <textarea id="description" name="description" rows="4" required 
                      class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#255D8A]"></textarea>
          </div>
  
          <div class="form-row grid grid-cols-2 gap-6 mb-6">
            <div class="form-group">
              <label for="environmental_fee" class="block text-sm font-medium text-gray-700 mb-2">
                Environmental Fee <span class="text-gray-400 text-xs">(Optional)</span>
              </label>
              <input type="text" id="environmental_fee" name="environmental_fee"
                     class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#255D8A]"
                     placeholder="Enter amount">
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
  
          <div class="form-buttons flex justify-end space-x-4 pt-4 border-t">
            <button type="button" class="btn btn-secondary px-6 py-2 rounded-md bg-gray-200 hover:bg-gray-300 transition-colors" 
                    onclick="closeModal()">Cancel</button>
            <button type="submit" class="btn btn-primary px-6 py-2 rounded-md bg-[#255D8A] text-white hover:bg-[#1e4d70] transition-colors">
              Save Itinerary
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <script>
    // Modal open/close
function openModal() {
  document.getElementById('addItineraryModal').classList.remove('hidden');
}
function closeModal() {
  document.getElementById('addItineraryModal').classList.add('hidden');
}
function toggleTransportDropdown(event) {
  event.preventDefault();
  const dropdown = document.getElementById('transportDropdown');
  const icon = document.getElementById('transportDropdownIcon');
  dropdown.classList.toggle('hidden');
  icon.style.transform = dropdown.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
}

// Toggle between grid and table view
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

// Form submit handler
document.addEventListener('DOMContentLoaded', () => {
  loadItineraries();
  loadDestinations();

  const form = document.querySelector('#addItineraryModal form');
  const fileInput = form.querySelector('input[type="file"]');
  const uploadArea = form.querySelector('.upload-area');

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

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(form);
    const files = fileInput.files;
    for(let i = 0; i < files.length; i++) {
      formData.append('images[]', files[i]);
    }

    const isEdit = formData.has('itinerary_id');
    const url = isEdit ? 
      '../../tripko-backend/api/itineraries/update.php' : 
      '../../tripko-backend/api/itineraries/create.php';

    try {
      const response = await fetch(url, {
        method: 'POST',
        body: formData
      });
      const data = await response.json();
      if(data.success) {
        alert(isEdit ? 'Itinerary updated!' : 'Itinerary added!');
        closeModal();
        form.reset();
        const preview = uploadArea.querySelector('.image-preview');
        if(preview) preview.remove();
        loadItineraries();
      } else {
        alert(`Failed to ${isEdit ? 'update' : 'add'} itinerary: ${data.message || 'Unknown error'}`);
      }
    } catch (error) {
      alert('Error: ' + error.message);
    }
  });
});

// Load and display itineraries
async function loadItineraries() {
  try {
    const response = await fetch('../../tripko-backend/api/itineraries/read.php');
    const data = await response.json();
    const gridContainer = document.querySelector('#gridView');
    const tableBody = document.getElementById('itineraryTableBody');
    gridContainer.innerHTML = '';
    tableBody.innerHTML = '';
    if (data && data.records && Array.isArray(data.records)) {
      data.records.forEach(itinerary => {
        // Format environmental fee once for both views
        const environmental_fee = itinerary.environmental_fee 
          ? `₱${parseFloat(itinerary.environmental_fee).toFixed(2)}` 
          : 'No fee';

        // Grid view
        gridContainer.innerHTML += `
          <div class="rounded-lg overflow-hidden border border-gray-200 shadow-md bg-white flex flex-col h-full transition-transform hover:scale-105 hover:shadow-lg">
            <div class="relative w-full h-48 bg-gray-100 flex items-center justify-center">
              <img src="${getImageUrl(itinerary.image_path)}"
                   alt="${itinerary.name || 'Itinerary'}"
                   class="w-full h-full object-cover transition-all duration-300" />
            </div>
            <div class="flex-1 flex flex-col p-4">
              <div class="mb-2">
                <span class="inline-block bg-[#255D8A] text-white text-xs px-3 py-1 rounded-full font-semibold mb-2">${itinerary.destination || 'Unknown'}</span>
              </div>
              <h3 class="text-lg font-bold mb-1 text-[#255D8A]">${itinerary.name}</h3>
              <p class="text-sm text-gray-700 mb-2 line-clamp-3">${itinerary.description}</p>
              <p class="text-xs text-gray-500 mt-auto flex items-center">
                <i class="fas fa-leaf mr-1"></i>${environmental_fee}
              </p>
            </div>
          </div>
        `;

        // Table view
        tableBody.innerHTML += `
          <tr class="hover:bg-gray-100 transition-colors">
            <td class="border border-gray-300 px-4 py-2">${itinerary.name}</td>
            <td class="border border-gray-300 px-4 py-2 line-clamp-2">${itinerary.description}</td>
            <td class="border border-gray-300 px-4 py-2">${itinerary.destination || 'N/A'}</td>
            <td class="border border-gray-300 px-4 py-2">${environmental_fee}</td>  
            <td class="border border-gray-300 px-4 py-2 text-center">
              <span class="inline-block px-2 py-1 text-xs rounded-full 
                          ${itinerary.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                ${itinerary.status || 'Active'}
              </span>
            </td>
            <td class="border border-gray-300 px-4 py-2 text-center">
              <div class="flex justify-center gap-2">
                <button onclick="editItinerary(${itinerary.itinerary_id})" 
                        class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">
                  Edit
                </button>
                <button onclick="toggleItineraryStatus(${itinerary.itinerary_id}, '${itinerary.status || 'active'}')" 
                        class="${itinerary.status === 'inactive' ? 'bg-green-600' : 'bg-red-600'} text-white px-3 py-1 rounded text-sm hover:${itinerary.status === 'inactive' ? 'bg-green-700' : 'bg-red-700'}">
                  ${itinerary.status === 'inactive' ? 'Activate' : 'Deactivate'}
                </button>
              </div>
            </td>
          </tr>
        `;
      });
    } else {
      gridContainer.innerHTML = `
        <div class="col-span-full text-center py-8 text-gray-500">
          <i class="fas fa-inbox text-4xl mb-3 block"></i>
          <p>No itineraries found</p>
        </div>
      `;
      tableBody.innerHTML = `
        <tr>
          <td colspan="6" class="text-center py-4 text-gray-500">
            <i class="fas fa-inbox text-4xl mb-3 block"></i>
            No itineraries found
          </td>
        </tr>
      `;
    }
  } catch (error) {
    document.querySelector('#gridView').innerHTML = `
      <div class="col-span-full text-center py-8 text-red-500">
        <i class="fas fa-exclamation-circle text-4xl mb-3 block"></i>
        <p>Failed to load itineraries. Please try again later.</p>
        <p class="text-sm mt-2">Error details: ${error.message}</p>
      </div>
    `;
    document.getElementById('itineraryTableBody').innerHTML = `
      <tr>
        <td colspan="6" class="text-center py-4 text-red-500">
          <i class="fas fa-exclamation-circle text-4xl mb-3 block"></i>
          Failed to load itineraries. Please try again later.
        </td>
      </tr>
    `;
  }
}

// Helper for image URL
function getImageUrl(imagePath) {
  if (!imagePath || imagePath === 'placeholder.jpg') {
    return 'https://placehold.co/400x300?text=No+Image';
  }
  return `/TripKo-System/uploads/${imagePath}`;
}

// Load destinations for the select dropdown
async function loadDestinations() {
  try {
    const response = await fetch('../../tripko-backend/api/towns/read.php');
    const data = await response.json();
    const select = document.querySelector('select');
    select.innerHTML = '<option value="" selected disabled>Select destination</option>';
    if (data && data.records && Array.isArray(data.records)) {
      data.records.forEach(town => {
        const option = document.createElement('option');
        option.value = town.town_id; // <-- This is the correct value!
        option.textContent = town.name;
        select.appendChild(option);
      });
    }
  } catch (error) {
    const select = document.querySelector('select');
    if (select) {
      select.innerHTML = '<option value="" disabled>Error loading destinations</option>';
    }
  }
}

// Edit itinerary function
async function editItinerary(id) {
  try {
    const response = await fetch(`../../tripko-backend/api/itineraries/read_single.php?id=${id}`);
    const data = await response.json();
    if (data.success && data.itinerary) {
      const itinerary = data.itinerary;
      // Populate form fields
      document.getElementById('destination_id').value = itinerary.destination_id;
      document.getElementById('name').value = itinerary.name;
      document.getElementById('description').value = itinerary.description;
      document.getElementById('environmental_fee').value = itinerary.environmental_fee || '';
      
      // Add itinerary ID to form for update
      let idInput = document.getElementById('itineraryId');
      if (!idInput) {
        idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.id = 'itineraryId';
        idInput.name = 'itinerary_id';
        document.getElementById('itineraryForm').appendChild(idInput);
      }
      idInput.value = id;
      
      openModal();
    } else {
      throw new Error(data.message || 'Failed to load itinerary details');
    }
  } catch (error) {
    console.error('Error loading itinerary:', error);
    alert('Failed to load itinerary details: ' + error.message);
  }
}

// Toggle itinerary status
async function toggleItineraryStatus(itineraryId, currentStatus) {
  try {
    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
    const response = await fetch('../../tripko-backend/api/itineraries/update_status.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        itinerary_id: itineraryId,
        status: newStatus
      })
    });

    const data = await response.json();
    if (data.success) {
      loadItineraries(); // Reload the list to show updated status
    } else {
      throw new Error(data.message || 'Failed to update status');
    }
  } catch (error) {
    console.error('Error updating status:', error);
    alert('Failed to update itinerary status: ' + error.message);
  }
}

// Form submission handler
document.getElementById('itineraryForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  const isEdit = formData.get('itinerary_id') ? true : false;
  
  try {
    const endpoint = isEdit ? 
      '../../tripko-backend/api/itineraries/update.php' : 
      '../../tripko-backend/api/itineraries/create.php';

    const response = await fetch(endpoint, {
      method: 'POST',
      body: formData
    });

    const data = await response.json();
    if (data.success) {
      alert(isEdit ? 'Itinerary updated successfully!' : 'Itinerary created successfully!');
      closeModal();
      loadItineraries();
      this.reset();
    } else {
      throw new Error(data.message || `Failed to ${isEdit ? 'update' : 'create'} itinerary`);
    }
  } catch (error) {
    console.error('Save error:', error);
    alert('Error: ' + error.message);
  }
});

// Modal open/close functions
// ...existing code...
  </script>
</body>
</html>