<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/** @var \Joomla\Component\Content\Site\View\Featured\HtmlView $this */
?>
<?php echo $this->item->event->beforeDisplayContent; ?>
<?php echo $this->item->introtext; ?>
<?php echo $this->item->event->afterDisplayContent; ?>
