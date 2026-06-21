/**
 *  2009-2026 Tecnoacquisti.com
 *
 *  For support feel free to contact us on our website at http://www.arteinformatica.eu
 *
 *  @author    Arte e Informatica <shop@tecnoacquisti.com>
 *  @copyright 2009-2026 Arte e Informatica
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
    var consentVersionCookieName = "displayCookieConsentVersion";
    var cookieConsentId = "cookieChoiceInfo";
    var acceptLinkId = "InformativaAccetto";
    var rejectLinkId = "InformativaReject";
    var customizeLinkId = "InformativaCustomize";
    var cancelPreferencesLinkId = "InformativaCancelPreferences";
    var savePreferencesLinkId = "InformativaSavePreferences";
    var preferencesPanelId = "InformativaPreferences";
    var preferencesModalId = "InformativaPreferencesModal";
    var preferencesOverlayId = "InformativaPreferencesOverlay";
    var closeCookieBlock = "close_cookie_block";
    var currentCategories = [];
    var consentModeEnabled = false;
    var consentLogEnabled = false;
    var consentLogUrl = "";
    var consentLogToken = "";
    var currentDisallowUrl = "#";
    var currentConsentVersion = "1";
    var preferencesOpenedAt = 0;
    var lastFocusedElement = null;
    var focusableSelector = [
      "a[href]",
      "button:not([disabled])",
      "input:not([disabled])",
      "select:not([disabled])",
      "textarea:not([disabled])",
      "[tabindex]:not([tabindex=\"-1\"])"
    ].join(",");

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

    function _createPreferencesPanel(categories) {
      var panel = document.createElement("div");
      var preferences = _getEffectivePreferences(categories);
      panel.id = preferencesPanelId;

      for (var i = 0; i < categories.length; i++) {
        var category = categories[i];
        var row = document.createElement("label");
        var input = document.createElement("input");
        var textWrapper = document.createElement("span");
        var title = document.createElement("strong");
        var description = document.createElement("span");

        row.className = "artcookie-category";
        textWrapper.className = "artcookie-category-text";
        input.type = "checkbox";
        input.name = "artcookie_category_" + category.key;
        input.value = category.key;
        input.checked = !!category.required || !!preferences[category.key];
        input.disabled = !!category.required;

        _setElementText(title, category.label || category.key);
        _setElementText(description, category.description || "");

        textWrapper.appendChild(title);
        textWrapper.appendChild(description);
        row.appendChild(input);
        row.appendChild(textWrapper);
        panel.appendChild(row);
      }

      return panel;
    }

    function _createModalActionLink(text, id) {
      var link = _createActionLink(text, id);
      link.className = "artcookie-modal-button";
      return link;
    }

    function _createPreferencesModal(
      acceptAllText,
      rejectAllText,
      acceptSelectionText,
      cancelText,
      titleText,
      categories,
      disallowUrl
    ) {
      var modal = document.createElement("div");
      var dialog = document.createElement("div");
      var header = document.createElement("div");
      var title = document.createElement("p");
      var body = document.createElement("div");
      var footer = document.createElement("div");
      var footerLeft = document.createElement("div");
      var footerRight = document.createElement("div");
      var cancelLink = _createModalActionLink(cancelText, cancelPreferencesLinkId);
      var rejectLink = _createModalActionLink(rejectAllText, rejectLinkId);
      var saveLink = _createModalActionLink(acceptSelectionText, savePreferencesLinkId);
      var acceptLink = _createModalActionLink(acceptAllText, acceptLinkId);

      modal.id = preferencesModalId;
      modal.className = "artcookie-preferences-modal";
      modal.setAttribute("aria-hidden", "true");

      dialog.className = "artcookie-preferences-dialog";
      dialog.setAttribute("role", "dialog");
      dialog.setAttribute("aria-modal", "true");
      dialog.setAttribute("aria-labelledby", "InformativaPreferencesTitle");
      dialog.setAttribute("tabindex", "-1");

      header.className = "artcookie-preferences-header";
      title.id = "InformativaPreferencesTitle";
      title.className = "artcookie-preferences-title";
      _setElementText(title, titleText);

      body.className = "artcookie-preferences-body";
      body.appendChild(_createPreferencesPanel(categories));

      footer.className = "artcookie-preferences-footer";
      footerLeft.className = "artcookie-preferences-footer-left";
      footerRight.className = "artcookie-preferences-footer-right";

      cancelLink.href = disallowUrl || "#";
      rejectLink.className += " artcookie-reject-button";
      saveLink.className += " artcookie-save-button";
      acceptLink.className += " artcookie-accept-button";

      footerLeft.appendChild(cancelLink);
      footerRight.appendChild(rejectLink);
      footerRight.appendChild(saveLink);
      footerRight.appendChild(acceptLink);
      footer.appendChild(footerLeft);
      footer.appendChild(footerRight);

      header.appendChild(title);
      dialog.appendChild(header);
      dialog.appendChild(body);
      dialog.appendChild(footer);
      modal.appendChild(dialog);

      return modal;
    }

    function _createPreferencesOverlay() {
      var overlay = document.createElement("div");
      overlay.id = preferencesOverlayId;
      overlay.className = "artcookie-preferences-overlay";
      overlay.setAttribute("aria-hidden", "true");
      return overlay;
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
      closeButtonContainer.setAttribute("role", "button");
      closeButtonContainer.setAttribute("tabindex", "0");
      closeButtonContainer.setAttribute("aria-label", "Reject optional cookies and close");
      closeButtonIcon.classList.add("material-icons");
      closeButtonIcon.setAttribute("aria-hidden", "true");
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

    function _removePreferencesModal() {
      var modal = document.getElementById(preferencesModalId);
      var overlay = document.getElementById(preferencesOverlayId);

      if (modal != null) {
        modal.parentNode.removeChild(modal);
      }

      if (overlay != null) {
        overlay.parentNode.removeChild(overlay);
      }
    }

    function _ensurePreferencesModal(
      acceptAllText,
      rejectAllText,
      acceptSelectionText,
      cancelText,
      titleText,
      categories,
      disallowUrl
    ) {
      _removePreferencesModal();

      var fragment = document.createDocumentFragment();
      fragment.appendChild(_createPreferencesOverlay());
      fragment.appendChild(
        _createPreferencesModal(
          acceptAllText,
          rejectAllText,
          acceptSelectionText,
          cancelText,
          titleText,
          categories,
          disallowUrl
        )
      );
      document.body.appendChild(fragment.cloneNode(true));
    }

    function _removeCookieConsent() {
      var cookieChoiceElement = document.getElementById(cookieConsentId);
      if (cookieChoiceElement != null) {
        cookieChoiceElement.parentNode.removeChild(cookieChoiceElement);
      }
    }

    function _closePreferencesModal() {
      var modal = document.getElementById(preferencesModalId);
      var overlay = document.getElementById(preferencesOverlayId);

      if (modal) {
        modal.setAttribute("aria-hidden", "true");
      }

      if (overlay) {
        overlay.setAttribute("aria-hidden", "true");
      }

      if (
        lastFocusedElement &&
        document.body.contains(lastFocusedElement) &&
        typeof lastFocusedElement.focus === "function"
      ) {
        lastFocusedElement.focus();
      }
    }

    function _openPreferencesModal() {
      var modal = document.getElementById(preferencesModalId);
      var overlay = document.getElementById(preferencesOverlayId);
      var dialog;
      var focusableElements;

      if (!modal || !overlay) {
        return false;
      }

      lastFocusedElement = document.activeElement;
      modal.setAttribute("aria-hidden", "false");
      overlay.setAttribute("aria-hidden", "false");
      preferencesOpenedAt = new Date().getTime();
      dialog = modal.querySelector(".artcookie-preferences-dialog");
      focusableElements = modal.querySelectorAll(focusableSelector);

      if (focusableElements.length > 0) {
        focusableElements[0].focus();
      } else if (dialog && typeof dialog.focus === "function") {
        dialog.focus();
      }

      return true;
    }

    function _isDisallowPreferencesLink(element) {
      var link = element;

      if (!link || link.id === cancelPreferencesLinkId) {
        return false;
      }

      if (typeof link.closest === "function") {
        link = link.closest("a");
      }

      if (!link || link.id === cancelPreferencesLinkId || !link.href) {
        return false;
      }

      return (
        link.href.indexOf("module/artcokiechoicespro/disallow") !== -1 ||
        (
          link.href.indexOf("module=artcokiechoicespro") !== -1 &&
          link.href.indexOf("controller=disallow") !== -1
        )
      );
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

    function _getStoredConsentVersion() {
      return _getCookieValue(consentVersionCookieName);
    }

    function _isCurrentConsentVersion() {
      return _getStoredConsentVersion() === currentConsentVersion;
    }

    function _shouldDisplayConsent() {
      return !_getStoredPreference() || !_isCurrentConsentVersion();
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
          "#" + preferencesModalId + " input[name=\"artcookie_category_" + category.key + "\"]"
        );

        preferences[category.key] = !!category.required || !!(input && input.checked);
      }

      return preferences;
    }

    function _getEffectivePreferences(categories) {
      var storedPreferences = _getStoredPreferences();
      var storedPreference = _getStoredPreference();

      if (storedPreferences && _isCurrentConsentVersion()) {
        return storedPreferences;
      }

      if (storedPreference === "y" && _isCurrentConsentVersion()) {
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
      var attributes = "; path=/; SameSite=Lax";

      expiryDate.setMonth(expiryDate.getMonth() + 6);

      if (window.location && window.location.protocol === "https:") {
        attributes += "; Secure";
      }

      document.cookie =
        name +
        "=" +
        encodeURIComponent(value) +
        "; expires=" +
        expiryDate.toUTCString() +
        attributes;
    }

    function _sendConsentLog(preferences, action) {
      var request;
      var params;

      if (!consentLogEnabled || !consentLogUrl) {
        return;
      }

      try {
        request = new XMLHttpRequest();
        params = [
          "token=" + encodeURIComponent(consentLogToken),
          "action=" + encodeURIComponent(action),
          "consent_version=" + encodeURIComponent(currentConsentVersion),
          "preferences=" + encodeURIComponent(JSON.stringify(preferences))
        ].join("&");

        request.open("POST", consentLogUrl, true);
        request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
        request.send(params);
      } catch (e) {
        return;
      }
    }

    function _savePreferences(preferences, action) {
      var accepted = false;

      for (var key in preferences) {
        if (Object.prototype.hasOwnProperty.call(preferences, key) && key !== "necessary") {
          accepted = accepted || !!preferences[key];
        }
      }

      _applyConsent(preferences, true);
      _writeCookie(cookieName, accepted ? "y" : "n");
      _writeCookie(preferencesCookieName, JSON.stringify(preferences));
      _writeCookie(consentVersionCookieName, currentConsentVersion);
      _sendConsentLog(preferences, action || "save_selection");
    }

    function _saveUserPreference(preference) {
      _savePreferences(
        _buildPreferenceMap(currentCategories, preference === "y"),
        preference === "y" ? "accept_all" : "reject_all"
      );
    }

    function _acceptLinkClick() {
      _saveUserPreference("y");
      _removeCookieConsent();
      _closePreferencesModal();
      return false;
    }

    function _rejectLinkClick() {
      _saveUserPreference("n");
      _removeCookieConsent();
      _closePreferencesModal();
      return false;
    }

    function _savePreferencesClick() {
      _savePreferences(_getPreferencesFromInputs(currentCategories), "save_selection");
      _removeCookieConsent();
      _closePreferencesModal();
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
      cancelText,
      rejectAllText,
      acceptSelectionText,
      acceptAllText,
      titleText,
      disallowUrl,
      categories,
      enableConsentMode,
      consentVersion,
      enableConsentLog,
      logUrl,
      logToken
    ) {
      currentCategories = _normalizeCategories(categories);
      consentModeEnabled = !!enableConsentMode;
      consentLogEnabled = !!enableConsentLog;
      consentLogUrl = logUrl || "";
      consentLogToken = logToken || "";
      currentDisallowUrl = disallowUrl || "#";
      currentConsentVersion = consentVersion || "1";
      _applyConsent(_getEffectivePreferences(currentCategories), false);

      if (currentCategories.length > 0) {
        _ensurePreferencesModal(
          acceptAllText,
          rejectAllText,
          acceptSelectionText,
          cancelText,
          titleText,
          currentCategories,
          currentDisallowUrl
        );
      }

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
            acceptSelectionText,
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
            acceptSelectionText,
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
      cancelText,
      rejectAllText,
      acceptSelectionText,
      acceptAllText,
      titleText,
      disallowUrl,
      categories,
      enableConsentMode,
      consentVersion,
      enableConsentLog,
      logUrl,
      logToken
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
        cancelText || "Cancel",
        rejectAllText || "Reject all",
        acceptSelectionText || "Accept selection",
        acceptAllText || "Accept all",
        titleText || "Cookie preferences",
        disallowUrl || "#",
        categories || [],
        enableConsentMode,
        consentVersion || "1",
        enableConsentLog,
        logUrl || "",
        logToken || ""
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
      cancelText,
      rejectAllText,
      acceptSelectionText,
      acceptAllText,
      titleText,
      disallowUrl,
      categories,
      enableConsentMode,
      consentVersion,
      enableConsentLog,
      logUrl,
      logToken
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
        cancelText || "Cancel",
        rejectAllText || "Reject all",
        acceptSelectionText || "Accept selection",
        acceptAllText || "Accept all",
        titleText || "Cookie preferences",
        disallowUrl || "#",
        categories || [],
        enableConsentMode,
        consentVersion || "1",
        enableConsentLog,
        logUrl || "",
        logToken || ""
      );
    }

    function _trapModalFocus(event) {
      var modal = document.getElementById(preferencesModalId);
      var focusableElements;
      var firstElement;
      var lastElement;

      if (
        !modal ||
        modal.getAttribute("aria-hidden") !== "false" ||
        event.key !== "Tab"
      ) {
        return;
      }

      focusableElements = modal.querySelectorAll(focusableSelector);

      if (!focusableElements.length) {
        event.preventDefault();
        return;
      }

      firstElement = focusableElements[0];
      lastElement = focusableElements[focusableElements.length - 1];

      if (event.shiftKey && document.activeElement === firstElement) {
        event.preventDefault();
        lastElement.focus();
      } else if (!event.shiftKey && document.activeElement === lastElement) {
        event.preventDefault();
        firstElement.focus();
      }
    }

    document.addEventListener("click", function (event) {
      var target = event.target;

      if (!target) {
        return;
      }

      if (target.id === acceptLinkId) {
        event.preventDefault();
        event.stopPropagation();
        _acceptLinkClick();
        return;
      }

      if (target.id === rejectLinkId) {
        event.preventDefault();
        event.stopPropagation();
        _rejectLinkClick();
        return;
      }

      if (target.id === customizeLinkId) {
        event.preventDefault();
        event.stopPropagation();
        _openPreferencesModal();
        return;
      }

      if (target.id === savePreferencesLinkId) {
        event.preventDefault();
        event.stopPropagation();
        _savePreferencesClick();
        return;
      }

      if (
        target.getAttribute("data-artcookie-preferences") === "1" ||
        (typeof target.closest === "function" &&
          target.closest("[data-artcookie-preferences=\"1\"]")) ||
        _isDisallowPreferencesLink(target)
      ) {
        if (_openPreferencesModal()) {
          event.preventDefault();
          event.stopPropagation();
        }
        return;
      }

      if (target.id === preferencesOverlayId) {
        event.preventDefault();
        event.stopPropagation();
        if (new Date().getTime() - preferencesOpenedAt < 300) {
          return;
        }
        _closePreferencesModal();
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

    document.addEventListener("keydown", function (event) {
      var target = event.target;

      if (event.key === "Escape") {
        _closePreferencesModal();
        return;
      }

      _trapModalFocus(event);

      if (
        target &&
        (
          target.id === closeCookieBlock ||
          (typeof target.closest === "function" &&
            target.closest("#" + closeCookieBlock))
        ) &&
        (event.key === "Enter" || event.key === " ")
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
