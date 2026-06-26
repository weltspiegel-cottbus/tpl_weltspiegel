<?php

/**
 * Showtimes box layout
 * Displays all shows across all formats of a movie in a sliding 7-day viewport.
 *
 * @package     Weltspiegel.Template
 * @copyright   Weltspiegel Cottbus
 * @license     MIT
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;

$movie = $displayData;

if (empty($movie->formats) || !is_array($movie->formats)) {
    return;
}

// Flatten shows from all formats, annotating each with its display category
$getCategory = static function ($format): string {
    $lang = strtolower($format->language ?? '');
    if (str_contains($lang, 'omu') || str_contains($lang, 'omü')) return 'OmU';
    if (str_contains($lang, 'ov'))                                  return 'OV';
    if ($format->is3D)                                              return '3D';
    return '2D';
};

$allShows = [];
foreach ($movie->formats as $format) {
    $category = $getCategory($format);
    foreach ($format->shows as $show) {
        $annotated = clone $show;
        $annotated->formatCategory = $category;
        $allShows[] = $annotated;
    }
}

usort($allShows, fn($a, $b) => strcmp($a->showStart, $b->showStart));

if (empty($allShows)) {
    return;
}

// Get current date/time (Europe/Berlin to match show data)
$timezone = new DateTimeZone('Europe/Berlin');
$now = new DateTime('now', $timezone);

$dateFrom = (new DateTime('now', $timezone))->setTime(0, 0);
$dateTo = (clone $dateFrom)->modify('+7 days');
$startingFromToday = true;

// Jump to the week containing the first show if it's beyond the current week
try {
    $firstShowStart = new DateTime($allShows[0]->showStart);

    if ($firstShowStart > $dateTo) {
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

$lastShow = end($allShows);
$lastShowDate = new DateTime($lastShow->showStart);

$countShowsInRange = function ($shows, $from, $to): int {
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

// Build viewports (one per week that has shows, empty weeks skipped)
$viewports = [];

while ($dateFrom <= $lastShowDate) {
    $showCount = $countShowsInRange($allShows, $dateFrom, $dateTo);

    if ($showCount > 0) {
        $viewport = [
            'dateFrom'     => clone $dateFrom,
            'dateTo'       => clone $dateTo,
            'days'         => [],
            'isFirstWeek'  => $startingFromToday,
            'label'        => $dateFrom->format('d.m.') . ' – ' . (clone $dateTo)->modify('-1 day')->format('d.m.Y'),
        ];

        $date = clone $dateFrom;
        for ($i = 0; $i < 7; $i++) {
            $midnight = (clone $date)->modify('+1 day');

            $dayShows = [];
            foreach ($allShows as $show) {
                try {
                    $showtime = new DateTime($show->showStart);
                    if ($showtime > $date && $showtime < $midnight) {
                        $dayShows[] = $show;
                    }
                } catch (Exception $e) {
                    continue;
                }
            }

            $viewport['days'][$date->format('Y-m-d')] = [
                'date'  => clone $date,
                'shows' => $dayShows,
            ];

            $date->modify('+1 day');
        }

        $viewports[] = $viewport;
    }

    $dateFrom = $dateTo;
    $dateTo = (clone $dateTo)->modify('+7 days');
    $startingFromToday = false;
}

if (empty($viewports)) {
    return;
}

$showtimesId  = 'showtimes-' . uniqid();
$hasNavigation = count($viewports) > 1;

$formatterDay  = new IntlDateFormatter('de_DE', IntlDateFormatter::NONE, IntlDateFormatter::NONE, 'Europe/Berlin');
$formatterDay->setPattern('EEE');
$formatterDate = new IntlDateFormatter('de_DE', IntlDateFormatter::NONE, IntlDateFormatter::NONE, 'Europe/Berlin');
$formatterDate->setPattern('dd.MM.');

?>

<div class="showbox" id="<?= $showtimesId ?>" <?= $hasNavigation ? 'data-has-navigation' : '' ?>>
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
            <div class="showbox-viewport <?= $viewportIndex === 0 ? 'active' : '' ?>"
                 data-viewport="<?= $viewportIndex ?>"
                 data-label="<?= htmlspecialchars($viewport['label']) ?>">
                <div class="showbox-grid">
                    <!-- Header row -->
                    <?php $dayIndex = 0; ?>
                    <?php foreach ($viewport['days'] as $dayData): ?>
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
                    <?php foreach ($viewport['days'] as $dayData): ?>
                        <div class="showbox-day-cell">
                            <?php foreach ($dayData['shows'] as $show):
                                $showDateTime  = new DateTime($show->showStart);
                                $bookingStart  = new DateTime($show->bookingStart);
                                $bookingEnd    = new DateTime($show->bookingEnd);
                                $isBookable    = ($now >= $bookingStart && $now <= $bookingEnd);
                            ?>
                                <div class="showbox-show">
                                    <?php if ($isBookable): ?>
                                        <?= LayoutHelper::render('booking.link', [
                                            'showId'  => $show->showId,
                                            'label'   => $showDateTime->format('H:i'),
                                            'options' => ['class' => 'showbox-time-link'],
                                        ]) ?>
                                    <?php else: ?>
                                        <span class="showbox-time-text"><?= $showDateTime->format('H:i') ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($show->hall)): ?>
                                        <span class="showbox-hall">
                                            <?= htmlspecialchars($show->hall) ?>
                                            <span class="showbox-format"><?= htmlspecialchars($show->formatCategory) ?></span>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
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
            foreach ($viewport['days'] as $dayData):
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
                    <?php foreach ($dayData['shows'] as $show):
                        $showDateTime = new DateTime($show->showStart);
                        $bookingStart = new DateTime($show->bookingStart);
                        $bookingEnd   = new DateTime($show->bookingEnd);
                        $isBookable   = ($now >= $bookingStart && $now <= $bookingEnd);
                    ?>
                        <span class="showbox-show">
                            <?php if ($isBookable): ?>
                                <?= LayoutHelper::render('booking.link', [
                                    'showId'  => $show->showId,
                                    'label'   => $showDateTime->format('H:i'),
                                    'options' => ['class' => 'showbox-time-link'],
                                ]) ?>
                            <?php else: ?>
                                <span class="showbox-time-text"><?= $showDateTime->format('H:i') ?></span>
                            <?php endif; ?>
                            <?php if (!empty($show->hall)): ?>
                                <span class="showbox-hall">
                                    <?= htmlspecialchars($show->hall) ?>
                                    <span class="showbox-format"><?= htmlspecialchars($show->formatCategory) ?></span>
                                </span>
                            <?php endif; ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </li>
        <?php
                endif;
                $isFirstDay = false;
            endforeach;
        endforeach;
        ?>
        </ul>
    </div>
</div>
