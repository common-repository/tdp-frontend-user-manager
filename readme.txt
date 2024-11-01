=== TDP - Frontend User Manager ===
Contributors: alessandro.tesoro
Tags: frontend user manager, custom field registration, custom redirects, custom registration, custom registration form, custom registration page, front-end login, front-end register, front-end registration, front-end user registration, registration page, user custom fields, user email, user login, user registration form
Requires at least: 3.8
Tested up to: 3.9
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The TDP Frontend User Manager plugin allows you to easily add frontend user registration login and password recovery forms to your WordPress website.

== Description ==
The TDP - Frontend User Manager plugin allows you to easily add frontend user registration login and password recovery forms to your WordPress website. The plugin creates an easy to use shortcode that you can use to display up to 3 different forms on your pages/posts or anywhere else where shortcodes are supported into your theme. Alternatively if you are a developer you can also include the forms through PHP in theme template files.

= Ajax Powered Forms =
Each form is powered by ajax, your users can sign up/login/recover password without the page refreshing, making the process fast, and unobtrusive to the browsing experience. The form also comes with plenty of css selectors that allow you to customize the layout of each form element.

**Note:** each form comes with no css styling, if your theme is well coded, it will automatically add basic styling to the form buttons and fields. Otherwise you can use css to modify the look of the form.

= Features List =

1. Ajax powered forms
1. No setup required
1. Plenty of css selectors to customize the layout
1. No additional css files are added to your website.
1. Lightweight plugin, only 1 additional .js file is added to your website, required for ajax processing.
1. Easy to extend registration form with ability to add additional field through wp hooks.
1. Wide range of actions and filters are provided to customize each section/feature of the forms.
1. One single shortcode to manage each form.

= Usage =

1. Create or edit a new/existing page or post and add the shortcode `[tdp_fum_form]`
1. By default the shortcode will display the login form to non logged in users.
1. If you wish to display a registration form, use the following shortcode `[tdp_fum_form form="register"]`
1. If you wish to display a password recovery form, use the following shortcode `[tdp_fum_form form="password"]`

= What's coming next? =
1. New features!
1. Full Frontend Users management
1. Frontend User Profiles Editing
1. Built-in custom registration fields builder
1. Re-Captcha on registration form/login form
1. Ability to customize password recovery and registration email text.
1. Ability to customize form redirects
1. And much more!

> **News And Updates**
> If you want to instantly get news and notifications of updates for this plugin, feel free to [follow me on twitter](https://twitter.com/themesdepot) or [signup to the free (no-spam) newsletter](http://eepurl.com/wVInb)
>
> ** Want More? **
> [Free WordPress plugins](http://profiles.wordpress.org/alessandrotesoro/) | [Premium WordPress Themes](http://themeforest.net/user/ThemesDepot/portfolio) | [Follow Me On Twitter](https://twitter.com/themesdepot) | [Signup To The Free (no-spam) newsletter](http://eepurl.com/wVInb)

== Installation ==
1. Upload "tdp-frontend-user-manager" folder to the "/wp-content/plugins/" directory. 
1. Activate the plugin through the "Plugins" menu in WordPress.
1. Add the **[tdp_fum_form]** shortcode to any page/post or widget(if your theme supports shortcodes in widgets) to display a form.
1. Modify the shortcode "form" parameter to display a different form options available are `[tdp_fum_form form="register"]` or `[tdp_fum_form form="password"]`

== Frequently Asked Questions ==
= How to display a login form =
Use the following shortcode to display a login form `[tdp_fum_form]`

= How to display a registration form =
Use the following shortcode to display a registration form `[tdp_fum_form form="register"]`

= How to display a password recovery form =
Use the following shortcode to display a password recovery form `[tdp_fum_form form="password"]`

= How can i customize the registration form fields ? =
If you are a developer have a look at the function **tdp_fum_process_registration** into the plugin file and read the comments / filters and actions for more information.

More specific information will be available very soon.

== Screenshots ==
No Screens available, it's just a few forms with no settigns panel.

== Changelog ==
= 1.0.0 =
* Initial release.