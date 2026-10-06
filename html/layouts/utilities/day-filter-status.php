<?php

/**
 * Day Filter Status Layout
 * One line under the chip row that says what the page shows and how much lies
 * beyond it.
 *
 * The chips only offer "Heute" and "Morgen", and that was read as the horizon of
 * the programme: visitors concluded nothing else was on. This line spells out
 * the scope in words — "Alle Tage: Vorstellungen bis Mittwoch, 14. Oktober" — and in
 * the filtered state names the day on screen with the way back in the same
 * sentence. No film count on purpose: it adds a number to compare, not an answer.
 *
 * Renders nothing without films, and nothing for the unfiltered state when the
 * component does not deliver the end date (an older version): the page then
 * stays as it was.
 *
 * @package     Weltspiegel.Template
 * @copyright   Weltspiegel Cottbus
 * @license     MIT
 *
 * Usage: LayoutHelper::render('utilities.day-filter-status', [
 *            'active' => 'heute'|'morgen'|null,
 *            'day'    => 'Y-m-d'|null,         // the day on screen, when filtered
 *            'shown'  => int,                  // films on screen
 *            'end'    => DateTimeInterface|null, // start of the last show to come
 *        ])
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;

/** @var array $displayData */
$active = $displayData['active'] ?? null;
$day    = $displayData['day'] ?? null;
$shown  = (int) ($displayData['shown'] ?? 0);
$end    = $displayData['end'] ?? null;

$formatter = new IntlDateFormatter('de_DE', IntlDateFormatter::NONE, IntlDateFormatter::NONE, 'Europe/Berlin');
$formatter->setPattern('EEEE, d. MMMM');

if ($shown < 1) {
    return;
}

if ($active === null || $day === null) {
    if ($end === null) {
        return;
    }

    $text = 'Alle Tage: Vorstellungen bis ' . $formatter->format($end);
    $link = false;
} else {
    $label = $active === 'heute' ? 'Heute' : 'Morgen';
    $date  = DateTimeImmutable::createFromFormat('!Y-m-d', $day, new DateTimeZone('Europe/Berlin'));
    $text  = $label . ($date ? ' (' . $formatter->format($date) . ')' : '');
    $link  = true;
}
?>
<p class="day-filter-status">
    <?= htmlspecialchars($text) ?><?php if ($link): ?>.
        <a class="day-filter-status__all"
           href="<?= htmlspecialchars(Route::_('index.php?option=com_weltspiegel&view=movies')) ?>">Alle Tage ansehen</a>
    <?php endif; ?>
</p>
