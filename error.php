<?php

/**
 * Error Page Template
 *
 * @package     Weltspiegel.Template
 * @copyright   Weltspiegel Cottbus
 * @license     MIT
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

/** @var Joomla\CMS\Document\ErrorDocument $this */

$app = Factory::getApplication();
$wa  = $this->getWebAssetManager();

// Enable assets
$wa->usePreset('template.weltspiegel');

$errorCode = $this->error->getCode();
$isNotFound = ($errorCode == 404);

?>
<!DOCTYPE html>
<html lang="<?= $this->language ?>" dir="<?= $this->direction ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $errorCode ?> - <?= $this->title ?></title>

    <link rel="icon" href="<?= Uri::root(true) ?>/media/templates/site/weltspiegel/images/favicon.ico">

    <jdoc:include type="styles" />
    <jdoc:include type="scripts" />
</head>
<body>
    <header>
        <jdoc:include type="modules" name="menu" style="none" />
    </header>

    <div class="full-height-wrapper">
        <div class="page-container">
            <main>
                <article class="error-page">
                    <h1 class="error-page__title"><?= $errorCode ?></h1>

                    <?php if ($isNotFound): ?>
                        <p class="error-page__message">Die angeforderte Seite existiert nicht mehr.</p>
                        <p class="error-page__hint">
                            Abgespielte Filme sind nicht mehr abrufbar, Veranstaltungen und
                            Vorschauen werden nach ihrem Termin archiviert.
                        </p>
                    <?php else: ?>
                        <p class="error-page__message"><?= htmlspecialchars($this->error->getMessage()) ?></p>
                    <?php endif; ?>

                    <nav class="error-page__nav">
                        <p>Weiter zu:</p>
                        <ul class="error-page__links">
                            <li><a href="<?= Uri::root() ?>">Startseite</a></li>
                            <li><a href="<?= Uri::root() ?>programm">Programm</a></li>
                            <li><a href="<?= Uri::root() ?>vorschauen">Vorschauen</a></li>
                            <li><a href="<?= Uri::root() ?>veranstaltungen">Veranstaltungen</a></li>
                        </ul>
                    </nav>
                </article>
            </main>
        </div>

        <footer>
            <jdoc:include type="modules" name="footer" style="none" />
        </footer>
    </div>
</body>
</html>
