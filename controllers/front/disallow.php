<?php
/**
 *  2009-2026 Tecnoacquisti.com
 *
 *  For support feel free to contact us on our website at http://www.arteinformatica.eu
 *
 *  @author    Arte e Informatica <shop@tecnoacquisti.com>
 *  @copyright 2009-2026 Arte e Informatica
 *  @license   One Paid Licence By WebSite Using This Module. No Rent. No Sell. No Share.
 *
 *  @version   1.0
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class ArtcokiechoicesproDisallowModuleFrontController extends ModuleFrontController
{
    protected $valid_token = false;

    public function __construct()
    {
        parent::__construct();

        $this->valid_token = md5(_COOKIE_KEY_ . $this->module->name) === Tools::getValue('token', '');

        if ($this->valid_token) {
            $cookie_names = [
                'displayCookieConsent',
                'displayCookieConsentPreferences',
            ];

            foreach ($cookie_names as $cookie_name) {
                setcookie($cookie_name, '', time() - 3600, '/');
                unset($_COOKIE[$cookie_name]);
            }
        }
    }

    public function init()
    {
        $this->page_name = 'Disallow Cookies';
        parent::init();
    }

    public function initContent()
    {
        parent::initContent();

        $this->context->smarty->assign([
            'artcokiechoicespro_valid_token' => $this->valid_token,
            'artcokiechoicespro_home_url' => $this->context->link->getPageLink('index', true),
        ]);

        $this->setTemplate('module:artcokiechoicespro/views/templates/front/disallow-17.tpl');
    }
}
