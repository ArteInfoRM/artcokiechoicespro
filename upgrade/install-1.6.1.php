<?php
/**
 *  2009-2026 Tecnoacquisti.com
 *
 *  For support feel free to contact us on our website at http://www.tecnoacquisti.com
 *
 *  @author    Arte e Informatica <shop@tecnoacquisti.com>
 *  @copyright 2009-2026 Arte e Informatica
 *  @license   One Paid Licence By WebSite Using This Module. No Rent. No Sell. No Share.
 *  @version   1.6.1
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_6_1($object)
{
    $ok = (bool) $object->registerHook('displayCustomerAccount');
    $default_bots = [
        'Googlebot',
        'Google-InspectionTool',
        'AdsBot-Google',
        'Mediapartners-Google',
        'bingbot',
        'BingPreview',
        'AdIdxBot',
        'MicrosoftPreview',
        'DuckDuckBot',
        'YandexBot',
        'Applebot',
        'facebookexternalhit',
        'facebot',
        'Twitterbot',
        'LinkedInBot',
        'Pinterestbot',
    ];

    if (Configuration::get('ARTCOKIECHOICESPRO_SEO_PROTECTION') === false) {
        $ok = $ok && Configuration::updateValue('ARTCOKIECHOICESPRO_SEO_PROTECTION', '1');
    }

    $configured_bots = (string) Configuration::get('ARTCOKIECHOICESPRO_SEO_BOTS');
    $merged_bots = [];
    $seen = [];
    $configured_bots = str_replace(['\\r\\n', '\\n', '\\r'], "\n", $configured_bots);
    $items = preg_split('/[\r\n,]+/', $configured_bots . "\n" . implode("\n", $default_bots));

    if (is_array($items)) {
        foreach ($items as $item) {
            $bot = trim(strip_tags((string) $item));
            $bot = preg_replace('/[[:cntrl:]]+/', '', $bot);

            if (!is_string($bot)) {
                continue;
            }

            $bot = trim($bot);

            if ($bot === '' || Tools::strlen($bot) < 3) {
                continue;
            }

            $bot = Tools::substr($bot, 0, 80);
            $key = Tools::strtolower($bot);

            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $merged_bots[] = $bot;
        }
    }

    $ok = $ok && Configuration::updateValue('ARTCOKIECHOICESPRO_SEO_BOTS', implode("\n", $merged_bots));

    return (bool) $ok;
}
