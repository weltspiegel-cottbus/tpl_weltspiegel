<?php

/**
 * FSK Badge Layout
 * Renders a single age-rating badge from the raw ALTERSFREIGABE string.
 *
 *  - "ab 0/6/12/16/18"  → coloured badge "FSK n"
 *  - "FSK folgt"        → grey pending badge "FSK ?"
 *  - "keine Angabe"/""  → nothing (omitted)
 *
 * @package     Weltspiegel.Template
 * @copyright   Weltspiegel Cottbus
 * @license     MIT
 *
 * Usage:
 *   LayoutHelper::render('movie.fsk', ['fsk' => $movie->fsk])                       // plain badge
 *   LayoutHelper::render('movie.fsk', ['fsk' => $movie->fsk, 'href' => '/...'])     // linked badge (+ #fsk-n)
 *
 * A rated badge links to its own row of the rules table (#fsk-12 and the like).
 * Without JavaScript the browser jumps there, which on a narrow screen is the
 * card for that rating; with it, the row is selected instead.
 *
 * A pending badge ("FSK folgt") links to the page without a fragment. There is
 * no row for it — and rightly so: if no rating has been given, the table has
 * nothing to say, and the page as a whole is the honest answer.
 */

\defined('_JEXEC') or die;

/** @var array $displayData */
$fsk  = trim((string) ($displayData['fsk'] ?? ''));
$href = $displayData['href'] ?? null;

if ($fsk === '') {
    return;
}

if (preg_match('/(\d+)/', $fsk, $m)) {
    $modifier = 'fsk-badge--' . (int) $m[1];
    $label    = 'FSK ' . (int) $m[1];
    $hash     = '#fsk-' . (int) $m[1];
} elseif (stripos($fsk, 'folgt') !== false) {
    $modifier = 'fsk-badge--pending';
    $label    = 'FSK ?';
    $hash     = '';
} else {
    // "keine Angabe" or anything unexpected → omit
    return;
}

$classes = 'fsk-badge fsk-badge--inline ' . $modifier;

if ($href !== null) {
    echo '<a class="' . $classes . '" href="' . htmlspecialchars($href . $hash) . '">' . $label . '</a>';
} else {
    echo '<span class="' . $classes . '">' . $label . '</span>';
}
