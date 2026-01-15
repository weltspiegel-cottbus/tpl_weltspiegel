<?php

/**
 * Current Events Module
 * Template override for Weltspiegel template
 *
 * @package     Weltspiegel.Template
 * @copyright   Weltspiegel Cottbus
 * @license     MIT
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Router\Route;

/**
 * @var Joomla\Registry\Registry $params
 * @var array $events
 */

// Don't display anything if there are no events
if (empty($events) || !is_array($events)) {
    return;
}

/**
 * Helper function to render show times for an event
 */
function renderShowTimes($event, $detailRoute): void
{
    if (empty($event->shows)) {
        echo '<div class="event-poster-card__shows event-poster-card__empty">&nbsp;</div>';
        return;
    }

    $showsByDay = [];
    $now = time();

    // Group shows by day
    foreach ($event->shows as $show) {
        $showTime = strtotime($show->showStart);
        if ($showTime >= $now) {
            $day = date('Y-m-d', $showTime);
            if (!isset($showsByDay[$day])) {
                $showsByDay[$day] = [];
            }
            $showsByDay[$day][] = $show;
        }
    }

    // Sort days
    ksort($showsByDay);

    // Get first day with all shows
    if (empty($showsByDay)) {
        echo '<div class="event-poster-card__shows event-poster-card__empty">&nbsp;</div>';
        return;
    }

    $nextDay = array_key_first($showsByDay);
    $nextShows = $showsByDay[$nextDay];
    $hasMoreDays = count($showsByDay) > 1;

    $dayDate = new DateTime($nextDay);
    $today = new DateTime();
    $tomorrow = (new DateTime())->modify('+1 day');

    // Format date in German
    $formatter = new IntlDateFormatter('de_DE', IntlDateFormatter::NONE, IntlDateFormatter::NONE);
    $formatter->setPattern('EEE, dd.MM.');
    $formattedDate = $formatter->format($dayDate);

    // Check if it's today or tomorrow
    $dayLabel = $formattedDate;
    if ($dayDate->format('Y-m-d') === $today->format('Y-m-d')) {
        $dayLabel = 'Heute (' . $formattedDate . ')';
    } elseif ($dayDate->format('Y-m-d') === $tomorrow->format('Y-m-d')) {
        $dayLabel = 'Morgen (' . $formattedDate . ')';
    }

    echo '<div class="event-poster-card__shows">';
    echo '<div class="event-poster-card__day">' . htmlspecialchars($dayLabel) . '</div>';
    echo '<div class="event-poster-card__times">';

    foreach ($nextShows as $show) {
        $showDateTime = new DateTime($show->showStart);

        $layout = new FileLayout('booking.link');
        $layout->addIncludePath(JPATH_SITE . '/components/com_weltspiegel/layouts');
        echo $layout->render([
            'showId' => $show->showId,
            'label' => $showDateTime->format('H:i'),
            'options' => ['class' => 'event-poster-card__time-link']
        ]);

        if ($show !== end($nextShows)) {
            echo '<span>|</span>';
        }
    }

    echo '</div>';

    if ($hasMoreDays) {
        echo '<div class="event-poster-card__more">';
        echo '<a href="' . $detailRoute . '" class="event-poster-card__more-link">Weitere Tage</a>';
        echo '</div>';
    }

    echo '</div>';
}

/**
 * Helper function to render an event card
 */
function renderEventCard($id, $event): void
{
    $detailRoute = Route::_('index.php?option=com_weltspiegel&view=cinetixxitem&event_id=' . $id);
    ?>
    <article class="event-poster-card">
        <a href="<?= $detailRoute ?>" class="event-poster-card__link">
            <?php if (!empty($event->poster)): ?>
                <img src="<?= htmlspecialchars($event->poster) ?>"
                     alt="Filmplakat <?= htmlspecialchars($event->title) ?>"
                     class="event-poster-card__img">
            <?php endif; ?>
        </a>
        <?php renderShowTimes($event, $detailRoute); ?>
    </article>
    <?php
}

// Separate events into categories
$categorizedEvents = [
    '3D' => [],
    '2D' => [],
    'OmU' => [],
    'OV' => []
];

foreach ($events as $id => $event) {
    // Check language property for OmU or OV
    $isOmU = !empty($event->language) && stripos($event->language, 'OmU') !== false;
    $isOV = !empty($event->language) && stripos($event->language, 'OV') !== false;

    if ($isOmU) {
        $categorizedEvents['OmU'][$id] = $event;
    } elseif ($isOV) {
        $categorizedEvents['OV'][$id] = $event;
    } elseif (!empty($event->is3D)) {
        $categorizedEvents['3D'][$id] = $event;
    } else {
        $categorizedEvents['2D'][$id] = $event;
    }
}

// Define category configuration
$categoryConfig = [
    '3D' => ['title' => 'Aktuell in 3D', 'id' => 'events-3d', 'label' => '3D'],
    '2D' => ['title' => 'Aktuell in 2D', 'id' => 'events-2d', 'label' => '2D'],
    'OmU' => ['title' => 'Aktuell in OmU', 'id' => 'events-omu', 'label' => 'OmU'],
    'OV' => ['title' => 'Aktuell in OV', 'id' => 'events-ov', 'label' => 'OV']
];

// Count categories with events
$categoryCount = count(array_filter($categorizedEvents, fn($cat) => !empty($cat)));

?>
<div class="mod-current-events">
    <h1>AKTUELL IM WELTSPIEGEL COTTBUS</h1>
    <?php if ($categoryCount > 1): ?>
        <nav class="mod-current-events__nav">
            <ul class="mod-current-events__nav-list">
                <?php foreach ($categoryConfig as $key => $config): ?>
                    <?php if (!empty($categorizedEvents[$key])): ?>
                        <li>
                            <a href="#<?= $config['id'] ?>" class="mod-current-events__nav-link">
                                <?= htmlspecialchars($config['label']) ?>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </nav>
    <?php endif; ?>

    <?php foreach ($categoryConfig as $key => $config): ?>
        <?php if (!empty($categorizedEvents[$key])): ?>
            <section class="mod-current-events__section" id="<?= $config['id'] ?>">
                <h2 class="mod-current-events__section-title"><?= htmlspecialchars($config['title']) ?></h2>
                <div class="mod-current-events__grid">
                    <?php foreach ($categorizedEvents[$key] as $id => $event): ?>
                        <?php renderEventCard($id, $event); ?>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
