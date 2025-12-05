<?php
/**
*  2009-2025 Art CookiesChoice Pro
*
*  For support feel free to contact us on our website at http://www.arteinformatica.eu
*
*  @author    Arte e Informatica <admin@arteinformatica.eu>
*  @copyright 2009-2025 Arte e Informatica
*  @license   One Paid Licence By WebSite Using This Module. No Rent. No Sell. No Share.
*
*  @version   1.5
*/
if (!defined('_PS_VERSION_')) {
    exit;
}

class ArtCokiechoicespro extends Module {
    public function __construct() {
        $this->name = 'artcokiechoicespro';
        $this->tab = 'front_office_features';
        $this->version = '1.5.4';
        $this->author = 'Tecnoacquisti.com';
        $this->need_instance = 0;

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('PrestaShop Banner Cookiechoices (Eu Cookie Law) GDPR');
        $this->description = $this->l('Free Cookie Tool: simple PrestaShop module that displays the EU Cookie Law banner based on Google\'s Cookiechoices.org. Updated to new 2022 rules.');

        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];

    }

    public function install() {
        $languages = Language::getLanguages(false);
        $artcookies_text = [];
        $artcookies_url = [];
        $artcookies_linktxt = [];
        $artcookies_buttomtxt = [];
        $artcookies_reject = [];

        foreach ($languages as $lang) {
            $artcookies_text[$lang['id_lang']] = pSQL(
                'We and selected third parties use cookies or similar technologies for technical purposes and, with your consent, for “experience enhancement”, “measurement” and “targeting and advertising” as specified in the cookie policy. Denying consent may make related features unavailable. You can freely give, deny, or withdraw your consent at any time. To find out more about the categories of personal information collected and the purposes for which such information will be used, please refer to our privacy policy. Use the “Accept” button to consent to the use of such technologies. Use the “Reject” button or close this notice to continue without accepting.'
            );
            $artcookies_url[$lang['id_lang']] = pSQL('#');
            $artcookies_linktxt[$lang['id_lang']] = pSQL('Read the Privacy Policy');
            $artcookies_buttomtxt[$lang['id_lang']] = pSQL('Accept');
            $artcookies_reject[$lang['id_lang']] = pSQL('Reject');
        }

       $this->_clearCache('artcookiechoices.tpl');

       return parent::install() &&
           Configuration::updateValue(Tools::strtoupper($this->name) . '_ACTIVE', '1') &&
           Configuration::updateValue(Tools::strtoupper($this->name) . '_CONSENTMODE', '0') &&
           Configuration::updateValue(Tools::strtoupper($this->name) . '_EXTACTIVE', '0') &&
           Configuration::updateValue(Tools::strtoupper($this->name) . '_PRIVACY_CMS', '0') &&
           Configuration::updateValue(Tools::strtoupper($this->name) . '_BANNER_COLOR', '#000000') &&
           Configuration::updateValue(Tools::strtoupper($this->name) . '_TEXT_COLOR', '#ffffff') &&
           Configuration::updateValue(Tools::strtoupper($this->name) . '_SHADOW', '1') &&
           Configuration::updateValue(Tools::strtoupper($this->name) . '_SHADOW_COLOR', '#000000') &&
           Configuration::updateValue(Tools::strtoupper($this->name) . '_BUTTON_COLOR', '#f77002') &&
           Configuration::updateValue(Tools::strtoupper($this->name) . '_BTEXT_COLOR', '#ffffff') &&
           Configuration::updateValue(Tools::strtoupper($this->name) . '_TEXT', $artcookies_text) &&
           Configuration::updateValue(Tools::strtoupper($this->name) . '_PRIVACY_EXT', $artcookies_url) &&
           Configuration::updateValue(Tools::strtoupper($this->name) . '_LINKTXT', $artcookies_linktxt) &&
           Configuration::updateValue(Tools::strtoupper($this->name) . '_BUTTUMTXT', $artcookies_buttomtxt) &&
           Configuration::updateValue(Tools::strtoupper($this->name) . '_REJECT', $artcookies_reject) &&
           Configuration::updateValue(Tools::strtoupper($this->name) . '_TARGET', '_self') &&
           Configuration::updateValue(Tools::strtoupper($this->name) . '_LOADKJS', '0') &&
           Configuration::updateValue(Tools::strtoupper($this->name) . '_POSITION', 'bottom') &&
           Configuration::updateValue(Tools::strtoupper($this->name) . '_REVOKE', '0') &&
           //$this->registerHook('footer') &&
           //$this->registerHook('top') &&
           $this->registerHook('header') &&
           $this->registerHook('CookiesDisable') &&
           $this->registerHook('displayFooterBefore') &&
           $this->registerHook('displayFooter') &&
           $this->registerHook('displayFooterAfter');
    }

    public function uninstall() {
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_ACTIVE');
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_CONSENTMODE');
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_EXTACTIVE');
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_PRIVACY_CMS');
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_BANNER_COLOR');
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_TEXT_COLOR');
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_SHADOW');
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_SHADOW_COLOR');
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_BUTTON_COLOR');
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_BTEXT_COLOR');
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_TEXT');
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_PRIVACY_EXT');
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_LINKTXT');
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_BUTTUMTXT');
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_REJECT');
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_TARGET');
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_LOADKJS');
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_COMPRESS');
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_POSITION');
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_DISABLE');
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_REVOKE');

       return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $output = null;
        $outputadv = null;
        $active_1 = 'active';
        $active_2 = '';
        $this->context->smarty->assign('module_dir', $this->_path);
        $basic_setting = $this->renderForm();
        $advanced_setting = $this->renderAdvForm();

        $useSsl = (bool) Configuration::get('PS_SSL_ENABLED_EVERYWHERE') || (bool) Configuration::get('PS_SSL_ENABLED');
        $shop_base_url = $this->context->link->getBaseLink((int) $this->context->shop->id, $useSsl);

    if (Tools::isSubmit('submitAdv')) {
        $active_1 = '';
        $active_2 = 'active';
        $artcookies_url = [];
        $languages = Language::getLanguages(false);
        $artcookies_extactive = Tools::getValue('ARTCOKIECHOICESPRO_EXTACTIVE');
        $artcookies_bcolor = Tools::getValue('ARTCOKIECHOICESPRO_BANNER_COLOR');
        $artcookies_txtcolor = Tools::getValue('ARTCOKIECHOICESPRO_TEXT_COLOR');
        $artcookies_cshadow = Tools::getValue('ARTCOKIECHOICESPRO_SHADOW_COLOR');
        $artcookies_button = Tools::getValue('ARTCOKIECHOICESPRO_BUTTON_COLOR');
        $artcookies_shadow = Tools::getValue('ARTCOKIECHOICESPRO_SHADOW');
        $artcookies_tbutton = Tools::getValue('ARTCOKIECHOICESPRO_BTEXT_COLOR');
        $artloadjs = Tools::getValue('ARTCOKIECHOICESPRO_LOADKJS');
        $artcookies_compress = Tools::getValue('ARTCOKIECHOICESPRO_COMPRESS');
        $artcookies_position = Tools::getValue('ARTCOKIECHOICESPRO_POSITION');
        $artcookies_disable = Tools::getValue('ARTCOKIECHOICESPRO_DISABLE');

        foreach ($languages as $lang) {
                 $artcookies_url[$lang['id_lang']] = pSQL(Tools::getValue('ARTCOKIECHOICESPRO_PRIVACY_EXT_' . $lang['id_lang']));
            }

        Configuration::updateValue('ARTCOKIECHOICESPRO_PRIVACY_EXT', $artcookies_url);
        Configuration::updateValue('ARTCOKIECHOICESPRO_EXTACTIVE', (int) $artcookies_extactive);
        Configuration::updateValue('ARTCOKIECHOICESPRO_BANNER_COLOR', pSQL($artcookies_bcolor));
        Configuration::updateValue('ARTCOKIECHOICESPRO_TEXT_COLOR', pSQL($artcookies_txtcolor));
        Configuration::updateValue('ARTCOKIECHOICESPRO_SHADOW_COLOR', pSQL($artcookies_cshadow));
        Configuration::updateValue('ARTCOKIECHOICESPRO_BUTTON_COLOR', pSQL($artcookies_button));
        Configuration::updateValue('ARTCOKIECHOICESPRO_BTEXT_COLOR', pSQL($artcookies_tbutton));
        Configuration::updateValue('ARTCOKIECHOICESPRO_POSITION', pSQL($artcookies_position));
        Configuration::updateValue('ARTCOKIECHOICESPRO_DISABLE', pSQL($artcookies_disable));
        Configuration::updateValue('ARTCOKIECHOICESPRO_LOADKJS', pSQL($artloadjs));
        Configuration::updateValue('ARTCOKIECHOICESPRO_SHADOW', (int) $artcookies_shadow);
        Configuration::updateValue('ARTCOKIECHOICESPRO_COMPRESS', (int) $artcookies_compress);

        $advanced_setting = $this->renderAdvForm();
        $this->_clearCache('artcookiechoices.tpl');
        $outputadv .= $this->displayConfirmation($this->l('Advanced settings updated'));
    }

    if (Tools::isSubmit('submitUpdate')) {
        $active_1 = 'active';
        $active_2 = '';
        $artcookies_text = [];
        $artcookies_linktxt = [];
        $artcookies_buttomtxt = [];
        $artcookies_reject = [];
        $languages = Language::getLanguages(false);
        $artcookies_active = Tools::getValue('ARTCOKIECHOICESPRO_ACTIVE');
        $artcookies_consentmode = Tools::getValue('ARTCOKIECHOICESPRO_CONSENTMODE');
        $artcookies_cms = Tools::getValue('ARTCOKIECHOICESPRO_PRIVACY_CMS');
        $artcookies_target = Tools::getValue('ARTCOKIECHOICESPRO_TARGET');
        $artcookies_revoke = Tools::getValue('ARTCOKIECHOICESPRO_REVOKE');

        foreach ($languages as $lang) {
            $artcookies_text[$lang['id_lang']] = urldecode(Tools::getValue('ARTCOKIECHOICESPRO_TEXT_' . $lang['id_lang']));
            $artcookies_linktxt[$lang['id_lang']] = urldecode(Tools::getValue('ARTCOKIECHOICESPRO_LINKTXT_' . $lang['id_lang']));
            $artcookies_buttomtxt[$lang['id_lang']] = urldecode(Tools::getValue('ARTCOKIECHOICESPRO_BUTTUMTXT_' . $lang['id_lang']));
            $artcookies_reject[$lang['id_lang']] = urldecode(Tools::getValue('ARTCOKIECHOICESPRO_REJECT_' . $lang['id_lang']));
        }

        Configuration::updateValue('ARTCOKIECHOICESPRO_TEXT', $artcookies_text);
        Configuration::updateValue('ARTCOKIECHOICESPRO_LINKTXT', $artcookies_linktxt);
        Configuration::updateValue('ARTCOKIECHOICESPRO_BUTTUMTXT', $artcookies_buttomtxt);
        Configuration::updateValue('ARTCOKIECHOICESPRO_REJECT', $artcookies_reject);
        Configuration::updateValue('ARTCOKIECHOICESPRO_ACTIVE', (int) $artcookies_active);
        Configuration::updateValue('ARTCOKIECHOICESPRO_CONSENTMODE', (int) $artcookies_consentmode);
        Configuration::updateValue('ARTCOKIECHOICESPRO_PRIVACY_CMS', (int) $artcookies_cms);
        Configuration::updateValue('ARTCOKIECHOICESPRO_REVOKE', (int) $artcookies_revoke);
        Configuration::updateValue('ARTCOKIECHOICESPRO_TARGET', $artcookies_target);
        $basic_setting = $this->renderForm();
        $this->_clearCache('artcookiechoices.tpl');
        $output .= $this->displayConfirmation($this->l('Basic settings updated'));
    }

    $link = $this->context->link->getModuleLink(
        $this->name,
        'disallow',
        [
            'token' => md5(_COOKIE_KEY_ . $this->name),
            ],
        true
    );

        $this->context->smarty->assign([
            'link' => $link,
            'shop_base_url' => $shop_base_url,
            'module_dir' => $this->_path,
            'active_1' => $active_1,
            'active_2' => $active_2,
            'basic_setting' => $basic_setting . $output,
            'advanced_setting' => $advanced_setting . $outputadv,
        ]);

    $this->context->smarty->assign('module_dir', $this->_path);

    return $this->display(__FILE__, 'views/templates/admin/configure.tpl');

    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitUpdate';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        $basic_setting = $helper->generateForm([$this->getConfigBasic()]);

        return $basic_setting;
    }

    protected function renderAdvForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitAdv';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = [
            'fields_value' => $this->getConfigAdvValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        $advanced_setting = $helper->generateForm([$this->getConfigAdv()]);

        return $advanced_setting;
    }

    /**
     * Create the structure of your basic form.
     */
    protected function getConfigAdv()
    {
        return [
            'form' => [
                'input' => [
                [
                        'type' => 'color',
                        'label' => $this->l('Background colour'),
                        'name' => Tools::strtoupper($this->name) . '_BANNER_COLOR',
                        'desc' => $this->l('Colour banner cookies background'),
                    ],

                [
                        'type' => 'color',
                        'label' => $this->l('Text colour'),
                        'name' => Tools::strtoupper($this->name) . '_TEXT_COLOR',
                        'desc' => $this->l('Colour for the text in the Banner Cookies'),
                    ],
                [
                        'type' => 'select',
                        'label' => $this->l('Banner Position'),
                        'name' => Tools::strtoupper($this->name) . '_POSITION',

                        'options' => [
                            'query' => [
                                 ['id' => 'top', 'name' => $this->l("top")],
                                 ['id' => 'bottom', 'name' => $this->l("bottom")],
                                ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                [
                        'type' => 'switch',
                        'label' => $this->l('Load jQUERY'),
                        'name' => Tools::strtoupper($this->name) . '_LOADKJS',
                        'is_bool' => true,
                        'desc' => $this->l('If your theme does not load jQUERY'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                /* array(
                        'type' => 'switch',
                        'label' => $this->l('JS Compress'),
                        'name' => Tools::strtoupper($this->name).'_COMPRESS',
                        'is_bool' => true,
                        'desc' => $this->l('Uses compressed js to reduce loading times'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),	*/
                [
                        'type' => 'switch',
                        'label' => $this->l('Add shadow'),
                        'name' => Tools::strtoupper($this->name) . '_SHADOW',
                        'is_bool' => true,
                        'desc' => $this->l('Add shadow to the banner'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                [
                        'type' => 'color',
                        'label' => $this->l('Shadow colour'),
                        'name' => Tools::strtoupper($this->name) . '_SHADOW_COLOR',
                        'desc' => $this->l('Colour for the shadow in the Banner Cookies'),
                    ],
                [
                        'type' => 'color',
                        'label' => $this->l('Button colour'),
                        'name' => Tools::strtoupper($this->name) . '_BUTTON_COLOR',
                        'desc' => $this->l('Colour for the button in the Banner Cookies'),
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Button text colour'),
                        'name' => Tools::strtoupper($this->name) . '_BTEXT_COLOR',
                        'desc' => $this->l('Colour for the text in button'),
                    ],
                [
                        'type' => 'switch',
                        'label' => $this->l('External link'),
                        'name' => Tools::strtoupper($this->name) . '_EXTACTIVE',
                        'is_bool' => true,
                        'desc' => $this->l('Use external link for privacy information (disabled use CMS)'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                /*array(
                        'type' => 'switch',
                        'label' => $this->l('Click on the page'),
                        'name' => Tools::strtoupper($this->name).'_DISABLE',
                        'is_bool' => true,
                        'desc' => $this->l('Disable the click on the page'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),	*/
                [
                        'type' => 'text',
                        'label' => $this->l('Alternative Privacy URL'),
                        'name' => Tools::strtoupper($this->name) . '_PRIVACY_EXT',
                        'lang' => true,
                        'autoload_rte' => true,
                        'desc' => $this->l('Link to external privacy information'),
                    ],

                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'name' => 'submitAdv',
                ],
            ],
        ];

    }

    protected function getConfigAdvValues()
    {
        $languages = Language::getLanguages(false);

        $artcookies_url = [];
        foreach ($languages as $language) {
             $artcookies_url[$language['id_lang']] = pSQL(Configuration::get('ARTCOKIECHOICESPRO_PRIVACY_EXT', $language['id_lang']));
        }

        return [
            'ARTCOKIECHOICESPRO_EXTACTIVE' => Tools::getValue('ARTCOKIECHOICESPRO_EXTACTIVE', Configuration::get('ARTCOKIECHOICESPRO_EXTACTIVE')),
            'ARTCOKIECHOICESPRO_LOADKJS' => Tools::getValue('ARTCOKIECHOICESPRO_LOADKJS', Configuration::get('ARTCOKIECHOICESPRO_LOADKJS')),
            'ARTCOKIECHOICESPRO_BANNER_COLOR' => Tools::getValue('ARTCOKIECHOICESPRO_BANNER_COLOR', Configuration::get('ARTCOKIECHOICESPRO_BANNER_COLOR')),
            'ARTCOKIECHOICESPRO_TEXT_COLOR' => Tools::getValue('ARTCOKIECHOICESPRO_TEXT_COLOR', Configuration::get('ARTCOKIECHOICESPRO_TEXT_COLOR')),
            'ARTCOKIECHOICESPRO_SHADOW' => Tools::getValue('ARTCOKIECHOICESPRO_SHADOW', Configuration::get('ARTCOKIECHOICESPRO_SHADOW')),
            'ARTCOKIECHOICESPRO_SHADOW_COLOR' => Tools::getValue('ARTCOKIECHOICESPRO_SHADOW_COLOR', Configuration::get('ARTCOKIECHOICESPRO_SHADOW_COLOR')),
            'ARTCOKIECHOICESPRO_BUTTON_COLOR' => Tools::getValue('ARTCOKIECHOICESPRO_BUTTON_COLOR', Configuration::get('ARTCOKIECHOICESPRO_BUTTON_COLOR')),
            'ARTCOKIECHOICESPRO_BTEXT_COLOR' => Tools::getValue('ARTCOKIECHOICESPRO_BTEXT_COLOR', Configuration::get('ARTCOKIECHOICESPRO_BTEXT_COLOR')),
            'ARTCOKIECHOICESPRO_COMPRESS' => Tools::getValue('ARTCOKIECHOICESPRO_COMPRESS', Configuration::get('ARTCOKIECHOICESPRO_COMPRESS')),
            'ARTCOKIECHOICESPRO_POSITION' => Tools::getValue('ARTCOKIECHOICESPRO_POSITION', Configuration::get('ARTCOKIECHOICESPRO_POSITION')),
            'ARTCOKIECHOICESPRO_DISABLE' => Tools::getValue('ARTCOKIECHOICESPRO_DISABLE', Configuration::get('ARTCOKIECHOICESPRO_DISABLE')),
            'ARTCOKIECHOICESPRO_PRIVACY_EXT' => Tools::getValue('ARTCOKIECHOICESPRO_PRIVACY_EXT', $artcookies_url),

        ];

    }

    /**
     * Create the structure of your basic form.
     */
    protected function getConfigBasic()
    {
        $cms_options = $this->getCmsLinks();

        return [
            'form' => [
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Activate Cookies Banner'),
                        'name' => Tools::strtoupper($this->name) . '_ACTIVE',
                        'is_bool' => true,
                        'desc' => $this->l('Activate the Banner Cookiechoices (EU Cookie Law)'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Activate Consent Mode'),
                        'name' => Tools::strtoupper($this->name) . '_CONSENTMODE',
                        'is_bool' => true,
                        'desc' => $this->l('Enable Google and Microsoft Consent Mode'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'textarea',
                        'label' => $this->l('Banner Notice Text'),
                        'name' => Tools::strtoupper($this->name) . '_TEXT',
                        'lang' => true,
                        'autoload_rte' => true,
                        'desc' => $this->l('Text to show in the cookies banner'),
                        'cols' => 60,
                        'rows' => 20,
                     ],
                     [
                        'type' => 'text',
                        'label' => $this->l('Privacy Link Text'),
                        'name' => Tools::strtoupper($this->name) . '_LINKTXT',
                        'lang' => true,
                        'autoload_rte' => true,
                        'desc' => $this->l('Text to privacy link'),

                     ],
                     [
                        'type' => 'text',
                        'label' => $this->l('Buttom Reject Text'),
                        'name' => Tools::strtoupper($this->name) . '_REJECT',
                        'lang' => true,
                        'autoload_rte' => true,
                        'desc' => $this->l('Text buttom'),

                     ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Buttom Accept Text'),
                        'name' => Tools::strtoupper($this->name) . '_BUTTUMTXT',
                        'lang' => true,
                        'autoload_rte' => true,
                        'desc' => $this->l('Text buttom'),

                    ],
                     [
                        'type' => 'select',
                        'label' => $this->l('Select Privacy URL'),
                        'name' => Tools::strtoupper($this->name) . '_PRIVACY_CMS',
                        'desc' => $this->l('Select CMS of your cookies text'),
                        'options' => [
                            'query' => $cms_options,
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Link target atribute'),
                        'name' => Tools::strtoupper($this->name) . '_TARGET',
                        'desc' => $this->l('The target attribute specifies where to open the linked document'),
                        'options' => [
                            'query' => [
                                 ['id' => '_self', 'name' => $this->l("_self: opens the linked document in the same frame as it was clicked (this is default)")],
                                 ['id' => '_blank', 'name' => $this->l("_blank: opens the linked document in a new window or tab")],
                                 ['id' => '_parent', 'name' => $this->l("_parent: opens the linked document in the parent frame")],
                                 ['id' => '_top', 'name' => $this->l("_top: opens the linked document in the full body of the window")],
                                 ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Unsubscribe link'),
                        'name' => Tools::strtoupper($this->name) . '_REVOKE',
                        'desc' => $this->l('Position of the link for cancel the consent'),
                        'options' => [
                            'query' => [
                                ['id' => '0', 'name' => $this->l("Select")],
                                ['id' => '1', 'name' => $this->l("displayFooterBefore")],
                                ['id' => '2', 'name' => $this->l("displayFooter")],
                                ['id' => '3', 'name' => $this->l("displayFooterAfter")],
                                ['id' => '4', 'name' => $this->l("CookiesDisable")],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'name' => 'submitUpdate',
                ],
            ],
        ];

    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        $languages = Language::getLanguages(false);
        $artcookies_text = [];
        $artcookies_linktxt = [];
        $artcookies_buttomtxt = [];
        $artcookies_reject = [];

        foreach ($languages as $language) {
            $artcookies_text[$language['id_lang']] = Configuration::get('ARTCOKIECHOICESPRO_TEXT', $language['id_lang']);
            $artcookies_linktxt[$language['id_lang']] = Configuration::get('ARTCOKIECHOICESPRO_LINKTXT', $language['id_lang']);
            $artcookies_buttomtxt[$language['id_lang']] = Configuration::get('ARTCOKIECHOICESPRO_BUTTUMTXT', $language['id_lang']);
            $artcookies_reject[$language['id_lang']] = Configuration::get('ARTCOKIECHOICESPRO_REJECT', $language['id_lang']);
        }

        return [
            'ARTCOKIECHOICESPRO_ACTIVE' => Tools::getValue('ARTCOKIECHOICESPRO_ACTIVE', Configuration::get('ARTCOKIECHOICESPRO_ACTIVE')),
            'ARTCOKIECHOICESPRO_CONSENTMODE' => Tools::getValue('ARTCOKIECHOICESPRO_CONSENTMODE', Configuration::get('ARTCOKIECHOICESPRO_CONSENTMODE')),
            'ARTCOKIECHOICESPRO_PRIVACY_CMS' => Tools::getValue('ARTCOKIECHOICESPRO_PRIVACY_CMS', Configuration::get('ARTCOKIECHOICESPRO_PRIVACY_CMS')),
            'ARTCOKIECHOICESPRO_TARGET' => Tools::getValue('ARTCOKIECHOICESPRO_TARGET', Configuration::get('ARTCOKIECHOICESPRO_TARGET')),
            'ARTCOKIECHOICESPRO_REVOKE' => Tools::getValue('ARTCOKIECHOICESPRO_REVOKE', Configuration::get('ARTCOKIECHOICESPRO_REVOKE')),
            'ARTCOKIECHOICESPRO_TEXT' => Tools::getValue('ARTCOKIECHOICESPRO_TEXT', $artcookies_text),
            'ARTCOKIECHOICESPRO_LINKTXT' => Tools::getValue('ARTCOKIECHOICESPRO_LINKTXT', $artcookies_linktxt),
            'ARTCOKIECHOICESPRO_BUTTUMTXT' => Tools::getValue('ARTCOKIECHOICESPRO_BUTTUMTXT', $artcookies_buttomtxt),
            'ARTCOKIECHOICESPRO_REJECT' => Tools::getValue('ARTCOKIECHOICESPRO_REJECT', $artcookies_reject),
        ];

    }

    public function getCmsLinks($lang = null)
    {
        if (!$lang) {
            $lang = $this->context->language->id;
        }
        $id_shop = (int) $this->context->shop->id;

        $cms_pages = CMS::getCMSPages(
            (int) $lang,
            null,
            true,
            $id_shop
        );

        $cms_options = [];
        foreach ($cms_pages as $cms) {
            $option = [];
            $option['name'] = $cms['meta_title'];
            $option['id'] = (int) $cms['id_cms'];
            $cms_options[] = $option;
        }

        $cms_options[] = ["name" => $this->l("Select CMS"), "id" => 0 ];

        return $cms_options;
    }

    public function cookiesBar()
    {
        $active_lang = $this->context->language->id;
        $art_privacy_info = Configuration::get(Tools::strtoupper($this->name . '_TEXT'), $active_lang);
        $art_privacy_text_link = Configuration::get(Tools::strtoupper($this->name . '_LINKTXT'), $active_lang);
        $art_privacy_button = Configuration::get(Tools::strtoupper($this->name . '_BUTTUMTXT'), $active_lang);
        $art_reject_button_txt = Configuration::get(Tools::strtoupper($this->name . '_REJECT'), $active_lang);
        $art_privacy_cms = Configuration::get(Tools::strtoupper($this->name . '_PRIVACY_CMS'));
        $art_extactive = Configuration::get(Tools::strtoupper($this->name . '_EXTACTIVE'));
        $art_target = Configuration::get(Tools::strtoupper($this->name . '_TARGET'));
        $art_consentmode = Configuration::get(Tools::strtoupper($this->name . '_CONSENTMODE'));

        if($art_extactive == 0) {
            $art_privacy_link = $this->context->link->getCMSLink((int) $art_privacy_cms);
        } else {
            $art_privacy_link = Configuration::get(Tools::strtoupper($this->name . '_PRIVACY_EXT'), $active_lang);
        }

        $arturi = Tools::getHttpHost(true) . __PS_BASE_URI__;
        $this->smarty->assign([
            'art_privacy_info' => $art_privacy_info,
            'arturi' => $arturi,
            'art_privacy_link' => $art_privacy_link,
            'art_privacy_button' => $art_privacy_button,
            'art_target' => $art_target,
            'art_consentmode' => (int) $art_consentmode,
            'art_privacy_text_link' => $art_privacy_text_link,
            'art_reject_button_txt' => $art_reject_button_txt,

            ]);

        return $this->display(__FILE__, 'artcookiechoices.tpl');
    }

    public function hookHeader($params)
    {

        if ($this->active) {
            $this->context->controller->addCSS($this->_path . '/views/css/artcookiechoicespro.css', 'all');
            }

        $arturi = Tools::getHttpHost(true) . __PS_BASE_URI__;
        $artcookies_bcolor = Configuration::get(Tools::strtoupper($this->name . '_BANNER_COLOR'));
        $artcookies_txtcolor = Configuration::get(Tools::strtoupper($this->name . '_TEXT_COLOR'));
        $artcookies_cshadow = Configuration::get(Tools::strtoupper($this->name . '_SHADOW_COLOR'));
        $artcookies_button = Configuration::get(Tools::strtoupper($this->name . '_BUTTON_COLOR'));
        $artcookies_shadow = Configuration::get(Tools::strtoupper($this->name . '_SHADOW'));
        $artcookies_tbutton = Configuration::get(Tools::strtoupper($this->name . '_BTEXT_COLOR'));
        $artloadjs = Configuration::get(Tools::strtoupper($this->name . '_LOADKJS'));
        $artcookies_position = Configuration::get(Tools::strtoupper($this->name . '_POSITION'));

        $this->smarty->assign([

            'artcookies_bcolor' => $artcookies_bcolor,
            'artcookies_shadow' => $artcookies_shadow,
            'artcookies_cshadow' => $artcookies_cshadow,
            'artcookies_txtcolor' => $artcookies_txtcolor,
            'artcookies_button' => $artcookies_button,
            'artloadjs' => (int) $artloadjs,
            'arturi' => pSQL($arturi),
            'artcookies_tbutton' => $artcookies_tbutton,
            'artcookies_position' => $artcookies_position,
            ]);

        return $this->display(__FILE__, 'artcookiesheader.tpl');
    }

    public function showUnsubscribe() {
        $link = $this->context->link->getModuleLink(
            $this->name,
            'disallow',
            [
                'token' => md5(_COOKIE_KEY_ . $this->name),
            ],
            true
        );

        $this->smarty->assign([
            'link' => $link,
        ]);

        return $this->display(__FILE__, 'unsubscribe.tpl');

    }

    public function hookDisplayFooterBefore() {
        $position = Configuration::get(Tools::strtoupper($this->name . '_REVOKE'));

        if ($position == 1) {
            return $this->showUnsubscribe();
        }
    }

    public function hookDisplayFooter() {
        $out = '';
        $position = Configuration::get(Tools::strtoupper($this->name . '_REVOKE'));
        $out .= $this->cookiesBar();

        if ($position == 2) {
            $out .= $this->showUnsubscribe();
        }

        return $out;
    }

    public function hookDisplayFooterAfter() {
        $position = Configuration::get(Tools::strtoupper($this->name . '_REVOKE'));

        if ($position == 3) {
            return $this->showUnsubscribe();
        }
    }

    public function hookCookiesDisable() {
        $position = Configuration::get(Tools::strtoupper($this->name . '_REVOKE'));

        if ($position == 4) {
            return $this->showUnsubscribe();
        }
    }
}
