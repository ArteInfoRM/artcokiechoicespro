# Art Cookie Choices Pro

Art Cookie Choices Pro is a PrestaShop module by Tecnoacquisti.com / Arte e
Informatica for managing a GDPR-oriented cookie banner with granular consent
preferences.

The module is designed for PrestaShop 1.7, 8 and 9, including both Classic and
Hummingbird-based themes. It supports Google Consent Mode v2 and Microsoft UET
Consent Mode without requiring core overrides.

## Features

- Cookie banner with accept, reject, customize and close actions.
- Dedicated cookie preferences popup with category-level consent choices.
- Footer link and customer account link to reopen cookie preferences.
- Consent reset controller protected by module token.
- Granular preferences stored in `displayCookieConsentPreferences`.
- Backward-compatible consent flag stored in `displayCookieConsent`.
- Configurable banner position: top, bottom or center.
- Configurable optional cookie categories:
  - Functional cookies
  - Analytics cookies
  - Performance cookies
  - Advertising cookies
  - Other cookies
- Google Consent Mode v2 support.
- Microsoft UET Consent Mode support.
- SEO protection that hides cookie UI from configured crawler user agents.
- Localized installation defaults for English, Italian, Spanish, French and German.
- Module translations aligned for English, Italian, Spanish, French, German,
  British English and Polish.
- Theme-friendly markup and CSS, with a native account-page tile layout.

## Compatibility

| PrestaShop version | Status |
| --- | --- |
| 1.7.8+ | Supported |
| 8.x | Supported |
| 9.x | Supported |
| 1.6.x | Not supported |

## Installation

1. Download the release ZIP package.
2. In the PrestaShop back office, open Modules > Module Manager.
3. Upload and install the ZIP package.
4. Configure banner copy, button labels, privacy link, categories and consent mode.

Release ZIP files are built from Git tags and use the `artcokiechoicespro/`
archive prefix, so the module extracts into the expected directory name.

## Cookie Preferences

The customize button opens a dedicated preferences popup. Its footer includes:

- Cancel: opens the consent reset controller.
- Reject all: rejects all optional categories.
- Accept selection: saves the selected categories.
- Accept all: accepts every category.

The footer and account-page links open the preferences popup instead of resetting
cookies immediately. The reset controller remains available through the Cancel
button inside the preferences popup.

## Account Page Link

The module registers `displayCustomerAccount` and adds a standard account-page
tile. The tile uses the native PrestaShop account layout classes and a Material
Icons icon for visual consistency with Classic and Hummingbird themes.

## SEO Protection

SEO protection is enabled by default. When the request user agent matches a
configured crawler signature, the module does not render the banner, preferences
popup, CSS, or preference links.

The default crawler list includes:

```text
Googlebot
Google-InspectionTool
AdsBot-Google
Mediapartners-Google
bingbot
BingPreview
AdIdxBot
MicrosoftPreview
DuckDuckBot
YandexBot
Applebot
facebookexternalhit
facebot
Twitterbot
LinkedInBot
Pinterestbot
```

The list accepts one signature per line. Comma-separated values are also
accepted. Matching is case-insensitive.

## Localized Defaults

On installation, the module preconfigures banner text and the following labels
for English, Italian, Spanish, French and German:

- Privacy link text
- Reject button text
- Accept button text
- Customize button text
- Save preferences button text

Any other active language falls back to English. Existing merchant-customized
texts are not overwritten by upgrades.

The module translation files also include the same interface keys for English,
Italian, Spanish, French, German, British English and Polish, so back-office and
front-office labels remain available through the PrestaShop translation system.

## Back Office Rendering

The configuration page uses PrestaShop HelperForm to generate trusted Back
Office form markup. The Smarty template keeps that generated markup unescaped so
the form HTML remains functional, with an inline comment documenting why the
`nofilter` modifier is intentional.

## Consent Mode Mapping

Google Consent Mode v2 signals are updated after user choices:

- Advertising cookies: `ad_storage`, `ad_user_data`, `ad_personalization`
- Analytics cookies: `analytics_storage`
- Performance cookies: `analytics_storage`
- Functional cookies: `functionality_storage`, `personalization_storage`
- Necessary cookies: `security_storage`

Microsoft UET `ad_storage` is mapped to the advertising category.

For Google Tag Assistant validation, keep Art Cookie Choices Pro before any
GA4, Google Tag Manager, Google Ads or Conversion Linker module in the
PrestaShop `displayHeader` / `header` hook order. The module sets Consent Mode
defaults in the header, and Google requires those defaults to run before any tag
reads consent state.

## Release Notes

Version `1.6.2` includes:

- Cookie preferences popup redesign.
- Account-page preferences tile.
- SEO protection for crawler user agents.
- Localized installation defaults for banner and button text.
- Completed module translation keys across bundled languages.
- Validator-oriented documentation for trusted Back Office HelperForm markup.
- Early Consent Mode defaults in the header to avoid late-default warnings in
  Google Tag Assistant.
- Consent Mode enabled by default on new module installations.
- Upgrade registration for the `header` hook when needed.
- Improved consent reset behavior.
- Removal of the old documentation image asset.
- Release archive cleanup through `.gitattributes`.

## Support

- GitHub issues: https://github.com/ArteInfoRM/artcokiechoicespro/issues
- Website: https://www.tecnoacquisti.com/

## License

See the included `LICENSE` file.
