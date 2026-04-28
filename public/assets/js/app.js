AOS.init({duration:650, once:true});

const mainNav = document.getElementById('mainNav');
window.addEventListener('scroll', () => {
  if (!mainNav) return;
  mainNav.classList.toggle('shrink', window.scrollY > 30);
});

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    const target = document.querySelector(this.getAttribute('href'));
    if (target) {
      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth' });
    }
  });
});

const lightbox = document.getElementById('lightbox');
const lightboxImg = document.getElementById('lightboxImg');
const closeBtn = document.querySelector('.lightbox-close');

document.querySelectorAll('[data-lightbox-src]').forEach(img => {
  img.addEventListener('click', () => {
    if (!lightbox || !lightboxImg) return;
    lightboxImg.src = img.getAttribute('data-lightbox-src');
    lightbox.style.display = 'flex';
  });
});

if (closeBtn && lightbox) {
  closeBtn.addEventListener('click', () => lightbox.style.display = 'none');
  lightbox.addEventListener('click', (e) => {
    if (e.target === lightbox) lightbox.style.display = 'none';
  });
}

// Admin Mobile Sidebar Toggle
const adminSidebarToggle = document.getElementById('adminSidebarToggle');
const adminSidebar = document.getElementById('adminSidebar');
const adminSidebarOverlay = document.createElement('div');
adminSidebarOverlay.className = 'admin-sidebar-overlay';
document.body.appendChild(adminSidebarOverlay);

function toggleAdminSidebar() {
  if (!adminSidebar || !adminSidebarOverlay) return;
  
  const isOpen = adminSidebar.classList.contains('show');
  
  if (isOpen) {
    adminSidebar.classList.remove('show');
    adminSidebarOverlay.classList.remove('show');
    document.body.style.overflow = '';
  } else {
    adminSidebar.classList.add('show');
    adminSidebarOverlay.classList.add('show');
    document.body.style.overflow = 'hidden';
  }
}

if (adminSidebarToggle) {
  adminSidebarToggle.addEventListener('click', toggleAdminSidebar);
}

if (adminSidebarOverlay) {
  adminSidebarOverlay.addEventListener('click', toggleAdminSidebar);
}

// Close sidebar when clicking on navigation links (mobile only)
if (adminSidebar) {
  const navLinks = adminSidebar.querySelectorAll('.nav-link');
  navLinks.forEach(link => {
    link.addEventListener('click', () => {
      if (window.innerWidth <= 991) {
        toggleAdminSidebar();
      }
    });
  });
}

// Handle window resize
window.addEventListener('resize', () => {
  if (window.innerWidth > 991 && adminSidebar) {
    adminSidebar.classList.remove('show');
    adminSidebarOverlay.classList.remove('show');
    document.body.style.overflow = '';
  }
});
