{*
**
*  2009-2025 Tecnoacquisti.com
*
*  For support feel free to contact us on our website at http://www.arteinformatica.eu
*
*  @author    Arte e Informatica <admin@arteinformatica.eu>
*  @copyright 2009-2025 Arte e Informatica
*  @license   One Paid Licence By WebSite Using This Module. No Rent. No Sell. No Share.
*
*}

{if $art_consentmode == 1}
{literal}
<script src="{/literal}{$arturi|escape:'htmlall':'UTF-8'}{literal}modules/artcokiechoicespro/views/js/cookiechoices-consentmode.js"></script>
{/literal}
{else}
{literal}
<script src="{/literal}{$arturi|escape:'htmlall':'UTF-8'}{literal}modules/artcokiechoicespro/views/js/cookiechoices.js"></script>
{/literal}
{/if}
{literal}
<script>
  document.addEventListener('DOMContentLoaded', function(event) {
    cookieChoices.showCookieConsentBar('{/literal}{$art_privacy_info|replace:'\'':'’'|escape:'htmlall':'UTF-8'}{literal}',
        '{/literal}{$art_privacy_button|replace:'\'':'’'|escape:'htmlall':'UTF-8'}{literal}', '{/literal}{$art_privacy_text_link|replace:'\'':'’'|escape:'htmlall':'UTF-8'}{literal}', '{/literal}{$art_privacy_link|escape:'htmlall':'UTF-8'}{literal}', '{/literal}{$art_target|escape:'htmlall':'UTF-8'}{literal}', '{/literal}{$art_reject_button_txt|escape:'htmlall':'UTF-8'}{literal}');
 });
</script>
{/literal}


