// Shared behavior for the LudLLM project pages.
// Palette persists across pages via the same localStorage key as the main site.

(function () {
  // ─── Palette switcher ───────────────────────────────
  const swatches = document.querySelectorAll('.swatch');
  swatches.forEach(s => {
    s.addEventListener('click', () => {
      const pal = s.dataset.pal;
      document.documentElement.setAttribute('data-theme', pal);
      swatches.forEach(x => x.classList.toggle('active', x === s));
      try { localStorage.setItem('sd-palette', pal); } catch (e) {}
    });
  });
  try {
    const saved = localStorage.getItem('sd-palette');
    if (saved) {
      document.documentElement.setAttribute('data-theme', saved);
      swatches.forEach(x => x.classList.toggle('active', x.dataset.pal === saved));
    }
  } catch (e) {}

  // ─── Local time in footer ───────────────────────────
  const timeEl = document.getElementById('local-time');
  if (timeEl) {
    const updateTime = () => {
      const t = new Date().toLocaleTimeString('en-US', {
        hour: '2-digit', minute: '2-digit',
        timeZone: 'America/Chicago', hour12: false
      });
      timeEl.textContent = t + ' local';
    };
    updateTime();
    setInterval(updateTime, 30000);
  }

  // ─── Tabs (per-novel page: full plot / story studio) ─
  // A .tabs group holds .tab buttons; panels are .tabpanel[data-tab].
  // The studio iframe loads lazily from data-src the first time its tab opens.
  function activateTab(group, name) {
    const tabs = group.querySelectorAll('.tab');
    tabs.forEach(t => t.classList.toggle('active', t.dataset.tab === name));
    document.querySelectorAll('.tabpanel').forEach(p => {
      const on = p.dataset.tab === name;
      p.classList.toggle('active', on);
      if (on) {
        const frame = p.querySelector('iframe[data-src]');
        if (frame && !frame.src) frame.src = frame.dataset.src;
      }
    });
    if (history.replaceState) history.replaceState(null, '', '#' + name);
  }

  document.querySelectorAll('.tabs').forEach(group => {
    group.querySelectorAll('.tab').forEach(tab => {
      tab.addEventListener('click', () => activateTab(group, tab.dataset.tab));
    });
    // honor a #plot / #studio hash on load, else default to the first tab
    const hash = (location.hash || '').replace('#', '');
    const wanted = group.querySelector('.tab[data-tab="' + hash + '"]') ? hash
      : group.querySelector('.tab').dataset.tab;
    activateTab(group, wanted);
  });
})();
