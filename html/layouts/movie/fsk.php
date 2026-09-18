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
 *   LayoutHelper::render('movie.fsk', ['fsk' => $movie->fsk, 'href' => '/...'])     // linked badge
 *
 * The link points at the information page as a whole, without a fragment: its
 * rules table sits at the very top, which is the answer a visitor clicking an
 * age badge is after. Until 2026-09-18 the link carried #fsk-n and jumped to
 * the explanation of that single rating further down the page.
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
} elseif (stripos($fsk, 'folgt') !== false) {
    $modifier = 'fsk-badge--pending';
    $label    = 'FSK ?';
} else {
    // "keine Angabe" or anything unexpected → omit
    return;
}

$classes = 'fsk-badge fsk-badge--inline ' . $modifier;

if ($href !== null) {
    echo '<a class="' . $classes . '" href="' . htmlspecialchars($href) . '">' . $label . '</a>';
} else {
    echo '<span class="' . $classes . '">' . $label . '</span>';
}
