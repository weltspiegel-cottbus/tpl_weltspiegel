<?php

/**
 * Single Event View
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

<article class="event">
    <h1 class="event__title"><?= $this->escape($this->title) ?></h1>

    <div class="event__poster">
        <img src="<?= htmlspecialchars($event->poster) ?>"
             alt="Filmplakat <?= $this->escape($event->title) ?>"
             class="event__poster-img">
    </div>

    <div class="event__details">
        Dauer: <?= htmlspecialchars($event->duration) ?>,
        Sprache: <?= htmlspecialchars($event->languageShort) ?>,
        FSK: <?= htmlspecialchars($event->fsk) ?>
    </div>

    <div class="event__showbox">
        <?= LayoutHelper::render('booking.showbox', $event) ?>
    </div>

    <div class="event__description">
        <?= $event->text ?>
    </div>

    <?php if (!empty($event->trailerId)): ?>
        <div class="event__trailer">
            <?= LayoutHelper::render('youtube.embed', ['videoId' => $event->trailerId]) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($event->images)): ?>
        <div class="event__gallery">
            <ul class="event__gallery-list">
                <?php foreach ($event->images as $index => $image): ?>
                    <li class="event__gallery-item">
                        <img src="<?= htmlspecialchars($image) ?>"
                             alt="Filmbild <?= ($index + 1) ?> zu <?= $this->escape($event->title) ?>"
                             class="event__gallery-img">
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</article>
