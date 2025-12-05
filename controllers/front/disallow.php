<?php
/**
 *  2009-2025 Tecnoacquisti.com
 *
 *  For support feel free to contact us on our website at http://www.arteinformatica.eu
 *
 *  @author    Arte e Informatica <admin@arteinformatica.eu>
 *  @copyright 2009-2025 Arte e Informatica
 *  @license   One Paid Licence By WebSite Using This Module. No Rent. No Sell. No Share.
 *  @version   1.0
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class ArtcokiechoicesproDisallowModuleFrontController extends ModuleFrontController
{
    public function init() {
        $this->page_name = 'Disallow Cookies';
        parent::init();
    }

    protected $valid_token = false;

    public function __construct()
    {
        parent::__construct();

        $this->valid_token = md5(_COOKIE_KEY_ . $this->module->name) == Tools::getValue('token', '');

        if ($this->valid_token) {
            $cookie_name = 'displayCookieConsent';
            setcookie($cookie_name, '', time() - 3600, '/');
            unset($_COOKIE[$cookie_name]);
            }
    }


    public function initContent()
    {
        parent::initContent();

        $context = Context::getContext();

        $context->smarty->assign([
            'artcokiechoicespro_valid_token' => $this->valid_token,
        ]);

        if (version_compare(_PS_VERSION_, '1.7.0', '<')) {
            $this->setTemplate('disallow.tpl');
        } else {
            $this->setTemplate('module:artcokiechoicespro/views/templates/front/disallow-17.tpl');
        }
    }


}
