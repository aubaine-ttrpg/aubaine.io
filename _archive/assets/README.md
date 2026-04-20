# Archive — `assets/` (Frontend)

v1 frontend source: Stimulus controllers, custom CSS, Webpack Encore entry points. Built by the (archived) `webpack.config.js` + `tailwind.config.js` + `postcss.config.js` at `_archive/` root.

## Entry point

- `app.js` — Webpack Encore entry. Imports `styles/app.css`, bootstraps Stimulus via `@hotwired/stimulus` + `@symfony/stimulus-bridge` for lazy controller loading.

## Stimulus controllers (`controllers/`)

Size is a rough LOC proxy for complexity.

| Controller | Role |
|---|---|
| **`skill_tree_builder_controller.js` (927 LOC)** | **The crown jewel.** Interactive grid editor: drag-drop skills onto cells, click-to-connect nodes (links), search skills against the DB via an admin JSON endpoint, inline-create skills without leaving the page, serialize everything for submit with CSRF. Pairs with `templates/dev/skill_tree_builder.html.twig` and `AdminSkillTreeController`'s JSON actions. |
| `action_fields_controller.js` | Toggles dependent form fields based on an action-type selector |
| `auto_dismiss_controller.js` | Dismisses flash alerts after a timeout (pairs with `templates/components/alerts.html.twig`) |
| `content_nav_controller.js` | Sticky/scroll-aware in-page nav (pairs with `templates/components/content_nav.html.twig`) |
| `csrf_protection_controller.js` | Adds CSRF tokens to async requests |
| `hello_controller.js` | Stimulus boilerplate, can be deleted |
| `icon_preview_controller.js` | Live SVG preview for the Tag icon upload field |
| `locale_toggle_controller.js` | Switch between en/fr on translatable forms |
| `multi_select_controller.js` | Enhances `<select multiple>` with chips + search |

### What to pull forward

- If v2 keeps a visual tree editor: `skill_tree_builder_controller.js` is the starting reference (but expect substantial rewrite — its data model is v1-shaped).
- If v2 keeps CSRF + flash + multi-select patterns: `csrf_protection`, `auto_dismiss`, `multi_select` are small and portable.
- `hello_controller.js` — drop.

## Styles (`styles/`)

- `app.css` — Tailwind base + `@apply` layer + imports of the sibling files
- `skill-grid.css` — grid cell sizing, hover states, drag indicators (pairs with `skill_tree_builder`)
- `skill-tree.css` — SVG link rendering, node shapes, connection-in-progress visuals
- `skill-plate.css` — the visual "plate" used to render a skill card (pairs with `templates/components/SkillPlate.html.twig`)

These are custom Tailwind — not plain stylesheets. They assume Tailwind's utility classes are available and use `@apply` heavily. If v2 uses a different CSS approach, adapt rather than copy.
