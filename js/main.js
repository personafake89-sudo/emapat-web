// Scroll to top button
const scrollBtn = document.getElementById('scrollTop');
window.addEventListener('scroll', () => {
  scrollBtn.classList.toggle('visible', window.scrollY > 300);
});
scrollBtn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

// Animated counters
function animateCounters() {
  document.querySelectorAll('.stat-number').forEach(el => {
    const target = +el.dataset.target;
    const duration = 1800;
    const step = target / (duration / 16);
    let current = 0;
    const timer = setInterval(() => {
      current += step;
      if (current >= target) { current = target; clearInterval(timer); }
      el.textContent = Math.floor(current).toLocaleString('es-PE');
    }, 16);
  });
}

// Trigger counters when stats section enters view
const statsSection = document.querySelector('.stats-section');
if (statsSection) {
  const observer = new IntersectionObserver(entries => {
    if (entries[0].isIntersecting) { animateCounters(); observer.disconnect(); }
  }, { threshold: 0.3 });
  observer.observe(statsSection);
}

// Contact form
document.getElementById('contactForm')?.addEventListener('submit', e => {
  e.preventDefault();
  const btn = e.target.querySelector('[type=submit]');
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
  btn.disabled = true;
  setTimeout(() => {
    btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Mensaje enviado';
    btn.classList.replace('btn-primary', 'btn-success');
    e.target.reset();
    setTimeout(() => {
      btn.innerHTML = '<i class="bi bi-send me-2"></i>Enviar Mensaje';
      btn.classList.replace('btn-success', 'btn-primary');
      btn.disabled = false;
    }, 3000);
  }, 1500);
});

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const target = document.querySelector(a.getAttribute('href'));
    if (target) {
      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
});
