// Handle account form submission
document.addEventListener('DOMContentLoaded', function() {
    const accountForm = document.getElementById('accountForm');
    const accountModal = document.getElementById('accountModal');

    accountForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        try {
            const response = await fetch('../../tripko-backend/api/users/create.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.text();
            console.log('Server response:', result);
            
            let data;
            try {
                data = JSON.parse(result);
            } catch (e) {
                console.error('Failed to parse server response:', e);
                throw new Error('Server returned invalid response');
            }

            if (data.success) {
                alert('User account created successfully!');
                accountForm.reset();
                closeAccountModal();
                location.reload(); // Refresh to show new user
            } else {
                throw new Error(data.message || 'Failed to create user account');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error creating user account: ' + error.message);
        }
    });
});

// Modal functions
function openAccountModal() {
    document.getElementById('accountModal').classList.remove('hidden');
}

function closeAccountModal() {
    document.getElementById('accountModal').classList.add('hidden');
}

function openProfileModal() {
    document.getElementById('profileModal').classList.remove('hidden');
}

function closeProfileModal() {
    document.getElementById('profileModal').classList.add('hidden');
}

let currentUserId = null;

function openStatusModal(userId, currentStatus) {
    currentUserId = userId;
    const statusText = document.getElementById('currentStatusText');
    statusText.textContent = currentStatus || 'active';
    statusText.className = 'font-semibold ' + 
        (currentStatus === 'inactive' ? 'text-red-600' : 'text-green-600');
    document.getElementById('statusModal').classList.remove('hidden');
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
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
            alert(`User status updated to ${newStatus}`);
            closeStatusModal();
            location.reload(); // Refresh to show updated status
        } else {
            throw new Error(data.message || 'Failed to update status');
        }
    } catch (error) {
        console.error('Error updating status:', error);
        alert('Failed to update user status: ' + error.message);
    }
}
