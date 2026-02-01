# Weltspiegel Template - Design Reference

A quick reference for design decisions, colors, breakpoints, and patterns used in the template.

## Colors

All colors are defined as CSS custom properties in `_variables.css`.

| Variable | Value | Usage |
|----------|-------|-------|
| `--color-bg-body` | `#100501` | Dark brown/black page background |
| `--color-fg-body` | `#ffffff` | White text color |
| `--color-accent` | `#f29400` | Orange accent (headings, links, highlights) |
| `--color-accent-hover` | 80% accent + black | Darker orange for hover states |

### Showbox Colors (Booking Grid)
| Variable | Value |
|----------|-------|
| `--showbox-header-bg` | `rgba(170, 119, 84, 0.4)` |
| `--showbox-cell-bg` | `rgba(112, 84, 69, 0.3)` |
| `--showbox-cell-bg-alt` | `rgba(135, 108, 94, 0.25)` |
| `--showbox-border` | `rgba(0, 0, 0, 0.3)` |

### Design Notes
- Dark cinema aesthetic with warm brown/orange tones
- Main content area has radial gradient overlay: `rgba(46, 30, 10, 0.9)` to `rgba(25, 16, 6, 0.9)`
- Paper texture background (`paper-pattern.jpg`) visible as frame around content
- Card borders use semi-transparent orange: `#f2940080`


## Breakpoints

| Breakpoint | Width | Layout Description |
|------------|-------|-------------------|
| Mobile | < 768px | Single column, stacked layout |
| Tablet | 768px+ | Centered content, side-by-side cards, flipped titles |
| Desktop | 1024px+ | Left-aligned with ticket image on left |
| Wide | 1280px+ | Centered with ticket, max-width container |

### Key Breakpoint: 768px
This is the primary mobile/desktop breakpoint used throughout:
- Card layouts switch from stacked to side-by-side (poster | content)
- Flipped titles become visible and rotate 90°
- Truncate height sync activates (JS constant: `DESKTOP_BREAKPOINT = 768`)
- Navigation switches to mobile hamburger menu


## Typography

### Fonts
- **Body**: `system-ui, -apple-system, sans-serif` (system font stack)
- **Headings**: `'Kameron', serif` (self-hosted woff2, weights: 400, 700)

### Font Sizes
- Body: `1rem` (16px default)
- Line height: `1.6` (body text)
- Headings: `font-weight: 500` (medium)
- Flipped titles: `clamp(2rem, 5vw, 2.5rem)`

### Self-Hosted Fonts
Located in `media/fonts/`:
- `kameron-v18-latin-regular.woff2`
- `kameron-v18-latin-700.woff2`


## Layout Patterns

### Page Container
```
Mobile:     [  content  ]
Tablet:     [  content  ] (centered, max-width)
Desktop:    [ticket][content    ] (left-aligned)
Wide:       [ticket][content    ] (centered)
```

The ticket image (`eintrittskarte.png`) appears at 1024px+ as a decorative element.

### Content Width
- `--content-width: 48rem` - Optimal reading width for flow text
- Full container calculation includes: content + padding + flipped title space

### Card Layouts (Cinetixx, Content Cards)
```
Mobile:     poster
            content
            details
            showbox

Desktop:    poster | content
            details
            showbox
```

Grid-based layout with fixed poster width (`10.75rem` on desktop).


## Key Design Patterns

### Flipped Titles (`.u-flipped-title`)
- Rotated 90° counterclockwise on desktop
- Positioned absolutely on the left side of content
- Uses `transform: rotate(270deg) translate(-100%, -100%)`
- Container needs `.u-flipped-title-container` for positioning context

### Truncation System (`.u-truncate`)
A reusable pattern for truncating long content with overflow detection.

**Structure:**
```html
<div class="u-truncate [card-specific-class]">
    <h2>Title</h2>
    <p class="u-truncate__tagline">Optional tagline</p>
    <div class="u-truncate__content">
        <div class="description">Content...</div>
    </div>
    <a class="u-truncate__more">…</a>
</div>
```

**How it works:**
1. JavaScript measures poster image height (`getBoundingClientRect()`)
2. Calculates header area (title + tagline) via `offsetTop` of `.u-truncate__content`
3. Remaining space is rounded UP to nearest line-height (`Math.ceil`)
4. Sets `--truncate-height` CSS variable on the container
5. `.is-overflowing` class added when content exceeds height
6. "Read more" link (…) shown only when overflowing

**CSS Variables:**
- `--truncate-height`: Dynamic, set by JS based on poster height
- `--truncate-content-height`: Fallback default (`15rem`)

### View Transitions
Enabled via `@view-transition { navigation: auto; }` for smooth page transitions.


## CSS Architecture

### File Structure
```
media/css/
├── template.css          # Main entry point (imports all partials)
├── _variables.css        # CSS custom properties
├── _fonts.css            # @font-face declarations
├── _utilities.css        # Reusable utility classes
├── _truncate.css         # Truncation pattern
├── _navigation.css       # Header & navigation
├── _footer.css           # Footer styles
├── _article.css          # Single article view
├── _featured.css         # Featured/homepage content
├── _cinetixx.css         # Cinetixx list view (movies)
├── _cinetixxitem.css     # Cinetixx detail view
├── _content-cards.css    # Blog/category cards (Vorschauen, Veranstaltungen)
├── _showbox.css          # Booking/showtimes grid
├── _youtube.css          # YouTube embed styles
├── _current-events.css   # Current events module
└── _cookie-consent.css   # Cookie consent banner
```

### Naming Convention
- BEM-style naming: `.block__element--modifier`
- Component-scoped: `.cinetixx-card__title`, `.content-card__description`
- Utility classes prefixed with `u-`: `.u-truncate`, `.u-flipped-title`

### Build System
- Vite for bundling and minification
- Output: `template.min.css`, `template.min.js`
- Sourcemaps enabled for JS debugging


## JavaScript

### Entry Point
`media/js/template.js` imports:
- `_truncate.js` - Overflow detection and height sync
- `_navigation.js` - Mobile menu toggle

### Truncate Module Exports
```javascript
export { updateTruncateStates, isOverflowing, syncAllCardHeights };
```

### Event Listeners
- `DOMContentLoaded`: Initialize truncate, setup image load listeners
- `load`: Re-check truncate states after all images loaded
- `resize`: Debounced (100ms) re-calculation of heights


## Quick Reference

### Adding a New Card Type
1. Add selectors to `CARD_SELECTORS` in `_truncate.js`
2. Create CSS file `_newcard.css` with BEM naming
3. Import in `template.css`
4. Use `.u-truncate` layout wrapper in template

### Changing Accent Color
Update `--color-accent` in `_variables.css`. Hover state auto-calculates via `color-mix()`.

### Adjusting Breakpoints
Main breakpoint is 768px. If changing:
1. Update media queries in CSS files
2. Update `DESKTOP_BREAKPOINT` constant in `_truncate.js`
