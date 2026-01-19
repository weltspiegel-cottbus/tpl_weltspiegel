<?php

/**
 * @package     Weltspiegel.Template
 * @subpackage  com_content
 *
 * @copyright   Weltspiegel Cottbus
 * @license     MIT
 */

defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;

/** @var Joomla\Component\Content\Site\View\Article\HtmlView $this */

// Check if this is a Vorschau article (category 8)
$isVorschau = ($this->item->catid == 8);

// Parse article attributes
$attribs = json_decode($this->item->attribs, true) ?: [];
$images = json_decode($this->item->images, true) ?: [];

// Component-managed Vorschau has source marker
$isComponentVorschau = $isVorschau && (!empty($attribs['source']) && $attribs['source'] === 'com_weltspiegel');

// Check for optional content elements
$hasYouTubeTrailer = !empty($attribs['youtube_url']);
$hasBannerImage = !empty($images['image_fulltext']) || !empty($images['image_intro']);
?>

<?php if ($isComponentVorschau): ?>
    <!-- Component-generated Vorschau article -->
    <article class="vorschau vorschau--full u-flipped-title-container">
        <?php if ($this->params->get('show_title')): ?>
            <h1 class="vorschau__title u-flipped-title"><?= $this->escape($this->item->title) ?></h1>
        <?php endif; ?>

        <?php if ($hasBannerImage): ?>
            <div class="vorschau__banner">
                <?php
                $bannerImage = !empty($images['image_fulltext']) ? $images['image_fulltext'] : $images['image_intro'];
                $bannerAlt = !empty($images['image_fulltext_alt']) ? $images['image_fulltext_alt'] : ($images['image_intro_alt'] ?? '');
                ?>
                <img src="<?= htmlspecialchars($bannerImage) ?>"
                     alt="<?= htmlspecialchars($bannerAlt ?: $this->item->title) ?>"
                     class="vorschau__banner-image">
            </div>
        <?php endif; ?>

        <?= $this->item->event->afterDisplayTitle ?>
        <?= $this->item->event->beforeDisplayContent ?>

        <div class="vorschau__body">
            <?= $this->item->fulltext ?: $this->item->introtext ?>
        </div>

        <?php if ($hasYouTubeTrailer): ?>
            <div class="vorschau__trailer">
                <?= LayoutHelper::render('com_weltspiegel.youtube.embed', [
                    'videoId' => $attribs['youtube_url']
                ]) ?>
            </div>
        <?php endif; ?>

        <?= $this->item->event->afterDisplayContent ?>
    </article>

<?php elseif ($isVorschau): ?>
    <!-- Freeform Vorschau article (minimal style) -->
    <article class="vorschau vorschau--simple u-flipped-title-container">
        <?php if ($this->params->get('show_title')): ?>
            <h1 class="vorschau__title u-flipped-title"><?= $this->escape($this->item->title) ?></h1>
        <?php endif; ?>

        <?= $this->item->event->afterDisplayTitle ?>
        <?= $this->item->event->beforeDisplayContent ?>

        <div class="vorschau__body">
            <?= $this->item->text ?>
        </div>

        <?= $this->item->event->afterDisplayContent ?>
    </article>

<?php else: ?>
    <!-- Standard article (not Vorschau) -->
    <article class="article u-flipped-title-container">
        <?php if ($this->params->get('show_title')): ?>
            <header class="article__header">
                <h1 class="article__title u-flipped-title"><?php echo $this->escape($this->item->title); ?></h1>
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
<?php endif; ?>
