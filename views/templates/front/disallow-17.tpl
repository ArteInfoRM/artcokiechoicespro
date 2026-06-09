{*
**
*  2009-2026 Tecnoacquisti.com
*
*  For support feel free to contact us on our website at http://www.arteinformatica.eu
*
*  @author    Arte e Informatica <shop@tecnoacquisti.com>
*  @copyright 2009-2026 Arte e Informatica
*  @version   2.8
*  @license   One Paid Licence By WebSite Using This Module. No Rent. No Sell. No Share.
*
*}

{extends file='page.tpl'}

{block name='page_content'}
  <section id="content" class="page-content page-disallow-cookies">
    {if $artcokiechoicespro_valid_token}
      <h1 class="h1">
        {l s='Cookie preferences updated' mod='artcokiechoicespro'}
      </h1>
      <p>
        {l s='Your cookie preferences have been reset. You will be redirected to the home page in a few seconds.' mod='artcokiechoicespro'}
      </p>
    {else}
      <h1 class="h1">
        {l s='Invalid request' mod='artcokiechoicespro'}
      </h1>
      <p>
        {l s='The security token is invalid or has expired. You will be redirected to the home page in a few seconds.' mod='artcokiechoicespro'}
      </p>
    {/if}

    <p>
      <a href="{$artcokiechoicespro_home_url|escape:'html':'UTF-8'}" class="btn btn-primary">
        {l s='Back to home page' mod='artcokiechoicespro'}
      </a>
    </p>
  </section>

{literal}
  <script>
    (function () {
      var url = '{/literal}{$artcokiechoicespro_home_url|escape:'javascript':'UTF-8'}{literal}';
      setTimeout(function () {
        window.location.href = url;
      }, 3000);
    })();
  </script>
{/literal}
{/block}
