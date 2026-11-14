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
//
// One state, `is-open`, for every way of opening a panel. With a mouse the panel
// opens when the pointer enters its item and closes shortly after it leaves; a
// click on the toggle flips it like for touch and keyboard. Because the script
// - not CSS :hover - owns the state, a click that closes a panel under the
// pointer stays closed until the pointer has left and come back, and the
// chevron (which follows aria-expanded) always shows the truth.
const initDesktopNav = () => {
  const nav = document.querySelector(".main-nav");
  const items = document.querySelectorAll(".main-nav__desktop-item--has-submenu");

  if (!nav || items.length === 0) return;

  // The CSS falls back to :hover only while this class is missing.
  nav.classList.add("main-nav--js");

  // Grace period before a panel closes after the pointer left: lets the pointer
  // cross from one item to the next without the first one flickering.
  const CLOSE_DELAY = 150;

  const toggleOf = (item) =>
    item.querySelector(".main-nav__desktop-toggle, .main-nav__desktop-heading-toggle");

  const setOpen = (item, open) => {
    item.classList.toggle("is-open", open);
    toggleOf(item)?.setAttribute("aria-expanded", String(open));
  };

  const closeAll = (except = null) => {
    items.forEach((item) => {
      if (item !== except) setOpen(item, false);
    });
  };

  const closeTimers = new WeakMap();
  const cancelClose = (item) => clearTimeout(closeTimers.get(item));

  items.forEach((item) => {
    const toggle = toggleOf(item);

    toggle?.addEventListener("click", (e) => {
      e.stopPropagation();
      cancelClose(item);

      const open = !item.classList.contains("is-open");
      closeAll(item);
      setOpen(item, open);
    });

    // Hover is a mouse thing. Touch and pen also fire pointer events, and a tap
    // must not be taken for a pointer that arrived and never left.
    item.addEventListener("pointerenter", (e) => {
      if (e.pointerType !== "mouse") return;

      cancelClose(item);
      closeAll(item);
      setOpen(item, true);
    });

    item.addEventListener("pointerleave", (e) => {
      if (e.pointerType !== "mouse") return;

      closeTimers.set(
        item,
        setTimeout(() => setOpen(item, false), CLOSE_DELAY),
      );
    });
  });

  // Close dropdowns when clicking outside
  document.addEventListener("click", (e) => {
    if (!e.target.closest(".main-nav__desktop-item")) closeAll();
  });

  // Close dropdowns on Escape key
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeAll();
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
