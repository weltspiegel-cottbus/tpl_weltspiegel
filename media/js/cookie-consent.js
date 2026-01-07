/**
 * Cookie Consent Module
 * Handles cookie consent banner and drawer functionality
 */

class CookieConsent {
    constructor() {
        this.storageKey = 'cookie_consent';
        this.banner = document.getElementById('cookieConsentBanner');
        this.drawer = document.getElementById('cookieConsentDrawer');
        this.enableBtn = document.getElementById('cookieConsentEnable');
        this.dismissBtn = document.getElementById('cookieConsentDismiss');

        if (!this.banner || !this.drawer || !this.enableBtn || !this.dismissBtn) {
            return;
        }

        this.init();
    }

    getConsent() {
        try {
            return localStorage.getItem(this.storageKey);
        } catch (e) {
            return null;
        }
    }

    setConsent(value) {
        try {
            localStorage.setItem(this.storageKey, value);
            // Dispatch event for other scripts to listen to
            window.dispatchEvent(new CustomEvent('cookieConsentChanged', {
                detail: { consent: value }
            }));
        } catch (e) {
            console.error('Could not save consent to localStorage:', e);
        }
    }

    showBanner() {
        this.banner.classList.remove('cookie-consent-hidden');
        this.drawer.style.display = 'none';
    }

    hideBanner() {
        this.banner.classList.add('cookie-consent-hidden');
        this.drawer.style.display = 'block';
    }

    init() {
        const consent = this.getConsent();

        if (consent === null) {
            // First visit - show banner
            this.showBanner();
        } else {
            // Already decided - show drawer
            this.hideBanner();
        }

        // Event listeners
        this.enableBtn.addEventListener('click', () => {
            this.setConsent('granted');
            this.hideBanner();
        });

        this.dismissBtn.addEventListener('click', () => {
            this.setConsent('denied');
            this.hideBanner();
        });

        this.drawer.addEventListener('click', () => {
            this.showBanner();
        });
    }
}

// Initialize cookie consent
function initCookieConsent() {
    new CookieConsent();
}

// Initialize on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCookieConsent);
} else {
    initCookieConsent();
}
