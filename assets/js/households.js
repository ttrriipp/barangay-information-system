// Get modal elements
const modal = document.getElementById('householdModal');
const modalBody = document.querySelector('.modal-body');
const closeButton = document.querySelector('.close-button');

// Get details modal elements
const detailsModal = document.getElementById('householdDetailsModal');
const detailsCloseButton = document.querySelector('.details-close-button');

// Get delete modal elements
const deleteModal = document.getElementById('deleteConfirmModal');
const closeDeleteModal = document.getElementById('closeDeleteModal');
const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
let householdToDelete = null;

// Search functionality
function searchHouseholds() {
    // Get the search input value
    const searchInput = document.getElementById('searchInput');
    const filter = searchInput.value.toUpperCase();
    const table = document.getElementById('householdTable');
    const rows = table.getElementsByTagName('tr');
    
    // Start from index 1 to skip the header row
    for (let i = 1; i < rows.length; i++) {
        let found = false;
        // Get all cells in the row except the last one (actions)
        const cells = rows[i].getElementsByTagName('td');
        
        // Search through each cell
        for (let j = 0; j < cells.length - 1; j++) {
            const cellText = cells[j].textContent || cells[j].innerText;
            if (cellText.toUpperCase().indexOf(filter) > -1) {
                found = true;
                break;
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
            const form = modalBody.querySelector('#householdForm');
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

// Function to view household details
function viewHousehold(id) {
    // Show the details modal
    detailsModal.style.display = 'block';
    
    // Clear previous content
    document.getElementById('household-name').textContent = 'Loading...';
    document.getElementById('members-list').innerHTML = '<tr><td colspan="3">Loading members...</td></tr>';
    
    // Fetch household details via AJAX
    fetch(`../controllers/get-household-details.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update household info
                document.getElementById('household-name').textContent = data.household.name + ' (' + data.household.address + ')';
                
                // Update members list
                const membersList = document.getElementById('members-list');
                membersList.innerHTML = '';
                
                if (data.members.length === 0) {
                    membersList.innerHTML = '<tr><td colspan="3" class="no-members">No members found in this household</td></tr>';
                } else {
                    data.members.forEach(member => {
                        const row = document.createElement('tr');
                        
                        const nameCell = document.createElement('td');
                        nameCell.textContent = member.fullname;
                        
                        const ageCell = document.createElement('td');
                        ageCell.textContent = member.age;
                        
                        const genderCell = document.createElement('td');
                        genderCell.textContent = member.sex;
                        
                        row.appendChild(nameCell);
                        row.appendChild(ageCell);
                        row.appendChild(genderCell);
                        
                        membersList.appendChild(row);
                    });
                }
            } else {
                document.getElementById('household-name').textContent = 'Error loading household details';
                document.getElementById('members-list').innerHTML = 
                    `<tr><td colspan="3" class="no-members">Error: ${data.message}</td></tr>`;
            }
        })
        .catch(error => {
            console.error('Error loading household details:', error);
            document.getElementById('household-name').textContent = 'Error loading household details';
            document.getElementById('members-list').innerHTML = 
                '<tr><td colspan="3" class="no-members">Error loading household details. Please try again.</td></tr>';
        });
}

// Function to close details modal
function closeDetailsModal() {
    detailsModal.style.display = 'none';
}

// Function to close modal
function closeModal() {
    modal.style.display = 'none';
    modalBody.innerHTML = '';  // Clear modal content
}

// Function to close delete confirmation modal
function closeDeleteConfirmModal() {
    deleteModal.style.display = 'none';
    householdToDelete = null;
}

// Add household function
function addHousehold() {
    openModal('partials/household-form.php');
}

// Edit household function
function editHousehold(id) {
    openModal('partials/household-form.php?id=' + id);
}

// Delete household function - opens confirmation modal
function deleteHousehold(id) {
    householdToDelete = id;
    deleteModal.style.display = 'block';
}

// Function to execute the actual deletion
function executeDelete() {
    if (!householdToDelete) return;
    
    // Send delete request to server
    fetch('../controllers/delete-household.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=' + householdToDelete
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
        alert('There was an error deleting the household.');
    });
}

// Close modal when clicking the close button
closeButton.addEventListener('click', closeModal);
detailsCloseButton.addEventListener('click', closeDetailsModal);

// Close modal when clicking outside of modal content
window.addEventListener('click', function(event) {
    if (event.target === modal) {
        closeModal();
    }
    if (event.target === detailsModal) {
        closeDetailsModal();
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
        if (detailsModal.style.display === 'block') {
            closeDetailsModal();
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