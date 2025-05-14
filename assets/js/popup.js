document.getElementById('residentForm').addEventListener('submit', function(event) {
    const age = document.getElementById('age').value;
    if (isNaN(age) || age <= 0) {
        alert('Please enter a valid age.');
        event.preventDefault();
    }
});
