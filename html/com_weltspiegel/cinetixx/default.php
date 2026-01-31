<?php

/**
 * Cinetixx List View
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

<div class="cinetixx u-flipped-title-container">
    <h1 class="cinetixx__title u-flipped-title"><?= $this->escape($this->title) ?></h1>

    <div class="cinetixx__list">
        <?php foreach ($this->items as $id => $event): ?>

            <?php
            // Check if this is the first movie outside current week
            if (!$futureHeadingShown && !empty($event->shows)) {
                try {
                    $firstShowDate = new DateTime($event->shows[0]->showStart);
                    $daysUntilFirstShow = $now->diff($firstShowDate)->days;
                    if ($daysUntilFirstShow >= 7) {
                        echo '<h2 class="cinetixx__section-title">Demnächst</h2>';
                        $futureHeadingShown = true;
                    }
                } catch (Exception $e) {
                    // Skip if date parsing fails
                }
            }

            $detailRoute = Route::_('index.php?view=cinetixxitem&event_id=' . $id);
            ?>

            <article class="cinetixx-card">
                <div class="cinetixx-card__poster">
                    <a href="<?= $detailRoute ?>" class="cinetixx-card__poster-link">
                        <img src="<?= htmlspecialchars($event->poster) ?>"
                             alt="Filmplakat <?= $this->escape($event->title) ?>"
                             class="cinetixx-card__poster-img">
                    </a>
                </div>

                <div class="cinetixx-card__content">
                    <h2 class="cinetixx-card__title">
                        <a href="<?= $detailRoute ?>" class="cinetixx-card__title-link">
                            <?= $this->escape($event->title) ?>
                        </a>
                    </h2>

                    <?= LayoutHelper::render('utilities.truncate', [
                        'content' => $event->text,
                        'link'    => $detailRoute,
                        'height'  => '11rem',
                        'class'   => 'cinetixx-card__description',
                    ]) ?>
                </div>

                <div class="cinetixx-card__details">
                    Dauer: <?= htmlspecialchars($event->duration) ?> min,
                    Sprache: <?= htmlspecialchars($event->languageShort) ?>,
                    FSK: <?= htmlspecialchars($event->fsk) ?><?php if (!empty($event->genre) && $event->genre !== '-'): ?>,
                    Genre: <?= htmlspecialchars($event->genre) ?><?php endif; ?>
                </div>

                <div class="cinetixx-card__showbox">
                    <?= LayoutHelper::render('booking.showbox', $event) ?>
                </div>
            </article>

        <?php endforeach; ?>
    </div>
</div>
