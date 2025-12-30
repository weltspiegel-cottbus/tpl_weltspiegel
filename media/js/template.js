/**
 * Weltspiegel Template
 * Main entry point for all JavaScript and CSS
 */

// Import CSS
import '../css/template.css';

// Mobile navigation toggle
const initMobileNav = () => {
    const nav = document.querySelector('.main-nav');
    const toggle = document.querySelector('.main-nav__toggle');
    const menu = document.querySelector('.main-nav__menu');

    if (!nav || !toggle || !menu) return;

    // Set initial state
    menu.setAttribute('aria-hidden', 'true');

    toggle.addEventListener('click', () => {
        const isExpanded = toggle.getAttribute('aria-expanded') === 'true';
        const newState = !isExpanded;

        // Update button state
        toggle.setAttribute('aria-expanded', newState);
        toggle.setAttribute('aria-label', newState ? 'Menü schließen' : 'Menü öffnen');

        // Update menu state
        menu.setAttribute('aria-hidden', !newState);

        // Update nav state
        nav.classList.toggle('main-nav--open', newState);
    });

    // Close menu on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && menu.getAttribute('aria-hidden') === 'false') {
            toggle.setAttribute('aria-expanded', 'false');
            toggle.setAttribute('aria-label', 'Menü öffnen');
            menu.setAttribute('aria-hidden', 'true');
            nav.classList.remove('main-nav--open');
        }
    });

    // Shrink logo on scroll
    let lastScrollY = window.scrollY;
    const handleScroll = () => {
        const scrollY = window.scrollY;

        if (scrollY > 50) {
            nav.classList.add('main-nav--scrolled');
        } else {
            nav.classList.remove('main-nav--scrolled');
        }

        lastScrollY = scrollY;
    };

    window.addEventListener('scroll', handleScroll, { passive: true });
};

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMobileNav);
} else {
    initMobileNav();
}

// Export for potential external use
export default {};
