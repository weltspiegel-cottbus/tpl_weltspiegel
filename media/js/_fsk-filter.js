/**
 * FSK Rules Table — two filter axes
 *
 * The table crosses age against rating, and both axes can be narrowed:
 *
 *   Age    — chip row above the table, built here. "Wie alt bist du?"
 *   Rating — the signets in the row headers, and the hash in the URL.
 *            "Der Film ist ab 12 — wer darf rein?"
 *
 * The rating axis is what the FSK badges on the film pages link to. They carry
 * #fsk-12 and the like: without JavaScript the browser jumps to that row, which
 * on a narrow screen is the card for that rating — the answer, natively, with
 * nothing loaded. With JavaScript the row is selected instead, and the table is
 * scrolled back to its top so the column headings stay in view.
 *
 * A fragment never reaches the server, so it cannot land in Joomla's page cache
 * key or be dropped as an unsafe URL parameter — which a ?fsk=12 could.
 *
 * Everything interactive is built here rather than shipped in the article: the
 * article stays plain markup, and a text filter has nothing to strip.
 *
 * The filters only set classes. What they mean is left to the CSS and differs by
 * width: narrow viewports drop what is filtered out, wide ones merely dim it,
 * because there the comparison across the whole matrix is the point.
 */

const DESKTOP = "(width >= 60.0625em)";

class FskTable {
  constructor() {
    this.table = document.querySelector("[data-fsk-table]");

    if (!this.table) {
      return;
    }

    this.headings = Array.from(this.table.querySelectorAll("thead [data-age]"));
    this.ageTargets = Array.from(this.table.querySelectorAll("[data-age]"));
    this.rows = Array.from(this.table.querySelectorAll("tr[data-fsk]"));

    if (this.headings.length === 0) {
      return;
    }

    this.age = "alle";
    this.fsk = null;

    this.buildAgeFilter();
    this.buildColumnButtons();
    this.buildRowButtons();
    this.applyFromHash();
  }

  /* --- Bedienelemente ---------------------------------------------------- */

  buildAgeFilter() {
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
      entries.push({
        value: heading.dataset.age,
        // The article supplies the short form; deriving it here would mean
        // parsing German wording in a script that should not care about it.
        short: heading.dataset.ageShort || heading.dataset.age,
        full: heading.dataset.age,
      });
    });

    this.ageButtons = entries.map((entry) => {
      const button = document.createElement("button");
      button.type = "button";
      button.className = "fsk-filter__button";
      button.dataset.ageValue = entry.value;
      button.textContent = entry.short;
      button.setAttribute("aria-label", entry.full);
      button.setAttribute("aria-pressed", String(entry.value === "alle"));
      button.addEventListener("click", () => this.setAge(entry.value));

      group.appendChild(button);

      return button;
    });

    bar.append(label, group, this.status);
    this.table.parentNode.insertBefore(bar, this.table);
  }

  /**
   * Column headings switch the age while a filter is on. Deliberately not an
   * entry point: before the first pick nothing invites a click there, and it
   * need not — that is what the chips are for. The impulse only arises once the
   * chips have taught that a column is an age group, and then the heading is
   * simply nearer than the way back up.
   *
   * Disabled below the breakpoint instead of removed: there the headings are
   * visually hidden, so an enabled button would be an invisible tab stop. A
   * disabled one is neither focusable nor clickable, and its text stays in the
   * accessibility tree.
   */
  buildColumnButtons() {
    this.columnButtons = this.headings.map((heading) => {
      const button = document.createElement("button");
      button.type = "button";
      button.className = "fsk__head-button";
      button.textContent = heading.textContent.trim();
      button.addEventListener("click", () => this.setAge(heading.dataset.age));

      heading.textContent = "";
      heading.appendChild(button);

      return button;
    });

    const desktop = window.matchMedia(DESKTOP);
    const sync = () => {
      this.columnButtons.forEach((b) => {
        b.disabled = !desktop.matches;
      });
    };

    sync();
    desktop.addEventListener("change", sync);
  }

  /** Row headers carry the signet; its alt text is the accessible name. */
  buildRowButtons() {
    this.rowButtons = this.rows.map((row) => {
      const head = row.querySelector(".fsk__rating");
      const button = document.createElement("button");
      button.type = "button";
      button.className = "fsk__head-button fsk__head-button--rating";
      button.setAttribute("aria-pressed", "false");

      while (head.firstChild) {
        button.appendChild(head.firstChild);
      }

      head.appendChild(button);
      button.addEventListener("click", () => this.toggleFsk(row.dataset.fsk));

      return button;
    });
  }

  /* --- Zustand ----------------------------------------------------------- */

  applyFromHash() {
    const match = /^#fsk-(\d+)$/.exec(window.location.hash);

    if (!match) {
      return;
    }

    const row = this.rows.find((r) => r.dataset.fsk === match[1]);

    if (!row) {
      return;
    }

    this.fsk = match[1];
    this.render();

    // No scrolling of our own: the browser lands on the row, and the column
    // headings stay pinned above it (see _fsk.css). Correcting the position
    // here used to be necessary because the headings scrolled away — and it was
    // a race against the browser, which re-applies the fragment as layout
    // settles.
  }

  setAge(value) {
    this.age = value;
    this.render();
  }

  toggleFsk(value) {
    // Clicking the selected rating again clears it — the row header is the only
    // control for this axis, so its way out belongs on the same control. The
    // status line says so in words, because nothing about a button announces
    // that pressing it again undoes it.
    this.fsk = this.fsk === value ? null : value;
    this.render();
    this.rememberInHash();
  }

  rememberInHash() {
    const hash = this.fsk === null ? window.location.pathname + window.location.search : "#fsk-" + this.fsk;

    window.history.replaceState(null, "", hash);
  }

  /* --- Darstellung ------------------------------------------------------- */

  render() {
    const allAges = this.age === "alle";

    this.ageTargets.forEach((element) => {
      element.classList.toggle("is-off", !allAges && element.dataset.age !== this.age);
    });

    this.rows.forEach((row) => {
      row.classList.toggle("is-off", this.fsk !== null && row.dataset.fsk !== this.fsk);
    });

    this.table.classList.toggle("is-filtered", !allAges);

    this.ageButtons.forEach((button) => {
      button.setAttribute("aria-pressed", String(button.dataset.ageValue === this.age));
    });

    this.rowButtons.forEach((button, index) => {
      button.setAttribute("aria-pressed", String(this.rows[index].dataset.fsk === this.fsk));
    });

    this.renderStatus();
  }

  renderStatus() {
    this.status.textContent = "";

    if (this.fsk === null && this.age === "alle") {
      return;
    }

    const teile = [];

    if (this.fsk !== null) {
      teile.push("Freigabe ab " + this.fsk);
    }

    if (this.age !== "alle") {
      teile.push("Alter " + this.age);
    }

    this.status.append("Angezeigt: " + teile.join(", ") + " ");

    const reset = document.createElement("button");
    reset.type = "button";
    reset.className = "fsk-filter__reset";
    reset.textContent = "ganze Tabelle zeigen";
    reset.addEventListener("click", () => {
      this.age = "alle";
      this.fsk = null;
      this.render();
      this.rememberInHash();
    });

    this.status.appendChild(reset);
  }
}

function initFskTable() {
  new FskTable();
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initFskTable);
} else {
  initFskTable();
}
