<?php
/**
 * Booking link layout
 * Renders a single show time as a link to the kinoheld.de booking widget.
 *
 * @package     Weltspiegel.Template
 * @copyright   Weltspiegel Cottbus
 * @license     MIT
 *
 * @var string $displayData['showId']   The Cinetixx SHOW_ID
 * @var string $displayData['label']    Link text (e.g. formatted show time)
 * @var array  $displayData['options']  Optional: 'class' for additional CSS classes
 */

\defined('_JEXEC') or die;

$showId  = $displayData['showId'] ?? '';
$label   = $displayData['label'] ?? '';
$options = $displayData['options'] ?? [];

$bookingUrl = 'https://www.kinoheld.de/kino-cottbus/filmtheater-weltspiegel/vorstellung/' . $showId . '?mode=widget#panel-seats';

$cssClass = 'booking-link';
if (!empty($options['class'])) {
    $cssClass .= ' ' . $options['class'];
}
?>
<a href="<?= htmlspecialchars($bookingUrl) ?>"
   class="<?= $cssClass ?>"
   target="_blank"
   rel="noopener noreferrer"><?= htmlspecialchars($label) ?></a>
