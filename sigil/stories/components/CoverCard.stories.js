export default {
    title: 'Components/Cover card',
    parameters: { layout: 'padded' },
    tags: ['autodocs'],
};

// Foot: a single control row (type badge sized to match the buttons + icon
// actions). Glyphs stand in for the app's ux_icon() output (Catalyst renders
// mdi SVGs).
const body = (type) => `
  <div class="cover-card__body">
    <span class="badge cover-card__type">${type}</span>
    <div class="cover-card__actions">
      <a class="icon-btn" href="#" aria-label="Réglages" title="Réglages">⚙</a>
      <a class="icon-btn" href="#" aria-label="Télécharger le PDF" title="Télécharger le PDF">⭳</a>
      <button class="icon-btn icon-btn--danger" type="button" aria-label="Supprimer" title="Supprimer">✕</button>
    </div>
  </div>`;

// Page count sits top right on its scrim.
const topbar = (pages) => `
  <div class="cover-card__topbar">
    <span class="cover-card__pages">${pages}</span>
  </div>`;

// Title, a hairline rule, and the updated line sit over the bottom scrim.
const overlay = (title, updated, sizeClass = '') => `
  <div class="cover-card__overlay">
    <h2 class="cover-card__title ${sizeClass}">${title}</h2>
    <hr class="cover-card__rule">
    <p class="cover-card__meta">${updated}</p>
  </div>`;

// Self-contained art: a gradient stands in for a real cover image.
const artCard = (title, type, updated, pages, sizeClass) => `
  <article class="cover-card">
    <a class="cover-card__link" href="#" aria-label="${title}"></a>
    <div class="cover-card__cover">
      <div class="cover-card__art" style="background:linear-gradient(150deg,#3a1f5c,#12081e)"></div>
      ${topbar(pages)}
      ${overlay(title, updated, sizeClass)}
    </div>
    ${body(type)}
  </article>`;

const fallbackCard = (title, type, updated, pages, sizeClass) => `
  <article class="cover-card">
    <a class="cover-card__link" href="#" aria-label="${title}"></a>
    <div class="cover-card__cover">
      <div class="cover-card__placeholder" aria-hidden="true">
        <span class="cover-card__monogram">${title.charAt(0).toUpperCase()}</span>
      </div>
      ${topbar(pages)}
      ${overlay(title, updated, sizeClass)}
    </div>
    ${body(type)}
  </article>`;

export const WithArt = () => `<div style="max-width:260px">${artCard('Druide', 'Domaine', 'Modifié il y a 3 jours', '18 pages')}</div>`;

export const Fallback = () => `<div style="max-width:260px">${fallbackCard('Berserker', 'Archétype', 'Modifié il y a 1 mois', '24 pages')}</div>`;

export const LongTitle = () => `<div style="max-width:260px">${fallbackCard('Grand Nécromancien Éternel', 'Archétype', 'Modifié hier', '20 pages', 'cover-card__title--xxlong')}</div>`;

export const Grid = () => `
  <ul class="card-grid">
    <li>${artCard('Druide', 'Domaine', 'Modifié il y a 3 jours', '18 pages')}</li>
    <li>${fallbackCard('Berserker', 'Archétype', 'Modifié il y a 1 mois', '24 pages')}</li>
    <li>${fallbackCard('Technomancien', 'Archétype', 'Modifié hier', '12 pages')}</li>
    <li>${artCard('Zdalmazd', 'Personnage', 'Modifié il y a 2 semaines', '32 pages')}</li>
  </ul>`;
