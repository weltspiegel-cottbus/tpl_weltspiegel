/**
 * Cookie Consent (v2 — category based)
 *
 * Consent is stored per category in localStorage as JSON:
 *   cookie_consent = { "youtube": true, "statistik": false }
 * A missing category counts as "not granted".
 *
 * ConsentManager is the single source of truth. The banner switches and feature
 * scripts (e.g. _youtube.js) only talk to it. Each change dispatches a
 * `cookieConsentChanged` event with detail { category, granted }.
 */

const STORAGE_KEY = "cookie_consent";

const ConsentManager = {
  /** @returns {Object<string, boolean>} */
  getAll() {
    let raw;
    try {
      raw = localStorage.getItem(STORAGE_KEY);
    } catch {
      return {};
    }
    if (!raw) return {};

    // Old v1 format: a single global "granted"/"denied" string. That global
    // consent does NOT map to a specific category, so we deliberately discard it
    // (and clean it up) and require fresh, per-category consent.
    if (raw === "granted" || raw === "denied") {
      try {
        localStorage.removeItem(STORAGE_KEY);
      } catch {
        /* ignore */
      }
      return {};
    }

    try {
      const obj = JSON.parse(raw);
      return obj && typeof obj === "object" ? obj : {};
    } catch {
      return {};
    }
  },

  isGranted(category) {
    return this.getAll()[category] === true;
  },

  set(category, granted) {
    const all = this.getAll();
    all[category] = !!granted;
    this._save(all);
    window.dispatchEvent(
      new CustomEvent("cookieConsentChanged", {
        detail: { category, granted: !!granted },
      }),
    );
  },

  _save(obj) {
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(obj));
    } catch (e) {
      console.error("Could not save consent to localStorage:", e);
    }
  },
};

/** Public helper for feature scripts. */
export function isConsentGranted(category) {
  return ConsentManager.isGranted(category);
}

class CookieConsent {
  constructor() {
    this.banner = document.getElementById("cookieConsentBanner");
    this.drawer = document.getElementById("cookieConsentDrawer");
    this.closeBtn = document.getElementById("cookieConsentClose");
    this.okBtn = document.getElementById("cookieConsentOk");

    if (!this.banner || !this.drawer) {
      return;
    }

    this.switches = [...this.banner.querySelectorAll("[data-consent-category]")];
    this.init();
  }

  showBanner() {
    this.banner.classList.remove("cookie-consent-hidden");
    this.drawer.style.display = "none";
    this.switches[0]?.focus();
  }

  hideBanner() {
    this.banner.classList.add("cookie-consent-hidden");
    this.drawer.style.display = "";
  }

  init() {
    // Wire each category switch to the consent manager (live toggle).
    const stored = ConsentManager.getAll();
    this.switches.forEach((sw) => {
      const category = sw.dataset.consentCategory;

      // Seed undecided categories with their configured default (persisted once).
      if (!(category in stored)) {
        ConsentManager.set(category, sw.dataset.consentDefault === "1");
      }

      sw.checked = ConsentManager.isGranted(category);
      sw.addEventListener("change", () => {
        ConsentManager.set(category, sw.checked);
      });
    });

    // No auto-open on first visit (only the strictly-necessary PHP session
    // cookie is set without consent). The banner opens via manual triggers only.
    this.hideBanner();

    // "OK" just closes — the switches already applied their state live.
    this.okBtn?.addEventListener("click", () => this.hideBanner());
    this.closeBtn?.addEventListener("click", () => this.hideBanner());

    // Drawer reopens the banner
    this.drawer.addEventListener("click", () => this.showBanner());

    // Close with Escape
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && !this.banner.classList.contains("cookie-consent-hidden")) {
        this.hideBanner();
      }
    });

    // External requests (e.g. trailer placeholder)
    window.addEventListener("showCookieBanner", () => this.showBanner());

    // Links anywhere (e.g. privacy policy) to reopen the banner
    document.addEventListener("click", (e) => {
      const trigger = e.target.closest("[data-cookie-settings]");
      if (!trigger) {
        return;
      }
      e.preventDefault();
      this.showBanner();
    });
  }
}

function initCookieConsent() {
  new CookieConsent();
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initCookieConsent);
} else {
  initCookieConsent();
}
