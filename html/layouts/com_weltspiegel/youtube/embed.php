<?php

/**
 * Layout for consent-aware YouTube embed
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
 * @var string $videoId     YouTube video ID
 * @var bool   $responsive  Use responsive 16:9 container (default: true)
 * @var string $placeholder Placeholder text (default: "YouTube-Trailer verfügbar")
 * @var string $hint        Hint text (default: "Bitte aktiviere YouTube...")
 */

$videoId = $displayData['videoId'] ?? '';
$responsive = $displayData['responsive'] ?? true;
$placeholder = $displayData['placeholder'] ?? 'YouTube-Trailer verfügbar';
$hint = $displayData['hint'] ?? 'Bitte aktiviere YouTube in den Cookie-Einstellungen';

if (empty($videoId)) {
    return;
}

$embedUrl = "https://www.youtube-nocookie.com/embed/{$videoId}";
$uniqueId = 'yt-' . $videoId . '-' . uniqid();

?>

<div class="youtube-embed">
    <div class="<?= $responsive ? 'youtube-embed--responsive' : 'youtube-embed--fixed' ?>">
        <!-- Placeholder -->
        <div id="<?= $uniqueId ?>-placeholder" class="youtube-embed__placeholder">
            <div class="youtube-embed__placeholder-content">
                <svg class="youtube-embed__placeholder-icon" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                    <path d="M6.271 5.055a.5.5 0 0 1 .52.038l3.5 2.5a.5.5 0 0 1 0 .814l-3.5 2.5A.5.5 0 0 1 6 10.5v-5a.5.5 0 0 1 .271-.445z"/>
                </svg>
                <p class="youtube-embed__placeholder-text"><?= htmlspecialchars($placeholder) ?></p>
                <small class="youtube-embed__placeholder-hint"><?= htmlspecialchars($hint) ?></small>
                <a href="https://www.youtube.com/watch?v=<?= htmlspecialchars($videoId) ?>"
                   class="youtube-embed__placeholder-link"
                   target="_blank"
                   rel="noopener noreferrer">
                    Auf YouTube ansehen
                </a>
            </div>
        </div>

        <!-- YouTube iframe -->
        <iframe id="<?= $uniqueId ?>-iframe"
                class="youtube-embed__iframe <?= $responsive ? 'youtube-embed__iframe--responsive' : 'youtube-embed__iframe--fixed' ?>"
                src=""
                data-src="<?= htmlspecialchars($embedUrl) ?>"
                frameborder="0"
                allowfullscreen
                referrerpolicy="strict-origin-when-cross-origin">
        </iframe>
    </div>
</div>
