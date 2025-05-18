// Get modal elements
const modal = document.getElementById('residentModal');
const modalBody = document.querySelector('.modal-body');
const closeButton = document.querySelector('.close-button');

// Get delete modal elements
const deleteModal = document.getElementById('deleteConfirmModal');
const closeDeleteModal = document.getElementById('closeDeleteModal');
const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
let residentToDelete = null;

// Search functionality
function searchResidents() {
    // Get the search input value
    const searchInput = document.getElementById('searchInput');
    const filter = searchInput.value.toUpperCase();
    const table = document.getElementById('residentTable');
    const rows = table.getElementsByTagName('tr');
    
    // If we're searching, hide the pagination as we're filtering the table directly
    const pagination = document.querySelector('.pagination');
    if (pagination) {
        pagination.style.display = filter.length > 0 ? 'none' : 'flex';
    }
    
    // Start from index 1 to skip the header row
    for (let i = 1; i < rows.length; i++) {
        let found = false;
        // Get all cells in the row except the last one (actions)
        const cells = rows[i].getElementsByTagName('td');
        
        // Check for match in resident number (first column - index 0)
        const residentNumber = cells[0].textContent || cells[0].innerText;
        if (residentNumber.toUpperCase().indexOf(filter) > -1) {
            found = true;
        } else {
            // Search through remaining cells
            for (let j = 1; j < cells.length - 1; j++) {
                const cellText = cells[j].textContent || cells[j].innerText;
                if (cellText.toUpperCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }
        
        // Show or hide the row based on search match
        if (found) {
            rows[i].style.display = '';
        } else {
            rows[i].style.display = 'none';
        }
    }
}

// Function to open modal and load content
function openModal(url) {
    // Show the modal
    modal.style.display = 'block';
    
    // Fetch and load form content via AJAX
    fetch(url)
        .then(response => response.text())
        .then(data => {
            modalBody.innerHTML = data;
            
            // No AJAX for the form - let it submit normally
            const form = modalBody.querySelector('#residentForm');
            if (form) {
                // Remove the event handler and let the form submit normally
                // This will redirect to the form action URL
            }
        })
        .catch(error => {
            console.error('Error loading modal content:', error);
            modalBody.innerHTML = '<p>Error loading form. Please try again.</p>';
        });
}

// Function to close modal
function closeModal() {
    modal.style.display = 'none';
    modalBody.innerHTML = '';  // Clear modal content
}

// Function to close delete confirmation modal
function closeDeleteConfirmModal() {
    deleteModal.style.display = 'none';
    residentToDelete = null;
}

// Add residents function
function addResident() {
    openModal('partials/resident-form.php');
}

// Edit resident function
function editResident(id) {
    openModal('partials/resident-form.php?id=' + id);
}

// Delete resident function - opens confirmation modal
function deleteResident(id) {
    residentToDelete = id;
    deleteModal.style.display = 'block';
}

// Function to execute the actual deletion
function executeDelete() {
    if (!residentToDelete) return;
    
    // Send delete request to server
    fetch('../controllers/delete-resident.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=' + residentToDelete
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal and reload page to show updated list
            closeDeleteConfirmModal();
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('There was an error deleting the resident.');
    });
}

// Close modal when clicking the close button
closeButton.addEventListener('click', closeModal);

// Close modal when clicking outside of modal content
window.addEventListener('click', function(event) {
    if (event.target === modal) {
        closeModal();
    }
    if (event.target === deleteModal) {
        closeDeleteConfirmModal();
    }
});

// Close modal when Escape key is pressed
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        if (modal.style.display === 'block') {
            closeModal();
        }
        if (deleteModal.style.display === 'block') {
            closeDeleteConfirmModal();
        }
    }
});

// Add event listeners for delete confirmation
confirmDeleteBtn.addEventListener('click', executeDelete);
cancelDeleteBtn.addEventListener('click', closeDeleteConfirmModal);
closeDeleteModal.addEventListener('click', closeDeleteConfirmModal);