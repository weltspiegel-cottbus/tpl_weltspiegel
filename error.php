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

// Same rule as Joomla's ErrorDocument applies to the HTTP status: a code outside
// 400-599 (an exception thrown without one carries 0) is a plain server error.
$errorCode = (int) $this->error->getCode();
if ($errorCode < 400 || $errorCode > 599) {
    $errorCode = 500;
}
$isNotFound = ($errorCode === 404);

// What the visitor reads. The exception's own message is for the developer: it
// can name hosts, queries or file paths, so it only appears with debugging on.
// Neither heading nor tab title carry it either.
if ($isNotFound) {
    $pageTitle = 'Seite nicht gefunden';
} elseif ($errorCode === 503) {
    $pageTitle = 'Vorübergehend nicht erreichbar';
} else {
    $pageTitle = 'Hier ist etwas schiefgelaufen';
}

?>
<!DOCTYPE html>
<html lang="<?= $this->language ?>" dir="<?= $this->direction ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $errorCode ?> - <?= $pageTitle ?></title>

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
                    <?php elseif ($errorCode === 503): ?>
                        <p class="error-page__message">Das Programm lässt sich gerade nicht laden.</p>
                        <p class="error-page__hint">
                            Das ist meist nur ein kurzer Aussetzer. Bitte versuchen Sie es in ein
                            paar Minuten noch einmal.
                        </p>
                    <?php else: ?>
                        <p class="error-page__message">Hier ist etwas schiefgelaufen.</p>
                        <p class="error-page__hint">Bitte versuchen Sie es später noch einmal.</p>
                    <?php endif; ?>

                    <?php if ($this->debug && !$isNotFound): ?>
                        <p class="error-page__hint"><?= htmlspecialchars($this->error->getMessage()) ?></p>
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
