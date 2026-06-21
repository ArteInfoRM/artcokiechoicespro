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
    if (
        Configuration::get('ARTCOKIECHOICESPRO_CONSENT_VERSION') === false
        && !Configuration::updateValue('ARTCOKIECHOICESPRO_CONSENT_VERSION', '1')
    ) {
        return false;
    }

    if (
        Configuration::get('ARTCOKIECHOICESPRO_AUTO_CONSENT_VERSION') === false
        && !Configuration::updateValue('ARTCOKIECHOICESPRO_AUTO_CONSENT_VERSION', '1')
    ) {
        return false;
    }

    if (
        Configuration::get('ARTCOKIECHOICESPRO_CONSENT_LOG') === false
        && !Configuration::updateValue('ARTCOKIECHOICESPRO_CONSENT_LOG', '0')
    ) {
        return false;
    }

    if (
        Configuration::get('ARTCOKIECHOICESPRO_CONSENT_LOG_RETENTION') === false
        && !Configuration::updateValue('ARTCOKIECHOICESPRO_CONSENT_LOG_RETENTION', '12')
    ) {
        return false;
    }

    Configuration::deleteByName('ARTCOKIECHOICESPRO_LOADKJS');

    if (method_exists($object, 'installConsentLogTable') && !$object->installConsentLogTable()) {
        return false;
    }

    if (method_exists($object, 'installConsentLogGuestColumn') && !$object->installConsentLogGuestColumn()) {
        return false;
    }

    if (method_exists($object, 'installConsentExportTab') && !$object->installConsentExportTab()) {
        return false;
    }

    return true;
}
