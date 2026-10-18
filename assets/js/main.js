/**
 * WebTech Lab Hub — Full-Stack Web Applications
 * Shared Client Scripts & Interactive Behaviors
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Mobile Menu Toggle
  const mobileToggle = document.getElementById('mobileNavToggle');
  const mobileMenu = document.getElementById('mobileNavMenu');

  if (mobileToggle && mobileMenu) {
    mobileToggle.addEventListener('click', () => {
      const isExpanded = mobileToggle.getAttribute('aria-expanded') === 'true';
      mobileToggle.setAttribute('aria-expanded', !isExpanded);
      mobileMenu.classList.toggle('is-open');
    });

    // Close on link click
    mobileMenu.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        mobileToggle.setAttribute('aria-expanded', 'false');
        mobileMenu.classList.remove('is-open');
      });
    });
  }

  // 2. Spec Accordion Toggles (Used across Experiment meta views)
  const specAccordions = document.querySelectorAll('.spec-accordion');
  specAccordions.forEach(accordion => {
    const header = accordion.querySelector('.spec-header');
    if (header) {
      header.addEventListener('click', () => {
        accordion.classList.toggle('is-open');
        const icon = header.querySelector('.accordion-chevron');
        if (icon) {
          icon.style.transform = accordion.classList.contains('is-open') ? 'rotate(180deg)' : 'rotate(0deg)';
        }
      });
    }
  });

  // 3. Smooth scrolling for hash anchors
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const targetId = this.getAttribute('href');
      if (targetId === '#') return;
      const targetEl = document.querySelector(targetId);
      if (targetEl) {
        e.preventDefault();
        targetEl.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }
    });
  });
});
