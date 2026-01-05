<?php

/**
 * Weltspiegel Template for Joomla
 *
 * @package     Weltspiegel.Template
 * @copyright   Weltspiegel Cottbus
 * @license     MIT
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

/** @var Joomla\CMS\Document\HtmlDocument $this */

$app = Factory::getApplication();
$wa  = $this->getWebAssetManager();

// Enable assets
$wa->usePreset('template.weltspiegel');

?>
<!DOCTYPE html>
<html lang="<?= $this->language ?>" dir="<?= $this->direction ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $this->getTitle() ?></title>
    <?php if ($this->getDescription()): ?>
        <meta name="description" content="<?= $this->getDescription() ?>">
    <?php endif; ?>

    <link rel="icon" href="<?= Uri::root(true) ?>/media/templates/site/weltspiegel/images/favicon.ico">

    <jdoc:include type="styles" />
    <jdoc:include type="scripts" />
</head>
<body>
    <header>
        <jdoc:include type="modules" name="menu" style="none" />
    </header>

    <div class="page-container">
        <main>
            <jdoc:include type="component" />
        </main>
    </div>
</body>
</html>
