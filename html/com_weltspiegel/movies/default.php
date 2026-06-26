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

<div class="movies u-flipped-title-container">
    <h1 class="movies__title u-flipped-title"><?= $this->escape($this->title) ?></h1>

    <div class="movies__list">
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
                    echo '<h2 class="movies__section-title">Demnächst</h2>';
                    $futureHeadingShown = true;
                }
            }

            $detailRoute = Route::_('index.php?option=com_weltspiegel&view=movie&movie_id=' . $movie->movieId);
            ?>

            <article class="movies-card">
                <div class="movies-card__poster">
                    <a href="<?= $detailRoute ?>" class="movies-card__poster-link">
                        <img src="<?= htmlspecialchars($movie->poster) ?>"
                             alt="Filmplakat <?= $this->escape($movie->title) ?>"
                             class="movies-card__poster-img">
                    </a>
                </div>

                <?php
                $titleHtml = '<h2 class="movies-card__title"><a href="' . $detailRoute . '" class="movies-card__title-link">' . $this->escape($movie->title) . '</a></h2>';
                ?>
                <?= LayoutHelper::render('utilities.truncate', [
                    'title'   => $titleHtml,
                    'content' => '<div class="movies-card__description">' . $movie->text . '</div>',
                    'link'    => $detailRoute,
                    'class'   => 'movies-card__content',
                ]) ?>

                <div class="movies-card__details">
                    Dauer: <?= htmlspecialchars($movie->duration) ?> min,
                    FSK: <?= htmlspecialchars($movie->fsk) ?><?php if (!empty($movie->genre) && $movie->genre !== '-'): ?>,
                    Genre: <?= htmlspecialchars($movie->genre) ?><?php endif; ?>
                </div>

                <div class="movies-card__showtimes">
                    <?= LayoutHelper::render('booking.showtimes', $movie) ?>
                </div>
            </article>

        <?php endforeach; ?>
    </div>
</div>
