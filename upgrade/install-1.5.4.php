<?php
/**
 *  2009-2025 Tecnoacquisti.com
 *
 *  For support feel free to contact us on our website at http://www.arteinformatica.eu
 *
 *  @author    Arte e Informatica <admin@arteinformatica.eu>
 *  @copyright 2009-2025 Arte e Informatica
 *  @license   One Paid Licence By WebSite Using This Module. No Rent. No Sell. No Share.
 *  @version   1.2.2
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_5_4($object)
{
    $ok = Configuration::deleteByName('ARTCOKIECHOICESPRO_DISABLE');
    $ok = $ok && Configuration::deleteByName('ARTCOKIECHOICESPRO_COMPRESS');
    $ok = $ok && Configuration::updateValue('ARTCOKIECHOICESPRO_CONSENTMODE', 0);

    return (bool) $ok;
}
