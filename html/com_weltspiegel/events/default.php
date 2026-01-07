<?php

/**
 * Events List View
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

<div class="events">
    <h1 class="events__title"><?= $this->escape($this->title) ?></h1>

    <div class="events__list">
        <?php foreach ($this->items as $id => $event): ?>

            <?php
            // Check if this is the first movie outside current week
            if (!$futureHeadingShown && !empty($event->shows)) {
                try {
                    $firstShowDate = new DateTime($event->shows[0]->showStart);
                    $daysUntilFirstShow = $now->diff($firstShowDate)->days;
                    if ($daysUntilFirstShow >= 7) {
                        echo '<h2 class="events__section-title">Demnächst</h2>';
                        $futureHeadingShown = true;
                    }
                } catch (Exception $e) {
                    // Skip if date parsing fails
                }
            }

            $detailRoute = Route::_('index.php?view=event&event_id=' . $id);
            ?>

            <article class="event-card">
                <div class="event-card__poster">
                    <a href="<?= $detailRoute ?>" class="event-card__poster-link">
                        <img src="<?= htmlspecialchars($event->poster) ?>"
                             alt="Filmplakat <?= $this->escape($event->title) ?>"
                             class="event-card__poster-img">
                    </a>
                </div>

                <h2 class="event-card__title">
                    <a href="<?= $detailRoute ?>" class="event-card__title-link">
                        <?= $this->escape($event->title) ?>
                    </a>
                </h2>

                <div class="event-card__description">
                    <?= $event->text ?>
                </div>

                <div class="event-card__details">
                    Dauer: <?= htmlspecialchars($event->duration) ?>,
                    Sprache: <?= htmlspecialchars($event->languageShort) ?>,
                    FSK: <?= htmlspecialchars($event->fsk) ?>
                </div>

                <div class="event-card__showbox">
                    <?= LayoutHelper::render('booking.showbox', $event) ?>
                </div>
            </article>

        <?php endforeach; ?>
    </div>
</div>
