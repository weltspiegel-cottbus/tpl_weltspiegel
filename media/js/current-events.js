/**
 * Current Events Module
 * Handles sticky navigation behavior
 */

class CurrentEventsNav {
    constructor() {
        this.nav = document.querySelector('.mod-current-events__nav');
        this.module = document.querySelector('.mod-current-events');

        if (!this.nav || !this.module) {
            return;
        }

        this.lastScrollY = window.scrollY;
        this.isNavVisible = true;

        this.init();
    }

    init() {
        // Track scroll direction
        window.addEventListener('scroll', () => this.handleScroll(), { passive: true });

        // Set up Intersection Observer for footer
        const footer = document.querySelector('footer');
        if (footer) {
            const observer = new IntersectionObserver(
                (entries) => this.handleIntersection(entries),
                { threshold: 0, rootMargin: '0px' }
            );
            observer.observe(footer);
        }

        // Detect sticky state using Intersection Observer
        this.setupStickyDetection();

        // Set up scroll spy for active nav links
        this.setupScrollSpy();
    }

    setupStickyDetection() {
        // Create a sentinel element right before the nav
        const sentinel = document.createElement('div');
        sentinel.style.height = '1px';
        sentinel.style.width = '1px';
        sentinel.style.pointerEvents = 'none';
        sentinel.style.visibility = 'hidden';
        this.nav.parentElement.insertBefore(sentinel, this.nav);

        // Observe when sentinel goes out of view = nav is stuck
        const stickyObserver = new IntersectionObserver(
            ([entry]) => {
                if (!entry.isIntersecting) {
                    this.nav.classList.add('mod-current-events__nav--stuck');
                } else {
                    this.nav.classList.remove('mod-current-events__nav--stuck');
                }
            },
            { threshold: 0, rootMargin: `-${getComputedStyle(this.nav).top} 0px 0px 0px` }
        );
        stickyObserver.observe(sentinel);
    }

    setupScrollSpy() {
        // Get all sections with IDs
        const sections = this.module.querySelectorAll('.mod-current-events__section[id]');
        if (sections.length === 0) return;

        // Get all nav links
        const navLinks = this.nav.querySelectorAll('.mod-current-events__nav-link');

        // Track which sections are currently intersecting
        const intersectingEntries = new Map();

        // Create observer for sections
        const scrollSpyObserver = new IntersectionObserver(
            (entries) => {
                // Update intersection tracking
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        intersectingEntries.set(entry.target.id, entry);
                    } else {
                        intersectingEntries.delete(entry.target.id);
                    }
                });

                // Find which section is closest to the top
                let activeId = null;
                let closestDistance = Infinity;

                intersectingEntries.forEach((entry, id) => {
                    const distance = Math.abs(entry.boundingClientRect.top);
                    if (distance < closestDistance) {
                        closestDistance = distance;
                        activeId = id;
                    }
                });

                // Update active states
                navLinks.forEach(link => {
                    const href = link.getAttribute('href');
                    if (href && href.startsWith('#')) {
                        const targetId = href.substring(1);
                        if (targetId === activeId) {
                            link.classList.add('mod-current-events__nav-link--active');
                        } else {
                            link.classList.remove('mod-current-events__nav-link--active');
                        }
                    }
                });
            },
            {
                threshold: [0, 0.1, 0.5, 0.9, 1],
                rootMargin: '-100px 0px -50% 0px' // Account for sticky nav + focus on top half
            }
        );

        // Observe all sections
        sections.forEach(section => scrollSpyObserver.observe(section));
    }

    handleScroll() {
        const currentScrollY = window.scrollY;
        const scrollingDown = currentScrollY > this.lastScrollY;

        // Always show nav when scrolling up
        if (!scrollingDown && !this.isNavVisible) {
            this.showNav();
        }

        this.lastScrollY = currentScrollY;
    }

    handleIntersection(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Trigger element is visible - hide nav
                this.hideNav();
            } else {
                // Trigger element is not visible - show nav
                this.showNav();
            }
        });
    }

    hideNav() {
        this.nav.classList.add('mod-current-events__nav--hidden');
        this.isNavVisible = false;
    }

    showNav() {
        this.nav.classList.remove('mod-current-events__nav--hidden');
        this.isNavVisible = true;
    }
}

// Initialize on DOM ready
function initCurrentEventsNav() {
    new CurrentEventsNav();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCurrentEventsNav);
} else {
    initCurrentEventsNav();
}
