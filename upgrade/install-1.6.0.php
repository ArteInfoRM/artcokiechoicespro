<?php
/**
 *  2009-2026 Tecnoacquisti.com
 *
 *  For support feel free to contact us on our website at http://www.arteinformatica.eu
 *
 *  @author    Arte e Informatica <shop@tecnoacquisti.com>
 *  @copyright 2009-2026 Arte e Informatica
 *  @license   One Paid Licence By WebSite Using This Module. No Rent. No Sell. No Share.
 *  @version   1.6.0
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_6_0($object)
{
    $languages = Language::getLanguages(false);
    $customize = [];
    $save_preferences = [];
    $category_defaults = [
        'necessary' => [
            'label' => 'Necessary cookies',
            'description' => 'Required for the shop to work and cannot be disabled.',
        ],
        'functional' => [
            'label' => 'Functional cookies',
            'description' => 'Help us provide enhanced features and remember your choices.',
        ],
        'analytics' => [
            'label' => 'Analytics cookies',
            'description' => 'Help us understand how customers use the shop.',
        ],
        'performance' => [
            'label' => 'Performance cookies',
            'description' => 'Help us measure and improve site performance.',
        ],
        'marketing' => [
            'label' => 'Advertising cookies',
            'description' => 'Allow personalized advertising and campaign measurement.',
        ],
        'other' => [
            'label' => 'Other cookies',
            'description' => 'Cover additional optional cookies not included in the other categories.',
        ],
    ];
    $category_keys = ['functional', 'analytics', 'performance', 'marketing', 'other'];
    $ok = true;

    foreach ($languages as $lang) {
        $customize[$lang['id_lang']] = pSQL('Customize');
        $save_preferences[$lang['id_lang']] = pSQL('Save preferences');
    }

    if (Configuration::get('ARTCOKIECHOICESPRO_CUSTOMIZE') === false) {
        if (!Configuration::updateValue('ARTCOKIECHOICESPRO_CUSTOMIZE', $customize)) {
            $ok = false;
        }
    }

    if (Configuration::get('ARTCOKIECHOICESPRO_SAVE_PREFS') === false) {
        if (!Configuration::updateValue('ARTCOKIECHOICESPRO_SAVE_PREFS', $save_preferences)) {
            $ok = false;
        }
    }

    foreach (array_merge(['necessary'], $category_keys) as $category_key) {
        $config_key = Tools::strtoupper($category_key);
        $labels = [];
        $descriptions = [];

        foreach ($languages as $lang) {
            $labels[$lang['id_lang']] = pSQL($category_defaults[$category_key]['label']);
            $descriptions[$lang['id_lang']] = pSQL($category_defaults[$category_key]['description']);
        }

        if (
            $category_key !== 'necessary'
            && Configuration::get('ARTCOKIECHOICESPRO_CAT_' . $config_key . '_ACTIVE') === false
        ) {
            if (
                !Configuration::updateValue(
                    'ARTCOKIECHOICESPRO_CAT_' . $config_key . '_ACTIVE',
                    '1'
                )
            ) {
                $ok = false;
            }
        }

        if (Configuration::get('ARTCOKIECHOICESPRO_CAT_' . $config_key . '_LABEL') === false) {
            if (
                !Configuration::updateValue(
                    'ARTCOKIECHOICESPRO_CAT_' . $config_key . '_LABEL',
                    $labels
                )
            ) {
                $ok = false;
            }
        }

        if (Configuration::get('ARTCOKIECHOICESPRO_CAT_' . $config_key . '_DESC') === false) {
            if (
                !Configuration::updateValue(
                    'ARTCOKIECHOICESPRO_CAT_' . $config_key . '_DESC',
                    $descriptions
                )
            ) {
                $ok = false;
            }
        }
    }

    if (Configuration::get('ARTCOKIECHOICESPRO_POSITION') === false) {
        if (!Configuration::updateValue('ARTCOKIECHOICESPRO_POSITION', 'bottom')) {
            $ok = false;
        }
    }

    return (bool) $ok;
}
