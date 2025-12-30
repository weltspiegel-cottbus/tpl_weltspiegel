<?php

/**
 * @package     Weltspiegel.Template
 * @subpackage  mod_menu
 *
 * @copyright   Weltspiegel Cottbus
 * @license     MIT
 */

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;

/** @var Joomla\CMS\Menu\MenuItem[] $list  */
/** @var int $active_id */

// Build nested structure from flat list (only 2 levels: parent + children)
$menuTree = [];
foreach ($list as $item) {
    // Only process top-level items (level 1)
    if ($item->level == 1) {
        $menuTree[] = [
            'item' => $item,
            'children' => $item->getChildren() ?: []
        ];
    }
}
?>
<nav class="main-nav">
    <a href="<?= Uri::root() ?>" class="main-nav__logo">
        <img src="<?= Uri::root(true) ?>/media/templates/site/weltspiegel/images/logo.png" alt="Weltspiegel Cottbus">
    </a>

    <button type="button" class="main-nav__toggle" aria-label="Menü öffnen" aria-expanded="false" aria-controls="main-menu">
        <span class="main-nav__hamburger"></span>
    </button>

    <div class="main-nav__menu" id="main-menu">
        <ul class="main-nav__list">
            <?php foreach ($menuTree as $parent): ?>
                <?php $isHeading = $parent['item']->type === 'heading'; ?>
                <?php $isActive = $parent['item']->id == $active_id; ?>
                <li class="main-nav__item<?= !empty($parent['children']) ? ' main-nav__item--has-children' : '' ?><?= $isActive ? ' main-nav__item--active' : '' ?>">
                    <?php if ($isHeading): ?>
                        <span class="main-nav__link main-nav__heading">
                            <?= $parent['item']->title ?>
                        </span>
                    <?php else: ?>
                        <a href="<?= $parent['item']->flink ?>" class="main-nav__link">
                            <?= $parent['item']->title ?>
                        </a>
                    <?php endif; ?>

                    <?php if (!empty($parent['children'])): ?>
                        <ul class="main-nav__submenu">
                            <?php foreach ($parent['children'] as $child): ?>
                                <?php $isChildActive = $child->id == $active_id; ?>
                                <li class="main-nav__subitem<?= $isChildActive ? ' main-nav__subitem--active' : '' ?>">
                                    <a href="<?= $child->flink ?>" class="main-nav__sublink">
                                        <?= $child->title ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>
