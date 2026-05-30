// Carousel indicators dinámicos
(function () {
  const indicators = document.getElementById('hero-carousel-indicators');
  const items = document.querySelectorAll('#heroCarousel .carousel-item');
  if (!indicators || !items.length) return;
  items.forEach((_, i) => {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.setAttribute('data-bs-target', '#heroCarousel');
    btn.setAttribute('data-bs-slide-to', i);
    if (i === 0) { btn.classList.add('active'); btn.setAttribute('aria-current', 'true'); }
    indicators.appendChild(btn);
  });
})();

// Portfolio filters — igual al original
function activar_noticia(el) {
  setFilter(el);
  toggle('filter-app', 'block');
  toggle('filter-card', 'none');
  toggle('filter-web', 'none');
}
function activar_foto(el) {
  setFilter(el);
  toggle('filter-app', 'none');
  toggle('filter-card', 'block');
  toggle('filter-web', 'none');
}
function activar_video(el) {
  setFilter(el);
  toggle('filter-app', 'none');
  toggle('filter-card', 'none');
  toggle('filter-web', 'block');
}
function toggle(cls, display) {
  document.querySelectorAll('.' + cls).forEach(el => el.style.display = display);
}
function setFilter(el) {
  document.querySelectorAll('#portfolio-flters li').forEach(li => li.classList.remove('filter-active'));
  if (el) el.classList.add('filter-active');
}

// Contact form feedback
document.getElementById('contactForm')?.addEventListener('submit', function (e) {
  e.preventDefault();
  const btn = this.querySelector('[type=submit]');
  const orig = btn.innerHTML;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
  btn.disabled = true;
  setTimeout(() => {
    btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Mensaje enviado con éxito';
    btn.classList.replace('btn-primary', 'btn-success');
    this.reset();
    setTimeout(() => {
      btn.innerHTML = orig;
      btn.classList.replace('btn-success', 'btn-primary');
      btn.disabled = false;
    }, 3500);
  }, 1500);
});
