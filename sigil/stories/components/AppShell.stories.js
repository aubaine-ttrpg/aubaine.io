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

// Flagpack flags, inlined for the static story (the app renders these via ux_icon).
const FLAG = {
    fr: '<svg class="status-bar__flag" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 24"><g fill="none" fill-rule="evenodd" clip-rule="evenodd"><path fill="#f50100" d="M22 0h10v24H22z"/><path fill="#2e42a5" d="M0 0h12v24H0z"/><path fill="#f7fcff" d="M10 0h12v24H10z"/></g></svg>',
    gb: '<svg class="status-bar__flag" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 24"><g fill="none"><path fill="#2e42a5" fill-rule="evenodd" d="M0 0v24h32V0z" clip-rule="evenodd"/><mask id="sb-uk" width="32" height="24" x="0" y="0" maskUnits="userSpaceOnUse" style="mask-type:luminance"><path fill="#fff" fill-rule="evenodd" d="M0 0v24h32V0z" clip-rule="evenodd"/></mask><g mask="url(#sb-uk)"><path fill="#fff" d="m-3.563 22.285l7.042 2.979l28.68-22.026l3.715-4.426l-7.53-.995l-11.698 9.491l-9.416 6.396z"/><path fill="#f50100" d="M-2.6 24.372L.989 26.1L34.54-1.599h-5.037z"/><path fill="#fff" d="m35.563 22.285l-7.042 2.979L-.159 3.238l-3.715-4.426l7.53-.995l11.698 9.491l9.416 6.396z"/><path fill="#f50100" d="m35.323 23.783l-3.588 1.728l-14.286-11.86l-4.236-1.324l-17.445-13.5H.806l17.434 13.18l4.631 1.588z"/><mask id="sb-uk2" fill="#fff"><path fill-rule="evenodd" d="M19.778-2h-7.556V8H-1.972v8h14.194v10h7.556V16h14.25V8h-14.25z" clip-rule="evenodd"/></mask><path fill="#f50100" fill-rule="evenodd" d="M19.778-2h-7.556V8H-1.972v8h14.194v10h7.556V16h14.25V8h-14.25z" clip-rule="evenodd"/><path fill="#fff" d="M12.222-2v-2h-2v2zm7.556 0h2v-2h-2zM12.222 8v2h2V8zM-1.972 8V6h-2v2zm0 8h-2v2h2zm14.194 0h2v-2h-2zm0 10h-2v2h2zm7.556 0v2h2v-2zm0-10v-2h-2v2zm14.25 0v2h2v-2zm0-8h2V6h-2zm-14.25 0h-2v2h2zm-7.556-8h7.556v-4h-7.556zm2 8V-2h-4V8zm-16.194 2h14.194V6H-1.972zm2 6V8h-4v8zm12.194-2H-1.972v4h14.194zm2 12V16h-4v10zm5.556-2h-7.556v4h7.556zm-2-8v10h4V16zm16.25-2h-14.25v4h14.25zm-2-6v8h4V8zm-12.25 2h14.25V6h-14.25zm-2-12V8h4V-2z" mask="url(#sb-uk2)"/></g></g></svg>',
};

const LANG = `
  <details class="status-bar__lang">
    <summary class="status-bar__lang-summary">${FLAG.gb}<span class="status-bar__lang-name">English</span></summary>
    <nav class="status-bar__lang-menu" aria-label="Language">
      <a class="status-bar__lang-item" href="#" onclick="return false" hreflang="en" aria-current="true">${FLAG.gb}<span class="status-bar__lang-name">English</span></a>
      <a class="status-bar__lang-item" href="#" onclick="return false" hreflang="fr">${FLAG.fr}<span class="status-bar__lang-name">Français</span></a>
    </nav>
  </details>`;

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
        </div>
      </header>
      <aside class="rail">
        <button type="button" class="icon-btn rail__toggle" id="sb-sidebar"
                aria-controls="sidenav" aria-expanded="true" aria-label="Toggle sidebar">
          <svg viewBox="0 0 16 16" fill="none" aria-hidden="true">
            <rect x="2" y="3" width="12" height="10" rx="1" stroke="currentColor" stroke-width="1.2"/>
            <path d="M6 3v10" stroke="currentColor" stroke-width="1.2"/>
          </svg>
        </button>
        <span class="rail__sep"></span>
        ${rail}
      </aside>
      <nav class="sidenav" id="sidenav">
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
      <footer class="status-bar"><span class="status-bar__pulse"></span><span>Aubaine Catalyst</span>${LANG}</footer>
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

    // Sidebar collapse toggle: flip <html data-sidebar>, mirroring sidebar_controller.
    const sidebarBtn = wrap.querySelector('#sb-sidebar');
    sidebarBtn.addEventListener('click', () => {
        const collapsed = document.documentElement.dataset.sidebar === 'collapsed';
        document.documentElement.dataset.sidebar = collapsed ? 'expanded' : 'collapsed';
        sidebarBtn.setAttribute('aria-expanded', String(collapsed));
    });

    return wrap;
};
