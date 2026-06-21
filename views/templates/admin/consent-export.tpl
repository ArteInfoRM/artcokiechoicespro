{*
*  2009-2026 Tecnoacquisti.com
*
*  For support feel free to contact us on our website at http://www.tecnoacquisti.com
*
*  @author    Arte e Informatica <shop@tecnoacquisti.com>
*  @copyright 2009-2026 Arte e Informatica
*  @license   One Paid Licence By WebSite Using This Module. No Rent. No Sell. No Share.
*}

<div class="panel artcookie-consent-export-page">
  <div class="panel-heading">
    <i class="icon-download"></i>
    {l s='Consent log export' mod='artcokiechoicespro'}
  </div>

  <p class="text-muted artcookie-consent-export-help">
    {l s='The export contains the minimized consent log only: anonymized IP, hashed technical identifiers, consent version, action and preferences.' mod='artcokiechoicespro'}
  </p>

  <form class="form-inline artcookie-consent-export-form" method="get" action="{$consent_export_url|escape:'htmlall':'UTF-8'}">
    <input type="hidden" name="controller" value="AdminArtCookieConsentExport">
    <input type="hidden" name="token" value="{$consent_export_token|escape:'htmlall':'UTF-8'}">

    <div class="form-group">
      <label for="artcookie_export_format">
        {l s='Export format' mod='artcokiechoicespro'}
      </label>
      <select id="artcookie_export_format" name="format" class="form-control artcookie-consent-export-format">
        <option value="csv">CSV</option>
        <option value="json">JSON</option>
        <option value="xml">XML</option>
      </select>
    </div>

    <div class="form-group">
      <label for="artcookie_export_date_from">
        {l s='Date from' mod='artcokiechoicespro'}
      </label>
      <input id="artcookie_export_date_from" type="date" name="date_from" class="form-control artcookie-consent-export-date">
    </div>

    <div class="form-group">
      <label for="artcookie_export_date_to">
        {l s='Date to' mod='artcokiechoicespro'}
      </label>
      <input id="artcookie_export_date_to" type="date" name="date_to" class="form-control artcookie-consent-export-date">
    </div>

    <div class="form-group">
      <label for="artcookie_export_action">
        {l s='Consent action' mod='artcokiechoicespro'}
      </label>
      <select id="artcookie_export_action" name="consent_action" class="form-control artcookie-consent-export-action">
        <option value="">{l s='All actions' mod='artcokiechoicespro'}</option>
        <option value="accept_all">{l s='Accept all' mod='artcokiechoicespro'}</option>
        <option value="reject_all">{l s='Reject all' mod='artcokiechoicespro'}</option>
        <option value="save_selection">{l s='Accept selection' mod='artcokiechoicespro'}</option>
      </select>
    </div>

    <button type="submit" class="btn btn-default artcookie-consent-export-button">
      <i class="icon-download"></i>
      {l s='Export consent log' mod='artcokiechoicespro'}
    </button>
  </form>
</div>
