// app.js
(function() {
  // Mobile nav toggle
  var navToggle = document.getElementById('navToggle');
  var topNav = document.getElementById('topNav');
  if (navToggle && topNav) {
    navToggle.addEventListener('click', function() {
      topNav.classList.toggle('open');
    });
  }

  // Reveal-on-scroll
  var els = Array.prototype.slice.call(document.querySelectorAll('.reveal'));
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('show');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15 });
    els.forEach(function(el) { io.observe(el); });
  } else {
    // Fallback
    els.forEach(function(el) { el.classList.add('show'); });
  }
})();


