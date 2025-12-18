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
    <jdoc:include type="head" />
</head>
<body>
    <jdoc:include type="component" />
</body>
</html>
