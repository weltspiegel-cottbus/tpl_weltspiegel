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

// Categories using card layout (Vorschauen: 8, Veranstaltungen: 9)
$useCardLayout = in_array($this->category->id, [8, 9]);

// Empty state messages per category
$emptyMessages = [
    8 => 'Zur Zeit sind keine Vorschauen verfügbar.',
    9 => 'Zur Zeit sind keine Veranstaltungen geplant.',
];
?>

<?php if ($useCardLayout): ?>
    <!-- Category blog with card layout -->
    <div class="content-list u-flipped-title-container">
        <h1 class="content-list__title u-flipped-title">
            <?= $this->escape($this->params->get('page_subheading') ?: $this->category->title) ?>
        </h1>

        <?php if ($this->params->get('show_description', 1) && !empty($this->category->description)): ?>
            <div class="content-list__description">
                <?= $this->category->description ?>
            </div>
        <?php endif; ?>

        <?php if (empty($this->items)): ?>
            <div class="content-list__empty">
                <p><?= $emptyMessages[$this->category->id] ?? 'Zur Zeit sind keine Einträge vorhanden.' ?></p>
            </div>
        <?php else: ?>
            <div class="content-list__items">
                <?php foreach ($this->items as $item): ?>
                    <?php
                    $attribs = json_decode($item->attribs, true);
                    $images = json_decode($item->images, true);
                    $posterImage = !empty($images['image_intro']) ? $images['image_intro'] : ($images['image_fulltext'] ?? '');
                    $posterAlt = !empty($images['image_intro_alt']) ? $images['image_intro_alt'] : ($images['image_fulltext_alt'] ?? '');
                    $hasPoster = !empty($posterImage);
                    $articleLink = Route::_(RouteHelper::getArticleRoute($item->slug, $item->catid, $item->language));
                    ?>
                    <article class="content-card<?= $hasPoster ? '' : ' content-card--no-poster' ?>">
                        <?php if ($hasPoster): ?>
                            <div class="content-card__poster">
                                <a href="<?= $articleLink ?>" class="content-card__poster-link">
                                    <img src="<?= htmlspecialchars($posterImage) ?>"
                                         alt="<?= htmlspecialchars($posterAlt ?: $item->title) ?>"
                                         class="content-card__poster-img">
                                </a>
                            </div>
                        <?php endif; ?>

                        <h2 class="content-card__title">
                            <a href="<?= $articleLink ?>" class="content-card__title-link">
                                <?= $this->escape($item->title) ?>
                            </a>
                        </h2>

                        <?php if (!empty($item->introtext)): ?>
                            <div class="content-card__description">
                                <?= $item->introtext ?>
                            </div>
                        <?php endif; ?>
                    </article>

                <?php endforeach; ?>
            </div>

            <?php if (($this->params->def('show_pagination', 1) == 1 || ($this->params->get('show_pagination') == 2)) && ($this->pagination->pagesTotal > 1)): ?>
                <div class="content-list__pagination">
                    <?= $this->pagination->getPagesLinks() ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

<?php else: ?>
    <!-- Standard category blog (not Vorschau) -->
    <?= LayoutHelper::render('joomla.content.category_default', $this) ?>
<?php endif; ?>
