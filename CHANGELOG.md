# Changelog
All notable changes to this project are documented below.

---

## [1.6.1] - 2026-06-09
### Added
- Added the cookie preferences link to the customer account page.
- Added SEO protection to hide the cookie banner from configured crawler user agents.
- Added localized default cookie notice text for English, Italian, Spanish, French and German.
- Added localized default cookie link and button labels for English, Italian, Spanish, French and German.
- Added a curated default crawler list for Google, Bing/Microsoft, DuckDuckGo, Yandex, Apple and social preview bots.
- Fixed crawler list storage to preserve real line breaks across PrestaShop versions.
- Updated the README with the current module behavior and setup notes.
- Fixed additional PrestaShop Validator warnings for license headers, array syntax, upgrade compatibility and template escaping.

## [1.6.0] – 2026-06-07
### Added
- Added banner `center` position in addition to `top` and `bottom`.
- Added configurable cookie categories: functional, analytics, performance, advertising and other cookies.
- Added a front-office preferences panel with customizable button labels.

### Improved
- Improved Google Consent Mode v2 mapping with granular consent keys for advertising, analytics, functionality, personalization and security storage.
- Improved Microsoft UET Consent Mode support by mapping advertising consent to `ad_storage`.
- Kept backward compatibility with the existing `displayCookieConsent` cookie while adding granular preference storage.

## [1.5.5] – 2025-02-08
### Added
- New disallow page with user feedback message and automatic redirect to the homepage.
- Added redirect button for improved UX when resetting cookie preferences.
- Added dedicated template `disallow-17.tpl` for consent-reset handling.
- Added new translations: **ES**, **FR**, **DE**, **PL**.
- Updated translations: **IT** and **EN**.

### Improved
- Refactored disallow controller for full compatibility with **PrestaShop 1.7, 8, and 9**.
- Removed obsolete configuration option `ARTCOKIECHOICESPRO_DISABLE` and legacy unused code.
- Improved JavaScript event handling for Accept, Reject and Close actions in both Standard Mode and Consent Mode v2.
- Enhanced DOM injection logic for better compatibility across themes and demo environments.
- Improved separation between controller logic and templates.

### Fixed
- Fixed missing click event bindings causing Accept/Reject buttons to fail on some installations.
- Fixed empty output on consent-reset page due to missing template rendering.
- Fixed multiple PrestaShop Validator warnings (Context usage, nofilter removal, escaping rules).

---

## [1.5.4] – 2025-01-02
### Added
- Full compatibility with **PrestaShop 9**.
- Integration of **Google Consent Mode v2**.
- Integration of **Microsoft UET Consent Mode**.

### Improved
- Complete removal of any auto-accept behavior.
- Better compliance with Italian Garante Privacy requirements.
- Updated consent logic for modern tracking platforms.

---

## [1.5.3] – 2023-XX-XX
### Added
- Compatibility with **PrestaShop 8**.

---

## [1.5.0] – 2022-12-09
### Added
- Updated to comply with the Italian Data Protection Authority (Garante Privacy).
- Added **Reject** button.
- Added **Close (X)** button that assigns `displayCookieConsent = 'n'`.
- Added dedicated controller allowing users to **change cookie preferences** at any time.
- Improved consent handling logic.

---

## [1.0.0] – Initial Release
- First public release of Art Cookie Choices Pro.
