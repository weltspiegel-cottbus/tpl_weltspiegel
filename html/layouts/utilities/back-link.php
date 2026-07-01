<?php

/**
 * Back Link Layout
 * "Zurück zur Übersicht" link for detail views. Renders a real link to the
 * given overview URL (fallback for direct access / no referrer); _back-link.js
 * upgrades the click to history.back() when the visitor came from our own site,
 * restoring their scroll position.
 *
 * @package     Weltspiegel.Template
 * @copyright   Weltspiegel Cottbus
 * @license     MIT
 *
 * Usage: LayoutHelper::render('utilities.back-link', ['href' => $overviewUrl])
 */

\defined('_JEXEC') or die;

/** @var array $displayData */
$href  = $displayData['href'] ?? '';
$label = $displayData['label'] ?? 'Zurück zur Übersicht';

if ($href === '') {
    return;
}
?>
<a class="detail__back" href="<?= htmlspecialchars($href) ?>" data-back-link>
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <line x1="19" y1="12" x2="5" y2="12"/>
        <polyline points="12 19 5 12 12 5"/>
    </svg>
    <?= htmlspecialchars($label) ?>
</a>
