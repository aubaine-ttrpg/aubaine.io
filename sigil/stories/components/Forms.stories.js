export default {
    title: 'Components/Forms',
    parameters: { layout: 'padded' },
    tags: ['autodocs'],
};

export const Fields = () => `
  <form class="panel panel--narrow form">
    <div class="field">
      <label class="field__label" for="title">Title</label>
      <input class="field__input" id="title" type="text" value="Parangon">
    </div>
    <div class="field">
      <label class="field__label" for="type">Book type</label>
      <select class="field__select" id="type">
        <option>Archetype</option><option>Domain</option><option>Character</option>
      </select>
    </div>
    <div class="field">
      <label class="field__label" for="blurb">Back-cover blurb</label>
      <textarea class="field__textarea" id="blurb">Déchaînez votre imagination.</textarea>
      <p class="field__help">Shown on the verso cover.</p>
    </div>
    <div class="form-actions">
      <button class="btn btn--primary" type="submit">Save</button>
      <button class="btn btn--ghost" type="button">Cancel</button>
    </div>
  </form>`;

export const WithError = () => `
  <form class="panel panel--narrow form">
    <div class="field">
      <label class="field__label" for="slug">Slug</label>
      <input class="field__input" id="slug" type="text" value="Le Parangon" aria-describedby="slug-err">
      <p class="field__error" id="slug-err">Lowercase letters, digits and hyphens only.</p>
    </div>
  </form>`;
