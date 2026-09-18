/**
 * Day Filter
 * Marks the chip row while it is stuck to the top of the viewport.
 *
 * The row is sticky in CSS alone; this only adds the class that squares off the
 * top corners once it sits against the header — rounded corners there would
 * float in mid-air.
 *
 * Deliberately without the sentinel element the home page module uses: the
 * listing is a flex column with a 2rem gap, and a sentinel would be a flex item
 * like any other. Even at zero height it would collect that gap, pushing the row
 * down and — worse — placing the measuring point 2rem above the row, so the
 * state flipped 2rem too early.
 *
 * Instead the row observes itself. The root is shrunk from the top by the row's
 * own sticky offset plus one pixel, so while the row rests below that band it is
 * fully inside it (ratio 1). The moment it sticks, its top edge sits one pixel
 * above the band and the ratio drops below 1. The extra check on the top edge
 * keeps a row that is simply scrolled out of view from counting as stuck.
 *
 * Without JavaScript nothing happens and the row stays rounded — it still
 * sticks, it just keeps its resting shape.
 */

class DayFilterStuck {
  constructor() {
    this.bar = document.querySelector(".day-filter");

    if (!this.bar) {
      return;
    }

    this.observe();
  }

  observe() {
    const offset = parseFloat(getComputedStyle(this.bar).top) || 0;

    const observer = new IntersectionObserver(
      ([entry]) => {
        const stuck = entry.intersectionRatio < 1 && entry.boundingClientRect.top <= offset + 1;

        this.bar.classList.toggle("day-filter--stuck", stuck);
      },
      { threshold: [1], rootMargin: `-${offset + 1}px 0px 0px 0px` },
    );

    observer.observe(this.bar);
  }
}

function initDayFilter() {
  new DayFilterStuck();
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initDayFilter);
} else {
  initDayFilter();
}
