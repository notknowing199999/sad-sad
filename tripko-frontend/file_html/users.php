<?php
require_once('../../tripko-backend/config/check_session.php');
checkAdminSession();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>TripKo Pangasinan - Users Management</title>  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Merriweather&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Kameron:wght@400;500;600;700&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="../file_css/dashboard.css" />
  <script src="../file_js/users.js" defer></script>
  <style>
    body {
      font-family: 'kameron';
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
      <div class="p-6 border-b border-blue-700">
        <div class="flex items-center space-x-4">
          <div class="p-2 bg-white bg-opacity-10 rounded-lg">
            <i class="fas fa-compass text-3xl"></i>
          </div>
          <div>
            <h1 class="text-2xl font-">TripKo</h1>
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
            </a>            <a href="itineraries.php" class="flex items-center px-4 py-3 text-white hover:bg-[#1e4d70] rounded-lg transition-colors group">
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
          <span>User Management</span>
        </div>
        <div class="flex items-center gap-4">
          <div>
            <input type="search" id="userSearch" placeholder="Search users" class="w-48 md:w-64 rounded-full border border-gray-400 bg-[#F3F1E7] py-1.5 px-4 text-gray-600 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#255D8A]" />
          </div>
          <button aria-label="Notifications" class="text-black text-xl focus:outline-none">
            <i class="fas fa-bell"></i>
          </button>
        </div>
      </header>

      <div class="flex justify-between items-center mb-6">
        <h2 class="font-medium text-xl">User Accounts</h2>
        <div class="flex gap-3">          <button onclick="openAccountModal()" class="bg-[#255D8A] text-white px-4 py-2 rounded-md hover:bg-[#1e4d70] transition-colors">
            <i class="fas fa-user-plus mr-2"></i>Add Account
          </button>
          <button onclick="openProfileModal()" class="bg-[#255D8A] text-white px-4 py-2 rounded-md hover:bg-[#1e4d70] transition-colors">
            <i class="fas fa-id-card mr-2"></i>Add Profile
          </button>
        </div>      </div>
      <!-- Users table view -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
              <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody id="usersTableBody" class="bg-white divide-y divide-gray-200">
            <!-- User rows will be dynamically added here -->
          </tbody>
        </table>
      </div>
    </main>
  </div>
  <!-- Account Creation Modal -->
  <div id="accountModal" class="fixed inset-0 hidden z-50">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
      <div class="form-container bg-white w-full max-w-4xl rounded-lg shadow-xl">
        <button type="button" class="absolute right-4 top-4 text-gray-500 hover:text-gray-700" onclick="closeAccountModal()">
          <i class="fas fa-times text-xl"></i>
        </button>
        
        <h2 id="accountModalTitle" class="text-2xl font-medium mb-6">Create New Account</h2>
        
        <form id="accountForm">
          <div class="form-row grid grid-cols-2 gap-6 mb-6">
            <div class="form-group">
              <label for="username" class="block text-[15px] font-medium text-gray-700 mb-2">
                Username <span class="text-red-500">*</span>
              </label>
              <input type="text" id="username" name="username" required 
                     class="w-full border rounded-md px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-[#255D8A]">
            </div>
            
            <div class="form-group">
              <label for="password" class="block text-[15px] font-medium text-gray-700 mb-2">
                Password <span class="text-red-500">*</span>
              </label>
              <input type="password" id="password" name="password" required 
                     class="w-full border rounded-md px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-[#255D8A]">
            </div>
          </div>
          
          <div class="form-row grid grid-cols-2 gap-6 mb-6">
            <div class="form-group">
              <label for="user_type" class="block text-[15px] font-medium text-gray-700 mb-2">
                User Type <span class="text-red-500">*</span>
              </label>
              <select id="user_type" name="user_type" required 
                      class="w-full border rounded-md px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-[#255D8A]">
                <option value="" disabled selected>Select user type</option>
                <option value="1">Admin</option>
                <option value="2">Regular User</option>
              </select>
            </div>
            
            <div class="form-group">
              <label for="status" class="block text-[15px] font-medium text-gray-700 mb-2">
                Status <span class="text-red-500">*</span>
              </label>
              <select id="status" name="status" required 
                      class="w-full border rounded-md px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-[#255D8A]">
                <option value="" disabled selected>Select status</option>
                <option value="1">Active</option>
                <option value="2">Inactive</option>
              </select>
            </div>
          </div>
          
          <div class="form-buttons flex justify-end space-x-2 pt-4 border-t">
            <button type="button" onclick="closeAccountModal()" 
                    class="px-4 py-2 rounded-md bg-gray-200 hover:bg-gray-300 transition-colors text-[15px]">
              Cancel
            </button>
            <button type="submit" 
                    class="px-4 py-2 rounded-md bg-[#255D8A] text-white hover:bg-[#1e4d70] transition-colors text-[15px]">
              Create Account
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- Profile Information Modal -->
  <div id="profileModal" class="fixed inset-0 hidden z-50">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
      <div class="form-container bg-white w-full max-w-4xl rounded-lg shadow-xl">
        <button type="button" class="absolute right-4 top-4 text-gray-500 hover:text-gray-700" onclick="closeProfileModal()">
          <i class="fas fa-times text-xl"></i>
        </button>
        
        <h2 id="profileModalTitle" class="text-2xl font-medium mb-6">Add Profile Information</h2>
        
        <form id="profileForm">
          <div class="form-row grid grid-cols-2 gap-6 mb-6">
            <div class="form-group col-span-2">
              <label for="select_user" class="block text-[15px] font-medium text-gray-700 mb-2">
                Select User <span class="text-red-500">*</span>
              </label>
              <select id="select_user" name="user_id" required 
                      class="w-full border rounded-md px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-[#255D8A]">
                <option value="" disabled selected>Select a user</option>
                <!-- Will be populated dynamically -->
              </select>
            </div>
          </div>

          <div class="form-row grid grid-cols-2 gap-6 mb-6">
            <div class="form-group">
              <label for="first_name" class="block text-[15px] font-medium text-gray-700 mb-2">
                First Name <span class="text-red-500">*</span>
              </label>
              <input type="text" id="first_name" name="first_name" required 
                     class="w-full border rounded-md px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-[#255D8A]">
            </div>
            
            <div class="form-group">
              <label for="last_name" class="block text-[15px] font-medium text-gray-700 mb-2">
                Last Name <span class="text-red-500">*</span>
              </label>
              <input type="text" id="last_name" name="last_name" required 
                     class="w-full border rounded-md px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-[#255D8A]">            </div>
          </div>

          <div class="form-row grid grid-cols-2 gap-6 mb-6">
            <div class="form-group">
              <label for="email" class="block text-[15px] font-medium text-gray-700 mb-2">
                Email <span class="text-red-500">*</span>
              </label>
              <input type="email" id="email" name="email" required 
                     class="w-full border rounded-md px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-[#255D8A]">
            </div>

            <div class="form-group">
              <label for="user_profile_dob" class="block text-[15px] font-medium text-gray-700 mb-2">
                Date of Birth <span class="text-red-500">*</span>
              </label>
              <input type="date" id="user_profile_dob" name="user_profile_dob" required 
                     class="w-full border rounded-md px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-[#255D8A]">
            </div>
          </div>

          <div class="form-row grid grid-cols-2 gap-6 mb-6">
            <div class="form-group">
              <label for="contact_number" class="block text-[15px] font-medium text-gray-700 mb-2">
                Contact Number <span class="text-gray-400 text-[13px]">(Optional)</span>
              </label>
              <input type="tel" id="contact_number" name="contact_number" 
                     class="w-full border rounded-md px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-[#255D8A]">
            </div>

            <div class="form-group">
              <label for="address" class="block text-[15px] font-medium text-gray-700 mb-2">
                Address <span class="text-gray-400 text-[13px]">(Optional)</span>
              </label>
              <textarea id="address" name="address" rows="3" 
                       class="w-full border rounded-md px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-[#255D8A]"></textarea>
            </div>
          </div>

          <div class="form-buttons flex justify-end space-x-2 pt-4 border-t">
            <button type="button" onclick="closeProfileModal()"
                    class="px-4 py-2 rounded-md bg-gray-200 hover:bg-gray-300 transition-colors text-[15px]">
              Cancel
            </button>
            <button type="submit" 
                    class="px-4 py-2 rounded-md bg-[#255D8A] text-white hover:bg-[#1e4d70] transition-colors text-[15px]">
              Save Profile
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- Edit User Modal -->
  <div id="userModal" class="fixed inset-0 hidden z-50">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
      <div class="form-container bg-white w-full max-w-4xl rounded-lg shadow-xl">
        <button type="button" class="absolute right-4 top-4 text-gray-500 hover:text-gray-700" onclick="closeModal()">
          <i class="fas fa-times text-xl"></i>
        </button>
        
        <h2 id="modalTitle" class="text-2xl font-medium mb-6">Edit User</h2>
        
        <form id="userForm">
          <div class="form-row grid grid-cols-2 gap-6 mb-6">
            <div class="form-group">
              <label for="username" class="block text-[15px] font-medium text-gray-700 mb-2">
                Username <span class="text-red-500">*</span>
              </label>
              <input type="text" id="username" name="username" required 
                     class="w-full border rounded-md px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-[#255D8A]">
            </div>
            
            <div class="form-group">
              <label for="password" class="block text-[15px] font-medium text-gray-700 mb-2">
                Password <span class="text-gray-400 text-[13px]">(Leave blank to keep current)</span>
              </label>
              <input type="password" id="password" name="password"
                     class="w-full border rounded-md px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-[#255D8A]">
            </div>
          </div>

          <div class="form-row grid grid-cols-2 gap-6 mb-6">
            <div class="form-group">
              <label for="user_type" class="block text-[15px] font-medium text-gray-700 mb-2">
                User Type <span class="text-red-500">*</span>
              </label>
              <select id="user_type" name="user_type" required 
                      class="w-full border rounded-md px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-[#255D8A]">
                <option value="" disabled selected>Select user type</option>
                <option value="1">Admin</option>
                <option value="2">Regular User</option>
              </select>
            </div>
            
            <div class="form-group">
              <label for="status" class="block text-[15px] font-medium text-gray-700 mb-2">
                Status <span class="text-red-500">*</span>
              </label>
              <select id="status" name="status" required 
                      class="w-full border rounded-md px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-[#255D8A]">
                <option value="" disabled selected>Select status</option>
                <option value="1">Active</option>
                <option value="2">Inactive</option>
              </select>
            </div>
          </div>

          <div class="form-row grid grid-cols-2 gap-6 mb-6">
            <div class="form-group">
              <label for="first_name" class="block text-[15px] font-medium text-gray-700 mb-2">
                First Name <span class="text-red-500">*</span>
              </label>
              <input type="text" id="first_name" name="first_name" required 
                     class="w-full border rounded-md px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-[#255D8A]">
            </div>
            
            <div class="form-group">
              <label for="last_name" class="block text-[15px] font-medium text-gray-700 mb-2">
                Last Name <span class="text-red-500">*</span>
              </label>
              <input type="text" id="last_name" name="last_name" required 
                     class="w-full border rounded-md px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-[#255D8A]">
            </div>
          </div>

          <div class="form-row grid grid-cols-2 gap-6 mb-6">
            <div class="form-group">
              <label for="user_profile_dob" class="block text-[15px] font-medium text-gray-700 mb-2">
                Date of Birth <span class="text-red-500">*</span>
              </label>
              <input type="date" id="user_profile_dob" name="user_profile_dob" required 
                     class="w-full border rounded-md px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-[#255D8A]">
            </div>

            <div class="form-group">
              <label for="email" class="block text-[15px] font-medium text-gray-700 mb-2">
                Email <span class="text-red-500">*</span>
              </label>
              <input type="email" id="email" name="email" required 
                     class="w-full border rounded-md px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-[#255D8A]">
            </div>
          </div>

          <div class="form-row grid grid-cols-2 gap-6 mb-6">
            <div class="form-group">
              <label for="contact_number" class="block text-[15px] font-medium text-gray-700 mb-2">
                Contact Number <span class="text-gray-400 text-[13px]">(Optional)</span>
              </label>
              <input type="tel" id="contact_number" name="contact_number" 
                     class="w-full border rounded-md px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-[#255D8A]">
            </div>

            <div class="form-group">
              <label for="address" class="block text-[15px] font-medium text-gray-700 mb-2">
                Address <span class="text-gray-400 text-[13px]">(Optional)</span>
              </label>
              <textarea id="address" name="address" rows="3" 
                       class="w-full border rounded-md px-3 py-2 text-[15px] focus:outline-none focus:ring-2 focus:ring-[#255D8A]"></textarea>
            </div>
          </div>

          <div class="form-buttons flex justify-end space-x-2 pt-4 border-t">
            <button type="button" onclick="closeModal()"
                    class="px-4 py-2 rounded-md bg-gray-200 hover:bg-gray-300 transition-colors text-[15px]">
              Cancel
            </button>
            <button type="submit" 
                    class="px-4 py-2 rounded-md bg-[#255D8A] text-white hover:bg-[#1e4d70] transition-colors text-[15px]">
              Save Changes
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- Status Toggle Modal -->
  <div id="statusModal" class="fixed inset-0 hidden z-50">
    <div class="bg-black bg-opacity-50 absolute inset-0"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
      <div class="bg-white relative z-10 p-6 rounded-lg shadow-lg w-full max-w-md">
        <h3 class="text-xl font-bold mb-4">Change User Status</h3>
        <p class="mb-4">Current status: <span id="currentStatusText" class="font-semibold"></span></p>
        <div class="space-y-3">
          <button onclick="updateUserStatus('active')" 
                  class="w-full py-2 px-4 rounded bg-green-600 text-white hover:bg-green-700 transition-colors">
            Set Active
          </button>
          <button onclick="updateUserStatus('inactive')" 
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
    // Initialize variables
    let userModal;
    let userForm;
    let accountModal;
    let profileModal;
    let accountForm;
    let profileForm;
    let modalTitle;    // Initialize these when DOM is loaded
    document.addEventListener('DOMContentLoaded', () => {
      userModal = document.getElementById('userModal');
      userForm = document.getElementById('userForm');
      accountModal = document.getElementById('accountModal');
      profileModal = document.getElementById('profileModal');
      accountForm = document.getElementById('accountForm');
      profileForm = document.getElementById('profileForm');
      modalTitle = document.getElementById('modalTitle');

      // Load initial data
      loadUsers();

      // Setup dropdowns
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

    function openModal() {
      userModal.classList.remove('hidden');
      userForm.reset();
      modalTitle.textContent = 'Add New User';
    }

    function closeModal() {
      userModal.classList.add('hidden');
      userForm.reset();
    }

    function openAccountModal() {
      accountModal.classList.remove('hidden');
      accountForm.reset();
    }

    function closeAccountModal() {
      accountModal.classList.add('hidden');
      accountForm.reset();
    }

    function openProfileModal() {
      profileModal.classList.remove('hidden');
      profileForm.reset();
      loadUsersForSelect();
    }

    function closeProfileModal() {
      profileModal.classList.add('hidden');
      profileForm.reset();
    }    // Transport dropdown function
    function toggleTransportDropdown(event) {
      event.preventDefault();
      const dropdown = document.getElementById('transportDropdown');
      const icon = document.getElementById('transportDropdownIcon');
      dropdown.classList.toggle('hidden');
      icon.style.transform = dropdown.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
    }

    function toggleTransportDropdown(event) {
      event.preventDefault();
      const dropdown = document.getElementById('transportDropdown');
      const icon = document.getElementById('transportDropdownIcon');
      dropdown.classList.toggle('hidden');
      icon.style.transform = dropdown.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';    }
    
    // Search functionality
    document.getElementById('userSearch').addEventListener('input', function(e) {
      const searchTerm = e.target.value.toLowerCase();
      const userRows = document.querySelectorAll('#usersTableBody tr');
      
      userRows.forEach(row => {
        if (row.querySelector('[colspan]')) return; // Skip loading/error rows
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
      });
    });
    
    // Load and display users
    async function loadUsers() {
      const tableBody = document.querySelector('#usersTableBody');
      // Show loading state
      tableBody.innerHTML = `
        <tr>
          <td colspan="5" class="px-6 py-4 text-center text-gray-500">
            <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
            <p>Loading users...</p>
          </td>
        </tr>
      `;
      
      try {
        console.log('Fetching users...');
        const response = await fetch('../../tripko-backend/api/users/read.php');
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        console.log('Received data:', data);
        tableBody.innerHTML = '';
        
        if (data.records && Array.isArray(data.records)) {
            console.log('Number of users:', data.records.length);
            data.records.forEach(user => {
              console.log('Processing user:', user);
              const fullName = `${user.first_name || ''} ${user.last_name || ''}`.trim() || 'No name provided';
              tableBody.innerHTML += `
              <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      <div class="h-10 w-10 rounded-full bg-[#255D8A] bg-opacity-10 flex items-center justify-center">
                        <i class="fas fa-user text-[#255D8A]"></i>
                      </div>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">${user.username}</div>
                      <div class="text-sm text-gray-500">${fullName}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                    user.status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                  }">
                    ${user.status}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  ${user.user_type}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  ${user.email || 'No email provided'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">                  <button onclick="editUser(${user.user_id})" class="text-blue-600 hover:text-blue-900 mr-3">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button onclick="openStatusModal(${user.user_id}, '${user.status}')" 
                          class="${user.status === 'Inactive' ? 'text-red-600' : 'text-green-600'} hover:opacity-80">
                    <i class="fas fa-toggle-on"></i>
                    Status
                  </button>
                </td>
              </tr>
            `;
          });} else {
          tableBody.innerHTML = `
            <tr>
              <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                <i class="fas fa-users text-2xl mb-2"></i>
                <p>No users found</p>
              </td>
            </tr>
          `;
        }
      } catch (error) {
        console.error('Error:', error);
        tableBody.innerHTML = `
          <tr>
            <td colspan="5" class="px-6 py-4 text-center text-red-500">
              <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
              <p>Failed to load users: ${error.message}</p>
              <button onclick="loadUsers()" class="mt-4 px-4 py-2 bg-[#255D8A] text-white rounded-md hover:bg-[#1e4d70] transition-colors">
                <i class="fas fa-sync-alt mr-2"></i>Try Again
              </button>
            </td>
          </tr>
        `;
      }
    }

    let currentUserId = null;

    function openStatusModal(userId, currentStatus) {
      currentUserId = userId;
      const modal = document.getElementById('statusModal');
      const statusText = document.getElementById('currentStatusText');
      statusText.textContent = currentStatus;
      statusText.className = 'font-semibold ' + 
        (currentStatus.toLowerCase() === 'inactive' ? 'text-red-600' : 'text-green-600');
      modal.classList.remove('hidden');
    }

    function closeStatusModal() {
      const modal = document.getElementById('statusModal');
      modal.classList.add('hidden');
      currentUserId = null;
    }

    async function updateUserStatus(newStatus) {
      if (!currentUserId) return;

      try {
        const response = await fetch('../../tripko-backend/api/users/update_status.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            user_id: currentUserId,
            status: newStatus
          })
        });
        
        const data = await response.json();
        if (data.success) {
          closeStatusModal();
          loadUsers(); // Refresh the user list
        } else {
          throw new Error(data.message || 'Failed to update status');
        }
      } catch (error) {
        console.error('Error updating status:', error);
        alert('Failed to update user status: ' + error.message);
      }
    }

    // Edit user
    async function editUser(userId) {
      try {
        const response = await fetch(`../../tripko-backend/api/users/read.php?user_id=${userId}`);
        const data = await response.json();
        const user = data.records.find(u => u.user_id === userId);
        
        if (!user) {
          throw new Error('User not found');
        }

        // Set form title
        document.getElementById('modalTitle').textContent = 'Edit User';
        
        // Set form values
        const form = document.getElementById('userForm');
        form.username.value = user.username;
        form.password.required = false; // Password not required for edit
        form.user_type.value = user.user_type_id;
        form.status.value = user.user_status_id;
        
        // Set profile information
        if (user.first_name) form.first_name.value = user.first_name;
        if (user.last_name) form.last_name.value = user.last_name;
        if (user.user_profile_dob) form.user_profile_dob.value = user.user_profile_dob;
        if (user.email) form.email.value = user.email;
        if (user.contact_number) form.contact_number.value = user.contact_number;
        
        // Add user_id to form for update
        const userIdInput = document.createElement('input');
        userIdInput.type = 'hidden';
        userIdInput.name = 'user_id';
        userIdInput.value = userId;
        form.appendChild(userIdInput);
        
        // Show modal
        openModal();
      } catch (error) {
        console.error('Edit error:', error);
        alert('Error: ' + error.message);
      }
    }

    // Form submission - Updated to handle both create and edit
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(form);
      const isEdit = formData.has('user_id');
      
      try {
        const url = isEdit 
          ? '../../tripko-backend/api/users/update.php'
          : '../../tripko-backend/api/users/create.php';

        const response = await fetch(url, {
          method: 'POST',
          body: formData
        });

        const data = await response.json();
        if(data.success) {
          alert(isEdit ? 'User updated successfully!' : 'User created successfully!');
          closeModal();
          loadUsers();
        } else {
          throw new Error(data.message || `Failed to ${isEdit ? 'update' : 'create'} user`);
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Error: ' + error.message);
      }
    });

    // Handle account creation
    document.getElementById('accountForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      
      try {
          const formData = new FormData(this);
          
          // Debug log
          console.log('Sending data:', {
              username: formData.get('username'),
              user_type: formData.get('user_type'),
              status: formData.get('status')
          });

          const response = await fetch('../../tripko-backend/api/users/create.php', {
              method: 'POST',
              body: formData,
              headers: {
                  'Accept': 'application/json'
              }
          });

          const result = await response.text();
          console.log('Raw response:', result);
          
          let data;
          try {
              data = JSON.parse(result);
          } catch (e) {
              console.error('Failed to parse JSON response:', e);
              throw new Error('Server returned invalid JSON');
          }

          if (data.success) {
              alert('User created successfully!');
              closeAccountModal();
              location.reload(); // Refresh to show new user
          } else {
              throw new Error(data.message || 'Failed to create user');
          }
      } catch (error) {
          console.error('Error:', error);
          alert('Error creating user: ' + error.message);
      }
    });

    // Handle profile creation
    profileForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(profileForm);
      
      try {
        const response = await fetch('../../tripko-backend/api/users/update_profile.php', {
          method: 'POST',
          body: formData
        });

        const data = await response.json();
        if (data.success) {
          alert('Profile updated successfully!');
          closeProfileModal();
          loadUsers();
        } else {
          throw new Error(data.message || 'Failed to update profile');
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Error: ' + error.message);
      }
    });

    // Load users for profile select dropdown
    async function loadUsersForSelect() {
      try {
        const response = await fetch('../../tripko-backend/api/users/read.php');
        const data = await response.json();
        const select = document.getElementById('select_user');
        select.innerHTML = '<option value="" disabled selected>Select a user</option>';
        
        if (data.records && Array.isArray(data.records)) {
          data.records.forEach(user => {
            if (!user.first_name) { // Only show users without profiles
              select.innerHTML += `
                <option value="${user.user_id}">${user.username}</option>
              `;
            }
          });
        }
      } catch (error) {
        console.error('Error loading users:', error);
        alert('Error loading users. Please try again.');
      }
    }

    // Delete user
    async function deleteUser(userId, username) {
      if (confirm(`Are you sure you want to delete the user "${username}"?`)) {
        try {
          const response = await fetch(`../../tripko-backend/api/users/delete.php?user_id=${userId}`, {
            method: 'DELETE'
          });
          const data = await response.json();
          if (data.success) {
            alert('User deleted successfully!');
            loadUsers();
          } else {
            throw new Error(data.message || 'Failed to delete user');
          }
        } catch (error) {
          console.error('Delete error:', error);
          alert('Error: ' + error.message);
        }
      }    }    // Setup form event listeners
    if (userForm) {
      userForm.addEventListener('submit', handleFormSubmit);
    }
    if (accountForm) {
      accountForm.addEventListener('submit', handleAccountCreation);
    }
    if (profileForm) {
      profileForm.addEventListener('submit', handleProfileCreation);
    }
  </script>
</body>
</html>