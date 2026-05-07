// BLUESTAR Website - JavaScript
document.addEventListener('DOMContentLoaded', function() {
  // === Navbar scroll effect ===
  const navbar = document.querySelector('.navbar');
  window.addEventListener('scroll', function() {
    if (window.scrollY > 20) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  });

  // === Mobile nav toggle ===
  const navToggle = document.querySelector('.nav-toggle');
  const navLinks = document.querySelector('.navbar-links');
  if (navToggle) {
    navToggle.addEventListener('click', function() {
      navLinks.classList.toggle('open');
    });
    // Close nav on link click
    document.querySelectorAll('.navbar-links a').forEach(function(link) {
      link.addEventListener('click', function() {
        navLinks.classList.remove('open');
      });
    });
  }

  // === FAQ accordion ===
  document.querySelectorAll('.faq-question').forEach(function(q) {
    q.addEventListener('click', function() {
      const item = this.parentElement;
      item.classList.toggle('open');
    });
  });

  // === Animated counter ===
  const counters = document.querySelectorAll('.counter');
  if (counters.length > 0) {
    const observer = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          const counter = entry.target;
          const target = parseInt(counter.getAttribute('data-target'), 10);
          const suffix = counter.getAttribute('data-suffix') || '';
          const isFloat = target !== parseInt(counter.getAttribute('data-target'));
          animateCounter(counter, target, suffix, isFloat);
          observer.unobserve(counter);
        }
      });
    }, { threshold: 0.5 });

    counters.forEach(function(c) { observer.observe(c); });
  }

  function animateCounter(el, target, suffix, isFloat) {
    const duration = 2000;
    const steps = 60;
    const increment = target / steps;
    let current = 0;
    let step = 0;

    function update() {
      step++;
      if (step >= steps) {
        el.textContent = (isFloat ? target.toFixed(1) : target.toLocaleString()) + suffix;
        return;
      }
      current = Math.min(current + increment, target);
      el.textContent = (isFloat ? current.toFixed(1) : Math.floor(current).toLocaleString()) + suffix;
      requestAnimationFrame(update);
    }
    update();
  }
});
