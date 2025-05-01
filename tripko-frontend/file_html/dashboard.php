<?php
require_once('../../tripko-backend/config/check_session.php');
checkAdminSession();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>TripKo Pangasinan Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
</head>
<body class="bg-white text-gray-900">
  <div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="flex flex-col justify-between bg-[#255D8A] w-64 p-6 text-white">
      <div>
        <div class="flex items-center gap-3 mb-10">
          <div class="rounded-full border border-white p-2">
            <i class="fas fa-user-circle text-3xl"></i>
          </div>
          <div class="font-semibold text-lg leading-tight">
            TripKo<br />Pangasinan
          </div>
        </div>
        <nav class="flex flex-col space-y-5 text-sm font-semibold">
          <a href="../file_html/dashboard.php" class="nav-link active">
            <i class="fas fa-home text-white text-lg"></i> Dashboard
          </a>
          <a href="../file_html/tourist_spot.php" class="nav-link">
            <i class="fas fa-umbrella-beach text-white text-lg"></i> Tourist Spots
          </a>
          <a href="../file_html/itineraries.html" class="nav-link">
            <i class="fas fa-map-marker-alt text-white text-lg"></i> Itineraries
          </a>
          <a href="../file_html/festival.html" class="nav-link">
            <i class="fas fa-carrot text-white text-lg"></i> Festivals
          </a>
          <a href="#" class="nav-link" onclick="toggleTransportDropdown(event)">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <i class="fas fa-bus text-white text-lg"></i>
                <span class="ml-2">Transportation</span>
              </div>
              <i class="fas fa-chevron-down text-sm transition-transform" id="transportDropdownIcon"></i>
            </div>
          </a>
          <div id="transportDropdown" class="hidden pl-8 mt-2 space-y-2">
            <a href="terminal-locations.html" class="nav-link">
              <i class="fas fa-map-marker-alt text-white text-lg"></i>
              <span class="ml-2">Terminals</span>
            </a>
            <a href="terminal-routes.html" class="nav-link">
              <i class="fas fa-route text-white text-lg"></i>  
              <span class="ml-2">Routes & Types</span>
            </a>
          </div>
          <a href="../file_html/fare.html" class="nav-link">
            <i class="fas fa-money-bill-wave text-white text-lg"></i> Fare
          </a>
          <a href="#" class="nav-link">
            <i class="fas fa-user-friends text-white text-lg"></i> Users
          </a>
          <a href="../file_html/reports.php" class="nav-link">
            <i class="fas fa-chart-bar text-white text-lg"></i> Reports
          </a>
          <a href="../../tripko-backend/config/confirm_logout.php" class="nav-link">
            <i class="fas fa-sign-out-alt text-white text-lg"></i> Sign Out
          </a>
        </nav>
      </div>
      <div class="flex items-center gap-3 font-semibold">
        <div class="rounded-full border border-white p-2">
          <i class="fas fa-user-circle text-3xl"></i>
        </div>
        <div>
          Administrator<br />
          <span class="text-sm">Administrator</span>
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
          <span>Dashboard Overview</span>
        </div>
        <div class="flex items-center gap-4">
          <select id="dashboardPeriod" class="rounded-full border border-gray-400 bg-[#F3F1E7] py-1.5 px-4 text-gray-600">
            <option value="7">Last 7 days</option>
            <option value="30" selected>Last 30 days</option>
            <option value="90">Last 3 months</option>
            <option value="365">Last year</option>
          </select>
          <button aria-label="Notifications" class="text-black text-xl focus:outline-none">
            <i class="fas fa-bell"></i>
          </button>
        </div>
      </header>

      <!-- Charts Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Tourism Trends -->
        <div class="bg-white p-6 rounded-lg shadow-md stats-card">
          <h3 class="text-lg font-semibold mb-4">Tourism Trends</h3>
          <div class="chart-container relative">
            <canvas id="tourismChart"></canvas>
            <div id="tourismChartError" class="error-message hidden"></div>
            <div id="tourismChartLoading" class="loading-overlay">
              <div class="text-gray-500">Loading...</div>
            </div>
          </div>
        </div>

        <!-- Transportation Analytics -->
        <div class="bg-white p-6 rounded-lg shadow-md stats-card">
          <h3 class="text-lg font-semibold mb-4">Transportation Distribution</h3>
          <div class="relative h-[300px] w-full">
            <canvas id="transportChart" style="z-index: 1;"></canvas>
            <div id="transportChartLoading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-90 z-10 hidden">
              <div class="text-gray-500">Loading...</div>
            </div>
            <div id="transportChartError" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-90 z-10 hidden">
              <div class="text-red-500"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
      <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md stats-card">
          <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-blue-100 rounded-full">
              <i class="fas fa-users text-[#255D8A] text-xl"></i>
            </div>
            <span class="text-sm font-medium text-gray-400">Total Visitors</span>
          </div>
          <h3 class="text-2xl font-bold text-gray-700" id="totalVisitors">Loading...</h3>
          <p class="text-sm text-gray-500 mt-2" id="visitorsTrend"></p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md stats-card">
          <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-green-100 rounded-full">
              <i class="fas fa-map-marker-alt text-green-600 text-xl"></i>
            </div>
            <span class="text-sm font-medium text-gray-400">Popular Destination</span>
          </div>
          <h3 class="text-2xl font-bold text-gray-700" id="popularSpot">Loading...</h3>
          <p class="text-sm text-gray-500 mt-2" id="spotVisits"></p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md stats-card">
          <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-yellow-100 rounded-full">
              <i class="fas fa-route text-yellow-600 text-xl"></i>
            </div>
            <span class="text-sm font-medium text-gray-400">Active Routes</span>
          </div>
          <h3 class="text-2xl font-bold text-gray-700" id="popularRoute">Loading...</h3>
          <p class="text-sm text-gray-500 mt-2" id="routeUsage"></p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md stats-card">
          <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-purple-100 rounded-full">
              <i class="fas fa-user-plus text-purple-600 text-xl"></i>
            </div>
            <span class="text-sm font-medium text-gray-400">New Users</span>
          </div>
          <h3 class="text-2xl font-bold text-gray-700" id="newUsers">Coming Soon</h3>
          <p class="text-sm text-gray-500 mt-2" id="usersTrend">User tracking not implemented</p>
        </div>
      </section>

      <!-- Recent Activities Section -->
      <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Spots -->
        <div class="bg-white p-6 rounded-lg shadow-md">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Recent Tourist Spots</h3>
            <a href="tourist_spot.php" class="text-[#255D8A] hover:underline text-sm">View All</a>
          </div>
          <div class="space-y-4" id="recentSpots">
            Loading...
          </div>
        </div>

        <!-- Recent Routes -->
        <div class="bg-white p-6 rounded-lg shadow-md">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Recent Routes</h3>
            <a href="terminal-routes.html" class="text-[#255D8A] hover:underline text-sm">View All</a>
          </div>
          <div class="space-y-4" id="recentRoutes">
            Loading...
          </div>
        </div>

        <!-- Recent Users (Hidden until user system is implemented) -->
        <div class="bg-white p-6 rounded-lg shadow-md hidden">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Recent Users</h3>
            <a href="#" class="text-[#255D8A] hover:underline text-sm">View All</a>
          </div>
          <div class="space-y-4" id="recentUsers">
            <div class="text-gray-500">User tracking coming soon</div>
          </div>
        </div>
      </section>
    </main>
  </div>

  <script>
    let charts = {
      tourism: null,
      transport: null
    };

    function destroyCharts() {
      Object.values(charts).forEach(chart => {
        if (chart) chart.destroy();
      });
      charts = {
        tourism: null,
        transport: null
      };
    }

    async function loadDashboardData() {
      try {
        // Show loading states
        document.querySelectorAll('.loading-overlay').forEach(el => el.classList.remove('hidden'));
        document.querySelectorAll('.error-message').forEach(el => el.classList.add('hidden'));

        const period = document.getElementById('dashboardPeriod').value;
        console.log('Loading dashboard data for period:', period);

        const response = await fetch(`../../tripko-backend/api/reports/get_reports.php?period=${period}`, {
          method: 'GET',
          credentials: 'include',
          headers: {
            'Accept': 'application/json'
          }
        });

        console.log('API Response status:', response.status);
        const responseText = await response.text();
        console.log('Raw API Response:', responseText);

        let data;
        try {
          data = JSON.parse(responseText);
          console.log('Parsed dashboard data:', data);
        } catch (e) {
          console.error('Failed to parse API response:', e);
          throw new Error('Invalid API response format');
        }

        if (!response.ok || !data.success) {
          if (response.status === 401 || responseText.includes('Not authenticated')) {
            window.location.href = 'SignUp_LogIn_Form.php';
            return;
          }
          throw new Error(data.message || `Error: ${response.status}`);
        }

        // Clear any previous error states
        document.querySelectorAll('.error-message').forEach(el => el.classList.add('hidden'));

        // Destroy existing charts before creating new ones
        destroyCharts();

        // Update tourism chart if we have data
        if (data.tourism?.monthlyData?.length > 0) {
          updateTourismChart(data.tourism.monthlyData);
        } else {
          document.getElementById('tourismChart').parentElement.innerHTML = 
            '<div class="flex items-center justify-center h-64 text-gray-500">No visitor data available</div>';
        }

        // Update transport chart if we have data
        if (data.transport?.typeDistribution?.length > 0) {
          console.log('Updating transport chart with:', data.transport.typeDistribution);
          updateTransportChart(data.transport.typeDistribution);
        } else {
          document.getElementById('transportChart').parentElement.innerHTML = 
            '<div class="flex items-center justify-center h-64 text-gray-500">No transport data available</div>';
        }

        // Update statistics
        document.getElementById('totalVisitors').textContent = 
          (data.tourism?.totalVisitors ?? 0).toLocaleString();
        
        const trend = data.tourism?.visitorTrend ?? 0;
        document.getElementById('visitorsTrend').innerHTML = trend !== 0 ? `
          <span class="${trend >= 0 ? 'text-green-600' : 'text-red-600'}">
            ${trend >= 0 ? '↑' : '↓'} 
            ${Math.abs(trend)}%
          </span> vs last period` : 'No trend data';

        document.getElementById('popularSpot').textContent = 
          data.tourism?.popularSpot || 'No data available';
        document.getElementById('spotVisits').textContent = 
          data.tourism?.popularSpotLocation || '';

        document.getElementById('popularRoute').textContent = 
          data.transport?.popularRoute?.name || 'No routes available';
        document.getElementById('routeUsage').textContent = 
          data.transport?.popularRoute?.fromTown && data.transport?.popularRoute?.toTown
            ? `${data.transport.popularRoute.fromTown} → ${data.transport.popularRoute.toTown}`
            : '';

        // Load recent activities
        await loadRecentActivities();

      } catch (error) {
        console.error('Dashboard error:', error);
        // Show error states
        document.querySelectorAll('.loading-overlay').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.error-message').forEach(el => {
          el.textContent = `Failed to load data: ${error.message}`;
          el.classList.remove('hidden');
        });

        // Update stats to show error state
        ['totalVisitors', 'popularSpot', 'popularRoute'].forEach(id => {
          document.getElementById(id).textContent = 'Error loading data';
        });
      } finally {
        // Always hide loading overlays
        document.querySelectorAll('.loading-overlay').forEach(el => el.classList.add('hidden'));
      }
    }

    async function loadRecentActivities() {
      try {
        // Load recent tourist spots
        const spotsResponse = await fetch('../../tripko-backend/api/tourist_spot/read.php');
        if (!spotsResponse.ok) throw new Error('Failed to load tourist spots');
        const spotsData = await spotsResponse.json();
        
        const recentSpotsHtml = spotsData.records
          ?.slice(0, 4)
          ?.map(spot => `
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-map-marker-alt text-[#255D8A]"></i>
              </div>
              <div>
                <h4 class="font-medium text-gray-700">${spot.name}</h4>
                <p class="text-sm text-gray-500">${spot.town_name}</p>
              </div>
            </div>
          `)
          .join('') || '<div class="text-gray-500">No spots available</div>';
        document.getElementById('recentSpots').innerHTML = recentSpotsHtml;

        // Load recent routes
        const routesResponse = await fetch('../../tripko-backend/api/terminal_routes/read.php');
        if (!routesResponse.ok) throw new Error('Failed to load routes');
        const routesData = await routesResponse.json();
        
        const recentRoutesHtml = routesData.records
          ?.slice(0, 4)
          ?.map(route => `
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-route text-[#255D8A]"></i>
              </div>
              <div>
                <h4 class="font-medium text-gray-700">${route.from_terminal} → ${route.to_terminal}</h4>
                <p class="text-sm text-gray-500">${route.transportation_types || 'No transport types'}</p>
              </div>
            </div>
          `)
          .join('') || '<div class="text-gray-500">No routes available</div>';
        document.getElementById('recentRoutes').innerHTML = recentRoutesHtml;

      } catch (error) {
        console.error('Error loading recent activities:', error);
        ['recentSpots', 'recentRoutes'].forEach(id => {
          document.getElementById(id).innerHTML = 
            '<div class="text-red-500">Error loading data</div>';
        });
      }
    }

    function toggleTransportDropdown(event) {
      event.preventDefault();
      const dropdown = document.getElementById('transportDropdown');
      const icon = document.getElementById('transportDropdownIcon');
      dropdown.classList.toggle('hidden');
      icon.style.transform = dropdown.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
    }

    function updateTourismChart(monthlyData) {
      const tourismCtx = document.getElementById('tourismChart').getContext('2d');
      charts.tourism = new Chart(tourismCtx, {
        type: 'line',
        data: {
          labels: monthlyData.map(d => {
            const [year, month] = d.month.split('-');
            return new Date(year, month - 1).toLocaleDateString('en-US', { 
              month: 'short', 
              year: '2-digit' 
            });
          }),
          datasets: [{
            label: 'Visitors',
            data: monthlyData.map(d => d.count),
            borderColor: '#255D8A',
            tension: 0.3,
            fill: true,
            backgroundColor: 'rgba(37, 93, 138, 0.1)'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              callbacks: {
                label: (context) => `Visitors: ${context.parsed.y.toLocaleString()}`
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: { 
                callback: value => value.toLocaleString(),
                font: { size: 11 }
              }
            },
            x: {
              ticks: { font: { size: 11 } }
            }
          }
        }
      });
    }

    function updateTransportChart(typeDistribution) {
      console.log('Updating transport chart with data:', typeDistribution);
      const transportCtx = document.getElementById('transportChart');
      const chartContainer = transportCtx.parentElement;
      
      // Clear any previous error messages and hide loading overlay
      const errorMessage = chartContainer.querySelector('.error-message');
      const loadingOverlay = chartContainer.querySelector('.loading-overlay');
      if (errorMessage) {
        errorMessage.classList.add('hidden');
      }
      if (loadingOverlay) {
        loadingOverlay.classList.add('hidden');
      }

      if (!typeDistribution || !Array.isArray(typeDistribution) || typeDistribution.length === 0) {
        console.error('Invalid transport distribution data');
        chartContainer.innerHTML = '<div class="flex items-center justify-center h-64 text-gray-500">No transport data available</div>';
        return;
      }

      // Sort data by count in descending order for better visualization
      const sortedData = [...typeDistribution].sort((a, b) => b.count - a.count);
      
      try {
        if (charts.transport) {
          charts.transport.destroy();
        }

        charts.transport = new Chart(transportCtx, {
          type: 'doughnut',
          data: {
            labels: sortedData.map(d => d.type),
            datasets: [{
              data: sortedData.map(d => d.count),
              backgroundColor: [
                '#255D8A',
                '#37799E',
                '#4A96B2',
                '#5DB3C6'
              ]
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
              legend: { 
                position: 'bottom',
                labels: { 
                  font: { size: 11 },
                  padding: 20,
                  generateLabels: (chart) => {
                    const data = chart.data;
                    const total = data.datasets[0].data.reduce((sum, value) => sum + value, 0);
                    return data.labels.map((label, i) => ({
                      text: `${label} (${data.datasets[0].data[i]} routes, ${Math.round((data.datasets[0].data[i]/total)*100)}%)`,
                      fillStyle: chart.data.datasets[0].backgroundColor[i],
                      index: i
                    }));
                  }
                }
              },
              tooltip: {
                enabled: true,
                callbacks: {
                  label: (context) => {
                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                    const percentage = total > 0 ? Math.round((context.raw/total)*100) : 0;
                    return `${context.label}: ${context.raw} routes (${percentage}%)`;
                  }
                }
              }
            },
            layout: {
              padding: {
                top: 20,
                bottom: 20,
                left: 20,
                right: 20
              }
            },
            animation: {
              animateRotate: true,
              animateScale: true,
              duration: 1000
            }
          }
        });
        console.log('Transport chart created successfully');
      } catch (error) {
        console.error('Error creating transport chart:', error);
        chartContainer.innerHTML = '<div class="flex items-center justify-center h-64 text-gray-500">Error loading transport data</div>';
      }
    }

    // Initialize dashboard
    document.addEventListener('DOMContentLoaded', () => {
      loadDashboardData();
      
      // Reload data when period changes
      document.getElementById('dashboardPeriod').addEventListener('change', () => {
        destroyCharts();
        loadDashboardData();
      });
    });
  </script>
</body>
</html>