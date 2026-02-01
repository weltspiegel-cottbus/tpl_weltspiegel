/**
 * Text Truncation - Overflow Detection & Height Sync
 * Detects overflow on .u-truncate elements and adds .is-overflowing class
 * Syncs content height with poster height for card layouts
 */

/** Minimum viewport width for desktop layout */
const DESKTOP_BREAKPOINT = 768;

/**
 * Check if an element's content overflows its container
 * @param {HTMLElement} el - Element to check
 * @returns {boolean} - True if content overflows
 */
function isOverflowing(el) {
    return el.scrollHeight > el.clientHeight || el.scrollWidth > el.clientWidth;
}

/**
 * Update overflow state on all truncate elements
 */
function updateTruncateStates() {
    const elements = document.querySelectorAll('.u-truncate');
    elements.forEach(el => {
        el.classList.toggle('is-overflowing', isOverflowing(el));
    });
}

/**
 * Card selectors for poster-to-content height sync
 */
const CARD_SELECTORS = [
    {
        card: '.cinetixx-card',
        poster: '.cinetixx-card__poster-img',
        content: '.cinetixx-card__content',
        description: '.cinetixx-card__description'
    },
    {
        card: '.content-card',
        poster: '.content-card__poster-img',
        content: '.content-card__content',
        description: '.content-card__description'
    }
];

/**
 * Round height UP to next line-height multiple
 * @param {number} height - Height in pixels
 * @param {number} lineHeight - Line height in pixels
 * @returns {number} - Rounded height in pixels
 */
function roundToLineHeight(height, lineHeight) {
    const lines = Math.ceil(height / lineHeight);
    return lines * lineHeight;
}

/**
 * Sync content height with poster height for a single card
 * @param {HTMLElement} card - Card element
 * @param {Object} selectors - Selectors for card elements
 */
function syncCardHeight(card, selectors) {
    const img = card.querySelector(selectors.poster);
    const content = card.querySelector(selectors.content);
    const contentWrapper = content?.querySelector('.u-truncate__content');
    const description = card.querySelector(selectors.description);

    if (!img || !content) return;

    // Only sync on desktop layout
    if (window.innerWidth < DESKTOP_BREAKPOINT) {
        content.style.removeProperty('--truncate-height');
        return;
    }

    // Set height if image is loaded
    if (img.complete && img.naturalHeight > 0) {
        const posterHeight = img.getBoundingClientRect().height;

        // Use content wrapper's offsetTop to get exact space used by title + tagline + gaps
        // This accounts for title, optional tagline, and their margins
        const headerAreaHeight = contentWrapper ? contentWrapper.offsetTop : 0;

        // Get description line-height for rounding
        const descLineHeight = description
            ? parseFloat(getComputedStyle(description).lineHeight) || 24
            : 24;

        // Calculate remaining space for description and round UP to next full line
        const remainingHeight = posterHeight - headerAreaHeight;
        const alignedRemaining = roundToLineHeight(remainingHeight, descLineHeight);

        // Total height = header area + aligned description space
        // Content may exceed poster height to ensure clean line cuts
        const totalHeight = headerAreaHeight + alignedRemaining;

        content.style.setProperty('--truncate-height', totalHeight + 'px');
    }
}

/**
 * Sync all card content heights with their poster heights
 */
function syncAllCardHeights() {
    CARD_SELECTORS.forEach(selectors => {
        document.querySelectorAll(selectors.card).forEach(cardEl => {
            syncCardHeight(cardEl, selectors);
        });
    });
}

/**
 * Setup image load listeners for height sync
 */
function setupImageLoadListeners() {
    CARD_SELECTORS.forEach(selectors => {
        document.querySelectorAll(selectors.card).forEach(cardEl => {
            const img = cardEl.querySelector(selectors.poster);
            if (img && !img.complete) {
                img.addEventListener('load', () => {
                    syncCardHeight(cardEl, selectors);
                    requestAnimationFrame(() => {
                        updateTruncateStates();
                    });
                }, { once: true });
            }
        });
    });
}

/**
 * Initialize all truncate functionality
 */
function initTruncate() {
    syncAllCardHeights();
    // Wait for reflow before checking overflow states
    requestAnimationFrame(() => {
        updateTruncateStates();
    });
}

// Run on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    setupImageLoadListeners();
    initTruncate();
});

// Re-check after all images load
window.addEventListener('load', initTruncate);

// Re-check on window resize (debounced)
let resizeTimeout;
window.addEventListener('resize', () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(initTruncate, 100);
});

// Export for potential external use
export { updateTruncateStates, isOverflowing, syncAllCardHeights };
