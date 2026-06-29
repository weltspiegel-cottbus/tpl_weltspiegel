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

// Categories using detail layout (Vorschauen: 8, Veranstaltungen: 9)
$useContentSingleLayout = in_array($this->item->catid, [8, 9]);

// Flipped title for detail layout
$flippedTitle = match ((int) $this->item->catid) {
    8 => 'Vorschau',
    9 => 'Veranstaltung',
    default => '',
};

// Parse article attributes
$attribs = json_decode($this->item->attribs, true) ?: [];
$images = json_decode($this->item->images, true) ?: [];

// Component-managed article has source marker
$isComponentManaged = $useContentSingleLayout && (!empty($attribs['source']) && $attribs['source'] === 'com_weltspiegel');

// Check for optional content elements
$hasYouTubeTrailer = !empty($attribs['youtube_url']);
$hasBannerImage = !empty($images['image_fulltext']) || !empty($images['image_intro']);

?>

<?php if ($isComponentManaged): ?>
    <!-- Component-managed article -->
    <article class="detail detail--full u-flipped-title-container">
        <span class="u-flipped-title u-flipped-title--desktop-only"><?= $flippedTitle ?></span>
        <div class="detail__inner">
            <?php if ($this->params->get('show_title')): ?>
                <h1 class="detail__title"><?= $this->escape($this->item->title) ?></h1>
            <?php endif; ?>

            <?php if ($hasBannerImage): ?>
                <div class="detail__poster">
                    <?php
                    $bannerImage = !empty($images['image_fulltext']) ? $images['image_fulltext'] : $images['image_intro'];
                    $bannerAlt = !empty($images['image_fulltext_alt']) ? $images['image_fulltext_alt'] : ($images['image_intro_alt'] ?? '');
                    ?>
                    <img src="<?= htmlspecialchars($bannerImage) ?>"
                         alt="<?= htmlspecialchars($bannerAlt ?: $this->item->title) ?>"
                         class="detail__poster-img">
                </div>
            <?php endif; ?>

            <?= $this->item->event->afterDisplayTitle ?>
            <?= $this->item->event->beforeDisplayContent ?>

            <?php if (!empty($attribs['tagline'])): ?>
                <p class="detail__tagline"><?= $this->escape($attribs['tagline']) ?></p>
            <?php endif; ?>

            <div class="detail__body">
                <?= $this->item->fulltext ?: $this->item->introtext ?>
            </div>

            <?php if ($hasYouTubeTrailer): ?>
                <div class="detail__trailer">
                    <?= LayoutHelper::render('com_weltspiegel.youtube.embed', [
                        'videoId' => $attribs['youtube_url']
                    ]) ?>
                </div>
            <?php endif; ?>

            <?= $this->item->event->afterDisplayContent ?>
        </div>
    </article>

<?php elseif ($useContentSingleLayout): ?>
    <!-- Freeform article in card-layout category -->
    <article class="detail detail--simple u-flipped-title-container">
        <span class="u-flipped-title u-flipped-title--desktop-only"><?= $flippedTitle ?></span>
        <div class="detail__inner">
            <?php if ($this->params->get('show_title')): ?>
                <h1 class="detail__title"><?= $this->escape($this->item->title) ?></h1>
            <?php endif; ?>

            <?php if ($hasBannerImage): ?>
                <div class="detail__poster">
                    <?php
                    $bannerImage = !empty($images['image_fulltext']) ? $images['image_fulltext'] : $images['image_intro'];
                    $bannerAlt = !empty($images['image_fulltext_alt']) ? $images['image_fulltext_alt'] : ($images['image_intro_alt'] ?? '');
                    ?>
                    <img src="<?= htmlspecialchars($bannerImage) ?>"
                         alt="<?= htmlspecialchars($bannerAlt ?: $this->item->title) ?>"
                         class="detail__poster-img">
                </div>
            <?php endif; ?>

            <?= $this->item->event->afterDisplayTitle ?>
            <?= $this->item->event->beforeDisplayContent ?>

            <div class="detail__body">
                <?= $this->item->text ?>
            </div>

            <?= $this->item->event->afterDisplayContent ?>
        </div>
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

            <?php if ($hasBannerImage): ?>
                <div class="article__banner">
                    <?php
                    $bannerImage = !empty($images['image_fulltext']) ? $images['image_fulltext'] : $images['image_intro'];
                    $bannerAlt = !empty($images['image_fulltext_alt']) ? $images['image_fulltext_alt'] : ($images['image_intro_alt'] ?? '');
                    ?>
                    <img src="<?= htmlspecialchars($bannerImage) ?>"
                         alt="<?= htmlspecialchars($bannerAlt ?: $this->item->title) ?>"
                         class="article__banner-image">
                </div>
            <?php endif; ?>

            <div class="article__body">
                <?php echo $this->item->text; ?>
            </div>

            <?php echo $this->item->event->afterDisplayContent; ?>
        </div>
    </article>
<?php endif; ?>
