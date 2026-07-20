export default {
    title: 'Components/Buttons',
    parameters: { layout: 'padded' },
    tags: ['autodocs'],
};

const rowStyle = 'display:flex;gap:12px;flex-wrap:wrap;align-items:center;margin-bottom:16px';

export const Variants = () => `
  <div style="${rowStyle}">
    <button class="btn btn--primary">Primary</button>
    <button class="btn btn--gold">Gold</button>
    <button class="btn btn--ghost">Ghost</button>
    <button class="btn btn--danger">Delete</button>
    <button class="btn btn--primary" disabled>Disabled</button>
  </div>
  <div style="${rowStyle}">
    <a class="btn btn--ghost" href="#">Link button</a>
    <button class="btn btn--primary" style="width:220px">Block-ish</button>
  </div>`;

export const IconButtons = () => `
  <div style="${rowStyle}">
    <button class="icon-btn" aria-label="Move up">▲</button>
    <button class="icon-btn" aria-label="Move down">▼</button>
    <button class="icon-btn icon-btn--danger" aria-label="Delete">✕</button>
    <button class="icon-btn" aria-label="Disabled" disabled>▲</button>
  </div>`;
