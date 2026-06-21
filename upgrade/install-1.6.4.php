<?php
/**
 *  2009-2026 Tecnoacquisti.com
 *
 *  For support feel free to contact us on our website at http://www.tecnoacquisti.com
 *
 *  @author    Arte e Informatica <shop@tecnoacquisti.com>
 *  @copyright 2009-2026 Arte e Informatica
 *  @license   One Paid Licence By WebSite Using This Module. No Rent. No Sell. No Share.
 *  @version   1.6.4
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_6_4($object)
{
    $result = true;

    if (Configuration::get('ARTCOKIECHOICESPRO_CONSENT_VERSION') === false) {
        $result = $result && Configuration::updateValue('ARTCOKIECHOICESPRO_CONSENT_VERSION', '1');
    }

    if (Configuration::get('ARTCOKIECHOICESPRO_AUTO_CONSENT_VERSION') === false) {
        $result = $result && Configuration::updateValue('ARTCOKIECHOICESPRO_AUTO_CONSENT_VERSION', '1');
    }

    if (Configuration::get('ARTCOKIECHOICESPRO_CONSENT_LOG') === false) {
        $result = $result && Configuration::updateValue('ARTCOKIECHOICESPRO_CONSENT_LOG', '0');
    }

    if (Configuration::get('ARTCOKIECHOICESPRO_CONSENT_LOG_RETENTION') === false) {
        $result = $result && Configuration::updateValue('ARTCOKIECHOICESPRO_CONSENT_LOG_RETENTION', '12');
    }

    Configuration::deleteByName('ARTCOKIECHOICESPRO_LOADKJS');

    if (method_exists($object, 'installConsentLogTable')) {
        $result = $result && $object->installConsentLogTable();
    }

    if (method_exists($object, 'installConsentLogGuestColumn')) {
        $result = $result && $object->installConsentLogGuestColumn();
    }

    if (method_exists($object, 'installConsentExportTab')) {
        $result = $result && $object->installConsentExportTab();
    }

    return (bool) $result;
}
