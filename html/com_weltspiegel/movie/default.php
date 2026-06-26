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

$movie = $this->item;

preg_match('/(\d+)/', $movie->fsk ?? '', $fskMatch);
$fskNum = isset($fskMatch[1]) ? (int) $fskMatch[1] : null;
?>

<article class="movieitem u-flipped-title-container">
    <span class="u-flipped-title u-flipped-title--desktop-only">Programm</span>
    <div class="movieitem__inner">
        <?php if ($fskNum !== null): ?>
            <a href="/service/fsk-und-jugendschutz" class="movieitem__fsk-link">
                <img src="/images/site/fsk/FSK-<?= $fskNum ?>.png"
                     alt="FSK <?= $fskNum ?>"
                     class="movieitem__fsk-img">
            </a>
        <?php endif; ?>

        <h1 class="movieitem__title"><?= $this->escape($this->title) ?></h1>

        <div class="movieitem__poster">
            <img src="<?= htmlspecialchars($movie->poster) ?>"
                 alt="Filmplakat <?= $this->escape($movie->title) ?>"
                 class="movieitem__poster-img">
        </div>

        <div class="movieitem__details">
            <span><b>Dauer:</b> <?= htmlspecialchars($movie->duration) ?> Min.</span>
            <span><b>FSK:</b> <?= htmlspecialchars($movie->fsk) ?></span>
            <?php if (!empty($movie->genre)): ?>
                <span><b>Genre:</b> <?= htmlspecialchars($movie->genre) ?></span>
            <?php endif; ?>
        </div>

        <div class="movieitem__showtimes">
            <?= LayoutHelper::render('booking.showtimes', $movie) ?>
        </div>

        <div class="movieitem__description">
            <?= $movie->text ?>
        </div>

        <?php if (!empty($movie->trailerId)): ?>
            <div class="movieitem__trailer">
                <?= LayoutHelper::render('youtube.embed', ['videoId' => $movie->trailerId]) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($movie->images)): ?>
            <div class="movieitem__gallery">
                <ul class="movieitem__gallery-list">
                    <?php foreach ($movie->images as $index => $image): ?>
                        <li class="movieitem__gallery-item">
                            <img src="<?= htmlspecialchars($image) ?>"
                                 alt="Filmbild <?= ($index + 1) ?> zu <?= $this->escape($movie->title) ?>"
                                 class="movieitem__gallery-img">
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</article>
