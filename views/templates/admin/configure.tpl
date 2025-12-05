{*
**
*  2009-2025 Tecnoacquisti.com
*
*  For support feel free to contact us on our website at http://www.arteinformatica.eu
*
*  @author    Arte e Informatica <admin@arteinformatica.eu>
*  @copyright 2009-2025 Arte e Informatica
*  @version   1.0.0
*  @license   One Paid Licence By WebSite Using This Module. No Rent. No Sell. No Share.
*
*}

{* Alert iniziale *}
{include file='module:artcokiechoicespro/views/templates/admin/allert.tpl'}

<ul class="nav nav-tabs" role="tablist">
  <li class="{$active_1|escape:'htmlall':'UTF-8'}">
    <a href="#template_1" role="tab" data-toggle="tab">
      {l s='Basic settings' mod='artcokiechoicespro'}
    </a>
  </li>
  <li class="{$active_2|escape:'htmlall':'UTF-8'}">
    <a href="#template_2" role="tab" data-toggle="tab">
      {l s='Advanced setting' mod='artcokiechoicespro'}
    </a>
  </li>
  <li>
    <a href="#template_3" role="tab" data-toggle="tab">
      {l s='Documentation' mod='artcokiechoicespro'}
    </a>
  </li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
  <div class="tab-pane {$active_1|escape:'htmlall':'UTF-8'}" id="template_1">
    {$basic_setting nofilter}
  </div>

  <div class="tab-pane {$active_2|escape:'htmlall':'UTF-8'}" id="template_2">
    {$advanced_setting nofilter}
  </div>

  <div class="tab-pane" id="template_3">
    {include file='module:artcokiechoicespro/views/templates/admin/documentation.tpl'}
  </div>
</div>

<p style="text-align: center;">
  {include file='module:artcokiechoicespro/views/templates/admin/copyright.tpl'}
</p>
