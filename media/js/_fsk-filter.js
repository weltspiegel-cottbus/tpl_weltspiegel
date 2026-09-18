/**
 * FSK Rules Table — age filter
 *
 * Lets a visitor pick their own age group; the table then answers only for that
 * one. On a phone that is the difference between five screens of scrolling and
 * half a screen — which is the one thing an HTML table can do that the poster
 * graphic every other cinema embeds cannot.
 *
 * Progressive enhancement: the control does not exist in the article at all,
 * this script builds it. Without JavaScript the full table simply stays, and
 * nothing points at a control that is not there. It also keeps <button>
 * elements out of the article, where a text filter could strip them.
 *
 * The filter only sets a class. What it means is left to the CSS and differs by
 * width: narrow viewports hide the other age groups, wide ones merely dim them,
 * because there the comparison across the whole matrix is the point.
 */

class FskAgeFilter {
  constructor() {
    this.table = document.querySelector("[data-fsk-table]");

    if (!this.table) {
      return;
    }

    this.headings = Array.from(this.table.querySelectorAll("thead [data-age]"));
    this.targets = Array.from(this.table.querySelectorAll("[data-age]"));

    if (this.headings.length === 0) {
      return;
    }

    this.build();
  }

  build() {
    const bar = document.createElement("div");
    bar.className = "fsk-filter";

    const label = document.createElement("span");
    label.className = "fsk-filter__label";
    label.id = "fsk-filter-label";
    label.textContent = "Wie alt bist du?";

    const group = document.createElement("div");
    group.className = "fsk-filter__options";
    group.setAttribute("role", "group");
    group.setAttribute("aria-labelledby", label.id);

    this.status = document.createElement("p");
    this.status.className = "fsk-filter__status";
    this.status.setAttribute("role", "status");

    const entries = [{ value: "alle", short: "Alle", full: "Alle Altersgruppen anzeigen" }];

    this.headings.forEach((heading) => {
      const full = heading.dataset.age;

      entries.push({
        value: full,
        // The article supplies the short form; deriving it here would mean
        // parsing German wording in a script that should not care about it.
        short: heading.dataset.ageShort || full,
        full,
      });
    });

    this.buttons = entries.map((entry) => {
      const button = document.createElement("button");
      button.type = "button";
      button.className = "fsk-filter__button";
      button.dataset.ageValue = entry.value;
      button.textContent = entry.short;
      button.setAttribute("aria-label", entry.full);
      button.setAttribute("aria-pressed", String(entry.value === "alle"));
      button.addEventListener("click", () => this.apply(entry.value));

      group.appendChild(button);

      return button;
    });

    bar.append(label, group, this.status);
    this.table.parentNode.insertBefore(bar, this.table);
  }

  apply(value) {
    const showAll = value === "alle";

    this.targets.forEach((element) => {
      element.classList.toggle("is-off", !showAll && element.dataset.age !== value);
    });

    this.table.classList.toggle("is-filtered", !showAll);

    this.buttons.forEach((button) => {
      button.setAttribute("aria-pressed", String(button.dataset.ageValue === value));
    });

    this.status.textContent = showAll ? "" : "Angezeigt: Regelungen für " + value + ".";
  }
}

function initFskAgeFilter() {
  new FskAgeFilter();
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initFskAgeFilter);
} else {
  initFskAgeFilter();
}
