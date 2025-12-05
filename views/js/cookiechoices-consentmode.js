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

  // IE8 fallback
  var supportsTextContent = "textContent" in document.body;

  var cookieChoices = (function () {
    var cookieName = "displayCookieConsent";
    var cookieConsentId = "cookieChoiceInfo";
    var acceptLinkId = "InformativaAccetto";
    var rejectLinkId = "InformativaReject";
    var closeCookieBlock = "close_cookie_block";

    /**
     * Helpers
     */

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

    function _createAcceptLink(acceptText) {
      var acceptLink = document.createElement("a");
      _setElementText(acceptLink, acceptText);
      acceptLink.id = acceptLinkId;
      acceptLink.href = "#";
      acceptLink.style.marginLeft = "24px";
      return acceptLink;
    }

    function _createRejectLink(rejectText) {
      var rejectLink = document.createElement("a");
      _setElementText(rejectLink, rejectText);
      rejectLink.id = rejectLinkId;
      rejectLink.href = "#";
      rejectLink.style.marginLeft = "24px";
      return rejectLink;
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

    function _createHeaderElement(
      cookieText,
      acceptText,
      linkText,
      linkHref,
      linkTarget,
      rejectText
    ) {
      var butterBarStyles = "";

      var cookieConsentElement = document.createElement("div");
      cookieConsentElement.id = cookieConsentId;
      cookieConsentElement.style.cssText = butterBarStyles;

      var closeButtonContainer = document.createElement("span");
      closeButtonContainer.id = closeCookieBlock;
      closeButtonContainer.style.cssText = "float: right;cursor: pointer;";

      var closeButtonIcon = document.createElement("i");
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

      cookieConsentElement.appendChild(_createRejectLink(rejectText));
      cookieConsentElement.appendChild(_createAcceptLink(acceptText));

      return cookieConsentElement;
    }

    function _createDialogElement(
      cookieText,
      acceptText,
      linkText,
      linkHref,
      linkTarget
    ) {
      var glassStyle =
        "position:fixed;width:100%;height:100%;z-index:9999;" +
        "bottom:0;left:0;opacity:0.5;filter:alpha(opacity=50);" +
        "background-color:#ccc;";
      var dialogStyle = "z-index:10000;position:fixed;left:50%;top:50%";
      var contentStyle =
        "position:relative;left:-50%;margin-top:-25%;" +
        "background-color:#fff;padding:20px;box-shadow:4px 4px 25px #888;";

      var cookieConsentElement = document.createElement("div");
      cookieConsentElement.id = cookieConsentId;

      var glassPanel = document.createElement("div");
      glassPanel.style.cssText = glassStyle;

      var content = document.createElement("div");
      content.style.cssText = contentStyle;

      var dialog = document.createElement("div");
      dialog.style.cssText = dialogStyle;

      var acceptLink = _createAcceptLink(acceptText);
      acceptLink.style.display = "block";
      acceptLink.style.textAlign = "right";
      acceptLink.style.marginTop = "8px";

      content.appendChild(_createConsentText(cookieText));
      if (!!linkText && !!linkHref) {
        content.appendChild(_createInformationLink(linkText, linkHref));
      }
      content.appendChild(acceptLink);
      dialog.appendChild(content);
      cookieConsentElement.appendChild(glassPanel);
      cookieConsentElement.appendChild(dialog);
      return cookieConsentElement;
    }

    function _removeCookieConsent() {
      var cookieChoiceElement = document.getElementById(cookieConsentId);
      if (cookieChoiceElement != null) {
        cookieChoiceElement.parentNode.removeChild(cookieChoiceElement);
      }
    }

    function _getStoredPreference() {
      var match = document.cookie.match(
        new RegExp(cookieName + "=([^;]+)")
      );
      return match ? match[1] : null; // 'y', 'n' o null
    }

    function _shouldDisplayConsent() {
      // Mostra il banner solo se il cookie non è presente
      return !_getStoredPreference();
    }

    /**
     * Google Consent Mode v2
     * Usa gtag se disponibile, altrimenti dataLayer.push().
     */

    function _googleConsentDefault(state) {
      // state: 'granted' | 'denied'
      if (typeof window.gtag === "function") {
        window.gtag("consent", "default", {
          ad_storage: state,
          analytics_storage: state,
          ad_user_data: state,
          ad_personalization: state,
        });
      } else if (window.dataLayer && Array.isArray(window.dataLayer)) {
        // Fallback per GTM: consenti di leggere lo stato con un trigger
        window.dataLayer.push({
          event: "default_consent",
          ad_storage: state,
          analytics_storage: state,
          ad_user_data: state,
          ad_personalization: state,
        });
      }
    }

    function _googleConsentUpdate(state) {
      // state: 'granted' | 'denied'
      if (typeof window.gtag === "function") {
        window.gtag("consent", "update", {
          ad_storage: state,
          analytics_storage: state,
          ad_user_data: state,
          ad_personalization: state,
        });
      } else if (window.dataLayer && Array.isArray(window.dataLayer)) {
        window.dataLayer.push({
          event: "consent_update",
          ad_storage: state,
          analytics_storage: state,
          ad_user_data: state,
          ad_personalization: state,
        });
      }
    }

    /**
     * Microsoft UET Consent Mode
     * Usa window.uetq se presente.
     */

    function _msConsentDefault(state) {
      // state: 'granted' | 'denied'
      if (!window.uetq) {
        return;
      }
      window.uetq.push("consent", "default", {
        ad_storage: state,
      });
    }

    function _msConsentUpdate(state) {
      // state: 'granted' | 'denied'
      if (!window.uetq) {
        return;
      }
      window.uetq.push("consent", "update", {
        ad_storage: state,
      });
    }

    /**
     * Applica il default di Consent Mode in base al cookie esistente.
     * - Primo accesso (nessun cookie): default = denied
     * - Visite successive:
     *    'y' => default granted
     *    'n' => default denied
     *
     * NB: questo è un approccio "basic consent mode".
     */
    function _applyInitialConsentFromCookie() {
      var pref = _getStoredPreference();
      var state = "denied";

      if (pref === "y") {
        state = "granted";
      } else if (pref === "n") {
        state = "denied";
      }

      _googleConsentDefault(state);
      _msConsentDefault(state);
    }

    /**
     * Salva la preferenza utente su cookie e manda l'update
     * ai motori di consent (Google + Microsoft).
     *
     * preference: 'y' (accetto) | 'n' (rifiuto)
     */
    function _saveUserPreference(preference) {
      var state = preference === "y" ? "granted" : "denied";

      // Aggiorna Google + Microsoft
      _googleConsentUpdate(state);
      _msConsentUpdate(state);

      // Persistenza locale a 1 anno
      var expiryDate = new Date();
      expiryDate.setFullYear(expiryDate.getFullYear() + 1);
      document.cookie =
        cookieName +
        "=" +
        preference +
        "; expires=" +
        expiryDate.toGMTString() +
        "; path=/";
    }

    /**
     * Event handlers per i pulsanti del banner
     */

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

    /**
     * Funzioni pubbliche per mostrare il banner
     */

    function _showCookieConsent(
      cookieText,
      acceptText,
      linkText,
      linkHref,
      linkTarget,
      isDialog,
      rejectText
    ) {
      if (_shouldDisplayConsent()) {
        _removeCookieConsent();
        var consentElement = isDialog
          ? _createDialogElement(
            cookieText,
            acceptText,
            linkText,
            linkHref,
            linkTarget
          )
          : _createHeaderElement(
            cookieText,
            acceptText,
            linkText,
            linkHref,
            linkTarget,
            rejectText
          );

        var fragment = document.createDocumentFragment();
        fragment.appendChild(consentElement);
        document.body.appendChild(fragment.cloneNode(true));

        document.getElementById(acceptLinkId).onclick = _acceptLinkClick;
        document.getElementById(rejectLinkId).onclick = _rejectLinkClick;
        var closeBtn = document.getElementById(closeCookieBlock);
        if (closeBtn) {
          closeBtn.onclick = _rejectLinkClick;
        }
      }
    }

    function showCookieConsentBar(
      cookieText,
      acceptText,
      linkText,
      linkHref,
      linkTarget,
      rejectText
    ) {
      _showCookieConsent(
        cookieText,
        acceptText,
        linkText,
        linkHref,
        linkTarget,
        false,
        rejectText
      );
    }

    function showCookieConsentDialog(
      cookieText,
      acceptText,
      linkText,
      linkHref,
      linkTarget,
      rejectText
    ) {
      _showCookieConsent(
        cookieText,
        acceptText,
        linkText,
        linkHref,
        linkTarget,
        true,
        rejectText
      );
    }

    /**
     * Init immediato: imposta il default di Consent Mode
     * PRIMA che gli script analitici/adv inizino a fare richieste.
     *
     * (Idealmente questo file va caricato in <head>, prima di gtag/GTAG/UET,
     * oppure aggiungi un frammento minimo inline nel template header, vedi note sotto.)
     */
    _applyInitialConsentFromCookie();

    // API esposta (stessa dell’originale)
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
