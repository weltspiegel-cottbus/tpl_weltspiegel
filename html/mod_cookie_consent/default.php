<?php

/**
 * Cookie Consent Module
 * Template override for Weltspiegel template
 *
 * @package     Weltspiegel.Template
 * @copyright   Weltspiegel Cottbus
 * @license     MIT
 */

\defined('_JEXEC') or die;

/**
 * @var string $consentText
 * @var string $buttonEnable
 * @var string $buttonDismiss
 * @var string $drawerText
 */

// Note: CSS and JS are loaded via template.css and template.js
// No need to register assets here

?>

<!-- Cookie Consent Banner -->
<div id="cookieConsentBanner" class="cookie-consent-banner cookie-consent-hidden">
    <button id="cookieConsentClose" class="cookie-consent-banner__close" type="button" aria-label="Schließen"></button>
    <div class="cookie-consent-banner__container">
        <p class="cookie-consent-banner__text">
            <?= htmlspecialchars($consentText) ?>
        </p>
        <div class="cookie-consent-banner__actions">
            <button id="cookieConsentDismiss" class="cookie-consent-banner__btn cookie-consent-banner__btn--decline">
                <?= htmlspecialchars($buttonDismiss) ?>
            </button>
            <button id="cookieConsentEnable" class="cookie-consent-banner__btn cookie-consent-banner__btn--accept">
                <?= htmlspecialchars($buttonEnable) ?>
            </button>
        </div>
    </div>
</div>

<!-- Cookie Consent Drawer (reopens banner) -->
<button id="cookieConsentDrawer" class="cookie-consent-drawer" type="button">
    <?= htmlspecialchars($drawerText) ?>
</button>
