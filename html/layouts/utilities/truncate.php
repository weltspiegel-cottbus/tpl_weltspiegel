<?php
/**
 * Truncate Layout
 * Wraps content in a container that truncates with overflow detection
 *
 * @package     Weltspiegel.Template
 * @copyright   Weltspiegel Cottbus
 * @license     MIT
 *
 * Usage:
 *   LayoutHelper::render('utilities.truncate', [
 *       'content' => $htmlContent,
 *       'link'    => $articleUrl,   // Optional, URL for "read more" ellipsis link
 *       'height'  => '12rem',       // Optional, default: 12rem
 *       'class'   => 'my-class',    // Optional, additional CSS class
 *   ])
 */

defined('_JEXEC') or die;

/** @var array $displayData */
$content = $displayData['content'] ?? '';
$link    = $displayData['link'] ?? '';
$height  = $displayData['height'] ?? '12rem';
$class   = $displayData['class'] ?? '';

if (empty($content)) {
    return;
}

$classes = 'u-truncate';
if (!empty($class)) {
    $classes .= ' ' . htmlspecialchars($class);
}

$style = '';
if ($height !== '12rem') {
    $style = ' style="--truncate-height: ' . htmlspecialchars($height) . '"';
}
?>
<div class="<?= $classes ?>"<?= $style ?>>
    <?= $content ?>
    <?php if (!empty($link)): ?>
        <a href="<?= htmlspecialchars($link) ?>" class="u-truncate__more" aria-label="Weiterlesen">…</a>
    <?php endif; ?>
</div>
