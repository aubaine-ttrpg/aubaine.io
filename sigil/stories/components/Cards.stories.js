export default {
    title: 'Components/Cards',
    parameters: { layout: 'padded' },
    tags: ['autodocs'],
};

export const Card = () => `
  <div class="card" style="max-width:420px">
    <h3 class="card__title">Parangon</h3>
    <p class="card__subtitle">Les protecteurs de la Weitzguard</p>
    <p class="card__meta">Archetype · v0.1 · 24 pages</p>
  </div>`;

export const HoverCard = () => `
  <div class="card card--hover" style="max-width:420px">
    <h3 class="card__title">Berserker</h3>
    <p class="card__subtitle">La fureur incarnée</p>
    <p class="card__meta">Archetype · v0.2</p>
  </div>`;

export const BookRow = () => `
  <div class="card" style="max-width:640px;display:flex;justify-content:space-between;align-items:center;gap:16px">
    <div>
      <div class="card__title">Druide</div>
      <div class="card__meta">Domaine · 18 pages</div>
    </div>
    <div class="card__actions">
      <a class="btn btn--ghost" href="#">Edit</a>
      <button class="icon-btn icon-btn--danger" aria-label="Delete">✕</button>
    </div>
  </div>`;

export const Panel = () => `
  <div class="panel panel--narrow">
    <h2>New book</h2>
    <p class="u-muted">Panels frame forms and settings.</p>
  </div>`;
