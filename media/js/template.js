/**
 * Weltspiegel Template
 * Main entry point for all JavaScript and CSS
 */

// Import CSS
import '../css/template.css';

// Import modules
import './showbox.js';

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

        // Prevent body scroll when menu is open
        document.body.classList.toggle('nav-open', newState);
    });

    // Close menu on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && menu.getAttribute('aria-hidden') === 'false') {
            toggle.setAttribute('aria-expanded', 'false');
            toggle.setAttribute('aria-label', 'Menü öffnen');
            menu.setAttribute('aria-hidden', 'true');
            nav.classList.remove('main-nav--open');
            document.body.classList.remove('nav-open');
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

// Desktop navigation dropdowns
const initDesktopNav = () => {
    // Handle both types of toggle buttons: .main-nav__desktop-toggle and .main-nav__desktop-heading-toggle
    const toggleButtons = document.querySelectorAll('.main-nav__desktop-toggle, .main-nav__desktop-heading-toggle');

    toggleButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.stopPropagation();
            const item = button.closest('.main-nav__desktop-item');
            const isOpen = item.classList.contains('is-open');

            // Close all other dropdowns
            document.querySelectorAll('.main-nav__desktop-item.is-open').forEach(openItem => {
                if (openItem !== item) {
                    openItem.classList.remove('is-open');
                    const toggle = openItem.querySelector('.main-nav__desktop-toggle, .main-nav__desktop-heading-toggle');
                    if (toggle) toggle.setAttribute('aria-expanded', 'false');
                }
            });

            // Toggle current dropdown
            item.classList.toggle('is-open');
            button.setAttribute('aria-expanded', !isOpen);
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.main-nav__desktop-item')) {
            document.querySelectorAll('.main-nav__desktop-item.is-open').forEach(item => {
                item.classList.remove('is-open');
                const toggle = item.querySelector('.main-nav__desktop-toggle, .main-nav__desktop-heading-toggle');
                if (toggle) toggle.setAttribute('aria-expanded', 'false');
            });
        }
    });

    // Close dropdowns on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.main-nav__desktop-item.is-open').forEach(item => {
                item.classList.remove('is-open');
                const toggle = item.querySelector('.main-nav__desktop-toggle, .main-nav__desktop-heading-toggle');
                if (toggle) toggle.setAttribute('aria-expanded', 'false');
            });
        }
    });
};

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        initMobileNav();
        initDesktopNav();
    });
} else {
    initMobileNav();
    initDesktopNav();
}

// Export for potential external use
export default {};
