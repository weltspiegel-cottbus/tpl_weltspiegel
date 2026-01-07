/**
 * YouTube Embed
 * Handles consent-aware YouTube video embedding
 */

class YouTubeEmbed {
    constructor(embedElement) {
        this.embed = embedElement;
        this.placeholder = this.embed.querySelector('.youtube-embed__placeholder');
        this.iframe = this.embed.querySelector('.youtube-embed__iframe');

        if (!this.placeholder || !this.iframe) {
            return;
        }

        this.init();
    }

    init() {
        this.checkConsent();
        window.addEventListener('cookieConsentChanged', () => this.checkConsent());
    }

    checkConsent() {
        try {
            const consent = localStorage.getItem('cookie_consent');
            if (consent === 'granted') {
                // Only show iframe after it has loaded
                this.iframe.addEventListener('load', () => {
                    this.iframe.style.display = 'block';
                    this.placeholder.style.display = 'none';
                }, { once: true });
                this.iframe.src = this.iframe.dataset.src;
            } else {
                this.placeholder.style.display = 'flex';
                this.iframe.style.display = 'none';
            }
        } catch (e) {
            this.placeholder.style.display = 'flex';
            this.iframe.style.display = 'none';
        }
    }
}

// Initialize all YouTube embeds on the page
function initYouTubeEmbeds() {
    const embeds = document.querySelectorAll('.youtube-embed');
    embeds.forEach(embed => new YouTubeEmbed(embed));
}

// Initialize on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initYouTubeEmbeds);
} else {
    initYouTubeEmbeds();
}
