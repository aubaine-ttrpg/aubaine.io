export default {
    title: 'Components/Hero',
    parameters: { layout: 'fullscreen' },
};

export const Landing = () => `
  <section style="position:relative;min-height:520px;display:grid;place-items:center;background:var(--color-bg);overflow:hidden">
    <div class="ambient"><div class="grain"></div><div class="vignette"></div></div>
    <div class="hero" style="position:relative;z-index:1">
      <span class="aubaine-logo aubaine-logo--xl" role="img" aria-label="Aubaine"></span>
      <p class="hero__eyebrow">Aubaine Archives</p>
      <h1 class="hero__title">The Great Codex</h1>
      <p class="hero__subtitle">A fiction-first, progression-oriented tabletop RPG. Free to read, download, and print.</p>
      <div class="hero__actions">
        <a class="btn btn--gold" href="#">Enter the codex</a>
        <a class="btn btn--ghost" href="#">Browse archetypes</a>
      </div>
    </div>
  </section>`;
