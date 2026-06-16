=== Axeptio - Cookie Banner - GDPR Consent & Compliance with a friendly touch ===
Contributors: Axeptio
Tags: Axeptio, GDPR, RGPD, Cookies, Consent
Requires at least: 5.0
Tested up to: 7.0
Stable tag: 2.6.3
Requires PHP: 7.4
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Axeptio | GDPR Cookie Banner - An Immersive Compliance Experience

## <a href="https://www.axept.io" target="_blank">★ Visit Axeptio ★</a> ##

== Description ==

Axeptio is a deeply customizable Consent Banner that makes your site compliant with GDPR, TCF v2.2, Google Consent Mode v2, and more —while providing users with a branded and premium cookie consent experience

**Axeptio is a powerful Consent Management Platform (CMP) that keeps your website compliant with global privacy laws.** From **GDPR, ePrivacy,** and **TCF v2.2** in Europe, to **Google Consent Mode v2, PIPEDA** and **Law 25** in Canada, the **CCPA** in the U.S. and even more.

[Axeptio](https://www.axept.io/) ensures your business meets every standard. By managing cookie consent in a clear and customizable way, it helps you build trust while staying fully compliant. Join 80.000 websites that trust Axeptio worldwide. 4.5/5 TrustPilot score.

== Key Features ==

[Instant Setup with Cookie Scanner](https://www.axept.io/shake-cookie-checker)

Automatically scan your site and generate a ready-to-use consent banner, complete with detected cookies and pre-configured design.

[100% Customizable to Your Brand](https://www.axept.io/cookie-banner)

Make the banner truly yours:
- Edit all texts to fit your tone of voice
- Adjust colors, themes, and layouts
- Add illustrations, logos, or Axeptio’s cookie visuals
- Embed images or videos for a more engaging experience

**Track and Understand Consent Behavior**
Gain full visibility with a real-time dashboard and connect with your analytics stack, including GA4, Matomo, Piano, and more.

**Optimization Tools That Drive Results**

Boost performance with advanced controls: A/B testing, consent walls, and fine-tuned timing or shadow effects.

[Efficiency by Design](https://www.axept.io/cookie-banner)

- Mobile ready and responsive across devices
- 1,500+ vendors pre-integrated with logos, icons, and policy links
- Lightweight CMP optimized for Core Web Vitals and SEO performance
- Audit-ready with instant consent logs and a clear compliance trail

**Autotranslation**

- Automatically detect the visitor’s browser language and IP, then show the banner in their preferred language (with support for 30+ languages).

Axeptio is free up to 200 unique visitors / month. Scan your website now on [www.axept.io/shake-cookie-checker](http://www.axept.io/shake-cookie-checker)

How to install Axeptio wordpress plugin

## Tutorial Video

https://youtu.be/bB8PjlhLxso

Configuration happens in the Wordpress admin part where you can input your client ID and link your website to your Axeptio account
User consent and customization happens on our website directly
Available in several languages

NOTE: JUST INSTALLING THIS PLUG-IN DOES NOT MAKE YOUR SITE GDPR COMPLIANT. EACH WEBSITE USES DIFFERENT COOKIES, YOU MUST ENSURE THAT THE REQUIRED CONFIGURATION IS IN PLACE.
Please follow our documentation to configure and test your settings for Axeptio and Wordpress.

== Screenshots ==

1. Axeptio widget Presentation
2. Integration sample on your website
3. Setup your project ID
4. Select your plugin connected with the vendorDB
5. Axeptio Back Office for color customization

== Installation ==

1. Upload the entire `Axeptio Plugin` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Look at your admin bar and enjoy using the new links there... (Axeptio SDK)
4. Go and manage your widget by adding your project ID...

== Documentation ==

Read all [our documentation](https://support.axeptio.eu/en)

== Why Choose Axeptio for WordPress? ==

Axeptio is not just a plugin, it's a complete solution that respects and strengthens your users' trust. Whether you're a blogger, an e-commerce site, or a business, Axeptio is the ideal choice for privacy management that's simple, effective, and compliant with current standards.

== FAQ ==

= Is Axeptio completely free ? =

Axeptio is free to use and always will be up to 200 visitors/month. If your website receives more visits, you will need to select a paid plan on our website.

= How to add a new Cookie in my tags list ? =

1. Log into your account and select your project
2. Select the "Here are our cookies" screen or create it if needed by clicking "New."
3. Click on "Add a new cookie" > our cookie library appears
4. Search for your cookie by entering its name
[Add New tag](https://support.axeptio.eu/en/articles/274092-how-to-add-a-new-cookie-in-my-tags-list)

= Customize my widget's aspect =

The idea is to customize the appearance of your widget so that it matches the design of your site perfectly.
All informations here : [Axeptio customization](https://support.axeptio.eu/en/articles/273978-3-widget-customisation)

== Changelog ==

### ✨ 2.6.2 ✨ ###

**Enhancements and Fixes:**

- 🔗 **Enhanced Signup Experience:** UTM parameters are now automatically added to signup links for better tracking and attribution.
- 🌐 **Improved French Translations:** Enhanced GTM events translation consistency in the French language pack for better user experience.
- 🛠️ **PHP 8.0+ Compatibility:** Fixed null pointer errors for improved compatibility with PHP 8.0 and newer versions.
- 📦 **Dependency Updates:** Updated package dependencies and resolved security vulnerabilities to ensure optimal performance and security.
- 🍪 **Enhanced WP Consent API Integration:** The 5 WP Consent API categories (functional, preferences, statistics, statistics-anonymous, marketing) are now displayed as virtual vendors in the Axeptio widget when the WP Consent API plugin is active.
- 🐛 **WP Consent API Bug Fix:** Fixed an initialization bug that prevented proper consent synchronization with WP Consent API.
- ⚡ **Direct Consent Updates:** WP Consent API consent updates now work independently of Google Consent Mode for improved reliability.

### 🔒 2.6.1 🔒 ###

- 🐛 Fix fatal error "=>" (T_DOUBLE_ARROW)

### ⚡️ 2.6.0 ⚡️ ###

This new version introduces several major improvements and fixes:

- ✅ **Compatibility with WP Consent API**
  Native integration with the [WP Consent API](https://wordpress.org/plugins/wp-consent-api/) plugin.
- 🔐 **Security fixes**
  Enhanced overall plugin security.
- ⚙️ **Customizable shortcode blocking screen**
  Customize the appearance and content of the screens that block shortcodes pending consent.
- 🔁 **Optional page reload on consent**
  Ability to trigger an automatic page reload after a blocking screen is validated (especially useful for HubSpot forms and other third-party services).
- 🌍 **Multilingual support for the Axeptio widget screen**
  Dynamic display of consent texts in the site's language.

### 📃 2.5.9.1 📃 ###

**Fix doc links**

### 🔄 2.5.9 🔄 ###

**Temporary Removal of WordPress Hook Caching System**
We have identified that the WordPress hook caching system could cause issues on certain site configurations.
To ensure optimal compatibility for all users, we have temporarily disabled this feature while we work on a more robust solution.

### 🚀 2.5.8 🚀 ###

**Fixes and Improvements:**
- **Critical Bug Fix:** Resolved an issue that prevented the proper loading of the website on specific WordPress site configurations.
- **Performance Enhancements:** Optimized rendering processes to reduce server load.
- **Improved Compatibility:** Updated to ensure better compatibility with the latest WordPress version and modern PHP environments.

We sincerely thank our users for their valuable feedback. We deeply apologize for any inconvenience caused to clients affected by this bug. We are committed to strengthening our testing processes to prevent such issues in the future.

### 🚀 2.5.7 🚀 ###

- **Code Refactoring:** Improved code readability and maintainability with better function organization and reduced duplication.
- **Performance Optimization:** Ensured efficient cache handling with minimal performance impact.

### ⚡️ 2.5.6 ⚡️ ###

**Shutdown the hook parser if no extension is filtered in the configuration 💤**

### 🔒 2.5.5 🔒 ###

**Security Update:**
- Removed temporarily local cookie handling to eliminate potential risks.

### 🔒 2.5.4 🔒 ###

**Security Enhancement:**
- Fixed a Local File Inclusion vulnerability
- Improved SDK proxy security with better file handling and encoding

### 🐞 2.5.3 🐞 ###

Hotfix for Alpine library load

### 🌟 2.5.2 🌟 ###

- **Advanced Configuration**: Updated `postConsentURL` field with a new label and a link to documentation for server-side configurations.
- **GTM Event Management**: Added a `triggerGTMEvents` parameter for precise control of events sent to the dataLayer. Choose between: all, none, or updates only.
- **Consent Mode V2**: Added 3 new consent purposes for enhanced flexibility:
  - Functionality storage
  - Personalization storage
  - Security storage
- **Community Contributions**: Resolved Alpine.js conflicts with an integrated PR. Thanks to [@sayedulsayem](https://github.com/sayedulsayem)!


### 🐞 2.5.1 🐞 ###

Fix cache data for the plugin recommended settings

Hotfix for undefined variable

### ⚡️ 2.5 ⚡️ ###

**Error Logging Tool Taking a Power Nap 💤**

In this release, we've temporarily disabled our error logging tool. But don't worry! It's just taking a break to come back stronger, faster, and smarter. 💪

### 🎉 2.4.9 🎉 ###

### 🖼️ 2.4.9 🖼️ ###

- **Custom Illustrations:** Upload your own image to the widget screen and make it uniquely yours. Show off your brand's personality with style! 🌟
- **Background Image Control:** Want a minimalist look? You can now disable the background image of the screen. Clean and simple! 🧼

Make your Axeptio widget truly your own with these exciting new customization options. Enjoy a smoother and more personalized user experience. Thank you for being awesome and sticking with us on this journey! 🚀

Keep creating, keep smiling, and keep complying! 😊

### 🐞 2.4.8 🐞 ###

Fix when priority is not set as integer (it's bad but not for all plugins)

### 🐞 2.4.7 🐞 ###

Hotfix for undefined variable

### 🚀 2.4.6 🚀 ###

**Improved Hook Detection Caching System**

We have greatly enhanced our caching system for hook detection, significantly boosting performance, especially for sites that use a large number of hooks.

### 📃 2.4.5 📃 ###

**Removed internal log**

### 🐞 2.4.4 🐞 ###

**Bugfix for PHP 7.4**

### 🛠️ 2.4.3 🛠️ ###

**Temporary Removal of Caching Feature 🔄**
We've identified an issue with our new caching system that might have affected performance for some users. As we work on a more robust solution, we have temporarily removed this feature to ensure the best experience for all our users.

- **Apologies for Any Inconvenience 🙏**
We're sorry for any trouble this might have caused. We're dedicated to getting it right, and appreciate your understanding as we make these adjustments.

### 🌟 2.4.2 🌟 ###

**Enhanced Plugin Hook Detection 🕵️‍♂️**
Discover the source of plugin hooks with our improved detection system. Faster, smarter, and quieter than ever—enjoy streamlined debugging without the noise!

- **Quick and Quiet Error Handling 🔇**
We've made error handling whisper-quiet. Bugs are logged without interrupting your workflow, keeping things smooth and serene.

- **Efficient Search with Smart Caching 🚀**
Our new caching system speeds up the search process, so you spend less time waiting and more time creating.

### 🐞 2.4.1 🐞 ###

- **Fixed a bug related to Axeptio cookie preferences**
Oops! One of our new features was a bit too eager to launch and introduced a bug in the Axeptio cookie preferences. We've now fixed this issue so everything works perfectly. 🚀🔧

Thank you for your patience and understanding. Enjoy an even smoother experience with this update! 🎉

### 🎨 2.4 🎨 ###

**Reorganized Settings Management Space 🎨**
We did some spring cleaning! The settings management space has been redesigned and reorganized for a more intuitive and pleasant navigation experience. 🧹✨

**Project Version History 🕰️**
Keep track of all your project versions with history based on the project ID. You can now restore your settings with ease whenever necessary. 🔄🗂️

**Link to Documentation 📚**
No more searching around! A direct link to the plugin documentation is now available. Head over to Axeptio.io for all the info you need. 🔗📖

**Set a Cookie Domain for the Axeptio SDK 🍪**
For those who like to share, you can now set a cookie domain for the Axeptio SDK. Perfect for WordPress multisite configurations. 🌐🤝

**Use a Proxy for the Axeptio SDK 🕵️‍♂️**
Load the Axeptio SDK from your WordPress site domain using a proxy. 🛡️🔍

### 🙏 2.3.32 🙏 ###

🐛 **PHP Dependency Bug**

- We tracked and fixed a tricky bug, born from a dependency less reliable than expected. It was an epic battle between humans and code. The error reminds us that even in the age of AI, humans still make a difference.

### 🚀 2.3.31 🚀 ###

🔧 **Improvements:**
- **New Parameter for Google Consent Mode:** Integration of a new parameter in the declaration of Google Consent Mode.
- **Harmony with Composer:** Resolved conflicts caused by extensions using divergent versions of Composer. Peaceful coexistence is our new creed. The Axeptio ecosystem adapts and ensures compatibility across versions.
- **Database Query Optimization:** We fine-tuned our query handling to avoid redundant calls to the database. Efficiency and performance are our guiding principles, ensuring an even smoother user experience.

###🌈 2.3.3 🌈###

🔍 **Google Consent Mode V2:**
– **Welcome, Google Consent Mode V2! :** At the forefront of GDPR compliance! Get ready to surf the GDPR wave with style and confidence. Axeptio and Google Consent Mode V2: the dynamic duo for flawless compliance. Before March 6, 2024, we're already prepared. Who does it better?

### 2.3.2 && 2.3.21 ###

- Fix error when remote networking issue

### 2.3.1 ###

- Fix error on primary key

###🌟 2.3 🌟###

🔧 **Fixes :**
- **Dynamic Mode Sorted :** The SDK's dynamic mode is now smoother than ever. No more hiccups!
- **Configuration Listing on Hold :** If WPML or Polylang are napping, we pause the language-based SDK config listings.
- **Hook Manager on Break :** No active Axeptio SDK? The Hook Manager is taking a well-deserved break.

🚀 **Improvements :**
- **Farewell, Indecisive Algorithm :** We're waving goodbye to the cookie extension suggestion algorithm. Hello clarity with the vendor db integration!
- **BFFs with WP Rocket :** We're now besties with WP Rocket's page cache. Talk about speed and efficiency!
- **DB Retrieval Optimized :** Fetching plugin configurations is now as fast as a cheetah!

### 2.2 ###

Added multilingual support

### 2.1.1 ###

Fix when hook or shortcode is set to "none" inb the extension manager.

### 2.1 ###

1. Added a new section in the settings for customize the text of the Axeptio widget.
2. Fix some bug related to hook that doesn't have to be overridden.

### 2.0.5 ~ 2.0.9 ###

Fix for bug report

### 2.0.4 ###

Fixed default step for vendors

### 2.0.3 ###

1. Added error reporting
2. Fix bugs on migration script

### 2.0.2 ###

Fix default Axeptio step for WordPress plugin library

### 2.0.1 ###

Updated the way of loading the sdk in order to be compatible with WordPress.com

### 2.0.0 ###

The 2.0 version of the Axeptio WordPress Plugin brings significant improvements and new features to help you manage cookies on your website more effectively. Here is a detailed list of changes in this new version:

1. **New Interface for Extension Management:** The plugin now comes with a new interface that allows you to manage installed extensions and precisely define the blocking of certain functionalities that might use cookies. This can be done through WordPress hooks and shortcodes.
2. **Introduction of Cookie Analysis Algorithm:** The plugin now provides an algorithm that analyses the likelihood of an extension having third-party cookies. This gives you a better understanding of potential privacy issues related to each extension.
3. **Access to Axeptio Recommended Configurations:** The plugin now allows you to access configurations recommended by Axeptio. Currently, there are about 15, but this will be expanded in the future.
4. **Shortcode Placeholder for Blocked Content:** We've added a placeholder for shortcodes to indicate when content is blocked. Users can unblock the content by accepting the cookie through a button, which opens the Axeptio widget to permit or deny the display of content.
5. **Refactoring of Main Extension Configuration Management:** We've refactored the way we handle the main configuration of the extension, making it more robust and easier to manage.
6. **Additional Language Support:** The plugin is now available in four more languages: German, Spanish, Italian, and Dutch. This will help in reaching a wider audience and serving users in their native languages.

In addition to these features, we have also fixed an issue related to the management of the Axeptio configuration version. Now, the plugin correctly manages different versions of the Axeptio configuration.

As always, we look forward to your feedback and appreciate your continued support for the Axeptio WordPress Plugin.

### 1.1.0 ###

- New UI
- Code refactoring
- New SDK loading method without additional http call (inline code)

### 1.0.0 ###

First release
