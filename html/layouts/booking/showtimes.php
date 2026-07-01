<?php

/**
 * Showtimes box layout
 * Displays all shows across all formats of a movie in a sliding 7-day viewport.
 *
 * Desktop: one grid per week, one cell-row per dimension the movie actually has.
 * A "3D" bookmark label always appears when the movie has 3D shows; "2D" only
 * appears alongside it (i.e. only for movies mixing 2D and 3D) — a 2D-only
 * movie gets a single, unlabeled row. Mobile keeps a single combined list
 * (formatCategory: language marker, else dimension) with badge, time, hall
 * shown horizontally in that order.
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

// Flatten shows from all formats, annotating each with its dimension (2D/3D) and
// an optional language marker (OmU/OV). "formatCategory" is the legacy combined
// label (language marker takes priority, else dimension) used by the mobile list.
$getDimension = static fn($format): string => !empty($format->is3D) ? '3D' : '2D';

$getLanguageMarker = static function ($format): ?string {
    $lang = strtolower($format->language ?? '');
    if (str_contains($lang, 'omu') || str_contains($lang, 'omü')) return 'OmU';
    if (str_contains($lang, 'ov'))                                  return 'OV';
    return null;
};

$allShows = [];
foreach ($movie->formats as $format) {
    $dimension      = $getDimension($format);
    $languageMarker = $getLanguageMarker($format);

    foreach ($format->shows as $show) {
        $annotated                 = clone $show;
        $annotated->dimension      = $dimension;
        $annotated->languageMarker = $languageMarker;
        $annotated->formatCategory = $languageMarker ?? $dimension;
        $allShows[] = $annotated;
    }
}

usort($allShows, fn($a, $b) => strcmp($a->showStart, $b->showStart));

// Desktop grid: one cell-row per dimension the movie actually has (2D and/or
// 3D). The "3D" bookmark label always appears when the movie has 3D shows.
// The "2D" label only appears alongside it (i.e. only when the movie mixes
// 2D and 3D) — a movie with only 2D shows gets a plain, unlabeled row. Per-show
// badges next to the hall only ever show the language marker (OmU/OV): the
// row label (if any) already conveys 2D/3D.
$movieHas2D = false;
$movieHas3D = false;
foreach ($movie->formats as $format) {
    if (!empty($format->is3D)) {
        $movieHas3D = true;
    } else {
        $movieHas2D = true;
    }
}

$sections = [];
if ($movieHas2D) {
    $sections[] = ['key' => 'shows2D', 'label' => $movieHas3D ? '2D' : null];
}
if ($movieHas3D) {
    $sections[] = ['key' => 'shows3D', 'label' => '3D'];
}

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
                'date'    => clone $date,
                'shows'   => $dayShows,
                'shows2D' => array_values(array_filter($dayShows, fn($s) => $s->dimension === '2D')),
                'shows3D' => array_values(array_filter($dayShows, fn($s) => $s->dimension === '3D')),
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
                    <!-- Header row (rendered once, even with multiple dimension rows below).
                         The first/last cells get an explicit corner radius (instead of relying
                         on the grid's own overflow: hidden) so the dimension label below can
                         poke out past the left edge without being clipped. -->
                    <?php $dayIndex = 0; ?>
                    <?php foreach ($viewport['days'] as $dayData):
                        $headerCornerClass = $dayIndex === 0 ? ' showbox-day-header--corner-tl' : ($dayIndex === 6 ? ' showbox-day-header--corner-tr' : '');
                    ?>
                        <div class="showbox-day-header<?= $headerCornerClass ?>">
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

                    <!-- One cell-row per dimension (2D and/or 3D — whichever the movie has).
                         Every row always renders all 7 days — even if a given week has no
                         shows for that dimension — so the grid height stays stable while
                         paging through weeks (no layout shift). -->
                    <?php $lastSectionIndex = array_key_last($sections); ?>
                    <?php foreach ($sections as $sectionIndex => $section):
                        $sectionKey    = $section['key'];
                        $isLastSection = $sectionIndex === $lastSectionIndex;
                        $dayIndex      = 0;
                    ?>
                        <?php foreach ($viewport['days'] as $dayData):
                            $isAltColumn = $dayIndex % 2 === 1;
                            $cellClasses = 'showbox-day-cell' . ($isAltColumn ? ' showbox-day-cell--alt' : '');
                            if ($isLastSection) {
                                $cellClasses .= $dayIndex === 0 ? ' showbox-day-cell--corner-bl' : ($dayIndex === 6 ? ' showbox-day-cell--corner-br' : '');
                            }
                        ?>
                            <div class="<?= $cellClasses ?>">
                                <?php if ($dayIndex === 0 && $section['label'] !== null): ?>
                                    <span class="showbox-dimension-label showbox-dimension-label--<?= strtolower($section['label']) ?>"><?= $section['label'] ?></span>
                                <?php endif; ?>
                                <?php foreach ($dayData[$sectionKey] as $show):
                                    $showDateTime = new DateTime($show->showStart);
                                    $bookingStart = new DateTime($show->bookingStart);
                                    $bookingEnd   = new DateTime($show->bookingEnd);
                                    $isBookable   = ($now >= $bookingStart && $now <= $bookingEnd);

                                    // The row label already conveys 2D/3D, so the per-show badge
                                    // only ever needs the language marker (OmU/OV), if any.
                                    $badgeText = $show->languageMarker;
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
                                            <span class="showbox-hall-row">
                                                <span class="showbox-hall"><?= htmlspecialchars($show->hall) ?></span>
                                                <?php if ($badgeText !== null): ?><span class="showbox-format showbox-format--<?= strtolower($badgeText) ?>"><?= htmlspecialchars($badgeText) ?></span><?php endif; ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php $dayIndex++; ?>
                        <?php endforeach; ?>
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
                            <?php if ($show->formatCategory !== '2D'): ?>
                                <span class="showbox-format showbox-format--<?= strtolower($show->formatCategory) ?>"><?= htmlspecialchars($show->formatCategory) ?></span>
                            <?php endif; ?>
                            <span class="showbox-show__details">
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
                                    <span class="showbox-hall"><?= htmlspecialchars($show->hall) ?></span>
                                <?php endif; ?>
                            </span>
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
