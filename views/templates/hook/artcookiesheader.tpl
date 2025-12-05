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

{if $artloadjs==1}
{literal}
<script src="{/literal}{$arturi|escape:'htmlall':'UTF-8'}{literal}modules/artcokiechoicespro/views/js/jquery-1.11.0.min.js"></script>
{/literal}
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
#cookieChoiceInfo #InformativaReject {ldelim}
	background: {$artcookies_button|escape:'htmlall':'UTF-8'} !important;
	color: {$artcookies_tbutton|escape:'htmlall':'UTF-8'} !important;
{rdelim}

#cookieChoiceInfo {ldelim}
{if $artcookies_position == 'bottom'}
bottom:0;
{else}
top:0;
{/if}
{rdelim}
</style>
