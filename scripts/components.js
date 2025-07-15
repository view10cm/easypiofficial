// Load Navbar
fetch('header.php')
  .then(res => res.text())
  .then(html => {
    document.getElementById('navbar-container').innerHTML = html;
  });

// Load Sidebar and then highlight active link
fetch('sidebar.php')
  .then(res => res.text())
  .then(html => {
    document.getElementById('sidebar-container').innerHTML = html;

    // Highlight active sidebar link after sidebar is loaded
    const currentPage = window.location.pathname.split('/').pop().toLowerCase();
    const navLinks = document.querySelectorAll('.sidebar .nav-link');
    navLinks.forEach(link => {
      const linkHref = link.getAttribute('href').split('/').pop().toLowerCase();
      if (linkHref === currentPage) {
        link.classList.add('bg-white', 'fw-bold', 'text-dark');
      } else {
        link.classList.remove('bg-white', 'fw-bold', 'text-dark');
      }
    });
  });

// Load Chatbot
fetch('../includes/chatbot.php')
  .then(res => res.text())
  .then(html => {
    document.getElementById('chatbot-container').innerHTML = html;
  });




