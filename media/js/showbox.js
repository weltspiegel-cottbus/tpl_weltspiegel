/**
 * Showbox Navigation
 * Handles viewport navigation for showtime grids with multiple weeks
 */

class ShowboxNavigation {
    constructor(showboxElement) {
        this.showbox = showboxElement;
        this.viewports = this.showbox.querySelectorAll('.showbox-viewport');
        this.prevBtn = this.showbox.querySelector('[data-action="prev"]');
        this.nextBtn = this.showbox.querySelector('[data-action="next"]');
        this.viewportInfo = this.showbox.querySelector('.showbox-viewport-info');
        this.currentViewportIndex = 0;

        if (!this.prevBtn || !this.nextBtn || !this.viewportInfo) {
            return;
        }

        this.init();
    }

    init() {
        // Event listeners
        this.prevBtn.addEventListener('click', () => this.navigate('prev'));
        this.nextBtn.addEventListener('click', () => this.navigate('next'));

        // Initialize display
        this.updateDisplay();
    }

    navigate(direction) {
        if (direction === 'prev' && this.currentViewportIndex > 0) {
            this.currentViewportIndex--;
        } else if (direction === 'next' && this.currentViewportIndex < this.viewports.length - 1) {
            this.currentViewportIndex++;
        }
        this.updateDisplay();
    }

    updateDisplay() {
        // Hide all viewports
        this.viewports.forEach(viewport => viewport.classList.remove('active'));

        // Show current viewport
        if (this.viewports[this.currentViewportIndex]) {
            this.viewports[this.currentViewportIndex].classList.add('active');

            // Update viewport info label
            const viewportLabel = this.viewports[this.currentViewportIndex].dataset.label;
            this.viewportInfo.textContent = viewportLabel;
        }

        // Update button states
        this.prevBtn.disabled = this.currentViewportIndex === 0;
        this.nextBtn.disabled = this.currentViewportIndex === this.viewports.length - 1;
    }
}

// Initialize all showboxes on the page
function initShowboxes() {
    const showboxes = document.querySelectorAll('.showbox[data-has-navigation]');
    showboxes.forEach(showbox => new ShowboxNavigation(showbox));
}

// Initialize on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initShowboxes);
} else {
    initShowboxes();
}
