<?php

/**
 * Format Badges Layout
 * Aggregates the distinct format variants of a movie across all its events:
 *  - dimension flags (2D / 3D) from $format->is3D
 *  - language versions from $format->language ("D, Deutsch" / "OmU, Englisch")
 *
 * Language label: German default → name only ("Deutsch"); subtitled/original
 * versions → "Name (Marker)", e.g. "Englisch (OmU)".
 *
 * @package     Weltspiegel.Template
 * @copyright   Weltspiegel Cottbus
 * @license     MIT
 *
 * Usage: LayoutHelper::render('booking.formats', $movie)
 */

\defined('_JEXEC') or die;

/** @var stdClass $displayData The movie object (with ->formats) */
$movie   = $displayData;
$formats = $movie->formats ?? [];

if (empty($formats)) {
    return;
}

$flags     = []; // '2D' / '3D', ordered unique
$languages = []; // language labels, ordered unique

foreach ($formats as $format) {
    $dim = !empty($format->is3D) ? '3D' : '2D';
    if (!in_array($dim, $flags, true)) {
        $flags[] = $dim;
    }

    $raw = trim((string) ($format->language ?? ''));
    if ($raw !== '') {
        $parts = array_map('trim', explode(',', $raw, 2));
        $code  = $parts[0] ?? '';
        $name  = ($parts[1] ?? '') !== '' ? $parts[1] : $code;

        // German default shows just the language name; OmU/OV add the marker.
        $label = (strcasecmp($code, 'D') === 0 || $code === '')
            ? $name
            : ($name !== '' ? $name . ' (' . $code . ')' : $code);
    } else {
        $label = trim((string) ($format->languageShort ?? ''));
    }

    if ($label !== '' && !in_array($label, $languages, true)) {
        $languages[] = $label;
    }
}

sort($flags); // '2D' before '3D'

if (empty($flags) && empty($languages)) {
    return;
}

// Bare badge spans — the caller wraps them in .format-badges (alongside the FSK badge).
foreach ($flags as $flag) {
    echo '<span class="format-badge format-badge--' . strtolower($flag) . '">' . htmlspecialchars($flag) . '</span>';
}
foreach ($languages as $language) {
    echo '<span class="format-badge format-badge--lang">' . htmlspecialchars($language) . '</span>';
}
