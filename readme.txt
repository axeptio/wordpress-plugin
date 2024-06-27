=== Axeptio - Cookie Banner - GDPR Consent & Compliance with a friendly touch ===
Contributors: Axeptio
Tags: Axeptio, GDPR, RGPD, Cookies, Consent, Privacy, eprivacy, consent, script, cmp, data, personnal, widget, googletagmanager, consentmanagement
Requires at least: 5.0
Tested up to: 6.5.5
Stable tag: 2.4
Requires PHP: 7.4
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Axeptio is the best solution to make your website GDPR compatible and make your visitors smile!

## <a href="https://www.axept.io" target="_blank">★ Visit Axeptio ★</a> ##

== Description ==

Transform your WordPress site's **privacy management** into a smooth and compliant experience with Axeptio. Our plugin, crafted for seamless integration with WordPress, offers a turnkey solution for adhering to privacy standards without compromising user experience.
With a unique and many times copied cookie consent widget, Axeptio will ensure you gather your users consent while properly informing them about their rights.
Very easy to install and configure, [Axeptio](https://www.axept.io) will be deployed on your site in just a few minutes.
Once the plugin is installed, you will need to configure it in the admin part of Wordpress. More customization and consent log are available on our dashboard.
With a large customization palette, you can chose what colors, logos and texts you want to display on your widget. Axeptio also supports several languages.

== Key Features ==

The plugin will download our SDK to display Axeptio on your website

* **Quick and Easy Installation:** Integrate Axeptio into your WordPress site in just a few clicks with our streamlined setup. Simply enter your client ID, and you're all set - the Axeptio code is automatically integrated.
* **Multilingual Compatibility:** Axeptio works seamlessly with WPML and PolyLang, ensuring privacy management is tailored to all your users, no matter their language.
* **Smart Extension Blocking:** Our advanced system of filters and hooks allows you to block extensions by default, ensuring full compliance and enhanced security.
* **Connection to Vendor Database:** Simplify extension blocking with direct access to our comprehensive database.
* **Automatic Updates:** Stay always up-to-date with the latest features and improvements effortlessly.
* **Advanced Customization:** Tailor the plugin's appearance to match your brand with customization options for colors, logo, and text directly from your Axeptio backoffice.
* **Dedicated Axeptio Support:** Access expert assistance for any questions or support needs.
* **Google Tag Manager Integration:** Easily integrate Axeptio with Google Tag Manager for optimized tag management.

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

Read all [our documentation](https://support.axeptio.eu/hc/en-gb)

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
[Add New tag](https://support.axeptio.eu/hc/en-gb/articles/7658814085137-How-to-add-a-new-Cookie-in-my-tags-list-)

= Customize my widget's aspect =

The idea is to customize the appearance of your widget so that it matches the design of your site perfectly.
All informations here : [Axeptio customization](https://support.axeptio.eu/hc/en-gb/articles/4402985038225-Customize-my-widget-s-aspect)

== Changelog ==

### 🎨 2.4 🎨 ###

**Reorganized Settings Management Space 🎨**
We did some spring cleaning! The settings management space has been redesigned and reorganized for a more intuitive and pleasant navigation experience. 🧹✨

**Project Version History 🕰️**
Keep track of all your project versions with history based on the project ID. You can now restore your settings with ease whenever necessary. 🔄🗂️

**Link to Documentation 📚**
No more searching around! A direct link to the plugin documentation is now available. Head over to Axeptio.io for all the info you need. 🔗📖

**Set a Cookie Domain for the Axeptio SDK 🍪**
For those who like to share, you can now set a cookie domain for the Axeptio SDK. Perfect for WordPress multisite configurations. 🌐🤝

** Use a Proxy for the Axeptio SDK 🕵️‍♂️**
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
