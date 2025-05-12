<script>
    // Get current page filename
    const currentPage = window.location.pathname.split('/').pop();
    
    // Find and highlight the active navigation item
    document.querySelectorAll('.sidebar nav ul li a').forEach(link => {
        const href = link.getAttribute('href');
        if (href === currentPage) {
            link.parentElement.classList.add('active');
        }
    });
</script>
</body>
</html>