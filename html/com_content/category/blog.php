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

/** @var Joomla\Component\Content\Site\View\Category\HtmlView $this */

// Check if this is the Vorschau category (category 8)
$isVorschauCategory = ($this->category->id == 8);
?>

<?php if ($isVorschauCategory): ?>
    <!-- Vorschau category blog -->
    <div class="vorschau-list">
        <?php if ($this->params->get('show_category_title', 1) || $this->params->get('page_subheading')): ?>
            <header class="vorschau-list__header u-flipped-title-container">
                <h1 class="vorschau-list__title u-flipped-title">
                    <?php echo $this->escape($this->params->get('page_subheading') ?: $this->category->title); ?>
                </h1>
            </header>
        <?php endif; ?>

        <?php if ($this->params->get('show_description', 1) && !empty($this->category->description)): ?>
            <div class="vorschau-list__description">
                <?php echo $this->category->description; ?>
            </div>
        <?php endif; ?>

        <div class="vorschau-grid">
            <?php foreach ($this->items as $item): ?>
                <?php
                // Check if component-generated
                $attribs = json_decode($item->attribs, true);
                $hasYouTubeTrailer = !empty($attribs['youtube_url']);
                $images = json_decode($item->images, true);
                $hasBannerImage = !empty($images['image_intro']) || !empty($images['image_fulltext']);
                $isComponentVorschau = $hasYouTubeTrailer || $hasBannerImage;
                ?>

                <?php if ($isComponentVorschau): ?>
                    <!-- Component-generated Vorschau card -->
                    <article class="vorschau-card vorschau-card--component">
                        <?php if ($hasBannerImage): ?>
                            <div class="vorschau-card__image">
                                <?php
                                $posterImage = !empty($images['image_intro']) ? $images['image_intro'] : $images['image_fulltext'];
                                $posterAlt = !empty($images['image_intro_alt']) ? $images['image_intro_alt'] : $images['image_fulltext_alt'];
                                ?>
                                <a href="<?php echo $item->link; ?>">
                                    <img src="<?php echo htmlspecialchars($posterImage); ?>"
                                         alt="<?php echo htmlspecialchars($posterAlt ?: $item->title); ?>"
                                         class="vorschau-card__poster">
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="vorschau-card__content">
                            <h2 class="vorschau-card__title">
                                <a href="<?php echo $item->link; ?>">
                                    <?php echo $this->escape($item->title); ?>
                                </a>
                            </h2>

                            <?php if (!empty($item->introtext)): ?>
                                <div class="vorschau-card__intro">
                                    <?php echo $item->introtext; ?>
                                </div>
                            <?php endif; ?>

                            <a href="<?php echo $item->link; ?>" class="vorschau-card__more">
                                [[ mehr ]]
                            </a>
                        </div>
                    </article>

                <?php else: ?>
                    <!-- Freeform Vorschau card -->
                    <article class="vorschau-card vorschau-card--simple">
                        <div class="vorschau-card__content">
                            <h2 class="vorschau-card__title">
                                <a href="<?php echo $item->link; ?>">
                                    <?php echo $this->escape($item->title); ?>
                                </a>
                            </h2>

                            <?php if (!empty($item->introtext)): ?>
                                <div class="vorschau-card__intro">
                                    <?php echo $item->introtext; ?>
                                </div>
                            <?php endif; ?>

                            <a href="<?php echo $item->link; ?>" class="vorschau-card__more">
                                [[ mehr ]]
                            </a>
                        </div>
                    </article>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <?php if (($this->params->def('show_pagination', 1) == 1 || ($this->params->get('show_pagination') == 2)) && ($this->pagination->pagesTotal > 1)): ?>
            <div class="vorschau-list__pagination">
                <?php echo $this->pagination->getPagesLinks(); ?>
            </div>
        <?php endif; ?>
    </div>

<?php else: ?>
    <!-- Standard category blog (not Vorschau) -->
    <?php echo LayoutHelper::render('joomla.content.category_default', $this); ?>
<?php endif; ?>
