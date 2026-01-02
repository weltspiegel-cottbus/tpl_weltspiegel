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
/** @var int[] $path */

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

    <!-- Mobile hamburger toggle -->
    <button type="button" class="main-nav__toggle" aria-label="Menü öffnen" aria-expanded="false" aria-controls="main-menu">
        <span class="main-nav__hamburger"></span>
    </button>

    <!-- Mobile menu drawer -->
    <div class="main-nav__menu" id="main-menu">
        <ul class="main-nav__list">
            <?php foreach ($menuTree as $parent): ?>
                <?php
                $isHeading = $parent['item']->type === 'heading';
                $isActive = $parent['item']->id == $active_id;
                $hasSubmenu = !empty($parent['children']);
                ?>
                <li class="main-nav__item<?= $hasSubmenu ? ' main-nav__item--has-children' : '' ?><?= $isActive ? ' main-nav__item--active' : '' ?>">
                    <?php if ($isHeading): ?>
                        <span class="main-nav__link main-nav__heading">
                            <?= $parent['item']->title ?>
                        </span>
                    <?php else: ?>
                        <a href="<?= $parent['item']->flink ?>" class="main-nav__link">
                            <?= $parent['item']->title ?>
                        </a>
                    <?php endif; ?>

                    <?php if ($hasSubmenu): ?>
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

    <!-- Desktop horizontal menu -->
    <ul class="main-nav__desktop">
        <?php foreach ($menuTree as $parent): ?>
            <?php
            $isHeading = $parent['item']->type === 'heading';
            $isActive = in_array($parent['item']->id, $path);
            $hasSubmenu = !empty($parent['children']);
            ?>
            <li class="main-nav__desktop-item<?= $hasSubmenu ? ' main-nav__desktop-item--has-submenu' : '' ?><?= $isActive ? ' main-nav__desktop-item--active' : '' ?>">
                <div class="main-nav__desktop-wrapper">
                    <?php if ($isHeading): ?>
                        <span class="main-nav__desktop-link"><?= $parent['item']->title ?></span>
                    <?php else: ?>
                        <a href="<?= $parent['item']->flink ?>" class="main-nav__desktop-link"<?= $parent['item']->id === $active_id ? ' aria-current="page"' : '' ?>>
                            <?= $parent['item']->title ?>
                        </a>
                    <?php endif; ?>

                    <?php if ($hasSubmenu): ?>
                        <button class="main-nav__desktop-toggle" aria-expanded="false" aria-label="<?= $parent['item']->title ?> Untermenü öffnen">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </button>
                    <?php endif; ?>
                </div>

                <?php if ($hasSubmenu): ?>
                    <ul class="main-nav__desktop-submenu">
                        <?php foreach ($parent['children'] as $child): ?>
                            <li class="main-nav__desktop-subitem<?= $child->id === $active_id ? ' main-nav__desktop-subitem--active' : '' ?>">
                                <a href="<?= $child->flink ?>" class="main-nav__desktop-sublink"<?= $child->id === $active_id ? ' aria-current="page"' : '' ?>>
                                    <?= $child->title ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
