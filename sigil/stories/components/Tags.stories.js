export default {
    title: 'Components/Chips & Badges',
    parameters: { layout: 'padded' },
    tags: ['autodocs'],
};

export const Chips = () => `
  <div class="chipset">
    <button class="chip chip--on">All</button>
    <button class="chip"><span class="chip__swatch" style="background:#c0392b"></span>Fire</button>
    <button class="chip"><span class="chip__swatch" style="background:#1f7fc0"></span>Water</button>
    <button class="chip"><span class="chip__swatch" style="background:#6d4db0"></span>Thunder</button>
    <button class="chip"><span class="chip__swatch" style="background:#201460"></span>Void</button>
  </div>`;

export const Badges = () => `
  <div style="display:flex;gap:12px;flex-wrap:wrap">
    <span class="badge">Passive</span>
    <span class="badge badge--gold">10 XP</span>
    <span class="badge badge--accent">Tier II</span>
    <span class="badge badge--danger">Draft</span>
  </div>`;
