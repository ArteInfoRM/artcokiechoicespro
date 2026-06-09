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

class ArtCokiechoicespro extends Module
{
    public function __construct()
    {
        $this->name = 'artcokiechoicespro';
        $this->tab = 'front_office_features';
        $this->version = '1.6.2';
        $this->author = 'Tecnoacquisti.com';
        $this->need_instance = 0;

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('PrestaShop Banner Cookiechoices (Eu Cookie Law) GDPR');
        $this->description = $this->l('Free Cookie Tool: simple PrestaShop module that displays the EU Cookie Law banner based on Google\'s Cookiechoices.org. Updated to new 2022 rules.');

        $this->ps_versions_compliancy = ['min' => '1.7.8.0', 'max' => _PS_VERSION_];
    }

    public function install()
    {
        $languages = Language::getLanguages(false);
        $artcookies_text = [];
        $artcookies_url = [];
        $artcookies_linktxt = [];
        $artcookies_buttomtxt = [];
        $artcookies_reject = [];
        $artcookies_customize = [];
        $artcookies_save_preferences = [];
        $category_defaults = $this->getDefaultCookieCategoryTexts();
        $category_labels = [];
        $category_descriptions = [];
        $privacy_notice_texts = $this->getDefaultPrivacyNoticeTexts();
        $interface_texts = $this->getDefaultInterfaceTexts();

        foreach ($languages as $lang) {
            $iso_code = isset($lang['iso_code']) ? Tools::strtolower((string) $lang['iso_code']) : 'en';
            $privacy_notice_text = isset($privacy_notice_texts[$iso_code]) ?
                $privacy_notice_texts[$iso_code] :
                $privacy_notice_texts['en'];
            $interface_text = isset($interface_texts[$iso_code]) ?
                $interface_texts[$iso_code] :
                $interface_texts['en'];

            $artcookies_text[$lang['id_lang']] = pSQL($privacy_notice_text);
            $artcookies_url[$lang['id_lang']] = pSQL('#');
            $artcookies_linktxt[$lang['id_lang']] = pSQL($interface_text['privacy_link']);
            $artcookies_buttomtxt[$lang['id_lang']] = pSQL($interface_text['accept']);
            $artcookies_reject[$lang['id_lang']] = pSQL($interface_text['reject']);
            $artcookies_customize[$lang['id_lang']] = pSQL($interface_text['customize']);
            $artcookies_save_preferences[$lang['id_lang']] = pSQL($interface_text['save_preferences']);

            foreach ($category_defaults as $category_key => $category_default) {
                $category_labels[$category_key][$lang['id_lang']] = pSQL($category_default['label']);
                $category_descriptions[$category_key][$lang['id_lang']] = pSQL($category_default['description']);
            }
        }

        $this->_clearCache('artcookiechoices.tpl');

        return parent::install()
            && Configuration::updateValue(Tools::strtoupper($this->name) . '_ACTIVE', '1')
            && Configuration::updateValue(Tools::strtoupper($this->name) . '_CONSENTMODE', '1')
            && Configuration::updateValue(Tools::strtoupper($this->name) . '_EXTACTIVE', '0')
            && Configuration::updateValue(Tools::strtoupper($this->name) . '_PRIVACY_CMS', '0')
            && Configuration::updateValue(Tools::strtoupper($this->name) . '_BANNER_COLOR', '#000000')
            && Configuration::updateValue(Tools::strtoupper($this->name) . '_TEXT_COLOR', '#ffffff')
            && Configuration::updateValue(Tools::strtoupper($this->name) . '_SHADOW', '1')
            && Configuration::updateValue(Tools::strtoupper($this->name) . '_SHADOW_COLOR', '#000000')
            && Configuration::updateValue(Tools::strtoupper($this->name) . '_BUTTON_COLOR', '#f77002')
            && Configuration::updateValue(Tools::strtoupper($this->name) . '_BTEXT_COLOR', '#ffffff')
            && Configuration::updateValue(Tools::strtoupper($this->name) . '_TEXT', $artcookies_text)
            && Configuration::updateValue(Tools::strtoupper($this->name) . '_PRIVACY_EXT', $artcookies_url)
            && Configuration::updateValue(Tools::strtoupper($this->name) . '_LINKTXT', $artcookies_linktxt)
            && Configuration::updateValue(Tools::strtoupper($this->name) . '_BUTTUMTXT', $artcookies_buttomtxt)
            && Configuration::updateValue(Tools::strtoupper($this->name) . '_REJECT', $artcookies_reject)
            && Configuration::updateValue(Tools::strtoupper($this->name) . '_CUSTOMIZE', $artcookies_customize)
            && Configuration::updateValue(Tools::strtoupper($this->name) . '_SAVE_PREFS', $artcookies_save_preferences)
            && Configuration::updateValue(Tools::strtoupper($this->name) . '_TARGET', '_self')
            && Configuration::updateValue(Tools::strtoupper($this->name) . '_LOADKJS', '0')
            && Configuration::updateValue(Tools::strtoupper($this->name) . '_POSITION', 'bottom')
            && Configuration::updateValue(Tools::strtoupper($this->name) . '_REVOKE', '0')
            && Configuration::updateValue(Tools::strtoupper($this->name) . '_SEO_PROTECTION', '1')
            && Configuration::updateValue(Tools::strtoupper($this->name) . '_SEO_BOTS', $this->getDefaultSeoBotList())
            && $this->installCookieCategoryConfiguration($category_labels, $category_descriptions)
            && $this->registerHook('header')
            && $this->registerHook('CookiesDisable')
            && $this->registerHook('displayFooterBefore')
            && $this->registerHook('displayFooter')
            && $this->registerHook('displayFooterAfter')
            && $this->registerHook('displayCustomerAccount');
    }

    public function uninstall()
    {
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
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_CUSTOMIZE');
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_SAVE_PREFS');
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_TARGET');
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_LOADKJS');
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_COMPRESS');
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_POSITION');
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_DISABLE');
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_REVOKE');
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_SEO_PROTECTION');
        Configuration::deleteByName(Tools::strtoupper($this->name) . '_SEO_BOTS');
        $this->deleteCookieCategoryConfiguration();

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
            $artcookies_seo_protection = Tools::getValue('ARTCOKIECHOICESPRO_SEO_PROTECTION');
            $artcookies_seo_bots = $this->normalizeSeoBotList((string) Tools::getValue('ARTCOKIECHOICESPRO_SEO_BOTS'));

            if (!in_array($artcookies_position, ['top', 'bottom', 'center'], true)) {
                $artcookies_position = 'bottom';
            }

            if ($artcookies_seo_bots === '') {
                $artcookies_seo_bots = $this->getDefaultSeoBotList();
            }

            foreach ($languages as $lang) {
                $artcookies_url[$lang['id_lang']] = pSQL(
                    Tools::getValue('ARTCOKIECHOICESPRO_PRIVACY_EXT_' . $lang['id_lang'])
                );
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
            Configuration::updateValue('ARTCOKIECHOICESPRO_SEO_PROTECTION', (int) $artcookies_seo_protection);
            Configuration::updateValue('ARTCOKIECHOICESPRO_SEO_BOTS', $artcookies_seo_bots);

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
            $artcookies_customize = [];
            $artcookies_save_preferences = [];
            $languages = Language::getLanguages(false);
            $artcookies_active = Tools::getValue('ARTCOKIECHOICESPRO_ACTIVE');
            $artcookies_consentmode = Tools::getValue('ARTCOKIECHOICESPRO_CONSENTMODE');
            $artcookies_cms = Tools::getValue('ARTCOKIECHOICESPRO_PRIVACY_CMS');
            $artcookies_target = Tools::getValue('ARTCOKIECHOICESPRO_TARGET');
            $artcookies_revoke = Tools::getValue('ARTCOKIECHOICESPRO_REVOKE');

            foreach ($languages as $lang) {
                $artcookies_text[$lang['id_lang']] = urldecode(
                    Tools::getValue('ARTCOKIECHOICESPRO_TEXT_' . $lang['id_lang'])
                );
                $artcookies_linktxt[$lang['id_lang']] = urldecode(
                    Tools::getValue('ARTCOKIECHOICESPRO_LINKTXT_' . $lang['id_lang'])
                );
                $artcookies_buttomtxt[$lang['id_lang']] = urldecode(
                    Tools::getValue('ARTCOKIECHOICESPRO_BUTTUMTXT_' . $lang['id_lang'])
                );
                $artcookies_reject[$lang['id_lang']] = urldecode(
                    Tools::getValue('ARTCOKIECHOICESPRO_REJECT_' . $lang['id_lang'])
                );
                $artcookies_customize[$lang['id_lang']] = urldecode(
                    Tools::getValue('ARTCOKIECHOICESPRO_CUSTOMIZE_' . $lang['id_lang'])
                );
                $artcookies_save_preferences[$lang['id_lang']] = urldecode(
                    Tools::getValue('ARTCOKIECHOICESPRO_SAVE_PREFS_' . $lang['id_lang'])
                );
            }

            Configuration::updateValue('ARTCOKIECHOICESPRO_TEXT', $artcookies_text);
            Configuration::updateValue('ARTCOKIECHOICESPRO_LINKTXT', $artcookies_linktxt);
            Configuration::updateValue('ARTCOKIECHOICESPRO_BUTTUMTXT', $artcookies_buttomtxt);
            Configuration::updateValue('ARTCOKIECHOICESPRO_REJECT', $artcookies_reject);
            Configuration::updateValue('ARTCOKIECHOICESPRO_CUSTOMIZE', $artcookies_customize);
            Configuration::updateValue('ARTCOKIECHOICESPRO_SAVE_PREFS', $artcookies_save_preferences);
            Configuration::updateValue('ARTCOKIECHOICESPRO_ACTIVE', (int) $artcookies_active);
            Configuration::updateValue('ARTCOKIECHOICESPRO_CONSENTMODE', (int) $artcookies_consentmode);
            Configuration::updateValue('ARTCOKIECHOICESPRO_PRIVACY_CMS', (int) $artcookies_cms);
            Configuration::updateValue('ARTCOKIECHOICESPRO_REVOKE', (int) $artcookies_revoke);
            Configuration::updateValue('ARTCOKIECHOICESPRO_TARGET', $artcookies_target);
            $this->saveCookieCategoryStatus();

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
                                 ['id' => 'top', 'name' => $this->l('top')],
                                 ['id' => 'bottom', 'name' => $this->l('bottom')],
                                 ['id' => 'center', 'name' => $this->l('center')],
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
                    [
                        'type' => 'switch',
                        'label' => $this->l('SEO protection'),
                        'name' => Tools::strtoupper($this->name) . '_SEO_PROTECTION',
                        'is_bool' => true,
                        'desc' => $this->l('Hide the cookie banner and preference links from known search engine crawlers.'),
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
                        'label' => $this->l('Crawler user agents'),
                        'name' => Tools::strtoupper($this->name) . '_SEO_BOTS',
                        'cols' => 60,
                        'rows' => 8,
                        'desc' => $this->l('One crawler signature per line. Comma-separated lists are also accepted. Matching is case-insensitive.'),
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
            $artcookies_url[$language['id_lang']] = pSQL(
                Configuration::get('ARTCOKIECHOICESPRO_PRIVACY_EXT', $language['id_lang'])
            );
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
            'ARTCOKIECHOICESPRO_SEO_PROTECTION' => Tools::getValue('ARTCOKIECHOICESPRO_SEO_PROTECTION', Configuration::get('ARTCOKIECHOICESPRO_SEO_PROTECTION')),
            'ARTCOKIECHOICESPRO_SEO_BOTS' => Tools::getValue(
                'ARTCOKIECHOICESPRO_SEO_BOTS',
                $this->normalizeSeoBotList((string) Configuration::get('ARTCOKIECHOICESPRO_SEO_BOTS'))
            ),
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
                        'type' => 'text',
                        'label' => $this->l('Customize Button Text'),
                        'name' => Tools::strtoupper($this->name) . '_CUSTOMIZE',
                        'lang' => true,
                        'desc' => $this->l('Text for the button that opens cookie preferences'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Save Preferences Button Text'),
                        'name' => Tools::strtoupper($this->name) . '_SAVE_PREFS',
                        'lang' => true,
                        'desc' => $this->l('Text for the button that saves selected cookie categories'),
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
                                 ['id' => '_self', 'name' => $this->l('_self: opens the linked document in the same frame as it was clicked (this is default)')],
                                 ['id' => '_blank', 'name' => $this->l('_blank: opens the linked document in a new window or tab')],
                                 ['id' => '_parent', 'name' => $this->l('_parent: opens the linked document in the parent frame')],
                                 ['id' => '_top', 'name' => $this->l('_top: opens the linked document in the full body of the window')],
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
                                ['id' => '0', 'name' => $this->l('Select')],
                                ['id' => '1', 'name' => $this->l('displayFooterBefore')],
                                ['id' => '2', 'name' => $this->l('displayFooter')],
                                ['id' => '3', 'name' => $this->l('displayFooterAfter')],
                                ['id' => '4', 'name' => $this->l('CookiesDisable')],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Functional cookies category'),
                        'name' => Tools::strtoupper($this->name) . '_CAT_FUNCTIONAL_ACTIVE',
                        'is_bool' => true,
                        'desc' => $this->l('Allow customers to manage functional cookie consent'),
                        'values' => [
                            [
                                'id' => 'functional_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'functional_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Analytics cookies category'),
                        'name' => Tools::strtoupper($this->name) . '_CAT_ANALYTICS_ACTIVE',
                        'is_bool' => true,
                        'desc' => $this->l('Allow customers to manage analytics cookie consent'),
                        'values' => [
                            [
                                'id' => 'analytics_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'analytics_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Performance cookies category'),
                        'name' => Tools::strtoupper($this->name) . '_CAT_PERFORMANCE_ACTIVE',
                        'is_bool' => true,
                        'desc' => $this->l('Allow customers to manage performance cookie consent'),
                        'values' => [
                            [
                                'id' => 'performance_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'performance_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Advertising cookies category'),
                        'name' => Tools::strtoupper($this->name) . '_CAT_MARKETING_ACTIVE',
                        'is_bool' => true,
                        'desc' => $this->l('Allow customers to manage advertising cookie consent'),
                        'values' => [
                            [
                                'id' => 'marketing_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'marketing_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Other cookies category'),
                        'name' => Tools::strtoupper($this->name) . '_CAT_OTHER_ACTIVE',
                        'is_bool' => true,
                        'desc' => $this->l('Allow customers to manage uncategorized or custom cookie consent'),
                        'values' => [
                            [
                                'id' => 'other_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'other_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ],
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
        $artcookies_customize = [];
        $artcookies_save_preferences = [];

        foreach ($languages as $language) {
            $artcookies_text[$language['id_lang']] = Configuration::get('ARTCOKIECHOICESPRO_TEXT', $language['id_lang']);
            $artcookies_linktxt[$language['id_lang']] = Configuration::get('ARTCOKIECHOICESPRO_LINKTXT', $language['id_lang']);
            $artcookies_buttomtxt[$language['id_lang']] = Configuration::get('ARTCOKIECHOICESPRO_BUTTUMTXT', $language['id_lang']);
            $artcookies_reject[$language['id_lang']] = Configuration::get('ARTCOKIECHOICESPRO_REJECT', $language['id_lang']);
            $artcookies_customize[$language['id_lang']] = Configuration::get('ARTCOKIECHOICESPRO_CUSTOMIZE', $language['id_lang']);
            $artcookies_save_preferences[$language['id_lang']] = Configuration::get('ARTCOKIECHOICESPRO_SAVE_PREFS', $language['id_lang']);
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
            'ARTCOKIECHOICESPRO_CUSTOMIZE' => Tools::getValue('ARTCOKIECHOICESPRO_CUSTOMIZE', $artcookies_customize),
            'ARTCOKIECHOICESPRO_SAVE_PREFS' => Tools::getValue('ARTCOKIECHOICESPRO_SAVE_PREFS', $artcookies_save_preferences),
            'ARTCOKIECHOICESPRO_CAT_FUNCTIONAL_ACTIVE' => Tools::getValue('ARTCOKIECHOICESPRO_CAT_FUNCTIONAL_ACTIVE', $this->getCookieCategoryActiveValue('functional')),
            'ARTCOKIECHOICESPRO_CAT_ANALYTICS_ACTIVE' => Tools::getValue('ARTCOKIECHOICESPRO_CAT_ANALYTICS_ACTIVE', $this->getCookieCategoryActiveValue('analytics')),
            'ARTCOKIECHOICESPRO_CAT_PERFORMANCE_ACTIVE' => Tools::getValue('ARTCOKIECHOICESPRO_CAT_PERFORMANCE_ACTIVE', $this->getCookieCategoryActiveValue('performance')),
            'ARTCOKIECHOICESPRO_CAT_MARKETING_ACTIVE' => Tools::getValue('ARTCOKIECHOICESPRO_CAT_MARKETING_ACTIVE', $this->getCookieCategoryActiveValue('marketing')),
            'ARTCOKIECHOICESPRO_CAT_OTHER_ACTIVE' => Tools::getValue('ARTCOKIECHOICESPRO_CAT_OTHER_ACTIVE', $this->getCookieCategoryActiveValue('other')),
        ];
    }

    public function getCookieCategoryKeys()
    {
        return [
            'functional',
            'analytics',
            'performance',
            'marketing',
            'other',
        ];
    }

    public function getDefaultCookieCategoryTexts()
    {
        return [
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
    }

    public function getDefaultPrivacyNoticeTexts()
    {
        return [
            'en' => 'We use cookies and similar technologies to make this website work, improve your experience, measure traffic, and personalize content or ads. Some cookies are necessary and are always active; optional cookies are used only with your consent. You can accept all cookies, reject optional cookies, or customize your preferences at any time. For more information, please read our cookie policy.',
            'it' => 'Utilizziamo cookie e tecnologie simili per far funzionare questo sito, migliorare la tua esperienza, misurare il traffico e personalizzare contenuti o annunci. Alcuni cookie sono necessari e sono sempre attivi; i cookie opzionali vengono utilizzati solo con il tuo consenso. Puoi accettare tutti i cookie, rifiutare quelli opzionali o personalizzare le preferenze in qualsiasi momento. Per maggiori informazioni, leggi la nostra cookie policy.',
            'es' => 'Utilizamos cookies y tecnologías similares para que este sitio funcione, mejorar tu experiencia, medir el tráfico y personalizar contenidos o anuncios. Algunas cookies son necesarias y están siempre activas; las cookies opcionales se utilizan solo con tu consentimiento. Puedes aceptar todas las cookies, rechazar las opcionales o personalizar tus preferencias en cualquier momento. Para más información, lee nuestra política de cookies.',
            'fr' => 'Nous utilisons des cookies et des technologies similaires pour faire fonctionner ce site, améliorer votre expérience, mesurer le trafic et personnaliser les contenus ou les publicités. Certains cookies sont nécessaires et restent toujours actifs; les cookies optionnels ne sont utilisés qu’avec votre consentement. Vous pouvez accepter tous les cookies, refuser les cookies optionnels ou personnaliser vos préférences à tout moment. Pour en savoir plus, consultez notre politique relative aux cookies.',
            'de' => 'Wir verwenden Cookies und ähnliche Technologien, damit diese Website funktioniert, um Ihre Erfahrung zu verbessern, den Datenverkehr zu messen und Inhalte oder Anzeigen zu personalisieren. Einige Cookies sind erforderlich und immer aktiv; optionale Cookies werden nur mit Ihrer Zustimmung verwendet. Sie können alle Cookies akzeptieren, optionale Cookies ablehnen oder Ihre Einstellungen jederzeit anpassen. Weitere Informationen finden Sie in unserer Cookie-Richtlinie.',
        ];
    }

    public function getDefaultInterfaceTexts()
    {
        return [
            'en' => [
                'privacy_link' => 'Read the cookie policy',
                'reject' => 'Reject',
                'accept' => 'Accept',
                'customize' => 'Customize',
                'save_preferences' => 'Save preferences',
            ],
            'it' => [
                'privacy_link' => 'Leggi la cookie policy',
                'reject' => 'Rifiuta',
                'accept' => 'Accetta',
                'customize' => 'Personalizza',
                'save_preferences' => 'Salva preferenze',
            ],
            'es' => [
                'privacy_link' => 'Leer la politica de cookies',
                'reject' => 'Rechazar',
                'accept' => 'Aceptar',
                'customize' => 'Personalizar',
                'save_preferences' => 'Guardar preferencias',
            ],
            'fr' => [
                'privacy_link' => 'Lire la politique relative aux cookies',
                'reject' => 'Refuser',
                'accept' => 'Accepter',
                'customize' => 'Personnaliser',
                'save_preferences' => 'Enregistrer les preferences',
            ],
            'de' => [
                'privacy_link' => 'Cookie-Richtlinie lesen',
                'reject' => 'Ablehnen',
                'accept' => 'Akzeptieren',
                'customize' => 'Anpassen',
                'save_preferences' => 'Einstellungen speichern',
            ],
        ];
    }

    public function installCookieCategoryConfiguration($category_labels = [], $category_descriptions = [])
    {
        $result = true;
        $category_defaults = $this->getDefaultCookieCategoryTexts();
        $category_keys = array_merge(['necessary'], $this->getCookieCategoryKeys());

        foreach ($category_keys as $category_key) {
            $config_key = Tools::strtoupper($category_key);
            $labels = isset($category_labels[$category_key]) ? $category_labels[$category_key] : [];
            $descriptions = isset($category_descriptions[$category_key]) ? $category_descriptions[$category_key] : [];

            if (empty($labels)) {
                $labels = $this->buildLocalizedDefaultValue($category_defaults[$category_key]['label']);
            }

            if (empty($descriptions)) {
                $descriptions = $this->buildLocalizedDefaultValue($category_defaults[$category_key]['description']);
            }

            if ($category_key !== 'necessary') {
                $result = $result && Configuration::updateValue(
                    Tools::strtoupper($this->name) . '_CAT_' . $config_key . '_ACTIVE',
                    '1'
                );
            }

            $result = $result && Configuration::updateValue(
                Tools::strtoupper($this->name) . '_CAT_' . $config_key . '_LABEL',
                $labels
            );
            $result = $result && Configuration::updateValue(
                Tools::strtoupper($this->name) . '_CAT_' . $config_key . '_DESC',
                $descriptions
            );
        }

        return $result;
    }

    public function deleteCookieCategoryConfiguration()
    {
        $category_keys = array_merge(['necessary'], $this->getCookieCategoryKeys());

        foreach ($category_keys as $category_key) {
            $config_key = Tools::strtoupper($category_key);
            Configuration::deleteByName(Tools::strtoupper($this->name) . '_CAT_' . $config_key . '_ACTIVE');
            Configuration::deleteByName(Tools::strtoupper($this->name) . '_CAT_' . $config_key . '_LABEL');
            Configuration::deleteByName(Tools::strtoupper($this->name) . '_CAT_' . $config_key . '_DESC');
        }
    }

    protected function saveCookieCategoryStatus()
    {
        foreach ($this->getCookieCategoryKeys() as $category_key) {
            $config_key = 'ARTCOKIECHOICESPRO_CAT_' . Tools::strtoupper($category_key) . '_ACTIVE';
            Configuration::updateValue($config_key, (int) Tools::getValue($config_key));
        }
    }

    protected function getCookieCategoryActiveValue($category_key)
    {
        $value = Configuration::get('ARTCOKIECHOICESPRO_CAT_' . Tools::strtoupper($category_key) . '_ACTIVE');

        if ($value === false) {
            return 1;
        }

        return (int) $value;
    }

    protected function buildLocalizedDefaultValue($value)
    {
        $localized_value = [];
        $languages = Language::getLanguages(false);

        foreach ($languages as $lang) {
            $localized_value[$lang['id_lang']] = pSQL($value);
        }

        return $localized_value;
    }

    protected function getLocalizedConfigurationValue($name, $id_lang, $fallback)
    {
        $value = Configuration::get($name, $id_lang);

        if ($value === false || $value === '') {
            return $fallback;
        }

        return $value;
    }

    protected function getDefaultSeoBotList()
    {
        return implode("\n", [
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
        ]);
    }

    protected function normalizeSeoBotList($bot_list)
    {
        return implode("\n", $this->parseSeoBotList($bot_list));
    }

    protected function parseSeoBotList($bot_list)
    {
        $normalized_bot_list = str_replace(['\\r\\n', '\\n', '\\r'], "\n", (string) $bot_list);
        $items = preg_split('/[\r\n,]+/', $normalized_bot_list);
        $bots = [];
        $seen = [];

        if (!is_array($items)) {
            return [];
        }

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
            $bots[] = $bot;
        }

        return $bots;
    }

    protected function isSeoBotRequest()
    {
        if (!(bool) Configuration::get('ARTCOKIECHOICESPRO_SEO_PROTECTION')) {
            return false;
        }

        if (!isset($_SERVER['HTTP_USER_AGENT']) || trim((string) $_SERVER['HTTP_USER_AGENT']) === '') {
            return false;
        }

        $bot_list = (string) Configuration::get('ARTCOKIECHOICESPRO_SEO_BOTS');

        if ($bot_list === '') {
            $bot_list = $this->getDefaultSeoBotList();
        }

        foreach ($this->parseSeoBotList($bot_list) as $bot) {
            if (stripos((string) $_SERVER['HTTP_USER_AGENT'], $bot) !== false) {
                return true;
            }
        }

        return false;
    }

    protected function getCookieCategoriesForFront($id_lang)
    {
        $category_defaults = $this->getDefaultCookieCategoryTexts();
        $categories = [];

        $categories[] = $this->buildCookieCategoryForFront('necessary', $id_lang, $category_defaults, true);

        foreach ($this->getCookieCategoryKeys() as $category_key) {
            $active = $this->getCookieCategoryActiveValue($category_key);

            if ($active !== 1) {
                continue;
            }

            $categories[] = $this->buildCookieCategoryForFront($category_key, $id_lang, $category_defaults, false);
        }

        return $categories;
    }

    protected function buildCookieCategoryForFront($category_key, $id_lang, $category_defaults, $required)
    {
        $config_key = Tools::strtoupper($category_key);
        $label = $this->getLocalizedConfigurationValue(
            'ARTCOKIECHOICESPRO_CAT_' . $config_key . '_LABEL',
            $id_lang,
            $category_defaults[$category_key]['label']
        );
        $description = $this->getLocalizedConfigurationValue(
            'ARTCOKIECHOICESPRO_CAT_' . $config_key . '_DESC',
            $id_lang,
            $category_defaults[$category_key]['description']
        );

        return [
            'key' => $category_key,
            'label' => $label,
            'description' => $description,
            'required' => (bool) $required,
            'google' => $this->getGoogleConsentKeysForCategory($category_key),
            'microsoft' => $this->getMicrosoftConsentKeysForCategory($category_key),
        ];
    }

    protected function getGoogleConsentKeysForCategory($category_key)
    {
        $map = [
            'necessary' => ['security_storage'],
            'functional' => ['functionality_storage', 'personalization_storage'],
            'analytics' => ['analytics_storage'],
            'performance' => ['analytics_storage'],
            'marketing' => ['ad_storage', 'ad_user_data', 'ad_personalization'],
            'other' => [],
        ];

        return isset($map[$category_key]) ? $map[$category_key] : [];
    }

    protected function getMicrosoftConsentKeysForCategory($category_key)
    {
        $map = [
            'marketing' => ['ad_storage'],
        ];

        return isset($map[$category_key]) ? $map[$category_key] : [];
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

        $cms_options[] = [
            'name' => $this->l('Select CMS'),
            'id' => 0,
        ];

        return $cms_options;
    }

    public function cookiesBar()
    {
        if ($this->isSeoBotRequest()) {
            return '';
        }

        $active_lang = $this->context->language->id;
        $art_privacy_info = Configuration::get(Tools::strtoupper($this->name . '_TEXT'), $active_lang);
        $art_privacy_text_link = Configuration::get(Tools::strtoupper($this->name . '_LINKTXT'), $active_lang);
        $art_privacy_button = Configuration::get(Tools::strtoupper($this->name . '_BUTTUMTXT'), $active_lang);
        $art_reject_button_txt = Configuration::get(Tools::strtoupper($this->name . '_REJECT'), $active_lang);
        $art_customize_button_txt = Configuration::get(Tools::strtoupper($this->name . '_CUSTOMIZE'), $active_lang);
        $art_save_preferences_txt = Configuration::get(Tools::strtoupper($this->name . '_SAVE_PREFS'), $active_lang);
        $art_cancel_preferences_txt = $this->l('Cancel');
        $art_reject_all_txt = $this->l('Reject all');
        $art_accept_selection_txt = $this->l('Accept selection');
        $art_accept_all_txt = $this->l('Accept all');
        $art_cookie_preferences_title = $this->l('Cookie preferences');
        $art_privacy_cms = Configuration::get(Tools::strtoupper($this->name . '_PRIVACY_CMS'));
        $art_extactive = Configuration::get(Tools::strtoupper($this->name . '_EXTACTIVE'));
        $art_target = Configuration::get(Tools::strtoupper($this->name . '_TARGET'));
        $art_consentmode = Configuration::get(Tools::strtoupper($this->name . '_CONSENTMODE'));

        if ($art_customize_button_txt === false || $art_customize_button_txt === '') {
            $art_customize_button_txt = 'Customize';
        }

        if ($art_save_preferences_txt === false || $art_save_preferences_txt === '') {
            $art_save_preferences_txt = 'Save preferences';
        }

        if ($art_extactive == 0) {
            $art_privacy_link = $this->context->link->getCMSLink((int) $art_privacy_cms);
        } else {
            $art_privacy_link = Configuration::get(Tools::strtoupper($this->name . '_PRIVACY_EXT'), $active_lang);
        }

        $arturi = Tools::getHttpHost(true) . __PS_BASE_URI__;
        $art_disallow_link = $this->context->link->getModuleLink(
            $this->name,
            'disallow',
            [
                'token' => md5(_COOKIE_KEY_ . $this->name),
            ],
            true
        );
        $art_cookie_categories_json = json_encode($this->getCookieCategoriesForFront((int) $active_lang));

        if ($art_cookie_categories_json === false) {
            $art_cookie_categories_json = '[]';
        }

        $this->smarty->assign([
            'art_privacy_info' => $art_privacy_info,
            'arturi' => $arturi,
            'art_privacy_link' => $art_privacy_link,
            'art_privacy_button' => $art_privacy_button,
            'art_target' => $art_target,
            'art_consentmode' => (int) $art_consentmode,
            'art_privacy_text_link' => $art_privacy_text_link,
            'art_reject_button_txt' => $art_reject_button_txt,
            'art_customize_button_txt' => $art_customize_button_txt,
            'art_save_preferences_txt' => $art_save_preferences_txt,
            'art_cancel_preferences_txt' => $art_cancel_preferences_txt,
            'art_reject_all_txt' => $art_reject_all_txt,
            'art_accept_selection_txt' => $art_accept_selection_txt,
            'art_accept_all_txt' => $art_accept_all_txt,
            'art_cookie_preferences_title' => $art_cookie_preferences_title,
            'art_disallow_link' => $art_disallow_link,
            'art_cookie_categories_base64' => base64_encode($art_cookie_categories_json),
            ]);

        return $this->display(__FILE__, 'artcookiechoices.tpl');
    }

    public function hookHeader($params)
    {
        if ($this->isSeoBotRequest()) {
            return '';
        }

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
        $art_consentmode = (int) Configuration::get(Tools::strtoupper($this->name . '_CONSENTMODE'));
        $art_cookie_categories_json = json_encode(
            $this->getCookieCategoriesForFront((int) $this->context->language->id)
        );

        if ($art_cookie_categories_json === false) {
            $art_cookie_categories_json = '[]';
        }

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
            'art_consentmode' => $art_consentmode,
            'art_cookie_categories_base64' => base64_encode($art_cookie_categories_json),
            ]);

        return $this->display(__FILE__, 'artcookiesheader.tpl');
    }

    public function showUnsubscribe()
    {
        if ($this->isSeoBotRequest()) {
            return '';
        }

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

    public function showAccountPreferencesLink()
    {
        if ($this->isSeoBotRequest()) {
            return '';
        }

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

        return $this->display(__FILE__, 'account-preferences.tpl');
    }

    public function hookDisplayFooterBefore()
    {
        $out = '';
        $position = Configuration::get(Tools::strtoupper($this->name . '_REVOKE'));

        if ($position == 1) {
            $out .= $this->showUnsubscribe();
        }

        return $out;
    }

    public function hookDisplayFooter()
    {
        $out = '';
        $position = Configuration::get(Tools::strtoupper($this->name . '_REVOKE'));
        $out .= $this->cookiesBar();

        if ($position == 2) {
            $out .= $this->showUnsubscribe();
        }

        return $out;
    }

    public function hookDisplayFooterAfter()
    {
        $out = '';
        $position = Configuration::get(Tools::strtoupper($this->name . '_REVOKE'));

        if ($position == 3) {
            $out .= $this->showUnsubscribe();
        }
        return $out;
    }

    public function hookDisplayCustomerAccount()
    {
        return $this->showAccountPreferencesLink();
    }

    public function hookCookiesDisable()
    {
        $out = '';
        $position = Configuration::get(Tools::strtoupper($this->name . '_REVOKE'));

        if ($position == 4) {
            $out .= $this->showUnsubscribe();
        }
        return $out;
    }
}
