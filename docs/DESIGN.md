# Weltspiegel Template - Design Reference

A quick reference for design decisions, colors, breakpoints, and patterns used in the template.

## Colors

All colors are defined as CSS custom properties in `_variables.css`.

| Variable               | Value              | Usage                                       |
| ----------------------- | ------------------ | ------------------------------------------- |
| `--color-bg-body`      | `#100501`          | Dark brown/black page background            |
| `--color-fg-body`      | `#ffffff`          | White text color                            |
| `--color-accent`       | `#f29400`          | Orange accent (headings, links, highlights) |
| `--color-accent-hover` | 80% accent + black | Darker orange for hover states              |

### Showbox Colors (Booking Grid)

| Variable                | Value                      |
| ------------------------ | --------------------------- |
| `--showbox-header-bg`   | `rgba(170, 119, 84, 0.4)`  |
| `--showbox-cell-bg`     | `rgba(112, 84, 69, 0.3)`   |
| `--showbox-cell-bg-alt` | `rgba(135, 108, 94, 0.25)` |
| `--showbox-border`      | `rgba(0, 0, 0, 0.3)`       |

### Format Badge Colors (2D/3D, language, duration, FSK)

Badges use a mix of the accent color and the official FSK age-rating colors (see
`_fsk-badge.css`). They are not CSS variables — colors are hardcoded per modifier class:

| Badge                                  | Style                                              |
| --------------------------------------- | --------------------------------------------------- |
| `.format-badge--3d`                    | Filled accent background, dark text (matches `.showbox-dimension-label--3d`) |
| `.format-badge--2d`                    | Dark filled background, accent text/border (matches `.showbox-dimension-label--2d`) |
| `.format-badge--lang` (language)       | Accent outline, accent text                         |
| `.format-badge--duration`              | Dark filled chip (`--color-bg-body`), white text/icon, no border — deliberately set apart from the other badges |
| `.fsk-badge--0/6/12/16/18`             | Official FSK colors (white/yellow/green/blue/red)   |
| `.fsk-badge--pending` ("FSK folgt")     | Grey                                                |

### Design Notes

- Dark cinema aesthetic with warm brown/orange tones
- Main content area has radial gradient overlay: `rgba(46, 30, 10, 0.9)` to `rgba(25, 16, 6, 0.9)`
- Paper texture background (`paper-pattern.jpg`) visible as frame around content
- Card/panel borders use semi-transparent orange: `#f2940080`

## Breakpoints

| Breakpoint | Width   | Layout Description                                   |
| ---------- | ------- | ----------------------------------------------------- |
| Mobile     | < 768px | Single column, stacked layout                        |
| Tablet     | 768px+  | Centered content, side-by-side cards, flipped titles |
| Desktop    | 1024px+ | Left-aligned with ticket image on left               |
| Wide       | 1280px+ | Centered with ticket, max-width container            |

### Key Breakpoint: 768px

This is the primary mobile/desktop breakpoint used throughout:

- Card/detail layouts switch from stacked to side-by-side (poster | content)
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

- `--content-width: 768px` - Optimal reading width for flow text
- Full container calculation includes: content + padding + flipped title space

**Fixed in `px`, not `rem` (deliberately):** `--content-width` and the ticket-reservation
terms in `.page-container`'s desktop `max-width`/`padding-left` (`192px` ticket space +
`16px` spacing, see `template.css`), plus the ticket image's own `left: 16px` offset, are
all pinned in `px`. Everything else in the layout (article padding, flipped-title space,
right-hand padding) intentionally still scales with the user's root font-size.

Reason: a user's root font-size preference (e.g. Firefox's "Nur Text zoomen" / "Zoom Text
Only") shrinks or grows every `rem` value simultaneously. The reading-width variable must
not shrink below its intended size just because of that setting. More importantly, the
ticket image itself is a *fixed-size* `192×217px` background asset — if the space reserved
for it (`padding-left`, the `max-width` ticket terms, and the image's own `left` offset)
were still `rem`-based, a smaller root font-size would shrink the reserved zone while the
image stayed the same size, and the ticket would visually overlap into the content area
(observed live at a client site with 12px root font-size instead of the usual 16px). A
*larger* root font-size has the mirror-image failure mode: the image's `left` offset would
grow past the (then-larger) reserved zone. Pinning both the reservation and the offset in
`px` keeps them mathematically consistent regardless of root font-size.

## Key Design Patterns

### Shared Listing Component (`.listing` / `.listing-card`)

Used by all list views: `movies`, `vorschauen` (category 8), `veranstaltungen` (category 9).
One shared component instead of per-view CSS — layout changes apply everywhere at once.

```
Mobile:     poster
            content (title + truncated description)
            meta (badges — optional)
            details (optional, e.g. genre)
            showtimes (optional)

Desktop:    poster | content
            meta / details / showtimes (full width, below)
```

- Grid-based, fixed poster width (`10.75rem` on desktop)
- Slots (`__meta`, `__details`, `__showtimes`, tagline via `utilities.truncate`) are only
  rendered when data exists — no empty gaps
- `.listing-card--no-poster` drops the poster column entirely (e.g. articles without an image)
- Rendered by: `com_weltspiegel/movies/default.php`, `com_content/category/blog.php`

### Shared Detail Component (`.detail` / `.detail__inner`)

Used by all single-item views: `movie`, `vorschau`, `veranstaltung` (not by generic articles —
those keep the plain `.article` layout).

```
Desktop:    [back-link]
            [FSK badge, top-right of .detail__inner]
            title
            poster (float left) | meta badges, details/tagline
            body (wraps around poster, flow-root contains the float)
            showtimes / trailer / gallery (clear: both, full width)
```

- `.detail__inner` uses `display: flow-root` so the border box always wraps the floated
  poster, even when the surrounding text is shorter than the poster image
- Poster width is a variable, `--detail-poster-width` (default `10.75rem` for movies).
  The article variants (`.detail--full`, `.detail--simple`) override it to `300px` — a
  deliberately larger, wrap-around banner image, not unified with the movie poster width
- Rendered by: `com_weltspiegel/movie/default.php`, `com_content/article/default.php`
  (the `isComponentManaged` / `useContentSingleLayout` branches only)

### Format Badges (`.format-badges`, `booking/formats.php`, `movie/fsk.php`, `movie/duration.php`)

A shared badge row used in both `.listing-card__meta` and `.detail__meta`:

- **FSK** (`movie/fsk.php`): parses `ALTERSFREIGABE`, renders a colored badge linked to
  `/service/fsk-und-jugendschutz#fsk-N`. Special cases: "FSK folgt" → grey "FSK ?" badge;
  "keine Angabe" → omitted entirely.
- **Format flags + languages** (`booking/formats.php`): aggregates **all** EVENT variants of
  a movie into two separate badge groups — dimension (2D/3D from `is3D`) and language
  (from `LANGUAGE`, e.g. "Englisch (OmU)"). This is additive to, and independent from, the
  `$getCategory` logic in `booking/showtimes.php` and `mod_current_events` (which serve a
  different purpose: a single per-show label / page sectioning — see inline comments there).
- **Duration** (`movie/duration.php`): clock icon + "N Min.", styled as a dark filled chip
  to stand apart from the outlined language badges. Always last in the row.

Order in both views: FSK → format flags/languages → duration.

### Back Link (`utilities/back-link.php`, `_back-link.js`)

"Zurück zur Übersicht" at the top of `.detail__inner`, on all three detail views (not on
generic articles). Progressive enhancement:

1. Renders as a real `<a href>` to the relevant overview (movies → `/programm`;
   vorschau/veranstaltung → their category route) — works without JS, handles direct access.
2. `_back-link.js` intercepts the click: if `document.referrer` is same-origin **and**
   browser history exists, it calls `history.back()` instead — restoring the visitor's exact
   scroll position on the list they came from.

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

Height sync currently targets only `.listing-card` (see `CARD_SELECTORS` in `_truncate.js`) —
detail views don't use the poster-height-sync variant of this pattern.

### Gallery Lightbox (`_gallery.css` / `_gallery.js`)

Native `<dialog>`-based lightbox for `{gallery ...}` placeholders (`com_weltspiegel.gallery.default`
layout). Aesthetic details:

- Frosted backdrop: `backdrop-filter: blur(12px)` + translucent dark tint (falls back to a
  plain dark overlay where `backdrop-filter` is unsupported)
- Open/close fade via `@starting-style` + `transition ... allow-discrete` (works with the
  native `display` toggle of `<dialog>`)
- Each image change re-triggers a subtle fade/scale-in animation (JS forces a reflow)
- Respects `prefers-reduced-motion`
- Optional teaser image (`{gallery ...|teaser=random}` or `|teaser=filename.jpg}`): rendered
  large above the grid; its duplicate grid thumbnail is hidden (`.gallery__item--teaser`) but
  stays in the DOM so lightbox indexing/navigation still includes it
- Optional `|altnames` flag: use cleaned-up filenames as alt text instead of the default
  "Article title – Bild N" (image editors set filenames deliberately, e.g. with hyphens kept
  as-is — only underscores are turned into spaces)

### Image Popout (`_image-popout.css` / `_image-popout.js`)

Generic, gallery-independent modal for showing a larger version of any image. Trigger via
`[data-image-popout="path/to/large.jpg"]` on an anchor wrapping a smaller `<img>`. Progressive
enhancement: without JS, the `href` on the same anchor still navigates to the large image
directly. Shares the lightbox's frosted/fade aesthetic. Useful for e.g. a small map preview
that opens a full-size map image — no third-party map service required.

### Cookie Consent v2 (`mod_cookie_consent`, category based)

The template override (`html/mod_cookie_consent/default.php`) renders one switch per
consent category, fully data-driven from a module subform parameter (`categories`) — the
module itself knows nothing about specific features (e.g. YouTube). `_cookie-consent.js`
exposes `isConsentGranted(category)` as the single source of truth; consumers like
`_youtube.js` only read/react to their own category via the `cookieConsentChanged` event.
No auto-open on first visit (the site only sets a strictly-necessary session cookie without
consent) — the banner opens via the drawer, a feature placeholder click, or any
`[data-cookie-settings]` link (e.g. from the privacy policy).

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
├── _article.css          # Single article view (generic, non-detail)
├── _featured.css         # Featured/homepage content
├── _listing.css          # Shared list view (movies, vorschauen, veranstaltungen)
├── _detail.css           # Shared detail view (movie, vorschau, veranstaltung)
├── _format-badges.css    # FSK/format/duration badge row (listing + detail)
├── _fsk-badge.css        # FSK badge colors (inline row + mod_current_events overlay)
├── _showtimes.css        # Booking/showtimes grid (still "showbox-*" class names)
├── _youtube.css          # YouTube embed styles
├── _gallery.css          # Image gallery + lightbox
├── _image-popout.css     # Generic single-image modal
├── _current-events.css   # Current events module
├── _cookie-consent.css   # Cookie consent banner (v2, category based)
└── _error.css            # Error page
```

### Naming Convention

- BEM-style naming: `.block__element--modifier`
- Shared components: `.listing-card__title`, `.detail__poster`, `.format-badge--duration`
- Utility classes prefixed with `u-`: `.u-truncate`, `.u-flipped-title`
- Showtimes grid keeps its historical `.showbox-*` naming (not renamed to avoid churn in
  `booking/showtimes.php` and `_showbox.js`)

### Build System

- Vite for bundling and minification
- Output: `template.min.css`, `template.min.js`
- Sourcemaps enabled for JS debugging

## JavaScript

### Entry Point

`media/js/template.js` imports:

- `_navigation.js` - Mobile menu toggle
- `_truncate.js` - Overflow detection and height sync
- `_showbox.js` - Showtimes viewport navigation (prev/next week)
- `_youtube.js` - Consent-aware YouTube embeds
- `_cookie-consent.js` - Consent manager + banner (category based)
- `_current-events.js` - Homepage current-events module
- `_gallery.js` - Gallery lightbox
- `_image-popout.js` - Generic image modal
- `_back-link.js` - Smart "back to overview" link (history vs. href fallback)

### Truncate Module Exports

```javascript
export { updateTruncateStates, isOverflowing, syncAllCardHeights };
```

### Event Listeners

- `DOMContentLoaded`: Initialize truncate, setup image load listeners
- `load`: Re-check truncate states after all images loaded
- `resize`: Debounced (100ms) re-calculation of heights

## Quick Reference

### Adding a New List or Detail View

Reuse the shared components instead of creating new CSS:

- List view: wrap items in `.listing` / `.listing-card`, use the `__meta`/`__details`/
  `__showtimes` slots only where you have data, render descriptions through the
  `utilities.truncate` layout with `'class' => 'listing-card__content'`.
- Detail view: wrap in `.detail` / `.detail__inner`, add `.detail__back` via
  `utilities.back-link`, use `.detail__poster` (float) + `.detail__body`.
- Only add a new CSS partial if the view needs something genuinely new — check
  `_listing.css` / `_detail.css` first.

### Changing Accent Color

Update `--color-accent` in `_variables.css`. Hover state auto-calculates via `color-mix()`.

### Adjusting Breakpoints

Main breakpoint is 768px. If changing:

1. Update media queries in CSS files
2. Update `DESKTOP_BREAKPOINT` constant in `_truncate.js`
