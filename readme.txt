=== Axeptio - GDPR Cookie Consent & Compliance with a friendly touch ===
Contributors: axeptio
Tags: Axeptio, GDPR, RGPD, Cookies, Consent, Privacy, eprivacy, consent, script, cmp, data, personnal, widget, googletagmanager, consentmanagement
Requires at least: 5.0
Tested up to: 6.1.1
Stable tag: 2.0.1
Requires PHP: 7.4
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Axeptio is the best solution to make your website GDPR compatible and make your visitors smile!

## <a href="https://www.axeptio.eu/" target="_blank">★ Visit Axeptio ★</a> ##

== Description ==

With a unique and many times copied cookie consent widget, Axeptio will ensure you gather your users consent while properly informing them about their rights.
Very easy to install and configure, [Axeptio](https://www.axeptio.eu) will be deployed on your site in just a few minutes.
Once the plugin is installed, you will need to configure it in the admin part of Wordpress. More customization and consent log are available on our dashboard.
With a large customization palette, you can chose what colors, logos and texts you want to display on your widget. Axeptio also supports several languages.

== Key Features ==

The plugin will download our SDK to display Axeptio on your website
Configuration happens in the Wordpress admin part where you can input your client ID and link your website to your Axeptio account
User consent and customization happens on our website directly
Available in several languages
NOTE: JUST INSTALLING THIS PLUG-IN DOES NOT MAKE YOUR SITE GDPR COMPLIANT. EACH WEBSITE USES DIFFERENT COOKIES, YOU MUST ENSURE THAT THE REQUIRED CONFIGURATION IS IN PLACE.
Please follow our documentation to configure and test your settings for Axeptio and Wordpress.

== Screenshots ==

1. Integration sample on your website
2. Find and install Axeptio plugin
3. Setup your project ID
4. Axeptio Back Office

== Installation ==

1. Upload the entire `Axeptio Plugin` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Look at your admin bar and enjoy using the new links there... (Axeptio SDK)
4. Go and manage your widget by adding your project ID...

== Documentation ==

Read all [our documentation](https://support.axeptio.eu/hc/en-gb)

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
