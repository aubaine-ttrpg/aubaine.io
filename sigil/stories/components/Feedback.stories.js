export default {
    title: 'Components/Feedback',
    parameters: { layout: 'padded' },
    tags: ['autodocs'],
};

export const Flashes = () => `
  <div style="max-width:560px;display:grid;gap:12px">
    <p class="flash flash--success" role="status">The book was saved.</p>
    <p class="flash flash--warning" role="status">Assets need rebuilding before export.</p>
    <p class="flash flash--error" role="alert">This slug is already taken.</p>
    <p class="flash" role="status">A neutral note.</p>
  </div>`;

export const EmptyState = () => `
  <div class="empty-state" style="max-width:560px">
    <p class="empty-state__title">No books yet</p>
    <p class="empty-state__hint">Create your first book, then add cover and skill-tree pages to it.</p>
    <div class="empty-state__actions">
      <a class="btn btn--primary" href="#">New book</a>
      <a class="btn btn--ghost" href="#">Import</a>
    </div>
  </div>`;
