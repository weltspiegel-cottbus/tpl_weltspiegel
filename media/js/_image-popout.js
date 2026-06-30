/**
 * Image Popout
 * Opens a larger image in a modal dialog when an element carrying
 * [data-image-popout="path/to/large.jpg"] is clicked.
 *
 * Progressive enhancement: place it on an <a href="large.jpg"> so it still
 * works as a plain link (direct navigation) when JS is unavailable.
 *
 *   <a href="/images/site/kontakt/karte-gross.jpg"
 *      data-image-popout="/images/site/kontakt/karte-gross.jpg">
 *     <img src="/images/site/kontakt/karte-klein.jpg" alt="Lageplan Weltspiegel">
 *   </a>
 *
 * Optional: data-image-popout-alt="…" overrides the alt text of the large image
 * (otherwise the inner <img>'s alt is reused).
 */

let dialog = null;

function ensureDialog() {
  if (dialog) return dialog;

  dialog = document.createElement("dialog");
  dialog.className = "image-popout";
  dialog.setAttribute("aria-label", "Bildansicht");
  dialog.innerHTML =
    '<button class="image-popout__close" type="button" aria-label="Schließen">' +
    '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">' +
    '<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>' +
    '<img class="image-popout__image" src="" alt="">';

  document.body.appendChild(dialog);

  dialog
    .querySelector(".image-popout__close")
    .addEventListener("click", () => dialog.close());

  // Close when clicking the backdrop area (the dialog itself, not the image)
  dialog.addEventListener("click", (e) => {
    if (e.target === dialog) dialog.close();
  });

  return dialog;
}

document.addEventListener("click", (e) => {
  const trigger = e.target.closest("[data-image-popout]");
  if (!trigger) return;

  const src = trigger.dataset.imagePopout;
  if (!src) return;

  e.preventDefault();

  const d = ensureDialog();
  const img = d.querySelector(".image-popout__image");
  img.src = src;
  img.alt =
    trigger.dataset.imagePopoutAlt || trigger.querySelector("img")?.alt || "";
  d.showModal();
});
