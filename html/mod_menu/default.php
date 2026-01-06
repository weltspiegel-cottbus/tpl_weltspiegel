<?php

/**
 * @package     Weltspiegel.Template
 * @subpackage  mod_menu
 *
 * @copyright   Weltspiegel Cottbus
 * @license     MIT
 */

defined('_JEXEC') or die;

/** @var Joomla\CMS\Menu\MenuItem[] $list  */

// Split menu items at separator
$beforeSeparator = [];
$afterSeparator = [];
$foundSeparator = false;

foreach ($list as $item) {
    if ($item->type === 'separator') {
        $foundSeparator = true;
        continue;
    }

    if (!$foundSeparator) {
        $beforeSeparator[] = $item;
    } else {
        $afterSeparator[] = $item;
    }
}

$currentYear = date('Y');
?>
<nav class="footer-nav">
    <div class="footer-nav__copyright">
        &copy; <?= $currentYear ?> Weltspiegel Cottbus
    </div>

    <?php if (!empty($beforeSeparator)): ?>
        <ul class="footer-nav__list footer-nav__list--primary">
            <?php foreach ($beforeSeparator as $item): ?>
                <li class="footer-nav__item">
                    <a href="<?= $item->flink ?>" class="footer-nav__link">
                        <?= $item->title ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if (!empty($afterSeparator)): ?>
        <ul class="footer-nav__list footer-nav__list--secondary">
            <?php foreach ($afterSeparator as $item): ?>
                <?php
                $params = $item->getParams();
                $image = $params->get('menu_image');
                ?>
                <li class="footer-nav__item">
                    <a href="<?= $item->flink ?>" class="footer-nav__link" target="_blank" rel="noopener noreferrer">
                        <?php if ($image): ?>
                            <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($item->title) ?>">
                        <?php else: ?>
                            <?= $item->title ?>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</nav>
