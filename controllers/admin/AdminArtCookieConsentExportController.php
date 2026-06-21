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

class AdminArtCookieConsentExportController extends ModuleAdminController
{
    protected $allowed_formats = ['csv', 'json', 'xml'];
    protected $allowed_actions = ['accept_all', 'reject_all', 'save_selection'];
    protected $columns = [
        'id_artcookie_consent_log',
        'id_shop',
        'id_guest',
        'id_customer',
        'consent_version',
        'preferences_json',
        'action',
        'ip_anonymized',
        'ip_hash',
        'user_agent_hash',
        'consent_hash',
        'date_add',
    ];

    public function initContent()
    {
        if (is_object($this->module) && method_exists($this->module, 'installConsentExportTab')) {
            $this->module->installConsentExportTab();
        }

        $this->exportConsentLog();
    }

    protected function exportConsentLog()
    {
        $format = Tools::strtolower((string) Tools::getValue('format', 'csv'));

        if (!in_array($format, $this->allowed_formats, true)) {
            $format = 'csv';
        }

        $filters = $this->getValidatedFilters();
        $filename = 'artcookie-consent-log-' . date('Ymd-His') . '.' . $format;

        $this->sendDownloadHeaders($format, $filename);

        if ($format === 'json') {
            $this->exportJson($filters);
        } elseif ($format === 'xml') {
            $this->exportXml($filters);
        } else {
            $this->exportCsv($filters);
        }

        exit;
    }

    protected function getValidatedFilters()
    {
        $filters = [
            'date_from' => '',
            'date_to' => '',
            'consent_action' => '',
        ];
        $date_from = (string) Tools::getValue('date_from');
        $date_to = (string) Tools::getValue('date_to');
        $consent_action = (string) Tools::getValue('consent_action');

        if ($date_from !== '' && Validate::isDate($date_from)) {
            $filters['date_from'] = $date_from . ' 00:00:00';
        }

        if ($date_to !== '' && Validate::isDate($date_to)) {
            $filters['date_to'] = $date_to . ' 23:59:59';
        }

        if (in_array($consent_action, $this->allowed_actions, true)) {
            $filters['consent_action'] = $consent_action;
        }

        return $filters;
    }

    protected function sendDownloadHeaders($format, $filename)
    {
        $content_types = [
            'csv' => 'text/csv; charset=utf-8',
            'json' => 'application/json; charset=utf-8',
            'xml' => 'application/xml; charset=utf-8',
        ];

        header('Content-Type: ' . $content_types[$format]);
        header('Content-Disposition: attachment; filename="' . str_replace('"', '', $filename) . '"');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
    }

    protected function exportCsv(array $filters)
    {
        $output = fopen('php://output', 'w');

        if ($output === false) {
            return;
        }

        fputcsv($output, $this->columns);

        foreach ($this->fetchRows($filters) as $row) {
            fputcsv($output, $this->normalizeRow($row));
        }

        fclose($output);
    }

    protected function exportJson(array $filters)
    {
        echo '[';
        $first = true;

        foreach ($this->fetchRows($filters) as $row) {
            if (!$first) {
                echo ',';
            }

            echo json_encode($this->normalizeRow($row));
            $first = false;
        }

        echo ']';
    }

    protected function exportXml(array $filters)
    {
        if (!class_exists('XMLWriter')) {
            $this->exportXmlFallback($filters);

            return;
        }

        $writer = new XMLWriter();
        $writer->openURI('php://output');
        $writer->startDocument('1.0', 'UTF-8');
        $writer->startElement('consent_logs');

        foreach ($this->fetchRows($filters) as $row) {
            $writer->startElement('consent_log');

            foreach ($this->normalizeRow($row) as $key => $value) {
                $writer->writeElement($key, (string) $value);
            }

            $writer->endElement();
        }

        $writer->endElement();
        $writer->endDocument();
        $writer->flush();
    }

    protected function exportXmlFallback(array $filters)
    {
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<consent_logs>' . "\n";

        foreach ($this->fetchRows($filters) as $row) {
            echo '  <consent_log>' . "\n";

            foreach ($this->normalizeRow($row) as $key => $value) {
                echo '    <' . $key . '>' . htmlspecialchars((string) $value, ENT_XML1, 'UTF-8') . '</' . $key . '>' . "\n";
            }

            echo '  </consent_log>' . "\n";
        }

        echo '</consent_logs>';
    }

    protected function fetchRows(array $filters)
    {
        $offset = 0;
        $limit = 500;

        do {
            $rows = Db::getInstance()->executeS($this->buildQuery($filters, $limit, $offset));

            if (!is_array($rows) || empty($rows)) {
                break;
            }

            foreach ($rows as $row) {
                yield $row;
            }

            $offset += $limit;
        } while (count($rows) === $limit);
    }

    protected function buildQuery(array $filters, $limit, $offset)
    {
        $where = [];

        if ($filters['date_from'] !== '') {
            $where[] = '`date_add` >= \'' . pSQL($filters['date_from']) . '\'';
        }

        if ($filters['date_to'] !== '') {
            $where[] = '`date_add` <= \'' . pSQL($filters['date_to']) . '\'';
        }

        if ($filters['consent_action'] !== '') {
            $where[] = '`action` = \'' . pSQL($filters['consent_action']) . '\'';
        }

        $sql = 'SELECT `' . implode('`, `', $this->columns) . '`
            FROM `' . _DB_PREFIX_ . 'artcookie_consent_log`';

        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY `date_add` DESC, `id_artcookie_consent_log` DESC
            LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset;

        return $sql;
    }

    protected function normalizeRow(array $row)
    {
        $normalized = [];

        foreach ($this->columns as $column) {
            $normalized[$column] = isset($row[$column]) ? (string) $row[$column] : '';
        }

        return $normalized;
    }
}
