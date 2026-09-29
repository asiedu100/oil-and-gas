// Mobile menu
const menuToggle = document.querySelector('.menu-toggle');
const siteNav = document.querySelector('#site-nav');
if (menuToggle && siteNav) {
  menuToggle.addEventListener('click', () => {
    const open = siteNav.getAttribute('data-open') !== 'true';
    siteNav.setAttribute('data-open', String(open));
    menuToggle.setAttribute('aria-expanded', String(open));
    menuToggle.textContent = open ? 'Close' : 'Menu';
  });
}

// Team bio dialogs
document.querySelectorAll('[data-bio]').forEach((btn) => {
  btn.addEventListener('click', () => {
    const dialog = document.querySelector('#bio-' + btn.dataset.bio);
    if (dialog) dialog.showModal();
  });
});
document.querySelectorAll('dialog.bio').forEach((dialog) => {
  const close = dialog.querySelector('[data-close]');
  if (close) close.addEventListener('click', () => dialog.close());
  dialog.addEventListener('click', (e) => {
    if (e.target === dialog) dialog.close();
  });
});
