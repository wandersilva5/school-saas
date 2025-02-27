document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    const main = document.querySelector('.main');
    const header = document.querySelector('.header');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            main.classList.toggle('full-width');
            header.classList.toggle('full-width');
        });
    }

    // Close sidebar on mobile when clicking outside
    document.addEventListener('click', function(event) {
        const isClickInsideSidebar = sidebar.contains(event.target);
        const isClickInsideToggle = sidebarToggle.contains(event.target);
        
        if (!isClickInsideSidebar && !isClickInsideToggle && window.innerWidth <= 768) {
            sidebar.classList.remove('active');
        }
    });

    // Handle active menu items
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.sidebar .nav-link');
    
    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('active');
            link.style.backgroundColor = 'rgba(255, 255, 255, 0.1)';
        }
    });
});