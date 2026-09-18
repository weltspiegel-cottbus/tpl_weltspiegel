<?php

/**
 * Day Filter Layout
 * The chip row above the programme listing: Alle · Heute · Morgen.
 *
 * Chips are plain links, so the state lives in the URL and can be shared,
 * bookmarked and used without JavaScript. A day with nothing left is rendered
 * as a <span> rather than a disabled link — HTML has no disabled link, and a
 * link into an empty page is a dead end. As a non-interactive element it also
 * drops out of the tab order.
 *
 * "Alle" is deliberately a value, not an action ("Zurücksetzen"): it is one of
 * three states of the same axis and belongs in the row as an equal. A reset
 * would be a different kind of control and would have to sit apart.
 *
 * @package     Weltspiegel.Template
 * @copyright   Weltspiegel Cottbus
 * @license     MIT
 *
 * Usage: LayoutHelper::render('utilities.day-filter', [
 *            'active'    => 'heute'|'morgen'|null,
 *            'available' => ['heute' => bool, 'morgen' => bool],
 *        ])
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;

/** @var array $displayData */
$active    = $displayData['active'] ?? null;
$available = $displayData['available'] ?? [];

$base = 'index.php?option=com_weltspiegel&view=movies';

$chips = [
    ['tag' => null,     'label' => 'Alle',   'empty' => ''],
    ['tag' => 'heute',  'label' => 'Heute',  'empty' => 'Heute gibt es keine Vorstellung mehr'],
    ['tag' => 'morgen', 'label' => 'Morgen', 'empty' => 'Morgen ist noch keine Vorstellung geplant'],
];
?>
<nav class="day-filter" aria-label="Programm nach Tag filtern">
    <ul class="day-filter__list">
        <?php foreach ($chips as $chip): ?>
            <?php
            $tag       = $chip['tag'];
            $isActive  = $active === $tag;
            // "Alle" is always available — it is the unfiltered state.
            $isOffered = $tag === null || !empty($available[$tag]);
            $classes   = 'day-filter__chip'
                . ($isActive ? ' day-filter__chip--active' : '')
                . ($isOffered ? '' : ' day-filter__chip--unavailable');
            ?>
            <li class="day-filter__item">
                <?php if ($isOffered): ?>
                    <a class="<?= $classes ?>"
                       href="<?= htmlspecialchars(Route::_($base . ($tag === null ? '' : '&tag=' . $tag))) ?>"
                        <?= $isActive ? ' aria-current="page"' : '' ?>>
                        <?= htmlspecialchars($chip['label']) ?>
                    </a>
                <?php else: ?>
                    <span class="<?= $classes ?>" title="<?= htmlspecialchars($chip['empty']) ?>">
                        <?= htmlspecialchars($chip['label']) ?>
                    </span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
