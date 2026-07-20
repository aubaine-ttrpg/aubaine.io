export default {
    title: 'Components/Chrome',
    parameters: { layout: 'fullscreen' },
};

export const Topbar = () => `
  <header class="topbar">
    <a class="topbar__brand" href="#">
      <span class="aubaine-logo" role="img" aria-label="Aubaine"></span>
      <span>
        <span class="topbar__brand-name">Aubaine</span>
        <span class="topbar__brand-sub"> · Catalyst</span>
      </span>
    </a>
    <nav class="topbar__nav" aria-label="Navigation">
      <a href="#" aria-current="true">Books</a>
      <a href="#">EN</a>
      <a href="#">FR</a>
      <button class="topbar__theme" aria-label="Toggle theme">◐</button>
    </nav>
  </header>`;

export const StatusBar = () => `
  <div class="status-bar">
    <span class="status-bar__pulse"></span>
    <span>Codex online</span>
    <span class="status-bar__sep">·</span>
    <span><b>7</b> archetypes</span>
    <span class="status-bar__sep">·</span>
    <span><b>142</b> skills indexed</span>
  </div>`;
