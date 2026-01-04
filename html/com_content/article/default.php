<?php

/**
 * @package     Weltspiegel.Template
 * @subpackage  com_content
 *
 * @copyright   Weltspiegel Cottbus
 * @license     MIT
 */

defined('_JEXEC') or die;

/** @var Joomla\Component\Content\Site\View\Article\HtmlView $this */
?>
<article class="article">
    <?php if ($this->params->get('show_title')): ?>
        <header class="article__header">
            <h1 class="article__title"><?php echo $this->escape($this->item->title); ?></h1>
        </header>
    <?php endif; ?>

    <div class="article__content">
        <?php echo $this->item->event->afterDisplayTitle; ?>
        <?php echo $this->item->event->beforeDisplayContent; ?>

        <div class="article__body">
            <?php echo $this->item->text; ?>
        </div>

        <?php echo $this->item->event->afterDisplayContent; ?>
    </div>
</article>
