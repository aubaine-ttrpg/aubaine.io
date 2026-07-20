import { el } from '../_helpers.js';

export default {
    title: 'Components/App shell',
    parameters: { layout: 'fullscreen' },
};

const ICON = {
    books: '<svg viewBox="0 0 16 16" fill="none"><path d="M3 2v12l5-2 5 2V2H3Z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/></svg>',
    trees: '<svg viewBox="0 0 16 16" fill="none"><path d="M8 2v12M8 6 4 3M8 8l4-3M8 11 5 9" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>',
    skills: '<svg viewBox="0 0 16 16" fill="none"><path d="m8 1 1.8 4.4L14 7l-4.2 1.6L8 13l-1.8-4.4L2 7l4.2-1.6L8 1Z" stroke="currentColor" stroke-width="1.1" stroke-linejoin="round"/></svg>',
    species: '<svg viewBox="0 0 16 16" fill="none"><circle cx="8" cy="6" r="3" stroke="currentColor" stroke-width="1.2"/><path d="M3 14c0-2.5 2.2-4 5-4s5 1.5 5 4" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>',
};

const SWITCH = `
  <span class="aubaine-switch" aria-hidden="true">
    <svg class="aubaine-switch__svg" viewBox="0 0 100 100">
      <defs><radialGradient id="sb-switch-void" cx="50%" cy="50%" r="50%">
        <stop offset="0%" stop-color="#0a0710"/><stop offset="55%" stop-color="#0a0710"/>
        <stop offset="78%" stop-color="#3a1f5c"/><stop offset="100%" stop-color="#12081e"/>
      </radialGradient></defs>
      <polygon class="aubaine-switch__frame" points="50,4 90,27 90,73 50,96 10,73 10,27"/>
      <polygon class="aubaine-switch__inner" points="50,13 82,31 82,69 50,87 18,69 18,31" fill="none"/>
      <path class="aubaine-switch__star" d="M50,24 L54.5,45.5 L76,50 L54.5,54.5 L50,76 L45.5,54.5 L24,50 L45.5,45.5 Z"/>
      <g class="aubaine-switch__void">
        <g transform="rotate(-24 50 50)">
          <ellipse cx="50" cy="50" rx="28" ry="9" fill="none" stroke="#b98fe0" stroke-width="2.2"/>
          <ellipse cx="50" cy="50" rx="21" ry="6.5" fill="none" stroke="#7b4fb0" stroke-width="1.4"/>
        </g>
        <circle cx="50" cy="50" r="14" fill="url(#sb-switch-void)"/>
        <circle cx="50" cy="50" r="14" fill="none" stroke="#c9a8f0" stroke-width="1.2"/>
        <circle cx="50" cy="50" r="9" fill="#07040d"/>
      </g>
    </svg>
  </span>`;

/** The Catalyst chrome: top bar (logo = theme switch), icon rail (sections),
 *  section sidebar (New + books grouped by type), stage, status ticker.
 *  Click the logo to morph it and swap the theme. */
export const Shell = () => {
    const rail = Object.entries(ICON).map(([key], i) =>
        `<a class="rail__btn ${i === 0 ? 'is-active' : ''}" href="#" title="${key}" onclick="return false">${ICON[key]}</a>`,
    ).join('');

    const wrap = el('div');
    wrap.style.height = '640px';
    wrap.innerHTML = `
    <div class="app" style="height:100%">
      <header class="topbar">
        <div class="topbar__brand">
          <button class="topbar__theme-logo" id="sb-theme" aria-label="Toggle theme">${SWITCH}</button>
          <a class="topbar__brand-link" href="#" onclick="return false">
            <span class="topbar__brand-name">Aubaine</span>
            <span class="topbar__brand-sub">Catalyst</span>
          </a>
        </div>
        <div class="topbar__nav">
          <nav class="topbar__crumbs" aria-label="Breadcrumb">
            <a class="topbar__crumb" href="#" onclick="return false">Books</a>
            <span class="topbar__crumb-sep">❯</span>
            <span class="topbar__crumb topbar__crumb--current">Parangon</span>
          </nav>
          <div class="topbar__actions"><a href="#" aria-current="true" onclick="return false">EN</a><a href="#" onclick="return false">FR</a></div>
        </div>
      </header>
      <aside class="rail">${rail}</aside>
      <nav class="sidenav">
        <div class="sidenav__group">
          <div class="sidenav__head"><span>Books</span><span>3</span></div>
          <a class="sidenav__item sidenav__item--new" href="#" onclick="return false"><span class="sidenav__plus">+</span><span class="sidenav__label">New book</span></a>
          <details class="sidenav__collapse" open>
            <summary class="sidenav__collapse-summary"><span class="sidenav__collapse-label">Archetype</span><span class="sidenav__tag">2</span></summary>
            <a class="sidenav__item is-active" href="#" onclick="return false"><span class="sidenav__dot"></span><span class="sidenav__label">Parangon</span></a>
            <a class="sidenav__item" href="#" onclick="return false"><span class="sidenav__dot"></span><span class="sidenav__label">Berserker</span></a>
          </details>
          <details class="sidenav__collapse" open>
            <summary class="sidenav__collapse-summary"><span class="sidenav__collapse-label">Domaine</span><span class="sidenav__tag">1</span></summary>
            <a class="sidenav__item" href="#" onclick="return false"><span class="sidenav__dot"></span><span class="sidenav__label">Druide</span></a>
          </details>
        </div>
      </nav>
      <main class="stage">
        <div class="stage__inner">
          <div class="page-head" style="display:flex;justify-content:space-between;align-items:flex-end">
            <h1>Parangon</h1>
            <button class="btn btn--gold">New book</button>
          </div>
          <div class="card" style="max-width:520px"><h3 class="card__title">Parangon</h3><p class="card__subtitle">Les protecteurs de la Weitzguard</p></div>
        </div>
      </main>
      <footer class="status-bar"><span class="status-bar__pulse"></span><span>Aubaine Catalyst</span></footer>
    </div>`;

    // Logo = theme switch: morph it and swap the theme.
    const btn = wrap.querySelector('#sb-theme');
    btn.addEventListener('click', () => {
        const root = document.documentElement;
        const next = root.dataset.theme === 'light' ? 'dark' : 'light';
        const badge = btn.querySelector('.aubaine-switch');
        root.classList.add('theme-changing');
        badge.classList.add('is-morphing');
        root.dataset.theme = next;
        setTimeout(() => { root.classList.remove('theme-changing'); badge.classList.remove('is-morphing'); }, 600);
    });

    return wrap;
};
