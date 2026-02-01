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

// Pre-calculate upcoming shows and filter out events without any
$now = time();
$eventsWithShows = [];

foreach ($events as $id => $event) {
    if (empty($event->shows)) {
        continue;
    }

    $showsByDay = [];
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

    // Only include events that have upcoming shows
    if (!empty($showsByDay)) {
        ksort($showsByDay);
        $eventsWithShows[$id] = [
            'event' => $event,
            'showsByDay' => $showsByDay
        ];
    }
}

// Don't display anything if no events have upcoming shows
if (empty($eventsWithShows)) {
    return;
}

// Separate events into categories
$categorizedEvents = [
    '3D' => [],
    '2D' => [],
    'OmU' => [],
    'OV' => []
];

foreach ($eventsWithShows as $id => $data) {
    $event = $data['event'];

    // Check language property for OmU or OV
    $isOmU = !empty($event->language) && stripos($event->language, 'OmU') !== false;
    $isOV = !empty($event->language) && stripos($event->language, 'OV') !== false;

    if ($isOmU) {
        $categorizedEvents['OmU'][$id] = $data;
    } elseif ($isOV) {
        $categorizedEvents['OV'][$id] = $data;
    } elseif (!empty($event->is3D)) {
        $categorizedEvents['3D'][$id] = $data;
    } else {
        $categorizedEvents['2D'][$id] = $data;
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
                    <?php foreach ($categorizedEvents[$key] as $id => $data):
                        $event = $data['event'];
                        $showsByDay = $data['showsByDay'];
                        $detailRoute = Route::_('index.php?option=com_weltspiegel&view=cinetixxitem&event_id=' . $id);

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
                    ?>
                        <article class="event-poster-card">
                            <a href="<?= $detailRoute ?>" class="event-poster-card__link">
                                <?php if (!empty($event->poster)): ?>
                                    <img src="<?= htmlspecialchars($event->poster) ?>"
                                         alt="Filmplakat <?= htmlspecialchars($event->title) ?>"
                                         class="event-poster-card__img">
                                <?php endif; ?>
                            </a>
                            <div class="event-poster-card__shows">
                                <div class="event-poster-card__day"><?= htmlspecialchars($dayLabel) ?></div>
                                <div class="event-poster-card__times">
                                    <?php foreach ($nextShows as $i => $show):
                                        $showDateTime = new DateTime($show->showStart);
                                        $layout = new FileLayout('booking.link');
                                        $layout->addIncludePath(JPATH_SITE . '/components/com_weltspiegel/layouts');
                                        echo $layout->render([
                                            'showId' => $show->showId,
                                            'label' => $showDateTime->format('H:i'),
                                            'options' => ['class' => 'event-poster-card__time-link']
                                        ]);
                                        if ($i < count($nextShows) - 1) {
                                            echo '<span>|</span>';
                                        }
                                    endforeach; ?>
                                </div>
                                <?php if ($hasMoreDays): ?>
                                    <div class="event-poster-card__more">
                                        <a href="<?= $detailRoute ?>" class="event-poster-card__more-link">Weitere Tage</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
