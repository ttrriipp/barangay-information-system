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
    const searchInput = document.getElementById('searchInput');
    const searchTerm = searchInput.value.trim();
    const tableBody = document.getElementById('residentTableBody');
    const pagination = document.querySelector('.pagination');
    
    // If search term is empty, reload the page to restore pagination
    if (searchTerm === '') {
        if (window.location.href.includes('?')) {
            window.location.href = window.location.href.split('?')[0];
        } else {
            window.location.reload();
        }
        return;
    }
    
    // Hide pagination while searching
    if (pagination) {
        pagination.style.display = 'none';
    }
    
    // Show loading indicator
    tableBody.innerHTML = '<tr><td colspan="10" style="text-align: center;">Searching...</td></tr>';
    
    // Fetch results from server
    fetch(`../controllers/search-residents.php?term=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Clear the table
                tableBody.innerHTML = '';
                
                if (data.residents.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="10" style="text-align: center;">No residents found</td></tr>';
                    return;
                }
                
                // Populate table with search results
                data.residents.forEach((resident, index) => {
                    const row = document.createElement('tr');
                    
                    // No column
                    const noCell = document.createElement('td');
                    noCell.textContent = index + 1;
                    row.appendChild(noCell);
                    
                    // Name column
                    const nameCell = document.createElement('td');
                    nameCell.textContent = resident.fullname;
                    row.appendChild(nameCell);
                    
                    // Address column
                    const addressCell = document.createElement('td');
                    addressCell.textContent = resident.address;
                    row.appendChild(addressCell);
                    
                    // Age column
                    const ageCell = document.createElement('td');
                    ageCell.textContent = resident.age;
                    row.appendChild(ageCell);
                    
                    // Sex column
                    const sexCell = document.createElement('td');
                    sexCell.textContent = resident.sex;
                    row.appendChild(sexCell);
                    
                    // Contact column
                    const contactCell = document.createElement('td');
                    contactCell.textContent = resident.contact;
                    row.appendChild(contactCell);
                    
                    // Civil Status column
                    const civilStatusCell = document.createElement('td');
                    civilStatusCell.textContent = resident.civil_status || 'N/A';
                    row.appendChild(civilStatusCell);
                    
                    // Occupation column
                    const occupationCell = document.createElement('td');
                    occupationCell.textContent = resident.occupation || 'N/A';
                    row.appendChild(occupationCell);
                    
                    // Voter column
                    const voterCell = document.createElement('td');
                    voterCell.textContent = resident.voter_status || 'N/A';
                    row.appendChild(voterCell);
                    
                    // Actions column
                    const actionsCell = document.createElement('td');
                    actionsCell.className = 'action-buttons';
                    
                    // View button
                    const viewBtn = document.createElement('button');
                    viewBtn.className = 'icon-button view-btn';
                    viewBtn.setAttribute('onclick', `openModal('partials/resident-view.php?id=${resident.id}')`);
                    viewBtn.title = 'View Resident Details';
                    viewBtn.innerHTML = '<i class="fas fa-eye"></i>';
                    actionsCell.appendChild(viewBtn);
                    
                    // Edit button
                    const editBtn = document.createElement('button');
                    editBtn.className = 'icon-button edit-btn';
                    editBtn.setAttribute('onclick', `editResident(${resident.id})`);
                    editBtn.title = 'Edit Resident';
                    editBtn.innerHTML = '<i class="fas fa-edit"></i>';
                    actionsCell.appendChild(editBtn);
                    
                    // Delete button
                    const deleteBtn = document.createElement('button');
                    deleteBtn.className = 'icon-button delete-btn';
                    deleteBtn.setAttribute('onclick', `deleteResident(${resident.id})`);
                    deleteBtn.title = 'Delete Resident';
                    deleteBtn.innerHTML = '<i class="fas fa-trash-alt"></i>';
                    actionsCell.appendChild(deleteBtn);
                    
                    row.appendChild(actionsCell);
                    tableBody.appendChild(row);
                });
            } else {
                tableBody.innerHTML = `<tr><td colspan="10" style="text-align: center;">Error: ${data.message}</td></tr>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            tableBody.innerHTML = '<tr><td colspan="10" style="text-align: center;">Error searching residents</td></tr>';
        });
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

// Add debounce search event listener
let searchTimeout;
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            searchResidents();
        }, 500); // 500ms delay
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