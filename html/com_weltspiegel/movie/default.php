<?php

/**
 * Single Movie View
 * Template override for Weltspiegel template
 *
 * @package     Weltspiegel.Template
 * @copyright   Weltspiegel Cottbus
 * @license     MIT
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

$movie = $this->item;
?>

<article class="detail u-flipped-title-container">
    <span class="u-flipped-title u-flipped-title--desktop-only">Programm</span>
    <div class="detail__inner">
        <?= LayoutHelper::render('utilities.back-link', ['href' => Route::_('index.php?option=com_weltspiegel&view=movies')]) ?>

        <h1 class="detail__title"><?= $this->escape($this->title) ?></h1>

        <div class="detail__poster">
            <img src="<?= htmlspecialchars($movie->poster) ?>"
                 alt="Filmplakat <?= $this->escape($movie->title) ?>"
                 class="detail__poster-img">
        </div>

        <div class="detail__meta">
            <div class="format-badges">
                <?= LayoutHelper::render('movie.fsk', ['fsk' => $movie->fsk, 'href' => '/service/fsk-und-jugendschutz']) ?>
                <?= LayoutHelper::render('booking.formats', $movie) ?>
                <?= LayoutHelper::render('movie.duration', $movie->duration) ?>
            </div>
        </div>

        <?php
        // Detail row deliberately limited to the genre: year and country were
        // dropped on the client's request (2026-08-20). Both are still delivered
        // by the model ($movie->year, $movie->country) should they ever return.
        // The wrapper is only rendered when there actually is a genre — its
        // margin would otherwise leave a stray gap, and Cinetixx frequently
        // delivers "-" as the genre.
        ?>
        <?php if (!empty($movie->genre) && $movie->genre !== '-'): ?>
            <div class="detail__details">
                <span><b>Genre:</b> <?= htmlspecialchars($movie->genre) ?></span>
            </div>
        <?php endif; ?>

        <div class="detail__showtimes">
            <?= LayoutHelper::render('booking.showtimes', $movie) ?>
        </div>

        <div class="detail__body">
            <?= $movie->text ?>
        </div>

        <?php if (!empty($movie->trailerId)): ?>
            <div class="detail__trailer">
                <?= LayoutHelper::render('youtube.embed', ['videoId' => $movie->trailerId]) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($movie->images)): ?>
            <div class="detail__gallery">
                <ul class="detail__gallery-list">
                    <?php foreach ($movie->images as $index => $image): ?>
                        <li class="detail__gallery-item">
                            <img src="<?= htmlspecialchars($image) ?>"
                                 alt="Filmbild <?= ($index + 1) ?> zu <?= $this->escape($movie->title) ?>"
                                 class="detail__gallery-img">
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</article>
