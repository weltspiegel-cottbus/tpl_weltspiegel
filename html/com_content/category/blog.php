<?php

/**
 * Category Blog View
 * Template override for Weltspiegel template
 *
 * @package     Weltspiegel.Template
 * @subpackage  com_content
 *
 * @copyright   Weltspiegel Cottbus
 * @license     MIT
 */

defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;

/** @var Joomla\Component\Content\Site\View\Category\HtmlView $this */

// Check if this is the Vorschau category (category 8)
$isVorschauCategory = ($this->category->id == 8);
?>

<?php if ($isVorschauCategory): ?>
    <!-- Vorschau category blog -->
    <div class="vorschau-list u-flipped-title-container">
        <h1 class="vorschau-list__title u-flipped-title">
            <?= $this->escape($this->params->get('page_subheading') ?: $this->category->title) ?>
        </h1>

        <?php if ($this->params->get('show_description', 1) && !empty($this->category->description)): ?>
            <div class="vorschau-list__description">
                <?= $this->category->description ?>
            </div>
        <?php endif; ?>

        <div class="vorschau-list__items">
            <?php foreach ($this->items as $item): ?>
                <?php
                // Check if component-generated (has image or YouTube)
                $attribs = json_decode($item->attribs, true);
                $hasYouTubeTrailer = !empty($attribs['youtube_url']);
                $images = json_decode($item->images, true);
                $posterImage = !empty($images['image_intro']) ? $images['image_intro'] : ($images['image_fulltext'] ?? '');
                $posterAlt = !empty($images['image_intro_alt']) ? $images['image_intro_alt'] : ($images['image_fulltext_alt'] ?? '');
                $hasPoster = !empty($posterImage);
                ?>

                <?php
                $articleLink = Route::_(RouteHelper::getArticleRoute($item->slug, $item->catid, $item->language));
                ?>
                <article class="vorschau-card<?= $hasPoster ? '' : ' vorschau-card--no-poster' ?>">
                    <?php if ($hasPoster): ?>
                        <div class="vorschau-card__poster">
                            <a href="<?= $articleLink ?>" class="vorschau-card__poster-link">
                                <img src="<?= htmlspecialchars($posterImage) ?>"
                                     alt="<?= htmlspecialchars($posterAlt ?: $item->title) ?>"
                                     class="vorschau-card__poster-img">
                            </a>
                        </div>
                    <?php endif; ?>

                    <h2 class="vorschau-card__title">
                        <a href="<?= $articleLink ?>" class="vorschau-card__title-link">
                            <?= $this->escape($item->title) ?>
                        </a>
                    </h2>

                    <?php if (!empty($item->introtext)): ?>
                        <div class="vorschau-card__description">
                            <?= $item->introtext ?>
                        </div>
                    <?php endif; ?>
                </article>

            <?php endforeach; ?>
        </div>

        <?php if (($this->params->def('show_pagination', 1) == 1 || ($this->params->get('show_pagination') == 2)) && ($this->pagination->pagesTotal > 1)): ?>
            <div class="vorschau-list__pagination">
                <?= $this->pagination->getPagesLinks() ?>
            </div>
        <?php endif; ?>
    </div>

<?php else: ?>
    <!-- Standard category blog (not Vorschau) -->
    <?= LayoutHelper::render('joomla.content.category_default', $this) ?>
<?php endif; ?>
