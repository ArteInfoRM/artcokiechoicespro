<?php
/**
 *  2009-2026 Tecnoacquisti.com
 *
 *  For support feel free to contact us on our website at http://www.arteinformatica.eu
 *
 *  @author    Arte e Informatica <shop@tecnoacquisti.com>
 *  @copyright 2009-2026 Arte e Informatica
 *  @license   One Paid Licence By WebSite Using This Module. No Rent. No Sell. No Share.
 *  @version   1.6.4
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class ArtcokiechoicesproConsentlogModuleFrontController extends ModuleFrontController
{
    public $ajax = true;

    public function initContent()
    {
        parent::initContent();

        header('Content-Type: application/json; charset=utf-8');

        if (!$this->module->isConsentLogEnabled()) {
            $this->renderJson(false, 'disabled');
        }

        if (!hash_equals($this->module->getConsentLogToken(), (string) Tools::getValue('token'))) {
            $this->renderJson(false, 'invalid_token');
        }

        $action = (string) Tools::getValue('action');
        $allowed_actions = ['accept_all', 'reject_all', 'save_selection'];

        if (!in_array($action, $allowed_actions, true)) {
            $this->renderJson(false, 'invalid_action');
        }

        $consent_version = (string) Tools::getValue('consent_version');

        if ($consent_version !== $this->module->getConsentVersion()) {
            $this->renderJson(false, 'invalid_version');
        }

        $preferences = $this->normalizePreferences((string) Tools::getValue('preferences'));

        if ($preferences === false) {
            $this->renderJson(false, 'invalid_preferences');
        }

        $preferences_json = json_encode($preferences);

        if ($preferences_json === false) {
            $this->renderJson(false, 'invalid_preferences');
        }

        if (method_exists($this->module, 'installConsentLogTable')) {
            $this->module->installConsentLogTable();
        }

        $id_shop = (int) $this->context->shop->id;
        $id_guest = $this->getContextGuestId();
        $id_customer = isset($this->context->customer) && $this->context->customer->isLogged()
            ? (int) $this->context->customer->id
            : 0;
        $ip_address = (string) Tools::getRemoteAddr();
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? (string) $_SERVER['HTTP_USER_AGENT'] : '';
        $ip_anonymized = $this->module->anonymizeIpAddress($ip_address);
        $ip_hash = hash('sha256', _COOKIE_KEY_ . '|ip|' . $ip_address);
        $user_agent_hash = hash('sha256', _COOKIE_KEY_ . '|ua|' . $user_agent);
        $consent_hash = hash(
            'sha256',
            implode('|', [
                $id_shop,
                $id_guest,
                $id_customer,
                $consent_version,
                $preferences_json,
                $ip_hash,
                $user_agent_hash,
            ])
        );

        if ($this->hasSameLatestConsent($id_shop, $id_guest, $id_customer, $ip_hash, $user_agent_hash, $consent_hash)) {
            $this->module->cleanupConsentLog();
            $this->renderJson(true, 'unchanged');
        }

        $inserted = Db::getInstance()->insert('artcookie_consent_log', [
            'id_shop' => $id_shop,
            'id_guest' => $id_guest,
            'id_customer' => $id_customer,
            'consent_hash' => pSQL($consent_hash),
            'consent_version' => pSQL($consent_version),
            'preferences_json' => pSQL($preferences_json),
            'action' => pSQL($action),
            'ip_anonymized' => pSQL($ip_anonymized),
            'ip_hash' => pSQL($ip_hash),
            'user_agent_hash' => pSQL($user_agent_hash),
            'date_add' => date('Y-m-d H:i:s'),
        ], false, true, Db::INSERT);

        $this->module->cleanupConsentLog();
        $this->renderJson((bool) $inserted, $inserted ? 'logged' : 'insert_failed');
    }

    protected function normalizePreferences($raw_preferences)
    {
        $decoded = json_decode($raw_preferences, true);
        $allowed_keys = ['necessary', 'functional', 'analytics', 'performance', 'marketing', 'other'];
        $preferences = [];

        if (!is_array($decoded)) {
            return false;
        }

        foreach ($allowed_keys as $key) {
            $preferences[$key] = $key === 'necessary'
                ? true
                : !empty($decoded[$key]);
        }

        return $preferences;
    }

    protected function getContextGuestId()
    {
        if (isset($this->context->cookie->id_guest)) {
            return (int) $this->context->cookie->id_guest;
        }

        if (isset($this->context->cart) && isset($this->context->cart->id_guest)) {
            return (int) $this->context->cart->id_guest;
        }

        return 0;
    }

    protected function hasSameLatestConsent($id_shop, $id_guest, $id_customer, $ip_hash, $user_agent_hash, $consent_hash)
    {
        $sql = 'SELECT `consent_hash`
            FROM `' . _DB_PREFIX_ . 'artcookie_consent_log`
            WHERE `id_shop` = ' . (int) $id_shop . '
                AND `id_guest` = ' . (int) $id_guest . '
                AND `id_customer` = ' . (int) $id_customer . '
                AND `ip_hash` = \'' . pSQL($ip_hash) . '\'
                AND `user_agent_hash` = \'' . pSQL($user_agent_hash) . '\'
            ORDER BY `date_add` DESC, `id_artcookie_consent_log` DESC';
        $latest_hash = (string) Db::getInstance()->getValue($sql);

        return $latest_hash !== '' && hash_equals($latest_hash, $consent_hash);
    }

    protected function renderJson($success, $message)
    {
        echo json_encode([
            'success' => (bool) $success,
            'message' => (string) $message,
        ]);
        exit;
    }
}
