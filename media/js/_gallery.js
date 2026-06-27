/**
 * Gallery Lightbox
 * Uses native <dialog> for focus trapping and backdrop handling
 */

class Gallery {
  constructor(el) {
    this.el = el;
    this.dialog = document.getElementById(el.id + "-lightbox");
    if (!this.dialog) return;

    this.items = [...el.querySelectorAll(".gallery__item")];
    this.images = this.items.map((a) => {
      const img = a.querySelector("img");
      return { src: img.src, alt: img.alt };
    });
    this.current = 0;
    this.touchStartX = 0;

    this.lightboxImg = this.dialog.querySelector(".gallery-lightbox__image");
    this.counterEl = this.dialog.querySelector(".gallery-lightbox__current");

    if (this.images.length === 1) {
      this.dialog.dataset.single = "";
    }

    this.bindEvents();
  }

  bindEvents() {
    this.items.forEach((a, i) => {
      a.addEventListener("click", (e) => {
        e.preventDefault();
        this.open(i);
      });
    });

    this.dialog
      .querySelector(".gallery-lightbox__close")
      .addEventListener("click", () => this.dialog.close());
    this.dialog
      .querySelector(".gallery-lightbox__prev")
      .addEventListener("click", () => this.navigate(-1));
    this.dialog
      .querySelector(".gallery-lightbox__next")
      .addEventListener("click", () => this.navigate(1));

    // Close on backdrop click (click lands on the <dialog> itself, not its children)
    this.dialog.addEventListener("click", (e) => {
      if (e.target === this.dialog) this.dialog.close();
    });

    this.dialog.addEventListener("keydown", (e) => {
      if (e.key === "ArrowLeft") this.navigate(-1);
      if (e.key === "ArrowRight") this.navigate(1);
    });

    this.dialog.addEventListener(
      "touchstart",
      (e) => {
        this.touchStartX = e.touches[0].clientX;
      },
      { passive: true },
    );

    this.dialog.addEventListener(
      "touchend",
      (e) => {
        const delta = e.changedTouches[0].clientX - this.touchStartX;
        if (Math.abs(delta) > 50) this.navigate(delta < 0 ? 1 : -1);
      },
      { passive: true },
    );
  }

  open(index) {
    this.current = index;
    this.update();
    this.dialog.showModal();
  }

  navigate(dir) {
    this.current = (this.current + dir + this.images.length) % this.images.length;
    this.update();
  }

  update() {
    const { src, alt } = this.images[this.current];
    this.lightboxImg.src = src;
    this.lightboxImg.alt = alt;
    this.counterEl.textContent = this.current + 1;
  }
}

document.querySelectorAll(".gallery[id]").forEach((el) => new Gallery(el));
