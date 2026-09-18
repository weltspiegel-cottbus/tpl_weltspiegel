<?php

/**
 * Movies List View
 * Template override for Weltspiegel template
 *
 * @package     Weltspiegel.Template
 * @copyright   Weltspiegel Cottbus
 * @license     MIT
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

require_once __DIR__ . '/../../inc/presale-window.php';

$now                = new DateTime();
$presaleCutoffDate  = weltspiegel_presale_cutoff_date($now);
$futureHeadingShown = false;

?>

<div class="listing u-flipped-title-container">
    <h1 class="listing__title u-flipped-title"><?= $this->escape($this->title) ?></h1>

    <?php // Editorial notice, maintained in the component options. Deliberately
          // outside the preview flag: it must work in production on its own.
          //
          // Printed unescaped on purpose — the field is meant to carry a link to
          // the event page, so escaping here would show the markup as text. This
          // is safe because the value is not request input: it is a component
          // option, settable only with core.options on com_weltspiegel, and the
          // field is declared type="editor" filter="safehtml" in config.xml, so
          // Joomla runs it through InputFilter's allowlist on save. Do not copy
          // this line to a place where the value comes from anywhere else. ?>
    <?php if ($this->notice !== ''): ?>
        <div class="programme-notice"><?= $this->notice ?></div>
    <?php endif; ?>

    <?php // An empty programme needs an explanation regardless of the day filter,
          // so this sits outside the preview flag. Suppressed when the editorial
          // notice is on: that one says the same thing, only better. ?>
    <?php if (empty($this->items) && $this->notice === ''): ?>
        <p class="day-filter-note">
            <?php // Someone who asked for a specific day deserves an answer to that
                  // question, not just a general statement. No dates are promised
                  // here — there are none. ?>
            <?php if ($this->fallbackFrom === 'heute'): ?>
                Heute gibt es keine Vorstellung mehr — und auch für die weiteren Tage
                liegen uns derzeit keine Termine vor.
            <?php elseif ($this->fallbackFrom === 'morgen'): ?>
                Morgen gibt es keine Vorstellung — und auch für die weiteren Tage
                liegen uns derzeit keine Termine vor.
            <?php else: ?>
                Für die kommenden Tage liegen uns noch keine Vorstellungen vor.
            <?php endif; ?>
        </p>
    <?php endif; ?>

    <?php // TEMPORARY: the whole day filter is behind a preview flag (?preview=1). ?>
    <?php if ($this->filterEnabled): ?>
        <?= LayoutHelper::render('utilities.day-filter', [
            'active'    => $this->activeTag,
            'available' => $this->availableTags,
        ]) ?>

        <?php // A fallback that landed on an empty programme must not announce
              // dates that are not there — the message above covers that case. ?>
        <?php if (!empty($this->items) && $this->fallbackFrom !== null): ?>
            <p class="day-filter-note">
                <?php if ($this->fallbackFrom === 'heute' && $this->activeTag === 'morgen'): ?>
                    Heute gibt es keine Vorstellung mehr — hier ist das Programm von morgen.
                <?php else: ?>
                    <?= $this->fallbackFrom === 'heute'
                        ? 'Heute gibt es keine Vorstellung mehr'
                        : 'Morgen gibt es keine Vorstellung' ?>
                    — hier ist unser Programm der nächsten Tage.
                <?php endif; ?>
            </p>
        <?php endif; ?>
    <?php endif; ?>

    <div class="listing__items">
        <?php foreach ($this->items as $movie): ?>

            <?php
            // Find earliest show across all formats for "Vorverkauf" detection
            $firstShowDate = null;
            foreach ($movie->formats as $format) {
                foreach ($format->shows as $show) {
                    try {
                        $showDate = new DateTime($show->showStart);
                        if ($firstShowDate === null || $showDate < $firstShowDate) {
                            $firstShowDate = $showDate;
                        }
                    } catch (Exception $e) {
                        continue;
                    }
                }
            }

            if (!$futureHeadingShown && $firstShowDate !== null) {
                // Compare by calendar day: the cutoff is inclusive of its own day.
                if ($firstShowDate->format('Y-m-d') > $presaleCutoffDate->format('Y-m-d')) {
                    echo '<h2 class="listing__section-title">Vorverkauf</h2>';
                    $futureHeadingShown = true;
                }
            }

            $detailRoute = Route::_('index.php?option=com_weltspiegel&view=movie&movie_id=' . $movie->movieId);
            ?>

            <article class="listing-card">
                <div class="listing-card__poster">
                    <a href="<?= $detailRoute ?>" class="listing-card__poster-link">
                        <img src="<?= htmlspecialchars($movie->poster) ?>"
                             alt="Filmplakat <?= $this->escape($movie->title) ?>"
                             class="listing-card__poster-img">
                    </a>
                </div>

                <?php
                $titleHtml = '<h2 class="listing-card__title"><a href="' . $detailRoute . '" class="listing-card__title-link">' . $this->escape($movie->title) . '</a></h2>';
                ?>
                <?= LayoutHelper::render('utilities.truncate', [
                    'title'   => $titleHtml,
                    'content' => '<div class="listing-card__description">' . $movie->text . '</div>',
                    'link'    => $detailRoute,
                    'class'   => 'listing-card__content',
                ]) ?>

                <div class="listing-card__meta">
                    <div class="format-badges">
                        <?= LayoutHelper::render('movie.fsk', ['fsk' => $movie->fsk, 'href' => '/service/fsk-und-jugendschutz']) ?>
                        <?= LayoutHelper::render('booking.formats', $movie) ?>
                        <?= LayoutHelper::render('movie.duration', $movie->duration) ?>
                    </div>
                </div>

                <div class="listing-card__showtimes">
                    <?= LayoutHelper::render('booking.showtimes', [
                        'movie' => $movie,
                        'day'   => $this->highlightDate,
                    ]) ?>
                </div>
            </article>

        <?php endforeach; ?>
    </div>
</div>
