# Art Cookie Choices Pro

ArtCookieChoicesPro is a free PrestaShop module developed by **Tecnoacquisti.com® / Arte e Informatica**, designed to manage a fully GDPR-compliant cookie banner, aligned with the latest guidelines of the Italian Data Protection Authority (Garante Privacy).

The module is lightweight, fast, and does not rely on external libraries. It includes full integration with **Google Consent Mode v2** and **Microsoft UET Consent Mode**, ensuring compatibility with modern tracking requirements.

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
![Built for PrestaShop](https://img.shields.io/badge/Built%20for-PrestaShop-DF0067?logo=prestashop&logoColor=white)

---

## 🚀 Features

- GDPR-compliant cookie banner with **Accept**, **Reject**, and **Close (X)** actions.
- No auto-accept behavior under any circumstance.
- Proper handling of the `displayCookieConsent` cookie based on user choices.
- Granular cookie preferences stored in `displayCookieConsentPreferences`.
- Link/controller allowing users to **reopen cookie preferences** at any time.
- Fully customizable text and behavior.
- Banner position options: **top**, **bottom**, and **center**.
- Configurable optional cookie categories:
  - Functional cookies
  - Analytics cookies
  - Performance cookies
  - Advertising cookies
  - Other cookies
- Support for:
  - **Google Consent Mode v2**
  - **Microsoft UET Consent Mode**
- HTML and CSS fully overridable by theme.
- No core modifications and full compatibility with PrestaShop caching systems.

---

## 🧩 PrestaShop Compatibility

| PrestaShop Version | Supported |
|--------------------|-----------|
| 1.7.x              | ✔️ |
| 8.x                | ✔️ |
| 9.x                | ✔️ |
| 1.6.x              | ❌ Removed |

---

## 📦 Installation

1. Download the latest release from the GitHub repository.
2. Go to **Back Office → Modules → Upload a Module** and install the ZIP package.
3. Configure banner text, buttons, and consent behavior.
4. (Optional) Configure tracking scripts using Google Consent Mode v2 or Microsoft UET.

---

## 🔧 Configuration Options

- Customizable banner text.
- Translatable button labels.
- Custom link to allow users to review/change preferences.
- Optional cookie categories can be enabled or disabled from the module configuration.
- Banner position can be set to top, bottom, or center.
- Consent Mode signals for:
  - `ad_storage`
  - `analytics_storage`
  - `ad_user_data`
  - `ad_personalization`
  - `functionality_storage`
  - `personalization_storage`
  - `security_storage`
- Microsoft UET `ad_storage` is mapped to the advertising category.

---

## 🔐 Privacy & Legal Compliance

- Designed according to GDPR and Italian Garante Privacy guidelines (2021 memo).
- Reflects Google’s Consent Mode v2 obligations starting in 2024.
- Ensures no tracking scripts are loaded unless the user provides explicit consent.

---


---

## 🧩 Google Consent Mode v2 Integration

The module provides default Consent Mode v2 signals and updates them after user choices.
Advertising consent controls `ad_storage`, `ad_user_data`, `ad_personalization`, and Microsoft UET `ad_storage`; analytics and performance consent control `analytics_storage`; functional consent controls `functionality_storage` and `personalization_storage`; necessary cookies keep `security_storage` granted.

---

## 📄 License

This module is released under the MIT License.
You may freely use, modify, and distribute the software—even for commercial purposes—as long as the original copyright notice and license text are preserved.

See the included LICENSE file for full terms.

---

## 🤝 Support & Contributions

For help, bug reports or feature requests:

👉 **GitHub Issues:** https://github.com/ArteInfoRM/artcokiechoicespro/issues  
👉 **Official website:** https://www.tecnoacquisti.com/

Contributions and pull requests are welcome.

---

## 🏷 Author

**Tecnoacquisti.com® – Arte e Informatica**  
Experts in e-commerce development, PrestaShop modules and digital compliance solutions.
