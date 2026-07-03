/**
 * Main Navigation
 * Handles mobile toggle, desktop dropdowns, and scroll effects
 */

// Mobile navigation toggle
const initMobileNav = () => {
  const nav = document.querySelector(".main-nav");
  const toggle = document.querySelector(".main-nav__toggle");
  const toggleText = toggle?.querySelector(".main-nav__toggle-text");
  const menu = document.querySelector(".main-nav__menu");

  if (!nav || !toggle || !menu) return;

  // Set initial state
  menu.setAttribute("aria-hidden", "true");
  menu.inert = true;

  toggle.addEventListener("click", () => {
    const isExpanded = toggle.getAttribute("aria-expanded") === "true";
    const newState = !isExpanded;

    // Update button state
    // The accessible name comes from the visually-hidden text span (not a
    // redundant aria-label) so there is a single source of truth to keep in
    // sync with the open/closed state.
    toggle.setAttribute("aria-expanded", newState);
    if (toggleText) toggleText.textContent = newState ? "Menü schließen" : "Menü öffnen";

    // Update menu state
    // The drawer is hidden off-screen via CSS transform (not display:none), so
    // it stays in the tab order unless explicitly made inert — otherwise
    // keyboard users can tab into links that are invisible and, per
    // aria-hidden, hidden from screen readers too.
    menu.setAttribute("aria-hidden", !newState);
    menu.inert = !newState;

    // Update nav state
    nav.classList.toggle("main-nav--open", newState);

    // Prevent body scroll when menu is open
    document.body.classList.toggle("nav-open", newState);
  });

  // Close menu on Escape key
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && menu.getAttribute("aria-hidden") === "false") {
      toggle.setAttribute("aria-expanded", "false");
      if (toggleText) toggleText.textContent = "Menü öffnen";
      menu.setAttribute("aria-hidden", "true");
      menu.inert = true;
      nav.classList.remove("main-nav--open");
      document.body.classList.remove("nav-open");
    }
  });

  // Shrink logo on scroll
  const handleScroll = () => {
    if (window.scrollY > 50) {
      nav.classList.add("main-nav--scrolled");
    } else {
      nav.classList.remove("main-nav--scrolled");
    }
  };

  window.addEventListener("scroll", handleScroll, { passive: true });
};

// Desktop navigation dropdowns
const initDesktopNav = () => {
  // Handle both types of toggle buttons: .main-nav__desktop-toggle and .main-nav__desktop-heading-toggle
  const toggleButtons = document.querySelectorAll(
    ".main-nav__desktop-toggle, .main-nav__desktop-heading-toggle",
  );

  toggleButtons.forEach((button) => {
    button.addEventListener("click", (e) => {
      e.stopPropagation();
      const item = button.closest(".main-nav__desktop-item");
      const isOpen = item.classList.contains("is-open");

      // Close all other dropdowns
      document.querySelectorAll(".main-nav__desktop-item.is-open").forEach((openItem) => {
        if (openItem !== item) {
          openItem.classList.remove("is-open");
          const toggle = openItem.querySelector(
            ".main-nav__desktop-toggle, .main-nav__desktop-heading-toggle",
          );
          if (toggle) toggle.setAttribute("aria-expanded", "false");
        }
      });

      // Toggle current dropdown
      item.classList.toggle("is-open");
      button.setAttribute("aria-expanded", !isOpen);
    });
  });

  // Close dropdowns when clicking outside
  document.addEventListener("click", (e) => {
    if (!e.target.closest(".main-nav__desktop-item")) {
      document.querySelectorAll(".main-nav__desktop-item.is-open").forEach((item) => {
        item.classList.remove("is-open");
        const toggle = item.querySelector(
          ".main-nav__desktop-toggle, .main-nav__desktop-heading-toggle",
        );
        if (toggle) toggle.setAttribute("aria-expanded", "false");
      });
    }
  });

  // Close dropdowns on Escape key
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      document.querySelectorAll(".main-nav__desktop-item.is-open").forEach((item) => {
        item.classList.remove("is-open");
        const toggle = item.querySelector(
          ".main-nav__desktop-toggle, .main-nav__desktop-heading-toggle",
        );
        if (toggle) toggle.setAttribute("aria-expanded", "false");
      });
    }
  });
};

// Initialize when DOM is ready
function initNavigation() {
  initMobileNav();
  initDesktopNav();
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initNavigation);
} else {
  initNavigation();
}
