/**
 * Text Truncation - Overflow Detection
 * Detects overflow on .u-truncate elements and adds .is-overflowing class
 */

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

// Run on DOM ready
document.addEventListener('DOMContentLoaded', updateTruncateStates);

// Re-check after images load (can affect layout dimensions)
window.addEventListener('load', updateTruncateStates);

// Re-check on window resize (debounced)
let resizeTimeout;
window.addEventListener('resize', () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(updateTruncateStates, 100);
});

// Export for potential external use
export { updateTruncateStates, isOverflowing };
