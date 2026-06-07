# Art Cookie Choices Pro - Development and Integration Notes

This document summarizes the technical information needed to maintain, extend, integrate, test, and release the `artcokiechoicespro` PrestaShop module.

## Module Scope

Art Cookie Choices Pro provides a front-office cookie banner with consent actions, a consent reset link, optional granular cookie preferences, and optional Google Consent Mode v2 / Microsoft UET Consent Mode integration.

The module is intentionally lightweight:

- No custom database tables.
- No Composer dependency required in production.
- Configuration is stored through PrestaShop `Configuration`.
- Front-office rendering uses Smarty templates.
- JavaScript is plain browser JavaScript.

## Compatibility

Minimum supported PrestaShop version: `1.7.8.0`.

Supported major versions:

- PrestaShop 1.7.8.x
- PrestaShop 8.x
- PrestaShop 9.x

Keep the module compatible with all supported versions. Do not introduce PS9-only APIs unless the code path is guarded by `_PS_VERSION_` checks.

## Main Files

| Path | Purpose |
|---|---|
| `artcokiechoicespro.php` | Main module class, install/uninstall, configuration forms, hooks, Smarty assignments |
| `views/templates/hook/artcookiechoices.tpl` | Front-office JavaScript loader and banner initialization |
| `views/templates/hook/artcookiesheader.tpl` | Dynamic front-office CSS based on module configuration |
| `views/css/artcookiechoicespro.css` | Base banner and preferences panel CSS |
| `views/js/cookiechoices-consentmode.js` | Banner behavior, preference storage, Consent Mode updates |
| `controllers/front/disallow.php` | Consent reset controller |
| `views/templates/front/disallow*.tpl` | Consent reset front-office templates |
| `upgrade/install-*.php` | Upgrade scripts for installed module versions |
| `README.md` | Public module overview |
| `CHANGELOG.md` | Release history |

## Configuration Keys

Configuration keys use the `ARTCOKIECHOICESPRO_` prefix.

Core keys:

- `ARTCOKIECHOICESPRO_ACTIVE`
- `ARTCOKIECHOICESPRO_CONSENTMODE`
- `ARTCOKIECHOICESPRO_EXTACTIVE`
- `ARTCOKIECHOICESPRO_PRIVACY_CMS`
- `ARTCOKIECHOICESPRO_PRIVACY_EXT`
- `ARTCOKIECHOICESPRO_TEXT`
- `ARTCOKIECHOICESPRO_LINKTXT`
- `ARTCOKIECHOICESPRO_BUTTUMTXT`
- `ARTCOKIECHOICESPRO_REJECT`
- `ARTCOKIECHOICESPRO_CUSTOMIZE`
- `ARTCOKIECHOICESPRO_SAVE_PREFS`
- `ARTCOKIECHOICESPRO_TARGET`
- `ARTCOKIECHOICESPRO_POSITION`
- `ARTCOKIECHOICESPRO_REVOKE`

Style keys:

- `ARTCOKIECHOICESPRO_BANNER_COLOR`
- `ARTCOKIECHOICESPRO_TEXT_COLOR`
- `ARTCOKIECHOICESPRO_SHADOW`
- `ARTCOKIECHOICESPRO_SHADOW_COLOR`
- `ARTCOKIECHOICESPRO_BUTTON_COLOR`
- `ARTCOKIECHOICESPRO_BTEXT_COLOR`
- `ARTCOKIECHOICESPRO_LOADKJS`

Cookie category keys:

- `ARTCOKIECHOICESPRO_CAT_FUNCTIONAL_ACTIVE`
- `ARTCOKIECHOICESPRO_CAT_ANALYTICS_ACTIVE`
- `ARTCOKIECHOICESPRO_CAT_PERFORMANCE_ACTIVE`
- `ARTCOKIECHOICESPRO_CAT_MARKETING_ACTIVE`
- `ARTCOKIECHOICESPRO_CAT_OTHER_ACTIVE`

Localized category labels and descriptions:

- `ARTCOKIECHOICESPRO_CAT_NECESSARY_LABEL`
- `ARTCOKIECHOICESPRO_CAT_NECESSARY_DESC`
- `ARTCOKIECHOICESPRO_CAT_FUNCTIONAL_LABEL`
- `ARTCOKIECHOICESPRO_CAT_FUNCTIONAL_DESC`
- `ARTCOKIECHOICESPRO_CAT_ANALYTICS_LABEL`
- `ARTCOKIECHOICESPRO_CAT_ANALYTICS_DESC`
- `ARTCOKIECHOICESPRO_CAT_PERFORMANCE_LABEL`
- `ARTCOKIECHOICESPRO_CAT_PERFORMANCE_DESC`
- `ARTCOKIECHOICESPRO_CAT_MARKETING_LABEL`
- `ARTCOKIECHOICESPRO_CAT_MARKETING_DESC`
- `ARTCOKIECHOICESPRO_CAT_OTHER_LABEL`
- `ARTCOKIECHOICESPRO_CAT_OTHER_DESC`

## Cookie Storage

The module keeps backward compatibility with the legacy binary cookie:

- `displayCookieConsent=y`: at least one optional category was accepted.
- `displayCookieConsent=n`: optional categories were rejected.

Granular preferences are stored in:

- `displayCookieConsentPreferences`

The granular cookie stores a JSON object keyed by category, for example:

```json
{
  "necessary": true,
  "functional": true,
  "analytics": false,
  "performance": false,
  "marketing": true,
  "other": false
}
```

Both cookies are written for one year and use `path=/`.

## Consent Categories

The front-office categories are built in `getCookieCategoriesForFront()`.

Default categories:

- `necessary`: always enabled and cannot be disabled by the visitor.
- `functional`: optional.
- `analytics`: optional.
- `performance`: optional.
- `marketing`: optional advertising category.
- `other`: optional uncategorized/custom category.

Back-office switches control whether optional categories are displayed in the preferences panel. The necessary category is always displayed.

## Google Consent Mode v2 Mapping

Consent Mode is applied only when `ARTCOKIECHOICESPRO_CONSENTMODE` is enabled.

Category mapping:

| Category | Google consent keys |
|---|---|
| `necessary` | `security_storage` |
| `functional` | `functionality_storage`, `personalization_storage` |
| `analytics` | `analytics_storage` |
| `performance` | `analytics_storage` |
| `marketing` | `ad_storage`, `ad_user_data`, `ad_personalization` |
| `other` | none |

Default behavior:

- `security_storage` remains `granted`.
- Optional categories default to `denied` until accepted.
- If either analytics or performance is accepted, `analytics_storage` becomes `granted`.
- If marketing is accepted, advertising consent keys become `granted`.

The JavaScript supports both direct `gtag()` calls and fallback `dataLayer.push()` events.

## Microsoft UET Consent Mode Mapping

Consent Mode is applied only when `ARTCOKIECHOICESPRO_CONSENTMODE` is enabled.

Category mapping:

| Category | Microsoft consent keys |
|---|---|
| `marketing` | `ad_storage` |

The JavaScript updates `window.uetq` when available.

## Front-Office Integration

The banner is normally rendered through footer hooks:

- `displayFooterBefore`
- `displayFooter`
- `displayFooterAfter`
- `CookiesDisable`

The main banner output is created by `cookiesBar()` and rendered with:

- `views/templates/hook/artcookiechoices.tpl`

Dynamic styling is rendered by `hookHeader()` with:

- `views/templates/hook/artcookiesheader.tpl`

The module adds:

- `views/css/artcookiechoicespro.css`
- `views/js/cookiechoices-consentmode.js`

The template calls:

```js
cookieChoices.showCookieConsentBar(
  cookieText,
  acceptText,
  linkText,
  linkHref,
  linkTarget,
  rejectText,
  customizeText,
  savePreferencesText,
  categories,
  consentModeEnabled
);
```

The last argument controls whether Google/Microsoft consent updates are emitted. The preferences UI is available independently from Consent Mode.

## Banner Position

Supported values:

- `top`
- `bottom`
- `center`

Validate submitted values server-side with an allowlist. Fallback should be `bottom`.

## Adding a New Category

To add a new optional category:

1. Add the key to `getCookieCategoryKeys()`.
2. Add default label and description to `getDefaultCookieCategoryTexts()`.
3. Add back-office switch fields in `getConfigBasic()`.
4. Add returned values in `getConfigFormValues()`.
5. Add install/uninstall configuration handling.
6. Add upgrade script defaults for existing installations.
7. Add Google/Microsoft mapping methods if the category affects consent keys.
8. Update README and CHANGELOG.
9. Test the front-office JSON output and JavaScript behavior.

Avoid adding database tables unless the module needs audit logging or per-cookie records.

## Validation and Security

Follow these rules when changing the module:

- Treat all merchant-entered configuration values as untrusted.
- Validate enum-like values such as banner position, link target, category status, and CMS ID.
- Cast booleans and IDs explicitly.
- Escape Smarty output according to context.
- Keep HTML in `.tpl` files, not in PHP strings.
- Do not expose secrets or third-party credentials in JavaScript, templates, URLs, logs, or release archives.
- Keep front-office JSON generated from structured arrays, not concatenated strings.

## Coding Standards

Follow the workspace PrestaShop coding standards:

- PHP compatible with PrestaShop 1.7.8+.
- No PS9-only type hints in legacy paths.
- Short array syntax.
- No blank line after PHPDoc when validator flags it.
- Aligned license headers.
- LF line endings and trailing newline.
- Source-code-facing text in English.
- ASCII-only code comments in PHP, JS, CSS, TPL, SQL, and shell files.

## Release Checklist

Before committing or releasing:

1. Update `@version` and `$this->version` in `artcokiechoicespro.php`.
2. Update `config.xml`.
3. Add an upgrade script when new configuration keys are introduced.
4. Update `CHANGELOG.md`.
5. Update `README.md` when behavior or integration changes.
6. Run syntax checks:

```bash
php -l artcokiechoicespro.php
php -l upgrade/install-1.6.0.php
node --check views/js/cookiechoices-consentmode.js
git diff --check
```

7. Test install/upgrade on local Docker targets:

```bash
cd /home/loris/ps9
./add-module.sh artcokiechoicespro --containers ps90,ps91,ps82,ps178
```

8. Verify module version and active state in each database.
9. Create an annotated tag only after the commit is pushed.
10. Build release ZIP from the tag with `git archive`.

## Local Docker Notes

Standard local containers:

- `ps90`: PrestaShop 9.0
- `ps91`: PrestaShop 9.1
- `ps82`: PrestaShop 8.2
- `ps178`: PrestaShop 1.7.8

Some local PrestaShop console commands may require environment fixes:

```bash
docker exec -e SERVER_PORT=443 ps9-ps82 php bin/console --no-debug prestashop:module upgrade artcokiechoicespro
docker exec -e SERVER_PORT=443 ps9-ps91 php bin/console prestashop:module upgrade artcokiechoicespro
```

PrestaShop 8 may print a core warning about `number_upgraded` during module upgrade. When the command exits successfully and the database version is updated, it can be treated as a local core warning rather than a module failure.

## Database Verification Examples

The workspace uses separate databases and prefixes:

| Container | Database | Prefix |
|---|---|---|
| `ps90` | `ps9` | `ps_` |
| `ps91` | `ps91` | `ps9_` |
| `ps82` | `ps82` | `ps8_` |
| `ps178` | `ps178` | `ps17_` |

Example:

```bash
docker exec ps9-db mysql -uroot -proot ps9 \
  -e "SELECT name, version, active FROM ps_module WHERE name='artcokiechoicespro';"
```

## Support Metadata

First public brand mention: Tecnoacquisti.com®.

Official support:

- Website: https://www.tecnoacquisti.com
- Help desk: https://help.tecnoacquisti.com
- GitHub organization: https://github.com/ArteInfoRM
