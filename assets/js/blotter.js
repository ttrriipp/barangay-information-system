// DOM elements
const searchInput = document.getElementById('searchInput');
const blotterTable = document.getElementById('blotterTable');
const blotterTableBody = document.getElementById('blotterTableBody');
const modal = document.getElementById('blotterModal');

// Add debounce search event listener
let searchTimeout;
if (searchInput) {
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            searchBlotters();
        }, 500); // 500ms delay
    });
}
const modalBody = modal.querySelector('.modal-body');
const closeButton = modal.querySelector('.close-button');
const deleteModal = document.getElementById('deleteConfirmModal');
const closeDeleteModal = document.getElementById('closeDeleteModal');
const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');

// Keep track of blotter ID for deletion
let blotterToDelete = null;

// Open modal with content from URL
function openModal(url) {
    // Show loading state
    modalBody.innerHTML = '<div class="loading-spinner"></div>';
    modal.style.display = 'block';
    
    // Fetch content
    fetch(url)
        .then(response => response.text())
        .then(data => {
            modalBody.innerHTML = data;
            
            // Add event listeners for form submission
            const addForm = document.getElementById('blotterAddForm');
            if (addForm) {
                addForm.addEventListener('submit', submitBlotterForm);
            }
            
            const editForm = document.getElementById('blotterEditForm');
            if (editForm) {
                editForm.addEventListener('submit', submitBlotterForm);
            }
        })
        .catch(error => {
            modalBody.innerHTML = `<p class="error-message">Error loading content: ${error.message}</p>`;
        });
}

// Handle form submission for adding blotter records
function submitBlotterForm(event) {
    event.preventDefault();
    
    const form = event.target;
    const formId = form.id;
    
    // Log form data
    console.log('Submitting form with ID:', formId);
    console.log('Action URL:', form.action);
    
    // For blotter forms, submit directly
    form.submit();
}

// Add new blotter record
function addBlotter() {
    openModal('partials/blotter-add-modal.php');
}

// Edit blotter record
function editBlotter(id) {
    openModal(`partials/blotter-edit.php?id=${id}`);
}

// Resolve blotter
function resolveBlotter(id) {
    // We'll just open the edit modal which has resolution options
    openModal(`partials/blotter-edit.php?id=${id}`);
}

// Delete blotter record
function deleteBlotter(id) {
    blotterToDelete = id;
    deleteModal.style.display = 'block';
}

// Confirm deletion
function confirmDelete() {
    if (blotterToDelete) {
        // Use AJAX to delete the record instead of page navigation
        fetch(`../controllers/delete-blotter.php?id=${blotterToDelete}`)
            .then(response => {
                if (response.ok) {
                    // Close the modal and refresh the page
                    deleteModal.style.display = 'none';
                    window.location.reload();
                } else {
                    throw new Error('Failed to delete blotter record');
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
                deleteModal.style.display = 'none';
            });
    }
}

// Search functionality
function searchBlotters() {
    const searchTerm = searchInput.value.trim();
    const tableBody = blotterTableBody;
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
    tableBody.innerHTML = '<tr><td colspan="8" style="text-align: center;">Searching...</td></tr>';
    
    // Fetch results from server
    fetch(`../controllers/search-blotters.php?term=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Clear the table
                tableBody.innerHTML = '';
                
                if (data.blotters.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="8" style="text-align: center;">No blotter records found</td></tr>';
                    return;
                }
                
                // Populate table with search results
                data.blotters.forEach((blotter, index) => {
                    const row = document.createElement('tr');
                    
                    // No column
                    const noCell = document.createElement('td');
                    noCell.textContent = index + 1;
                    row.appendChild(noCell);
                    
                    // Blotter ID column
                    const idCell = document.createElement('td');
                    idCell.textContent = blotter.blotter_id;
                    row.appendChild(idCell);
                    
                    // Incident Type column
                    const typeCell = document.createElement('td');
                    typeCell.textContent = blotter.incident_type;
                    row.appendChild(typeCell);
                    
                    // Complainant column
                    const complainantCell = document.createElement('td');
                    complainantCell.textContent = blotter.complainant_name;
                    row.appendChild(complainantCell);
                    
                    // Respondent column
                    const respondentCell = document.createElement('td');
                    respondentCell.textContent = blotter.respondent_name;
                    row.appendChild(respondentCell);
                    
                    // Incident Date column
                    const dateCell = document.createElement('td');
                    dateCell.textContent = blotter.incident_date_formatted;
                    row.appendChild(dateCell);
                    
                    // Status column
                    const statusCell = document.createElement('td');
                    const statusSpan = document.createElement('span');
                    statusSpan.className = `status-badge status-${blotter.status.toLowerCase()}`;
                    statusSpan.textContent = blotter.status;
                    statusCell.appendChild(statusSpan);
                    row.appendChild(statusCell);
                    
                    // Actions column
                    const actionsCell = document.createElement('td');
                    actionsCell.className = 'action-buttons';
                    
                    // View button
                    const viewBtn = document.createElement('button');
                    viewBtn.className = 'icon-button view-btn';
                    viewBtn.setAttribute('onclick', `openModal('partials/blotter-view.php?id=${blotter.id}')`);
                    viewBtn.title = 'View Blotter Details';
                    viewBtn.innerHTML = '<i class="fas fa-eye"></i>';
                    actionsCell.appendChild(viewBtn);
                    
                    // Edit button
                    const editBtn = document.createElement('button');
                    editBtn.className = 'icon-button edit-btn';
                    editBtn.setAttribute('onclick', `editBlotter(${blotter.id})`);
                    editBtn.title = 'Edit Blotter';
                    editBtn.innerHTML = '<i class="fas fa-edit"></i>';
                    actionsCell.appendChild(editBtn);
                    
                    // Delete button
                    const deleteBtn = document.createElement('button');
                    deleteBtn.className = 'icon-button delete-btn';
                    deleteBtn.setAttribute('onclick', `deleteBlotter(${blotter.id})`);
                    deleteBtn.title = 'Delete Blotter';
                    deleteBtn.innerHTML = '<i class="fas fa-trash-alt"></i>';
                    actionsCell.appendChild(deleteBtn);
                    
                    row.appendChild(actionsCell);
                    tableBody.appendChild(row);
                });
            } else {
                tableBody.innerHTML = `<tr><td colspan="8" style="text-align: center;">Error: ${data.message}</td></tr>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            tableBody.innerHTML = '<tr><td colspan="8" style="text-align: center;">Error searching blotter records</td></tr>';
        });
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Close modal when clicking the X button
    closeButton.addEventListener('click', function() {
        modal.style.display = 'none';
    });
    
    // Close modal when clicking outside of it
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
        if (event.target === deleteModal) {
            deleteModal.style.display = 'none';
        }
    });
    
    // Listen for messages from iframe content
    window.addEventListener('message', function(event) {
        if (event.data === 'closeBlotterModal') {
            modal.style.display = 'none';
        }
    });
    
    // Close delete modal
    closeDeleteModal.addEventListener('click', function() {
        deleteModal.style.display = 'none';
    });
    
    // Confirm delete button
    confirmDeleteBtn.addEventListener('click', confirmDelete);
    
    // Cancel delete button
    cancelDeleteBtn.addEventListener('click', function() {
        deleteModal.style.display = 'none';
    });
    
    // Check for success/error messages from form submission
    if (document.querySelector('.alert')) {
        setTimeout(function() {
            document.querySelector('.alert').style.display = 'none';
        }, 5000);
    }
}); 