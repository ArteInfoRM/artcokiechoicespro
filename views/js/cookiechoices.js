/**
 *  2009-2025 Tecnoacquisti.com
 *
 *  For support feel free to contact us on our website at http://www.arteinformatica.eu
 *
 *  @author    Arte e Informatica <admin@arteinformatica.eu>
 *  @copyright 2009-2025 Arte e Informatica
 *  @license   One Paid Licence By WebSite Using This Module.
 *             No Rent. No Sell. No Share.
 */

(function(window) {

  if (!!window.cookieChoices) {
    return window.cookieChoices;
  }

  var document = window.document;

  // IE8 does not support textContent, so we should fallback to innerText.
  var supportsTextContent = 'textContent' in document.body;

  var cookieChoices = (function() {

    var cookieName = 'displayCookieConsent';
    var cookieConsentId = 'cookieChoiceInfo';
    var acceptLinkId = 'InformativaAccetto';
    var rejectLinkId = 'InformativaReject';
    var closeCookieBlock = 'close_cookie_block';

    function _createHeaderElement(cookieText, acceptText, linkText, linkHref, linkTarget, rejectText) {
      var butterBarStyles = '';

      var cookieConsentElement = document.createElement('div');
      cookieConsentElement.id = cookieConsentId;
      cookieConsentElement.style.cssText = butterBarStyles;

      var closeButtonContainer = document.createElement('span');
      closeButtonContainer.id = 'close_cookie_block';
      closeButtonContainer.style.cssText = 'float: right;cursor: pointer;';

      var closeButtonIcon = document.createElement('i');
      closeButtonIcon.classList.add('material-icons');
      closeButtonIcon.textContent = 'close';

      closeButtonContainer.appendChild(closeButtonIcon);
      cookieConsentElement.appendChild(closeButtonContainer);

      cookieConsentElement.appendChild(_createConsentText(cookieText));

      if (!!linkText && !!linkHref) {
        cookieConsentElement.appendChild(_createInformationLink(linkText, linkHref, linkTarget));
      }

      cookieConsentElement.appendChild(_createRejectLink(rejectText));
      cookieConsentElement.appendChild(_createAcceptLink(acceptText));

      return cookieConsentElement;
    }

    function _createDialogElement(cookieText, acceptText, linkText, linkHref, linkTarget) {
		
      var glassStyle = 'position:fixed;width:100%;height:100%;z-index:9999;' +
          'bottom:0;left:0;opacity:0.5;filter:alpha(opacity=50);' +
          'background-color:#ccc;';
      var dialogStyle = 'z-index:10000;position:fixed;left:50%;top:50%';
      var contentStyle = 'position:relative;left:-50%;margin-top:-25%;' +
          'background-color:#fff;padding:20px;box-shadow:4px 4px 25px #888;';

      var cookieConsentElement = document.createElement('div');
      cookieConsentElement.id = cookieConsentId;

      var glassPanel = document.createElement('div');
      glassPanel.style.cssText = glassStyle;

      var content = document.createElement('div');
      content.style.cssText = contentStyle;

      var dialog = document.createElement('div');
      dialog.style.cssText = dialogStyle;

      var acceptLink = _createAcceptLink(acceptText);
      acceptLink.style.display = 'block';
      acceptLink.style.textAlign = 'right';
      acceptLink.style.marginTop = '8px';

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

    function _setElementText(element, text) {
      if (supportsTextContent) {
        element.textContent = text;
      } else {
        element.innerText = text;
      }
    }

    function _createConsentText(cookieText) {
      var consentText = document.createElement('div');
      _setElementText(consentText, cookieText);
      consentText.id = 'InformativaSpan';
      return consentText;
    }

    function _createAcceptLink(acceptText) {
      var acceptLink = document.createElement('a');
      _setElementText(acceptLink, acceptText);
      acceptLink.id = 'InformativaAccetto';
      acceptLink.href = '#';
      acceptLink.style.marginLeft = '24px';
      return acceptLink;
    }

    function _createRejectLink(rejectText) {
      var rejectLink = document.createElement('a');
      _setElementText(rejectLink, rejectText);
      rejectLink.id = 'InformativaReject';
      rejectLink.href = '#';
      rejectLink.style.marginLeft = '24px';
      return rejectLink;
    }

    function _createInformationLink(linkText, linkHref, linkTarget) {
      var infoLink = document.createElement('a');
      _setElementText(infoLink, linkText);
      infoLink.href = linkHref;
      infoLink.id = 'InformativaClick';
      infoLink.target = linkTarget;
      infoLink.style.marginLeft = '8px';
      return infoLink;
    }

    function _acceptLinkClick() {
      _saveUserPreference('y');
      _removeCookieConsent();
      return false;
    }

    function _rejectLinkClick() {
      _saveUserPreference('n');
      _removeCookieConsent();
      return false;
    }

    function _showCookieConsent(cookieText, acceptText, linkText, linkHref, linkTarget, isDialog, rejectText) {
      if (_shouldDisplayConsent()) {
        _removeCookieConsent();
        var consentElement = (isDialog) ?
            _createDialogElement(cookieText, acceptText, linkText, linkHref, linkTarget) :
            _createHeaderElement(cookieText, acceptText, linkText, linkHref, linkTarget, rejectText);

        var fragment = document.createDocumentFragment();
        fragment.appendChild(consentElement);
        document.body.appendChild(fragment.cloneNode(true));

        document.getElementById(acceptLinkId).onclick = _acceptLinkClick;
        document.getElementById(rejectLinkId).onclick = _rejectLinkClick;
        document.getElementById(closeCookieBlock).onclick = _rejectLinkClick;
      }
    }

    function showCookieConsentBar(cookieText, acceptText, linkText, linkHref, linkTarget, rejectText) {
      _showCookieConsent(cookieText, acceptText, linkText, linkHref, linkTarget, false, rejectText);
    }

    function showCookieConsentDialog(cookieText, acceptText, linkText, linkHref, linkTarget, rejectText) {
      _showCookieConsent(cookieText, acceptText, linkText, linkHref, linkTarget, true, rejectText);
    }

    function _removeCookieConsent() {
      var cookieChoiceElement = document.getElementById(cookieConsentId);
      if (cookieChoiceElement != null) {
        cookieChoiceElement.parentNode.removeChild(cookieChoiceElement);
      }
    }

    function _saveUserPreference(preference) {
      // Set the cookie expiry to one year after today.
      var expiryDate = new Date();
      expiryDate.setFullYear(expiryDate.getFullYear() + 1);
      document.cookie = cookieName + '='+preference+'; expires=' + expiryDate.toGMTString() + ('; path=/');
    }

    function _shouldDisplayConsent() {
      // Display the header only if the cookie has not been set.
      return !document.cookie.match(new RegExp(cookieName + '=([^;]+)'));
    }

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
