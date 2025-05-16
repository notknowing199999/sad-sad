<?php
require_once('../../tripko-backend/config/check_session.php');
checkAdminSession();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>TripKo Pangasinan - Municipality Management</title>  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Merriweather&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Kameron:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../file_css/dashboard.css" />
  <style>
    body {
        font-family: 'Kameron', serif;
        font-size: 17px;
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
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .form-control {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        transition: border-color 0.15s ease-in-out;
    }

    .form-control:focus {
        border-color: #255D8A;
        outline: none;
        box-shadow: 0 0 0 2px rgba(37, 93, 138, 0.1);
    }

    .required {
        color: #e11d48;
    }    .upload-area {
        border: 2px dashed #d1d5db;
        padding: 2rem;
        text-align: center;
        border-radius: 0.5rem;
        transition: all 0.15s ease-in-out;
        background-color: white;
    }

    .upload-area:hover #uploadText {
        color: #255D8A;
    }

    #uploadText {
        cursor: pointer;
        padding: 1rem;
        transition: all 0.15s ease-in-out;
    }

    #uploadText:hover {
        background-color: rgba(37, 93, 138, 0.05);
        border-radius: 0.5rem;
    }    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: 40;
        pointer-events: auto;
    }
      .modal-container {
        position: relative;
        z-index: 9999;
    }

    .form-container {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 2rem;
        width: 95%;
        max-width: 800px;
        margin: auto;
    }    .upload-container {
        position: relative;
        isolation: isolate;
        pointer-events: none;
    }
    
    .upload-area, 
    .upload-area * {
        pointer-events: auto;
    }

    .modal-content {
        pointer-events: none;
    }

    .modal-content > * {
        pointer-events: auto;
    }
  </style>
</head>
<body>
  <div class="flex h-screen bg-gray-100">
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
      </div>      <!-- Navigation Menu -->
       <nav class="flex-1 p-6 space-y-2">
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
               <a href="towns.php" class="flex items-center px-4 py-3 text-white hover:bg-[#1e4d70] rounded-lg transition-colors group">
              <i class="fas fa-chart-bar w-6"></i>
              <span class="ml-3">Towns</span>
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
            <span>Municipality Management</span>
          </div>
          <div class="flex items-center gap-4">
            <div>
              <input type="search" placeholder="Search" class="w-48 md:w-64 rounded-full border border-gray-400 bg-[#F3F1E7] py-1.5 px-4 text-gray-600 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#255D8A]" />
            </div>
            <button aria-label="Notifications" class="text-black text-xl focus:outline-none">
              <i class="fas fa-bell"></i>
            </button>
          </div>
        </div>        <div class="flex justify-end mt-4">
          <button onclick="openModal()" class="bg-[#255D8A] text-white px-4 py-2 rounded-md hover:bg-[#1e4d70] transition-colors">
            + Add New Municipality
          </button>
        </div>
      </header>

      <!-- Municipality Table View -->
      <div id="tableView">
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <table class="w-full border-collapse">
            <thead class="bg-gray-50">
              <tr>
                <th class="border border-gray-300 px-4 py-3 text-left font-semibold">Name</th>
                <th class="border border-gray-300 px-4 py-3 text-left font-semibold">Status</th>
                <th class="border border-gray-300 px-4 py-3 text-left font-semibold">Image</th>
                <th class="border border-gray-300 px-4 py-3 text-center font-semibold">Actions</th>
              </tr>
            </thead>
            <tbody id="townTableBody">
              <tr id="loadingRow">
                <td colspan="4" class="text-center py-8">
                  <div class="flex items-center justify-center text-gray-500">
                    <i class="fas fa-circle-notch fa-spin mr-2"></i>
                    <span>Loading municipalities...</span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>      <!-- Add/Edit Municipality Modal -->      <div id="municipalityModal" class="fixed inset-0 hidden" style="z-index: 9999;">
        <div class="modal-overlay" onclick="closeModal()"></div>
        <div class="relative min-h-screen flex items-center justify-center p-4">          <div class="form-container bg-white relative modal-content">
            <button type="button" class="absolute right-4 top-4 text-gray-500 hover:text-gray-700" onclick="closeModal()">
              <i class="fas fa-times text-xl"></i>
            </button>
            
            <h2 class="text-2xl font-bold mb-6" id="modalTitle">Add New Municipality</h2>
            
            <form id="municipalityForm" onsubmit="handleSubmit(event)">
              <input type="hidden" name="town_id" id="townId">
                <div class="form-group mb-6">
                <label>Municipality Name <span class="required">*</span></label>
                <input type="text" name="name" required class="form-control">
              </div>              <div class="form-group mb-6">
                <label>Municipality Image</label>
                <div class="upload-container">
                  <input type="file" name="image" accept="image/*" class="hidden" id="imageInput" onchange="handleImageSelect(event)">
                  <div class="upload-area">
                    <div id="uploadText" class="cursor-pointer" onclick="triggerFileInput(event)">
                      <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                      <p class="text-gray-500">Click here to upload an image</p>
                    </div>
                    <div id="imagePreview" class="hidden mt-4">
                      <!-- Preview will be added here -->
                    </div>
                  </div>
                </div>
              </div>

              <div class="flex justify-end gap-3">
                <button type="submit" id="submitBtn" class="px-4 py-2 bg-[#255D8A] text-white rounded hover:bg-[#1e4d70]">Save Municipality</button>
                <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50">Cancel</button>
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
            <h3 class="text-xl font-bold mb-4">Change Municipality Status</h3>
            <p class="mb-4">Current status: <span id="currentStatusText" class="font-semibold"></span></p>
            <div class="space-y-3">
              <button onclick="updateMunicipalityStatus('active')" 
                      class="w-full py-2 px-4 rounded bg-green-600 text-white hover:bg-green-700 transition-colors">
                Set Active
              </button>
              <button onclick="updateMunicipalityStatus('inactive')" 
                      class="w-full py-2 px-4 rounded bg-red-600 text-white hover:bg-red-700 transition-colors">
                Set Inactive
              </button>
              <button onclick="closeStatusModal()" 
                      class="w-full py-2 px-4 rounded bg-gray-200 text-gray-800 hover:bg-gray-300 transition-colors">
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script>
    let currentTownId = null;

    document.addEventListener('DOMContentLoaded', () => {
      loadTableView();
      
      // Close dropdown when clicking outside
      document.addEventListener('click', (e) => {
        if (!e.target.closest('#transportDropdown') && !e.target.closest('[onclick*="toggleTransportDropdown"]')) {
          const dropdown = document.getElementById('transportDropdown');
          const icon = document.getElementById('transportDropdownIcon');
          if (dropdown) {
            dropdown.classList.add('hidden');
            if (icon) {
              icon.style.transform = 'rotate(0deg)';
            }
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

    async function loadTableView() {
      try {
        const response = await fetch('../../tripko-backend/api/towns/read.php');
        const data = await response.json();
        const tableBody = document.getElementById('townTableBody');
        tableBody.innerHTML = '';
        
        if (data && data.records && Array.isArray(data.records)) {
          data.records.forEach(town => {
            const statusClass = town.status === 'inactive' ? 'bg-red-50' : '';
            const statusColor = town.status === 'inactive' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800';
            
            tableBody.innerHTML += `
              <tr class="hover:bg-gray-50 transition-colors ${statusClass}">
                <td class="border border-gray-300 px-4 py-3">
                  <div class="text-sm font-medium text-gray-900">${town.name || ''}</div>
                </td>
                <td class="border border-gray-300 px-4 py-3">
                  <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${statusColor}">
                    ${town.status || 'active'}
                  </span>
                </td>
                <td class="border border-gray-300 px-4 py-3">
                  <img src="${town.image_path ? '../../uploads/towns/' + town.image_path : '../file_images/placeholder.jpg'}" 
                       alt="${town.name}" 
                       class="h-16 w-24 object-cover rounded"
                       onerror="this.src='../file_images/placeholder.jpg'">
                </td>
                <td class="border border-gray-300 px-4 py-3 text-center">
                  <div class="flex justify-center gap-2">
                    <button onclick='editMunicipality(${JSON.stringify(town)})' 
                            class="bg-[#255d8a] text-white px-3 py-1.5 rounded text-sm hover:bg-[#1e4d70] transition-colors">
                      <i class="fas fa-edit mr-1"></i>Edit
                    </button>
                    <button onclick="openStatusModal(${town.town_id}, '${town.status || 'active'}')"
                            class="${town.status === 'inactive' ? 'bg-red-600' : 'bg-green-600'} text-white px-3 py-1.5 rounded text-sm hover:opacity-90 transition-opacity">
                      <i class="fas ${town.status === 'inactive' ? 'fa-toggle-off' : 'fa-toggle-on'} mr-1"></i>Status
                    </button>
                  </div>
                </td>
              </tr>
            `;
          });
        } else {
          tableBody.innerHTML = `
            <tr>
              <td colspan="4" class="text-center py-8 text-gray-500">
                <i class="fas fa-info-circle text-xl mb-2"></i>
                <p>No municipalities found</p>
              </td>
            </tr>
          `;
        }
      } catch (error) {
        console.error('Error loading table data:', error);
        tableBody.innerHTML = `
          <tr>
            <td colspan="4" class="text-center py-8 text-red-500">
              <i class="fas fa-exclamation-circle text-xl mb-2"></i>
              <p>Error loading municipalities</p>
            </td>
          </tr>
        `;
      }
    }

    function openStatusModal(townId, currentStatus) {
      currentTownId = townId;
      const modal = document.getElementById('statusModal');
      const statusText = document.getElementById('currentStatusText');
      statusText.textContent = currentStatus;
      statusText.className = 'font-medium ' + 
        (currentStatus === 'inactive' ? 'text-red-600' : 'text-green-600');
      modal.classList.remove('hidden');
    }

    function closeStatusModal() {
      document.getElementById('statusModal').classList.add('hidden');
      currentTownId = null;
    }

    async function updateMunicipalityStatus(newStatus) {
      if (!currentTownId) return;

      try {
        // Show loading state
        const buttons = document.querySelectorAll('#statusModal button');
        buttons.forEach(btn => {
          btn.disabled = true;
          if (btn.textContent.trim().toLowerCase().includes(newStatus)) {
            btn.innerHTML = `<i class="fas fa-circle-notch fa-spin mr-2"></i>Updating...`;
          }
        });

        const response = await fetch('../../tripko-backend/api/towns/toggle_status.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ 
            town_id: currentTownId,
            status: newStatus 
          })
        });

        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        if (data.success) {
          closeStatusModal();
          await loadTableView();
          showNotification(`Municipality status updated to ${newStatus}`, 'success');
        } else {
          throw new Error(data.message || 'Failed to update status');
        }
      } catch (error) {
        console.error('Error updating status:', error);
        showNotification(error.message, 'error');
      } finally {
        // Reset button states
        const buttons = document.querySelectorAll('#statusModal button');
        buttons.forEach(btn => {
          btn.disabled = false;
          if (btn.innerHTML.includes('fa-spin')) {
            const statusText = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
            btn.innerHTML = `Set ${statusText}`;
          }
        });
      }
    }

    function openModal(municipalityData = null) {
      const modal = document.getElementById('municipalityModal');
      const form = document.getElementById('municipalityForm');
      const modalTitle = document.getElementById('modalTitle');
      const imagePreview = document.getElementById('imagePreview');
      const uploadText = document.getElementById('uploadText');
      
      // Reset form and preview
      form.reset();
      imagePreview.innerHTML = '';
      imagePreview.classList.add('hidden');
      uploadText.classList.remove('hidden');

      if (municipalityData) {
        // Editing existing municipality
        console.log('Editing municipality:', municipalityData);
        modalTitle.textContent = 'Edit Municipality';
        document.getElementById('townId').value = municipalityData.town_id;
        form.name.value = municipalityData.name || '';
        if (municipalityData.image_path) {
          showImagePreview('../../uploads/towns/' + municipalityData.image_path);
        }
      } else {
        // Adding new municipality
        console.log('Adding new municipality');
        modalTitle.textContent = 'Add New Municipality';
        document.getElementById('townId').value = '';
      }
      
      modal.classList.remove('hidden');
    }

    function closeModal() {
      const modal = document.getElementById('municipalityModal');
      const form = document.getElementById('municipalityForm');
      const imagePreview = document.getElementById('imagePreview');
      const uploadText = document.getElementById('uploadText');
      
      // Reset form and clear data
      form.reset();
      document.getElementById('townId').value = '';
      imagePreview.innerHTML = '';
      imagePreview.classList.add('hidden');
      uploadText.classList.remove('hidden');
      
      // Reset any error states
      form.querySelectorAll('.form-control').forEach(input => {
        input.classList.remove('border-red-500');
      });
      
      // Hide modal
      modal.classList.add('hidden');
      
      // Enable submit button and reset text
      const submitBtn = document.getElementById('submitBtn');
      submitBtn.disabled = false;
      submitBtn.innerHTML = 'Save Municipality';
    }

    function editMunicipality(municipality) {
      if (!municipality || !municipality.town_id) {
        showNotification('Invalid municipality data', 'error');
        return;
      }

      try {
        openModal(municipality);
      } catch (error) {
        console.error('Error setting up edit form:', error);
        showNotification('Error loading municipality data', 'error');
      }
    }

    function showImagePreview(url) {
      const preview = document.getElementById('imagePreview');
      const uploadText = document.getElementById('uploadText');
      
      preview.innerHTML = `
        <div class="relative">
          <img src="${url}" 
               alt="Preview" 
               class="max-h-48 rounded-lg mx-auto shadow-sm cursor-pointer hover:shadow transition-shadow"
               onclick="triggerFileInput(event)"
               onerror="handleImageError(this)">
          <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
            <span class="text-white px-4 py-2 rounded cursor-pointer">
              <i class="fas fa-camera mr-2"></i>Change image
            </span>
          </div>
        </div>
      `;
      
      uploadText.classList.add('hidden');
      preview.classList.remove('hidden');
    }

    function handleImageError(img) {
      img.parentElement.innerHTML = `
        <div class="text-center p-4 bg-red-50 rounded-lg">
          <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
          <p class="mt-2 text-red-600">Error loading image</p>
          <button onclick="triggerFileInput(event)" 
                  class="mt-2 text-blue-600 hover:text-blue-800 text-sm">
            Upload new image
          </button>
        </div>
      `;
    }

    function triggerFileInput(event) {
      if (event) {
        event.preventDefault();
        event.stopPropagation();
      }
      const fileInput = document.getElementById('imageInput');
      fileInput?.click();
    }
    
    function showNotification(message, type = 'success') {
      // Remove any existing notifications
      const existingNotifications = document.querySelectorAll('.notification');
      existingNotifications.forEach(notification => notification.remove());

      // Create new notification
      const notification = document.createElement('div');
      notification.className = `notification fixed top-4 right-4 p-4 rounded-lg shadow-lg transform transition-all duration-300 ease-in-out z-50 flex items-center space-x-3 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
      } text-white`;

      notification.innerHTML = `
        <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
        <span>${message}</span>
      `;

      // Add to document
      document.body.appendChild(notification);

      // Animate in
      setTimeout(() => {
        notification.style.transform = 'translateY(10px)';
      }, 100);

      // Remove after delay
      setTimeout(() => {
        notification.style.transform = 'translateY(-100%)';
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
      }, 3000);
    }

    // Handle form submission
    async function handleSubmit(event) {
      event.preventDefault();
      const form = document.getElementById('municipalityForm');
      const formData = new FormData(form);
      const townId = document.getElementById('townId').value;
      const submitBtn = document.getElementById('submitBtn');

      try {
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i>Saving...';

        // Validate form data
        const name = formData.get('name').trim();
        if (!name) {
          throw new Error('Municipality name is required');
        }

        const isEditing = townId && townId.trim() !== '';
        const url = `../../tripko-backend/api/towns/${isEditing ? 'update' : 'create'}.php`;

        // Validate image if one is selected
        const imageFile = formData.get('image');
        if (imageFile && imageFile.size > 0) {
          const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
          if (!validTypes.includes(imageFile.type)) {
            throw new Error('Please select a valid image file (JPEG, PNG, or GIF)');
          }
          if (imageFile.size > 5 * 1024 * 1024) {
            throw new Error('Image size should be less than 5MB');
          }
        }

        // Send request
        const response = await fetch(url, {
          method: 'POST',
          body: formData
        });

        if (!response.ok) {
          throw new Error(`Server error: ${response.status}`);
        }

        const data = await response.json();
        if (data.success) {
          closeModal();
          await loadTableView();
          showNotification(
            isEditing ? 'Municipality updated successfully' : 'Municipality added successfully', 
            'success'
          );
        } else {
          throw new Error(data.message || 'Error saving municipality');
        }
      } catch (error) {
        console.error('Error:', error);
        showNotification(error.message, 'error');
      } finally {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Save Municipality';
      }
    }
  </script>
</body>
</html>
