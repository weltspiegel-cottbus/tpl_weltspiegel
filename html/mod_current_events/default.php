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

use Joomla\CMS\Router\Route;

/**
 * @var Joomla\Registry\Registry $params
 * @var array $movies
 */

if (empty($movies) || !is_array($movies)) {
    return;
}

$now = time();

$getCategory = static function ($format): string {
    $lang = strtolower($format->language ?? '');
    if (str_contains($lang, 'omu') || str_contains($lang, 'omü')) return 'OmU';
    if (str_contains($lang, 'ov'))  return 'OV';
    if ($format->is3D)              return '3D';
    return '2D';
};

$sections = ['3D' => [], '2D' => [], 'OmU' => [], 'OV' => []];

foreach ($movies as $movie) {
    foreach ($movie->formats as $format) {
        $category = $getCategory($format);

        foreach ($format->shows as $show) {
            $showTime = strtotime($show->showStart);
            if ($showTime < $now) {
                continue;
            }
            $day = date('Y-m-d', $showTime);

            if (!isset($sections[$category][$movie->movieId])) {
                $sections[$category][$movie->movieId] = [
                    'movie'      => $movie,
                    'showsByDay' => [],
                ];
            }

            $sections[$category][$movie->movieId]['showsByDay'][$day][] = $show;
        }
    }
}

foreach ($sections as &$sectionMovies) {
    foreach ($sectionMovies as &$data) {
        ksort($data['showsByDay']);
    }
}
unset($sectionMovies, $data);

$sections = array_filter($sections, fn($s) => !empty($s));

if (empty($sections)) {
    return;
}

$sectionConfig = [
    '3D'  => ['title' => 'Aktuell in 3D',  'id' => 'events-3d',  'label' => '3D'],
    '2D'  => ['title' => 'Aktuell in 2D',  'id' => 'events-2d',  'label' => '2D'],
    'OmU' => ['title' => 'Aktuell in OmU', 'id' => 'events-omu', 'label' => 'OmU'],
    'OV'  => ['title' => 'Aktuell in OV',  'id' => 'events-ov',  'label' => 'OV'],
];

$today    = (new DateTime())->format('Y-m-d');
$tomorrow = (new DateTime('+1 day'))->format('Y-m-d');
$formatter = new IntlDateFormatter('de_DE', IntlDateFormatter::NONE, IntlDateFormatter::NONE);
$formatter->setPattern('EEE, dd.MM.');

?>
<div class="mod-current-events">
    <h2>AKTUELL IM WELTSPIEGEL</h2>

    <?php if (count($sections) > 1): ?>
        <nav class="mod-current-events__nav">
            <ul class="mod-current-events__nav-list">
                <?php foreach ($sectionConfig as $key => $config): ?>
                    <?php if (isset($sections[$key])): ?>
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

    <?php foreach ($sectionConfig as $key => $config): ?>
        <?php if (empty($sections[$key])): continue; endif; ?>

        <section class="mod-current-events__section" id="<?= $config['id'] ?>">
            <h3 class="mod-current-events__section-title"><?= htmlspecialchars($config['title']) ?></h3>
            <div class="mod-current-events__grid">
                <?php foreach ($sections[$key] as $movieId => $data):
                    $movie       = $data['movie'];
                    $showsByDay  = $data['showsByDay'];
                    $detailRoute = Route::_('index.php?option=com_weltspiegel&view=movie&movie_id=' . $movie->movieId);

                    $nextDay     = array_key_first($showsByDay);
                    $nextShows   = $showsByDay[$nextDay];
                    $hasMoreDays = count($showsByDay) > 1;

                    $dayDate       = new DateTime($nextDay);
                    $formattedDate = $formatter->format($dayDate);

                    preg_match('/(\d+)/', $movie->fsk ?? '', $fskMatch);
                    $fskNum = isset($fskMatch[1]) ? (int) $fskMatch[1] : null;

                    if ($nextDay === $today) {
                        $dayLabel = 'Heute (' . $formattedDate . ')';
                    } elseif ($nextDay === $tomorrow) {
                        $dayLabel = 'Morgen (' . $formattedDate . ')';
                    } else {
                        $dayLabel = $formattedDate;
                    }
                ?>
                    <article class="event-poster-card">
                        <div class="event-poster-card__poster">
                            <a href="<?= $detailRoute ?>" class="event-poster-card__link">
                                <?php if (!empty($movie->poster)): ?>
                                    <img src="<?= htmlspecialchars($movie->poster) ?>"
                                         alt="Filmplakat <?= htmlspecialchars($movie->title) ?>"
                                         class="event-poster-card__img">
                                <?php endif; ?>
                            </a>
                            <?php if ($fskNum !== null): ?>
                                <span class="fsk-badge fsk-badge--<?= $fskNum ?>">FSK <?= $fskNum ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="event-poster-card__shows">
                            <div class="event-poster-card__day"><?= htmlspecialchars($dayLabel) ?></div>
                            <div class="event-poster-card__times">
                                <?php foreach ($nextShows as $i => $show):
                                    $showDateTime = new DateTime($show->showStart);
                                ?>
                                    <a href="<?= htmlspecialchars($show->bookingLink) ?>"
                                       class="event-poster-card__time-link"><?= $showDateTime->format('H:i') ?></a>
                                    <?php if ($i < count($nextShows) - 1): ?>
                                        <span>|</span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
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
    <?php endforeach; ?>
</div>
