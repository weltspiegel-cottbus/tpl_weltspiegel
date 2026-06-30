<?php

/**
 * Cookie Consent Module
 * Template override for Weltspiegel template (Consent v2 — category-based)
 *
 * @package     Weltspiegel.Template
 * @copyright   Weltspiegel Cottbus
 * @license     MIT
 */

\defined('_JEXEC') or die;

/**
 * @var string   $consentText
 * @var string   $buttonOk     Label of the "OK / close" button
 * @var string   $drawerText
 * @var object[] $categories   Each: ->id, ->label, ->description, ->default (bool)
 */

// Note: CSS and JS are loaded via template.css and template.js
// No need to register assets here

?>

<!-- Cookie Consent Banner -->
<div id="cookieConsentBanner" class="cookie-consent-banner cookie-consent-hidden">
    <button id="cookieConsentClose" class="cookie-consent-banner__close" type="button" aria-label="Schließen"></button>
    <div class="cookie-consent-banner__inner">
        <p class="cookie-consent-banner__text">
            <?= htmlspecialchars($consentText) ?>
            Näheres dazu in unserer <a href="/datenschutz#cookies">Datenschutzerklärung</a>.
        </p>

        <ul class="cookie-consent-categories">
            <?php foreach ($categories as $cat): ?>
                <li class="cookie-consent-category">
                    <label class="cookie-consent-category__label">
                        <span class="cookie-consent-category__text">
                            <span class="cookie-consent-category__name"><?= htmlspecialchars($cat->label) ?></span>
                            <?php if ($cat->description !== ''): ?>
                                <span class="cookie-consent-category__desc"><?= htmlspecialchars($cat->description) ?></span>
                            <?php endif; ?>
                        </span>
                        <input
                            class="cookie-consent-switch"
                            type="checkbox"
                            role="switch"
                            data-consent-category="<?= htmlspecialchars($cat->id) ?>"
                            data-consent-default="<?= $cat->default ? '1' : '0' ?>"
                            aria-label="<?= htmlspecialchars($cat->label) ?>"
                        >
                    </label>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="cookie-consent-banner__actions">
            <button id="cookieConsentOk" class="cookie-consent-banner__btn cookie-consent-banner__btn--ok">
                <?= htmlspecialchars($buttonOk) ?>
            </button>
        </div>
    </div>
</div>

<!-- Cookie Consent Drawer (reopens banner) -->
<button id="cookieConsentDrawer" class="cookie-consent-drawer" type="button">
    <?= htmlspecialchars($drawerText) ?>
</button>
