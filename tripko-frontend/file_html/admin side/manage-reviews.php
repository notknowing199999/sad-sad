<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_type'], ['admin', 'tourism_officer'])) {
    header('Location: ../login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reviews - TripKo Admin</title>
    <link rel="stylesheet" href="../../file_css/admin-reviews.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php include 'components/sidebar.php'; ?>

    <div class="content">
        <div class="header">
            <h1>Review Management</h1>
            <p>Manage and moderate user reviews</p>
        </div>

        <div class="filters">
            <div class="search">
                <i class='bx bx-search'></i>
                <input type="text" id="searchInput" placeholder="Search reviews...">
            </div>
            <div class="filter-options">
                <select id="spotFilter">
                    <option value="">All Tourist Spots</option>
                </select>
                <select id="ratingFilter">
                    <option value="">All Ratings</option>
                    <option value="5">5 Stars</option>
                    <option value="4">4 Stars</option>
                    <option value="3">3 Stars</option>
                    <option value="2">2 Stars</option>
                    <option value="1">1 Star</option>
                </select>
                <select id="statusFilter">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="hidden">Hidden</option>
                    <option value="flagged">Flagged</option>
                </select>
            </div>
        </div>

        <div class="reviews-container">
            <div class="loading">
                <i class='bx bx-loader-alt bx-spin'></i>
                <p>Loading reviews...</p>
            </div>
        </div>
    </div>

    <!-- Review Details Modal -->
    <div id="reviewModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Review Details</h2>
            <div class="review-details">
                <div class="section">
                    <h3>Tourist Spot</h3>
                    <p id="spotName"></p>
                </div>
                <div class="section">
                    <h3>User Information</h3>
                    <div class="user-info">
                        <img id="userAvatar" src="" alt="User Avatar">
                        <div>
                            <p id="username"></p>
                            <p id="userEmail"></p>
                        </div>
                    </div>
                </div>
                <div class="section">
                    <h3>Rating</h3>
                    <div id="rating" class="rating"></div>
                </div>
                <div class="section">
                    <h3>Review Content</h3>
                    <p id="reviewContent"></p>
                </div>
                <div class="section">
                    <h3>Review Status</h3>
                    <select id="reviewStatus">
                        <option value="active">Active</option>
                        <option value="hidden">Hidden</option>
                        <option value="flagged">Flagged</option>
                    </select>
                </div>
                <div class="section">
                    <h3>Timestamps</h3>
                    <p>Created: <span id="createdAt"></span></p>
                    <p>Updated: <span id="updatedAt"></span></p>
                </div>
            </div>
            <div class="modal-actions">
                <button id="saveStatus" class="primary-btn">Save Changes</button>
                <button id="deleteReview" class="danger-btn">Delete Review</button>
            </div>
        </div>
    </div>

    <script>
        const reviewsContainer = document.querySelector('.reviews-container');
        const searchInput = document.getElementById('searchInput');
        const spotFilter = document.getElementById('spotFilter');
        const ratingFilter = document.getElementById('ratingFilter');
        const statusFilter = document.getElementById('statusFilter');
        const reviewModal = document.getElementById('reviewModal');
        let currentReview = null;

        // Load tourist spots for filter
        async function loadSpots() {
            try {
                const response = await fetch('../../../tripko-backend/api/spots/list.php');
                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message);
                }

                const spots = data.data;
                spots.forEach(spot => {
                    const option = document.createElement('option');
                    option.value = spot.spot_id;
                    option.textContent = spot.name;
                    spotFilter.appendChild(option);
                });
            } catch (error) {
                console.error('Failed to load spots:', error);
            }
        }

        // Load reviews with filters
        async function loadReviews() {
            const search = searchInput.value;
            const spot = spotFilter.value;
            const rating = ratingFilter.value;
            const status = statusFilter.value;

            try {
                const params = new URLSearchParams({
                    search,
                    spot_id: spot,
                    rating,
                    status
                });

                const response = await fetch(
                    `../../../tripko-backend/api/reviews/admin/list.php?${params}`
                );
                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message);
                }

                reviewsContainer.innerHTML = data.data.length ? 
                    renderReviews(data.data) :
                    '<div class="no-results">' +
                        '<i class="bx bx-message-square-x"></i>' +
                        '<p>No reviews found matching your criteria</p>' +
                    '</div>';

            } catch (error) {
                reviewsContainer.innerHTML = `
                    <div class="error">
                        <i class='bx bx-error-circle'></i>
                        <p>${error.message || 'Failed to load reviews'}</p>
                    </div>`;
            }
        }

        function renderReviews(reviews) {
            return `
                <div class="reviews-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Tourist Spot</th>
                                <th>User</th>
                                <th>Rating</th>
                                <th>Review</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${reviews.map(review => `
                                <tr>
                                    <td>${review.spot_name}</td>
                                    <td>
                                        <div class="user-cell">
                                            <img src="${review.profile_image || '../../file_images/default-avatar.png'}" 
                                                 alt="${review.username}">
                                            <span>${review.username}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="rating">
                                            ${renderStars(review.rating)}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="review-preview">
                                            ${review.content.substring(0, 100)}${review.content.length > 100 ? '...' : ''}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-badge ${review.status}">
                                            ${review.status.charAt(0).toUpperCase() + review.status.slice(1)}
                                        </span>
                                    </td>
                                    <td>${formatDate(review.created_at)}</td>
                                    <td>
                                        <button onclick="viewReview(${JSON.stringify(review).replace(/"/g, '&quot;')})">
                                            <i class='bx bx-show'></i>
                                        </button>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>`;
        }

        function renderStars(rating) {
            let stars = '';
            for (let i = 1; i <= 5; i++) {
                stars += `<i class='bx ${i <= rating ? 'bxs-star' : 'bx-star'}'></i>`;
            }
            return stars;
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        function viewReview(review) {
            currentReview = review;
            document.getElementById('spotName').textContent = review.spot_name;
            document.getElementById('userAvatar').src = review.profile_image || '../../file_images/default-avatar.png';
            document.getElementById('username').textContent = review.username;
            document.getElementById('userEmail').textContent = review.email;
            document.getElementById('rating').innerHTML = renderStars(review.rating);
            document.getElementById('reviewContent').textContent = review.content;
            document.getElementById('reviewStatus').value = review.status;
            document.getElementById('createdAt').textContent = formatDate(review.created_at);
            document.getElementById('updatedAt').textContent = formatDate(review.updated_at);
            reviewModal.style.display = 'block';
        }

        // Event Listeners
        searchInput.addEventListener('input', debounce(loadReviews, 300));
        spotFilter.addEventListener('change', loadReviews);
        ratingFilter.addEventListener('change', loadReviews);
        statusFilter.addEventListener('change', loadReviews);

        document.getElementById('saveStatus').addEventListener('click', async () => {
            const newStatus = document.getElementById('reviewStatus').value;
            
            try {
                const response = await fetch('../../../tripko-backend/api/reviews/admin/update-status.php', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        review_id: currentReview.review_id,
                        status: newStatus
                    })
                });

                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message);
                }

                reviewModal.style.display = 'none';
                Swal.fire('Success!', 'Review status has been updated.', 'success');
                loadReviews();

            } catch (error) {
                Swal.fire('Error!', error.message, 'error');
            }
        });

        document.getElementById('deleteReview').addEventListener('click', async () => {
            try {
                const result = await Swal.fire({
                    title: 'Delete Review?',
                    text: 'This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'No, keep it'
                });

                if (result.isConfirmed) {
                    const response = await fetch(
                        `../../../tripko-backend/api/reviews/admin/delete.php?id=${currentReview.review_id}`,
                        { method: 'DELETE' }
                    );

                    const data = await response.json();

                    if (!data.success) {
                        throw new Error(data.message);
                    }

                    reviewModal.style.display = 'none';
                    Swal.fire('Deleted!', 'The review has been deleted.', 'success');
                    loadReviews();
                }
            } catch (error) {
                Swal.fire('Error!', error.message, 'error');
            }
        });

        // Modal controls
        document.querySelector('.close').addEventListener('click', () => {
            reviewModal.style.display = 'none';
        });

        window.addEventListener('click', (e) => {
            if (e.target === reviewModal) {
                reviewModal.style.display = 'none';
            }
        });

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Initial load
        loadSpots();
        loadReviews();
    </script>
</body>
</html>