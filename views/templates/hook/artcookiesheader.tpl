{*
**
*  2009-2026 Tecnoacquisti.com
*
*  For support feel free to contact us on our website at http://www.arteinformatica.eu
*
*  @author    Arte e Informatica <shop@tecnoacquisti.com>
*  @copyright 2009-2026 Arte e Informatica
*  @license   One Paid Licence By WebSite Using This Module. No Rent. No Sell. No Share.
*
*}

{if $art_consentmode|intval == 1}
<script>
  window.dataLayer = window.dataLayer || [];
  window.gtag = window.gtag || function() {ldelim}
    window.dataLayer.push(arguments);
  {rdelim};
  (function() {ldelim}
    var categories = [];
    var preferences = null;
    var consentVersion = '{$art_consent_version|escape:'javascript':'UTF-8'}';
    var storedConsentVersion = null;
    var googleConsent = {ldelim}
      ad_storage: 'denied',
      analytics_storage: 'denied',
      ad_user_data: 'denied',
      ad_personalization: 'denied',
      functionality_storage: 'denied',
      personalization_storage: 'denied',
      security_storage: 'granted'
    {rdelim};
    var microsoftConsent = {ldelim}
      ad_storage: 'denied'
    {rdelim};

    function readCookie(name) {ldelim}
      var parts = document.cookie ? document.cookie.split(';') : [];
      var prefix = name + '=';
      var value;

      for (var index = 0; index < parts.length; index++) {ldelim}
        value = parts[index].replace(/^\s+/, '');

        if (value.indexOf(prefix) === 0) {ldelim}
          return decodeURIComponent(value.substring(prefix.length));
        {rdelim}
      {rdelim}

      return null;
    {rdelim}

    function setConsentValue(consent, key, state) {ldelim}
      if (state === 'granted' || consent[key] !== 'granted') {ldelim}
        consent[key] = state;
      {rdelim}
    {rdelim}

    try {ldelim}
      categories = JSON.parse(window.atob('{$art_cookie_categories_base64|escape:'javascript':'UTF-8'}'));
    {rdelim} catch (error) {ldelim}
      categories = [];
    {rdelim}

    try {ldelim}
      storedConsentVersion = readCookie('displayCookieConsentVersion');
      if (storedConsentVersion === consentVersion) {ldelim}
        preferences = JSON.parse(readCookie('displayCookieConsentPreferences'));
      {rdelim}
    {rdelim} catch (error) {ldelim}
      preferences = null;
    {rdelim}

    if (!preferences) {ldelim}
      preferences = {ldelim}
        necessary: true
      {rdelim};
    {rdelim}

    for (var categoryIndex = 0; categoryIndex < categories.length; categoryIndex++) {ldelim}
      var category = categories[categoryIndex];
      var state = preferences[category.key] ? 'granted' : 'denied';
      var googleKeys = category.google || [];
      var microsoftKeys = category.microsoft || [];

      for (var googleIndex = 0; googleIndex < googleKeys.length; googleIndex++) {ldelim}
        setConsentValue(googleConsent, googleKeys[googleIndex], state);
      {rdelim}

      for (var microsoftIndex = 0; microsoftIndex < microsoftKeys.length; microsoftIndex++) {ldelim}
        setConsentValue(microsoftConsent, microsoftKeys[microsoftIndex], state);
      {rdelim}
    {rdelim}

    window.gtag('consent', 'default', googleConsent);
    window.uetq = window.uetq || [];
    window.uetq.push('consent', 'default', microsoftConsent);
  {rdelim}());
</script>
{/if}
<style type="text/css">
#cookieChoiceInfo {ldelim}
		background-color: {$artcookies_bcolor|escape:'htmlall':'UTF-8'} !important;
		color: {$artcookies_txtcolor|escape:'htmlall':'UTF-8'} !important;
		{if $artcookies_shadow == 1}
		box-shadow:0 0 6px {$artcookies_cshadow|escape:'htmlall':'UTF-8'} !important;
		{/if}
{rdelim}
#cookieChoiceInfo #InformativaClick {ldelim}
	color: {$artcookies_txtcolor|escape:'htmlall':'UTF-8'} !important;
	{rdelim}
#cookieChoiceInfo #InformativaAccetto,
#cookieChoiceInfo #InformativaReject,
#cookieChoiceInfo #InformativaCustomize,
#InformativaPreferencesModal #InformativaAccetto,
#InformativaPreferencesModal #InformativaReject,
#InformativaPreferencesModal #InformativaSavePreferences {ldelim}
	background: {$artcookies_button|escape:'htmlall':'UTF-8'} !important;
	color: {$artcookies_tbutton|escape:'htmlall':'UTF-8'} !important;
{rdelim}

#InformativaPreferencesModal .artcookie-preferences-header {ldelim}
	border-top: 4px solid {$artcookies_button|escape:'htmlall':'UTF-8'} !important;
{rdelim}

#cookieChoiceInfo {ldelim}
{if $artcookies_position == 'bottom'}
bottom:0;
left:0;
width:100%;
transform:none;
{elseif $artcookies_position == 'center'}
top:50%;
left:50%;
width:calc(100% - 32px);
max-width:720px;
border-radius:8px;
transform:translate(-50%, -50%);
{else}
top:0;
left:0;
width:100%;
transform:none;
{/if}
{rdelim}
</style>
