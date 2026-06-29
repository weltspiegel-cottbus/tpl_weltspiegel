/**
 * YouTube Embed
 * Handles consent-aware YouTube video embedding
 */

class YouTubeEmbed {
  constructor(embedElement) {
    this.embed = embedElement;
    this.placeholder = this.embed.querySelector(".youtube-embed__placeholder");
    this.iframe = this.embed.querySelector(".youtube-embed__iframe");

    if (!this.placeholder || !this.iframe) {
      return;
    }

    this.init();
  }

  init() {
    this.checkConsent();
    window.addEventListener("cookieConsentChanged", () => this.checkConsent());

    // Make placeholder clickable to show cookie consent
    this.placeholder.style.cursor = "pointer";
    this.placeholder.addEventListener("click", (e) => {
      // Don't trigger banner if clicking the YouTube link
      if (e.target.classList.contains("youtube-embed__placeholder-link")) {
        return;
      }
      window.dispatchEvent(new CustomEvent("showCookieBanner"));
    });
  }

  checkConsent() {
    try {
      const consent = localStorage.getItem("cookie_consent");
      if (consent === "granted") {
        // Reveal the iframe only once its content has loaded. The iframe keeps
        // its layout box while hidden (visibility, not display), so YouTube
        // measures the real size and serves a sharp poster.
        this.iframe.addEventListener(
          "load",
          () => {
            this.iframe.style.visibility = "visible";
            this.placeholder.style.display = "none";
          },
          { once: true },
        );
        this.iframe.src = this.iframe.dataset.src;
      } else {
        this.hideIframe();
      }
    } catch {
      this.hideIframe();
    }
  }

  hideIframe() {
    this.placeholder.style.display = "flex";
    this.iframe.style.visibility = "hidden";

    // Tear down the YouTube document so playback and scripts stop immediately
    // and no further cookies/requests are sent after consent is withdrawn.
    if (this.iframe.src && this.iframe.src !== "about:blank") {
      this.iframe.src = "about:blank";
    }
  }
}

// Initialize all YouTube embeds on the page
function initYouTubeEmbeds() {
  const embeds = document.querySelectorAll(".youtube-embed");
  embeds.forEach((embed) => new YouTubeEmbed(embed));
}

// Initialize on DOM ready
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initYouTubeEmbeds);
} else {
  initYouTubeEmbeds();
}
