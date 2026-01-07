<?php

/**
 * Layout for displaying showtimes with sliding 7-day viewport navigation
 * Template override for Weltspiegel template
 *
 * @package     Weltspiegel.Template
 * @copyright   Weltspiegel Cottbus
 * @license     MIT
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;

/**
 * @var object $event Event object with shows array
 */
$event = $displayData;

// Return early if no shows
if (empty($event->shows) || !is_array($event->shows)) {
    return;
}

// Get current date/time (use Europe/Berlin timezone to match show data)
$timezone = new DateTimeZone('Europe/Berlin');
$now = new DateTime('now', $timezone);
$today = $now->format('Y-m-d');

// Initial date range: today + 7 days
$dateFrom = (new DateTime('now', $timezone))->setTime(0, 0);
$dateTo = (clone $dateFrom)->modify('+7 days');
$startingFromToday = true;

// Check if first show is beyond current week
if (!empty($event->shows)) {
    try {
        $firstShowStart = new DateTime($event->shows[0]->showStart);

        if ($firstShowStart > $dateTo) {
            // Jump to the week containing the first show, aligned to current weekday
            $firstShowtimeWeekday = $firstShowStart->format('D');
            $currentWeekday = (new DateTime())->format('D');

            if ($firstShowtimeWeekday !== $currentWeekday) {
                $firstShowStart->modify('last ' . $currentWeekday);
            }
            $firstShowStart->setTime(0, 0);

            $dateFrom = $firstShowStart;
            $dateTo = (clone $dateFrom)->modify('+7 days');
            $startingFromToday = false;
        }
    } catch (Exception $e) {
        // Continue with default date range
    }
}

// Get the last show date (shows are sorted)
$lastShow = end($event->shows);
$lastShowDate = new DateTime($lastShow->showStart);

// Build viewports (weeks with shows)
$viewports = [];

// Helper function to count shows in a date range
$countShowsInRange = function($shows, $from, $to) {
    $count = 0;
    foreach ($shows as $show) {
        try {
            $showtime = new DateTime($show->showStart);
            if ($showtime > $from && $showtime < $to) {
                $count++;
            }
        } catch (Exception $e) {
            continue;
        }
    }
    return $count;
};

// Build weeks until we pass the last show (skip empty weeks)
while ($dateFrom <= $lastShowDate) {
    // Check if this week has any shows
    $showCount = $countShowsInRange($event->shows, $dateFrom, $dateTo);

    // Only create viewport if there are shows this week
    if ($showCount > 0) {
    $viewport = [
        'dateFrom' => clone $dateFrom,
        'dateTo' => clone $dateTo,
        'days' => [],
        'isFirstWeek' => $startingFromToday,
        'label' => $dateFrom->format('d.m.') . ' - ' . (clone $dateTo)->modify('-1 day')->format('d.m.Y')
    ];

    // Build 7 days for this viewport
    $date = clone $dateFrom;
    for ($i = 0; $i < 7; $i++) {
        $midnight = (clone $date)->modify('+1 day');
        $dayKey = $date->format('Y-m-d');

        $dayShows = [];
        foreach ($event->shows as $show) {
            try {
                $showtime = new DateTime($show->showStart);
                if ($showtime > $date && $showtime < $midnight) {
                    $dayShows[] = $show;
                }
            } catch (Exception $e) {
                continue;
            }
        }

        $viewport['days'][$dayKey] = [
            'date' => clone $date,
            'shows' => $dayShows
        ];

        $date->modify('+1 day');
    }

        $viewports[] = $viewport;
    }

    // Move to next week (whether we added a viewport or not)
    $dateFrom = $dateTo;
    $dateTo = (clone $dateTo)->modify('+7 days');
    $startingFromToday = false;
}

// Return early if no viewports (no shows in any range)
if (empty($viewports)) {
    return;
}

// Generate unique ID for this showbox instance
$showboxId = 'showbox-' . uniqid();
$hasNavigation = count($viewports) > 1;

// Format date labels (with Europe/Berlin timezone)
$formatterDay = new IntlDateFormatter('de_DE', IntlDateFormatter::NONE, IntlDateFormatter::NONE, 'Europe/Berlin');
$formatterDay->setPattern('EEE');
$formatterDate = new IntlDateFormatter('de_DE', IntlDateFormatter::NONE, IntlDateFormatter::NONE, 'Europe/Berlin');
$formatterDate->setPattern('dd.MM.');

?>

<div class="showbox" id="<?= $showboxId ?>" <?= $hasNavigation ? 'data-has-navigation' : '' ?>>
    <!-- Desktop: Grid Layout with Navigation -->
    <div class="showbox-desktop">
        <?php if ($hasNavigation): ?>
            <div class="showbox-navigation">
                <button type="button" class="showbox-nav-btn" data-action="prev" aria-label="Vorherige Woche">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <span class="showbox-viewport-info"></span>
                <button type="button" class="showbox-nav-btn" data-action="next" aria-label="Nächste Woche">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        <?php endif; ?>

        <?php foreach ($viewports as $viewportIndex => $viewport): ?>
            <div class="showbox-viewport <?= $viewportIndex === 0 ? 'active' : '' ?>" data-viewport="<?= $viewportIndex ?>" data-label="<?= htmlspecialchars($viewport['label']) ?>">
                <div class="showbox-grid">
                    <!-- Header row (day labels) -->
                    <?php $dayIndex = 0; ?>
                    <?php foreach ($viewport['days'] as $dayKey => $dayData): ?>
                        <div class="showbox-day-header">
                            <?php if ($dayIndex === 0 && $viewport['isFirstWeek']): ?>
                                <span class="showbox-day-header-label">Heute</span>
                            <?php else: ?>
                                <span class="showbox-day-header-label">
                                    <?= $formatterDay->format($dayData['date']) ?><br><?= $formatterDate->format($dayData['date']) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <?php $dayIndex++; ?>
                    <?php endforeach; ?>

                    <!-- Showtime cells -->
                    <?php foreach ($viewport['days'] as $dayKey => $dayData): ?>
                        <div class="showbox-day-cell">
                            <?php if (!empty($dayData['shows'])): ?>
                                <?php foreach ($dayData['shows'] as $show): ?>
                                    <?php
                                    $showDateTime = new DateTime($show->showStart);
                                    $bookingStart = new DateTime($show->bookingStart);
                                    $bookingEnd = new DateTime($show->bookingEnd);
                                    $isBookable = ($now >= $bookingStart && $now <= $bookingEnd);
                                    ?>
                                    <?php if ($isBookable): ?>
                                        <?= LayoutHelper::render('booking.link', [
                                            'showId' => $show->showId,
                                            'label' => $showDateTime->format('H:i'),
                                            'options' => ['class' => 'showbox-time-link']
                                        ]) ?>
                                    <?php else: ?>
                                        <span class="showbox-time-text"><?= $showDateTime->format('H:i') ?></span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Mobile: Vertical List (All viewports combined) -->
    <div class="showbox-mobile">
        <ul class="showbox-mobile-list">
        <?php
        $isFirstDay = true;
        foreach ($viewports as $viewport):
            $dayIndex = 0;
            foreach ($viewport['days'] as $dayKey => $dayData):
                if (!empty($dayData['shows'])):
        ?>
            <li class="showbox-mobile-item">
                <div class="showbox-mobile-day">
                    <?php if ($isFirstDay && $viewport['isFirstWeek']): ?>
                        Heute
                    <?php else: ?>
                        <?= $formatterDay->format($dayData['date']) ?>, <?= $formatterDate->format($dayData['date']) ?>
                    <?php endif; ?>
                </div>
                <div class="showbox-mobile-times">
                    <?php foreach ($dayData['shows'] as $show): ?>
                        <?php
                        $showDateTime = new DateTime($show->showStart);
                        $bookingStart = new DateTime($show->bookingStart);
                        $bookingEnd = new DateTime($show->bookingEnd);
                        $isBookable = ($now >= $bookingStart && $now <= $bookingEnd);
                        ?>
                        <?php if ($isBookable): ?>
                            <?= LayoutHelper::render('booking.link', [
                                'showId' => $show->showId,
                                'label' => $showDateTime->format('H:i'),
                                'options' => ['class' => 'showbox-time-link']
                            ]) ?>
                        <?php else: ?>
                            <span class="showbox-time-text"><?= $showDateTime->format('H:i') ?></span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </li>
        <?php
                endif;
                $isFirstDay = false;
                $dayIndex++;
            endforeach;
        endforeach;
        ?>
        </ul>
    </div>
</div>
