<?php
/**
 *  2009-2026 Tecnoacquisti.com
 *
 *  For support feel free to contact us on our website at http://www.tecnoacquisti.com
 *
 *  @author    Arte e Informatica <shop@tecnoacquisti.com>
 *  @copyright 2009-2026 Arte e Informatica
 *  @license   One Paid Licence By WebSite Using This Module. No Rent. No Sell. No Share.
 *  @version   1.6.2
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_6_2($object)
{
    $ok = (bool) $object->registerHook('header');

    if (Configuration::get('ARTCOKIECHOICESPRO_CONSENTMODE') === false) {
        $ok = $ok && Configuration::updateValue('ARTCOKIECHOICESPRO_CONSENTMODE', '1');
    }

    return (bool) $ok;
}
