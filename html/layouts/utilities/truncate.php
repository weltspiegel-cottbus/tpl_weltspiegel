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
 *       'title'   => $titleHtml,    // Optional, title HTML to include before content
 *       'tagline' => $taglineText,  // Optional, short text displayed below title (e.g. release date)
 *       'content' => $htmlContent,
 *       'link'    => $articleUrl,   // Optional, URL for "read more" ellipsis link
 *       'height'  => '15rem',       // Optional, default uses --truncate-content-height CSS var
 *       'class'   => 'my-class',    // Optional, additional CSS class
 *   ])
 */

defined('_JEXEC') or die;

/** @var array $displayData */
$title   = $displayData['title'] ?? '';
$tagline = $displayData['tagline'] ?? '';
$content = $displayData['content'] ?? '';
$link    = $displayData['link'] ?? '';
$height  = $displayData['height'] ?? '';
$class   = $displayData['class'] ?? '';

if (empty($content) && empty($title)) {
    return;
}

$classes = 'u-truncate';
if (!empty($class)) {
    $classes .= ' ' . htmlspecialchars($class);
}

$style = '';
if (!empty($height)) {
    $style = ' style="--truncate-height: ' . htmlspecialchars($height) . '"';
}
?>
<div class="<?= $classes ?>"<?= $style ?>>
    <?= $title ?>
    <?php if (!empty($tagline)): ?>
        <p class="u-truncate__tagline"><?= htmlspecialchars($tagline) ?></p>
    <?php endif; ?>
    <div class="u-truncate__content">
        <?= $content ?>
    </div>
    <?php if (!empty($link)): ?>
        <a href="<?= htmlspecialchars($link) ?>" class="u-truncate__more" aria-label="Weiterlesen">…</a>
    <?php endif; ?>
</div>
