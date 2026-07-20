export default {
    title: 'Components/Content',
    parameters: { layout: 'padded' },
    tags: ['autodocs'],
};

export const SectionHead = () => `
  <div style="max-width:640px">
    <h3 class="section-head">Skill trees</h3>
    <p class="u-muted">A rotated gold diamond marks each section.</p>
  </div>`;

export const Prose = () => `
  <div class="prose">
    <h2>Resolution</h2>
    <p>Play is driven by <b>fiction first</b>. Mechanics exist to give that fiction weight.
    When the outcome is uncertain and interesting, roll <b>3d12</b> and keep the three dice.</p>
    <p>Characters grow through <em>meaningful, lasting progression</em>.</p>
    <blockquote>What survives the last adventure begins the next.</blockquote>
  </div>`;

export const Callouts = () => `
  <div style="max-width:640px;display:grid;gap:16px">
    <aside class="callout">
      <p class="callout__title">Upcast</p>
      <p class="callout__body">Spend one extra energy to widen the area by one range band.</p>
    </aside>
    <aside class="callout callout--gold">
      <p class="callout__title">Rule</p>
      <p class="callout__body">Three 12s is a critical success regardless of the target.</p>
    </aside>
  </div>`;
