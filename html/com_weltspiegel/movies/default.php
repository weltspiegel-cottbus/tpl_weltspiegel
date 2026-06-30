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

$now = new DateTime();
$futureHeadingShown = false;

?>

<div class="listing u-flipped-title-container">
    <h1 class="listing__title u-flipped-title"><?= $this->escape($this->title) ?></h1>

    <div class="listing__list">
        <?php foreach ($this->items as $movie): ?>

            <?php
            // Find earliest show across all formats for "Demnächst" detection
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
                $daysUntilFirstShow = $now->diff($firstShowDate)->days;
                if ($daysUntilFirstShow >= 7) {
                    echo '<h2 class="listing__section-title">Demnächst</h2>';
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
                    <?= LayoutHelper::render('booking.showtimes', $movie) ?>
                </div>
            </article>

        <?php endforeach; ?>
    </div>
</div>
