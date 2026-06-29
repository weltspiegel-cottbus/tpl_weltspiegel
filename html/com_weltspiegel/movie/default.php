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

<article class="detail u-flipped-title-container">
    <span class="u-flipped-title u-flipped-title--desktop-only">Programm</span>
    <div class="detail__inner">
        <?php if ($fskNum !== null): ?>
            <a href="/service/fsk-und-jugendschutz" class="detail__fsk-link">
                <img src="/images/site/fsk/FSK-<?= $fskNum ?>.png"
                     alt="FSK <?= $fskNum ?>"
                     class="detail__fsk-img">
            </a>
        <?php endif; ?>

        <h1 class="detail__title"><?= $this->escape($this->title) ?></h1>

        <div class="detail__poster">
            <img src="<?= htmlspecialchars($movie->poster) ?>"
                 alt="Filmplakat <?= $this->escape($movie->title) ?>"
                 class="detail__poster-img">
        </div>

        <div class="detail__details">
            <span><b>Dauer:</b> <?= htmlspecialchars($movie->duration) ?> Min.</span>
            <span><b>FSK:</b> <?= htmlspecialchars($movie->fsk) ?></span>
            <?php if (!empty($movie->genre)): ?>
                <span><b>Genre:</b> <?= htmlspecialchars($movie->genre) ?></span>
            <?php endif; ?>
        </div>

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
