const submitButton = document.querySelector('#submit_button');
const form = document.querySelector('form');

// Add a flag to track if username validation is complete
let usernameValidated = false;

// Form submission handler
form.addEventListener('submit', function(e) {
  // If username hasn't been validated yet, prevent submission and trigger validation
  if (!usernameValidated && usernameInput.value) {
    e.preventDefault();
    checkUsernameAvailability();
    
    // Show checking message
    const errorMessage = document.createElement('div');
    errorMessage.className = 'form-error';
    errorMessage.textContent = 'Checking username availability...';
    errorMessage.style.color = '#999';
    
    // Remove any existing error message
    const existingError = document.querySelector('.form-error');
    if (existingError) {
      existingError.remove();
    }
    
    // Add the message at the top of the form
    form.insertBefore(errorMessage, form.firstChild);
    return;
  }
  
  // Check for validation errors
  if (usernameStatus.style.color === 'red' || passwordStatus.style.color === 'red') {
    e.preventDefault();
    
    // Focus on the first field with an error
    if (usernameStatus.style.color === 'red') {
      usernameInput.focus();
    } else if (passwordStatus.style.color === 'red') {
      retypePasswordInput.focus();
    }
    
    // Show error message at the top of the form
    const errorMessage = document.createElement('div');
    errorMessage.className = 'form-error';
    errorMessage.textContent = 'Please fix the errors before submitting.';
    errorMessage.style.color = 'red';
    
    // Remove any existing error message
    const existingError = document.querySelector('.form-error');
    if (existingError) {
      existingError.remove();
    }
    
    // Add the error message at the top of the form
    form.insertBefore(errorMessage, form.firstChild);
  }
});

// Username availability check
const usernameInput = document.querySelector('#username');
const usernameStatus = document.createElement('div');
usernameStatus.className = 'username-status';
usernameInput.parentNode.insertBefore(usernameStatus, usernameInput.nextSibling);

// Password validation elements
const passwordInput = document.querySelector('#password');
const retypePasswordInput = document.querySelector('#retypePassword');
const passwordStatus = document.createElement('div');
passwordStatus.className = 'password-status';
retypePasswordInput.parentNode.insertBefore(passwordStatus, retypePasswordInput.nextSibling);

let typingTimer;
const doneTypingInterval = 500; // Wait for 500ms after user stops typing

usernameInput.addEventListener('input', function() {
  // Reset validation flag when username changes
  usernameValidated = false;
  
  usernameStatus.textContent = 'Checking...';
  usernameStatus.style.color = '#999';
  clearTimeout(typingTimer);
  
  if (usernameInput.value) {
    typingTimer = setTimeout(checkUsernameAvailability, doneTypingInterval);
  } else {
    usernameStatus.textContent = '';
  }
});

// Add event listener for password confirmation
retypePasswordInput.addEventListener('input', function() {
  validatePasswords();
});

passwordInput.addEventListener('input', function() {
  if (retypePasswordInput.value) {
    validatePasswords();
  }
});

function validatePasswords() {
  if (passwordInput.value !== retypePasswordInput.value) {
    passwordStatus.textContent = '✗ Passwords do not match';
    passwordStatus.style.color = 'red';
    submitButton.disabled = true;
  } else {
    passwordStatus.textContent = '✓ Passwords match';
    passwordStatus.style.color = 'green';
    submitButton.disabled = false;
  }
}

function checkUsernameAvailability() {
  const username = usernameInput.value;
  
  if (!username) {
    usernameStatus.textContent = '';
    return;
  }
  
  const formData = new FormData();
  formData.append('username', username);
  
  fetch('../controllers/check_username.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    usernameValidated = true;
    
    if (data.available) {
      usernameStatus.textContent = '✓ Username available';
      usernameStatus.style.color = 'green';
      submitButton.disabled = false;
      
      // Check if passwords match before enabling submit
      if (passwordInput.value && retypePasswordInput.value && 
          passwordInput.value !== retypePasswordInput.value) {
        submitButton.disabled = true;
      }
    } else {
      usernameStatus.textContent = '✗ Username already taken';
      usernameStatus.style.color = 'red';
      submitButton.disabled = true;
    }
  })
  .catch(error => {
    console.error('Error checking username:', error);
    usernameStatus.textContent = '';
    usernameValidated = false;
  });
}
