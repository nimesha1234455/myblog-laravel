<style>
  /* ---------- Splash loader ---------- */
  #page-loader {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: grid;
    place-items: center;
    background:
      radial-gradient(circle at 20% 20%, rgba(59,130,246,.25), transparent 45%),
      radial-gradient(circle at 80% 80%, rgba(147,51,234,.25), transparent 45%),
      #0a0e27;
    transition: opacity .5s ease, visibility .5s ease;
  }
  #page-loader.hidden { opacity: 0; visibility: hidden; }

  .loader-card {
    text-align: center;
    color: #fff;
    animation: loaderFadeUp .6s ease both;
  }

  .loader-logo {
    width: 72px;
    height: 72px;
    margin: 0 auto 18px;
    display: grid;
    place-items: center;
    border-radius: 20px;
    color: #fff;
    background: linear-gradient(135deg, #3b82f6, #9333ea);
    box-shadow: 0 10px 40px rgba(59,130,246,.45);
    animation: loaderPulse 1.8s ease-in-out infinite;
  }

  .loader-title { font-size: 1.9rem; font-weight: 800; letter-spacing: .5px; margin: 0; }
  .loader-sub   { margin: 6px 0 22px; font-size: .9rem; color: rgba(255,255,255,.6); }

  .loader-bar {
    width: 180px;
    height: 4px;
    margin: 0 auto;
    border-radius: 999px;
    background: rgba(255,255,255,.12);
    overflow: hidden;
  }
  .loader-bar span {
    display: block;
    width: 40%;
    height: 100%;
    border-radius: inherit;
    background: linear-gradient(90deg, #3b82f6, #9333ea);
    animation: loaderSlide 1.2s ease-in-out infinite;
  }

  @keyframes loaderSlide  { 0% { transform: translateX(-100%); } 100% { transform: translateX(350%); } }
  @keyframes loaderPulse  { 0%,100% { transform: scale(1); } 50% { transform: scale(1.07); } }
  @keyframes loaderFadeUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: none; } }

  /* ---------- Top progress bar ---------- */
  #top-progress {
    position: fixed;
    top: 0;
    left: 0;
    height: 3px;
    width: 0;
    z-index: 10000;
    pointer-events: none;
    background: linear-gradient(90deg, #3b82f6, #9333ea, #ec4899);
    box-shadow: 0 0 10px rgba(59,130,246,.8);
    transition: width .3s ease, opacity .4s ease;
  }

  @media (prefers-reduced-motion: reduce) {
    .loader-logo, .loader-bar span, .loader-card { animation: none; }
  }
</style>

<!-- Top progress bar -->
<div id="top-progress"></div>

<!-- Branded splash loader -->
<div id="page-loader" aria-live="polite" aria-busy="true">
  <div class="loader-card">
    <div class="loader-logo">
      <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor"
           stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 20h9"/>
        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"/>
      </svg>
    </div>
    <h1 class="loader-title">MyBlog</h1>
    <p class="loader-sub">Write. Share. Inspire.</p>
    <div class="loader-bar"><span></span></div>
  </div>
</div>

<script>
(() => {
  const loader = document.getElementById('page-loader');
  const bar    = document.getElementById('top-progress');
  let timer;

  /* ---------- Top progress bar ---------- */
  const startBar = () => {
    clearInterval(timer);
    bar.style.opacity = 1;
    let w = 10;
    bar.style.width = w + '%';
    timer = setInterval(() => {
      w += (90 - w) * 0.08;
      bar.style.width = w + '%';
    }, 200);
  };

  const finishBar = () => {
    clearInterval(timer);
    bar.style.width = '100%';
    setTimeout(() => { bar.style.opacity = 0; bar.style.width = 0; }, 400);
  };

  /* ---------- Splash: session ekat palaweni wathawt  withari ---------- */
  let seen = false;
  try { seen = !!sessionStorage.getItem('splashSeen'); } catch (e) {}

  if (seen) {
    loader.remove();
  } else {
    const minTime = 700;
    const t0 = performance.now();

    const hideSplash = () => {
      const wait = Math.max(0, minTime - (performance.now() - t0));
      setTimeout(() => {
        loader.classList.add('hidden');
        loader.setAttribute('aria-busy', 'false');
        setTimeout(() => loader.remove(), 600);
        try { sessionStorage.setItem('splashSeen', '1'); } catch (e) {}
      }, wait);
    };

    if (document.readyState === 'complete') hideSplash();
    else window.addEventListener('load', hideSplash);
  }

  /* ---------- Link click karaddi top bar ---------- */
  document.addEventListener('click', e => {
    const a = e.target.closest('a[href]');
    if (!a || a.target === '_blank' || a.hasAttribute('download') || e.ctrlKey || e.metaKey || e.shiftKey) return;
    const url = new URL(a.href, location.href);
    if (url.origin === location.origin && url.href !== location.href && !a.hash) startBar();
  });

  /* ---------- Form submit karddi ---------- */
  document.addEventListener('submit', startBar);

  /* ---------- Load unam / back button eken awam ---------- */
  window.addEventListener('load', finishBar);
  window.addEventListener('pageshow', e => { if (e.persisted) finishBar(); });
})();
</script>