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
