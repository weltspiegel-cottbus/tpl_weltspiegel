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
    $hash     = '#fsk-folgt';
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
