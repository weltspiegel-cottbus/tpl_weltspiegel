/**
 * Back Link
 * A [data-back-link] element navigates via its href by default — a safe
 * fallback for direct access or an external/absent referrer. When the visitor
 * arrived from within our own site, it instead goes back in history, which
 * restores their previous scroll position (e.g. long movie list).
 */

document.addEventListener("click", (e) => {
  const link = e.target.closest("[data-back-link]");
  if (!link) return;

  let sameOriginReferrer = false;
  try {
    sameOriginReferrer =
      !!document.referrer &&
      new window.URL(document.referrer).origin === window.location.origin;
  } catch {
    sameOriginReferrer = false;
  }

  if (sameOriginReferrer && window.history.length > 1) {
    e.preventDefault();
    window.history.back();
  }
  // otherwise: follow the href (the overview fallback)
});
