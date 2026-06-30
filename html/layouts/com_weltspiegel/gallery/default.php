<?php

/**
 * Layout for image gallery with lightbox
 * Template override for Weltspiegel template
 *
 * @package     Weltspiegel.Template
 * @copyright   Weltspiegel Cottbus
 * @license     MIT
 */

\defined('_JEXEC') or die;

/**
 * Layout variables
 * ----------------
 * @var string $folder   Relative path to the image folder (e.g. "images/vorschauen/album")
 * @var array  $images   List of image file paths relative to site root
 * @var array  $options  Parsed options from the tag (cols, preview, etc.)
 */

$folder       = $displayData['folder'] ?? '';
$images       = $displayData['images'] ?? [];
$options      = $displayData['options'] ?? [];
$teaserImage  = $displayData['teaserImage'] ?? null;
$articleTitle = $displayData['articleTitle'] ?? '';
$useAltNames  = isset($options['altnames']);

if (empty($images)) {
    return;
}

$cols      = max(1, min(6, (int) ($options['cols'] ?? 3)));
$galleryId = 'gallery-' . md5($folder);
$total     = count($images);

// Find teaser index within the images array for correct lightbox start position
$teaserIndex = 0;
if ($teaserImage !== null) {
    foreach ($images as $i => $img) {
        if ($img === $teaserImage) {
            $teaserIndex = $i;
            break;
        }
    }
}

?>
<?php if ($teaserImage !== null):
    $teaserAlt = $useAltNames
        ? str_replace('_', ' ', pathinfo($teaserImage, PATHINFO_FILENAME))
        : ($articleTitle !== '' ? $articleTitle . ' – Titelbild' : '');
?>
<a class="gallery__teaser"
   href="<?= htmlspecialchars($teaserImage) ?>"
   data-gallery="<?= $galleryId ?>"
   data-index="<?= $teaserIndex ?>">
    <img class="gallery__teaser-image"
         src="<?= htmlspecialchars($teaserImage) ?>"
         alt="<?= htmlspecialchars($teaserAlt) ?>"
         <?= $useAltNames ? 'title="' . htmlspecialchars($teaserAlt) . '"' : '' ?>
         loading="lazy">
</a>
<?php endif; ?>
<div class="gallery gallery--cols-<?= $cols ?>" id="<?= $galleryId ?>">
    <?php foreach ($images as $i => $image): ?>
    <?php
    $alt = $useAltNames
        ? str_replace('_', ' ', pathinfo($image, PATHINFO_FILENAME))
        : ($articleTitle !== '' ? $articleTitle . ' – Bild ' . ($i + 1) : '');
    // The teaser image is shown large above; keep its grid item in the DOM (so the
    // lightbox still indexes all images) but hide the duplicate thumbnail.
    $isTeaser = ($teaserImage !== null && $i === $teaserIndex);
    ?>
    <a class="gallery__item<?= $isTeaser ? ' gallery__item--teaser' : '' ?>" href="<?= htmlspecialchars($image) ?>">
        <img
            class="gallery__image"
            src="<?= htmlspecialchars($image) ?>"
            alt="<?= htmlspecialchars($alt) ?>"
            <?= $useAltNames ? 'title="' . htmlspecialchars($alt) . '"' : '' ?>
            loading="lazy"
        >
    </a>
    <?php endforeach; ?>
</div>

<dialog class="gallery-lightbox" id="<?= $galleryId ?>-lightbox" aria-label="Bildergalerie">
    <button class="gallery-lightbox__close" type="button" aria-label="Schließen">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
    </button>

    <button class="gallery-lightbox__prev" type="button" aria-label="Vorheriges Bild">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <polyline points="15 18 9 12 15 6"/>
        </svg>
    </button>

    <div class="gallery-lightbox__stage">
        <img class="gallery-lightbox__image" src="" alt="" loading="eager">
    </div>

    <button class="gallery-lightbox__next" type="button" aria-label="Nächstes Bild">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <polyline points="9 18 15 12 9 6"/>
        </svg>
    </button>

    <p class="gallery-lightbox__caption"></p>

    <p class="gallery-lightbox__counter" aria-live="polite">
        <span class="gallery-lightbox__current">1</span> / <?= $total ?>
    </p>
</dialog>
