/**
 *  2009-2025 Tecnoacquisti.com
 *
 *  For support feel free to contact us on our website at http://www.arteinformatica.eu
 *
 *  @author    Arte e Informatica <shop@tecnoacquisti.com>
 *  @copyright 2009-2025 Arte e Informatica
 *  @license   One Paid Licence By WebSite Using This Module.
 *             No Rent. No Sell. No Share.
 */

(function (window) {
  "use strict";

  if (!!window.cookieChoices) {
    return window.cookieChoices;
  }

  var document = window.document;
  var supportsTextContent = "textContent" in document.body;

  var cookieChoices = (function () {
    var cookieName = "displayCookieConsent";
    var preferencesCookieName = "displayCookieConsentPreferences";
    var cookieConsentId = "cookieChoiceInfo";
    var acceptLinkId = "InformativaAccetto";
    var rejectLinkId = "InformativaReject";
    var customizeLinkId = "InformativaCustomize";
    var savePreferencesLinkId = "InformativaSavePreferences";
    var preferencesPanelId = "InformativaPreferences";
    var closeCookieBlock = "close_cookie_block";
    var currentCategories = [];
    var consentModeEnabled = false;

    function _setElementText(element, text) {
      if (supportsTextContent) {
        element.textContent = text;
      } else {
        element.innerText = text;
      }
    }

    function _createConsentText(cookieText) {
      var consentText = document.createElement("div");
      _setElementText(consentText, cookieText);
      consentText.id = "InformativaSpan";
      return consentText;
    }

    function _createActionLink(text, id) {
      var link = document.createElement("a");
      _setElementText(link, text);
      link.id = id;
      link.href = "#";
      link.style.marginLeft = "24px";
      return link;
    }

    function _createInformationLink(linkText, linkHref, linkTarget) {
      var infoLink = document.createElement("a");
      _setElementText(infoLink, linkText);
      infoLink.href = linkHref;
      infoLink.id = "InformativaClick";
      infoLink.target = linkTarget;
      infoLink.style.marginLeft = "8px";
      return infoLink;
    }

    function _normalizeCategories(categories) {
      if (!categories || !categories.length) {
        return [];
      }

      return categories;
    }

    function _createPreferencesPanel(categories, saveText) {
      var panel = document.createElement("div");
      panel.id = preferencesPanelId;
      panel.style.display = "none";

      for (var i = 0; i < categories.length; i++) {
        var category = categories[i];
        var row = document.createElement("label");
        var input = document.createElement("input");
        var textWrapper = document.createElement("span");
        var title = document.createElement("strong");
        var description = document.createElement("span");

        row.className = "artcookie-category";
        input.type = "checkbox";
        input.name = "artcookie_category_" + category.key;
        input.value = category.key;
        input.checked = !!category.required;
        input.disabled = !!category.required;

        _setElementText(title, category.label || category.key);
        _setElementText(description, category.description || "");

        textWrapper.appendChild(title);
        textWrapper.appendChild(description);
        row.appendChild(input);
        row.appendChild(textWrapper);
        panel.appendChild(row);
      }

      panel.appendChild(_createActionLink(saveText, savePreferencesLinkId));

      return panel;
    }

    function _createHeaderElement(
      cookieText,
      acceptText,
      linkText,
      linkHref,
      linkTarget,
      rejectText,
      customizeText,
      saveText,
      categories
    ) {
      var cookieConsentElement = document.createElement("div");
      var closeButtonContainer = document.createElement("span");
      var closeButtonIcon = document.createElement("i");

      cookieConsentElement.id = cookieConsentId;
      closeButtonContainer.id = closeCookieBlock;
      closeButtonContainer.style.cssText = "float: right;cursor: pointer;";
      closeButtonIcon.classList.add("material-icons");
      closeButtonIcon.textContent = "close";

      closeButtonContainer.appendChild(closeButtonIcon);
      cookieConsentElement.appendChild(closeButtonContainer);
      cookieConsentElement.appendChild(_createConsentText(cookieText));

      if (!!linkText && !!linkHref) {
        cookieConsentElement.appendChild(
          _createInformationLink(linkText, linkHref, linkTarget)
        );
      }

      if (categories.length > 1) {
        cookieConsentElement.appendChild(
          _createActionLink(customizeText, customizeLinkId)
        );
        cookieConsentElement.appendChild(
          _createPreferencesPanel(categories, saveText)
        );
      }

      cookieConsentElement.appendChild(_createActionLink(rejectText, rejectLinkId));
      cookieConsentElement.appendChild(_createActionLink(acceptText, acceptLinkId));

      return cookieConsentElement;
    }

    function _createDialogElement(
      cookieText,
      acceptText,
      linkText,
      linkHref,
      linkTarget,
      rejectText,
      customizeText,
      saveText,
      categories
    ) {
      return _createHeaderElement(
        cookieText,
        acceptText,
        linkText,
        linkHref,
        linkTarget,
        rejectText,
        customizeText,
        saveText,
        categories
      );
    }

    function _removeCookieConsent() {
      var cookieChoiceElement = document.getElementById(cookieConsentId);
      if (cookieChoiceElement != null) {
        cookieChoiceElement.parentNode.removeChild(cookieChoiceElement);
      }
    }

    function _getCookieValue(name) {
      var match = document.cookie.match(new RegExp(name + "=([^;]+)"));
      return match ? decodeURIComponent(match[1]) : null;
    }

    function _getStoredPreference() {
      return _getCookieValue(cookieName);
    }

    function _getStoredPreferences() {
      var value = _getCookieValue(preferencesCookieName);

      if (!value) {
        return null;
      }

      try {
        return JSON.parse(value);
      } catch (e) {
        return null;
      }
    }

    function _shouldDisplayConsent() {
      return !_getStoredPreference();
    }

    function _buildPreferenceMap(categories, accepted) {
      var preferences = {};

      for (var i = 0; i < categories.length; i++) {
        preferences[categories[i].key] = !!categories[i].required || !!accepted;
      }

      return preferences;
    }

    function _getPreferencesFromInputs(categories) {
      var preferences = {};

      for (var i = 0; i < categories.length; i++) {
        var category = categories[i];
        var input = document.querySelector(
          "#cookieChoiceInfo input[name=\"artcookie_category_" + category.key + "\"]"
        );

        preferences[category.key] = !!category.required || !!(input && input.checked);
      }

      return preferences;
    }

    function _getEffectivePreferences(categories) {
      var storedPreferences = _getStoredPreferences();
      var storedPreference = _getStoredPreference();

      if (storedPreferences) {
        return storedPreferences;
      }

      if (storedPreference === "y") {
        return _buildPreferenceMap(categories, true);
      }

      return _buildPreferenceMap(categories, false);
    }

    function _setConsentValue(consent, key, state) {
      if (state === "granted" || consent[key] !== "granted") {
        consent[key] = state;
      }
    }

    function _buildConsentState(categories, preferences) {
      var googleConsent = {
        ad_storage: "denied",
        analytics_storage: "denied",
        ad_user_data: "denied",
        ad_personalization: "denied",
        functionality_storage: "denied",
        personalization_storage: "denied",
        security_storage: "granted"
      };
      var microsoftConsent = {
        ad_storage: "denied"
      };

      for (var i = 0; i < categories.length; i++) {
        var category = categories[i];
        var state = preferences[category.key] ? "granted" : "denied";
        var googleKeys = category.google || [];
        var microsoftKeys = category.microsoft || [];

        for (var googleIndex = 0; googleIndex < googleKeys.length; googleIndex++) {
          _setConsentValue(googleConsent, googleKeys[googleIndex], state);
        }

        for (var microsoftIndex = 0; microsoftIndex < microsoftKeys.length; microsoftIndex++) {
          _setConsentValue(microsoftConsent, microsoftKeys[microsoftIndex], state);
        }
      }

      return {
        google: googleConsent,
        microsoft: microsoftConsent
      };
    }

    function _googleConsentDefault(consent) {
      if (typeof window.gtag === "function") {
        window.gtag("consent", "default", consent);
      } else if (window.dataLayer && Array.isArray(window.dataLayer)) {
        window.dataLayer.push({
          event: "default_consent",
          ad_storage: consent.ad_storage,
          analytics_storage: consent.analytics_storage,
          ad_user_data: consent.ad_user_data,
          ad_personalization: consent.ad_personalization,
          functionality_storage: consent.functionality_storage,
          personalization_storage: consent.personalization_storage,
          security_storage: consent.security_storage
        });
      }
    }

    function _googleConsentUpdate(consent) {
      if (typeof window.gtag === "function") {
        window.gtag("consent", "update", consent);
      } else if (window.dataLayer && Array.isArray(window.dataLayer)) {
        window.dataLayer.push({
          event: "consent_update",
          ad_storage: consent.ad_storage,
          analytics_storage: consent.analytics_storage,
          ad_user_data: consent.ad_user_data,
          ad_personalization: consent.ad_personalization,
          functionality_storage: consent.functionality_storage,
          personalization_storage: consent.personalization_storage,
          security_storage: consent.security_storage
        });
      }
    }

    function _msConsentDefault(consent) {
      if (!window.uetq) {
        return;
      }
      window.uetq.push("consent", "default", consent);
    }

    function _msConsentUpdate(consent) {
      if (!window.uetq) {
        return;
      }
      window.uetq.push("consent", "update", consent);
    }

    function _applyConsent(preferences, update) {
      if (!consentModeEnabled) {
        return;
      }

      var consent = _buildConsentState(currentCategories, preferences);

      if (update) {
        _googleConsentUpdate(consent.google);
        _msConsentUpdate(consent.microsoft);
      } else {
        _googleConsentDefault(consent.google);
        _msConsentDefault(consent.microsoft);
      }
    }

    function _writeCookie(name, value) {
      var expiryDate = new Date();
      expiryDate.setFullYear(expiryDate.getFullYear() + 1);
      document.cookie =
        name +
        "=" +
        encodeURIComponent(value) +
        "; expires=" +
        expiryDate.toGMTString() +
        "; path=/";
    }

    function _savePreferences(preferences) {
      var accepted = false;

      for (var key in preferences) {
        if (Object.prototype.hasOwnProperty.call(preferences, key) && key !== "necessary") {
          accepted = accepted || !!preferences[key];
        }
      }

      _applyConsent(preferences, true);
      _writeCookie(cookieName, accepted ? "y" : "n");
      _writeCookie(preferencesCookieName, JSON.stringify(preferences));
    }

    function _saveUserPreference(preference) {
      _savePreferences(_buildPreferenceMap(currentCategories, preference === "y"));
    }

    function _acceptLinkClick() {
      _saveUserPreference("y");
      _removeCookieConsent();
      return false;
    }

    function _rejectLinkClick() {
      _saveUserPreference("n");
      _removeCookieConsent();
      return false;
    }

    function _savePreferencesClick() {
      _savePreferences(_getPreferencesFromInputs(currentCategories));
      _removeCookieConsent();
      return false;
    }

    function _togglePreferencesPanel() {
      var panel = document.getElementById(preferencesPanelId);

      if (!panel) {
        return false;
      }

      panel.style.display = panel.style.display === "none" ? "block" : "none";
      return false;
    }

    function _showCookieConsent(
      cookieText,
      acceptText,
      linkText,
      linkHref,
      linkTarget,
      isDialog,
      rejectText,
      customizeText,
      saveText,
      categories,
      enableConsentMode
    ) {
      currentCategories = _normalizeCategories(categories);
      consentModeEnabled = !!enableConsentMode;
      _applyConsent(_getEffectivePreferences(currentCategories), false);

      if (_shouldDisplayConsent()) {
        _removeCookieConsent();

        var consentElement = isDialog
          ? _createDialogElement(
            cookieText,
            acceptText,
            linkText,
            linkHref,
            linkTarget,
            rejectText,
            customizeText,
            saveText,
            currentCategories
          )
          : _createHeaderElement(
            cookieText,
            acceptText,
            linkText,
            linkHref,
            linkTarget,
            rejectText,
            customizeText,
            saveText,
            currentCategories
          );

        var fragment = document.createDocumentFragment();
        fragment.appendChild(consentElement);
        document.body.appendChild(fragment.cloneNode(true));
      }
    }

    function showCookieConsentBar(
      cookieText,
      acceptText,
      linkText,
      linkHref,
      linkTarget,
      rejectText,
      customizeText,
      saveText,
      categories,
      enableConsentMode
    ) {
      _showCookieConsent(
        cookieText,
        acceptText,
        linkText,
        linkHref,
        linkTarget,
        false,
        rejectText,
        customizeText || "Customize",
        saveText || "Save preferences",
        categories || [],
        enableConsentMode
      );
    }

    function showCookieConsentDialog(
      cookieText,
      acceptText,
      linkText,
      linkHref,
      linkTarget,
      rejectText,
      customizeText,
      saveText,
      categories,
      enableConsentMode
    ) {
      _showCookieConsent(
        cookieText,
        acceptText,
        linkText,
        linkHref,
        linkTarget,
        true,
        rejectText,
        customizeText || "Customize",
        saveText || "Save preferences",
        categories || [],
        enableConsentMode
      );
    }

    document.addEventListener("click", function (event) {
      var target = event.target;

      if (!target) {
        return;
      }

      if (target.id === acceptLinkId) {
        event.preventDefault();
        _acceptLinkClick();
        return;
      }

      if (target.id === rejectLinkId) {
        event.preventDefault();
        _rejectLinkClick();
        return;
      }

      if (target.id === customizeLinkId) {
        event.preventDefault();
        _togglePreferencesPanel();
        return;
      }

      if (target.id === savePreferencesLinkId) {
        event.preventDefault();
        _savePreferencesClick();
        return;
      }

      if (
        target.id === closeCookieBlock ||
        (typeof target.closest === "function" &&
          target.closest("#" + closeCookieBlock))
      ) {
        event.preventDefault();
        _rejectLinkClick();
      }
    });

    var exports = {};
    exports.showCookieConsentBar = showCookieConsentBar;
    exports.showCookieConsentDialog = showCookieConsentDialog;
    exports._acceptLinkClick = _acceptLinkClick;
    exports._rejectLinkClick = _rejectLinkClick;

    return exports;
  })();

  window.cookieChoices = cookieChoices;
  return cookieChoices;
})(this);
