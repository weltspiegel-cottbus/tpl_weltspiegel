<?php

/**
 * Duration Badge Layout
 * Renders the runtime as a badge with a clock icon.
 *
 * @package     Weltspiegel.Template
 * @copyright   Weltspiegel Cottbus
 * @license     MIT
 *
 * Usage: LayoutHelper::render('movie.duration', $movie->duration)
 */

\defined('_JEXEC') or die;

/** @var string $displayData Runtime in minutes */
$duration = trim((string) $displayData);

if ($duration === '' || $duration === '0') {
    return;
}
?>
<span class="format-badge format-badge--duration">
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <circle cx="12" cy="12" r="9"/>
        <polyline points="12 7 12 12 15 14"/>
    </svg>
    <?= htmlspecialchars($duration) ?> Min.
</span>
