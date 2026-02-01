<?php

/**
 * Single Cinetixx Item View
 * Template override for Weltspiegel template
 *
 * @package     Weltspiegel.Template
 * @copyright   Weltspiegel Cottbus
 * @license     MIT
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;

$event = $this->item;
?>

<article class="cinetixxitem u-flipped-title-container">
    <span class="u-flipped-title u-flipped-title--desktop-only">Programm</span>
    <h1 class="cinetixxitem__title"><?= $this->escape($this->title) ?></h1>

    <div class="cinetixxitem__poster">
        <img src="<?= htmlspecialchars($event->poster) ?>"
             alt="Filmplakat <?= $this->escape($event->title) ?>"
             class="cinetixxitem__poster-img">
    </div>

    <div class="cinetixxitem__details">
        Dauer: <?= htmlspecialchars($event->duration) ?>,
        Sprache: <?= htmlspecialchars($event->languageShort) ?>,
        FSK: <?= htmlspecialchars($event->fsk) ?>
    </div>

    <div class="cinetixxitem__showbox">
        <?= LayoutHelper::render('booking.showbox', $event) ?>
    </div>

    <div class="cinetixxitem__description">
        <?= $event->text ?>
    </div>

    <?php if (!empty($event->trailerId)): ?>
        <div class="cinetixxitem__trailer">
            <?= LayoutHelper::render('youtube.embed', ['videoId' => $event->trailerId]) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($event->images)): ?>
        <div class="cinetixxitem__gallery">
            <ul class="cinetixxitem__gallery-list">
                <?php foreach ($event->images as $index => $image): ?>
                    <li class="cinetixxitem__gallery-item">
                        <img src="<?= htmlspecialchars($image) ?>"
                             alt="Filmbild <?= ($index + 1) ?> zu <?= $this->escape($event->title) ?>"
                             class="cinetixxitem__gallery-img">
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</article>
