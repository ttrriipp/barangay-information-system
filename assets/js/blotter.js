// DOM elements
const searchInput = document.getElementById('searchInput');
const blotterTable = document.getElementById('blotterTable');
const blotterTableBody = document.getElementById('blotterTableBody');
const modal = document.getElementById('blotterModal');
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
        fetch(`controllers/delete-blotter.php?id=${blotterToDelete}`)
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
    const searchTerm = searchInput.value.toLowerCase();
    const rows = blotterTableBody.getElementsByTagName('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        let found = false;
        
        for (let j = 0; j < cells.length; j++) {
            const cellText = cells[j].textContent.toLowerCase();
            if (cellText.indexOf(searchTerm) > -1) {
                found = true;
                break;
            }
        }
        
        row.style.display = found ? '' : 'none';
    }
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