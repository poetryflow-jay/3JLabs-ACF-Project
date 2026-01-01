=== Advanced Members for ACF ===
Tags: acf, advanced custom fields, members, registration, account
Stable tag: 1.2.4
Requires at least: 5.8
Tested up to: 6.8
Requires PHP: 7.0
Contributors: danbilabs
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A Lightweight & Powerful Membership Plugin for ACF Users. Seamlessly Use ACF Field Groups as Membership Forms

== Description ==

Advanced Members for ACF is a a lightweight, powerful membership plugin with a modern interface, designed for Advanced Custom Fields (ACF) users.


**No Complex Integration. This is ACF Add-on!**

Advanced Members is different from traditional membership plugins. Instead of integration with ACF, It use ACF Field Groups as a Membership Forms. You can seamlessly operate building user meta and membership forms together.

**All Membership Forms**

Advance Members provide various membership forms Registration Form Account Form Password Reset Delete Account Change Password

**Seamlessly Use ACF Field Groups as Membership Forms**

Advanced Members is fully featured Membership plugin. Simplify your membership site development with Advanced Members.

- Local Avatar
- Disable Admin Bar
- Content Restriction
- Menu Visibility
- Redirects
- Automated Emails
- Customizable Email Templates
- Google reCAPTCHA

**Work Great Lost of Plugins**

Advanced Members does not require a large amount of code as an ACF add-on. If you already use ACF, Advanced Members will be the fastest membership plugin.

**Lightweight & Super Fast!**

Advanced Members does not require a large amount of code as an ACF add-on. You can check this plugin's performance on PluginsTest or WP Hive. If you already use ACF, Advanced Members will be the fastest membership plugin.

**Future Development**

We're committed to continually improving Advanced Members for ACF. Stay tuned for updates and new features to further enhance your membership management experience.

**Useful Links**

[Official Site](https://advanced-members.com) | [Documentation](https://advanced-members.com/doc/getting-started/) | [Support Forum](https://wordpress.org/support/plugin/advanced-members/)


== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/advanced-members` directory, or install the plugin through the WordPress plugins screen directly.
2. Make sure ACF v6.2 is installed and activated.
3. Activate the plugin through the 'Plugins' screen in WordPress.

== Frequently Asked Questions ==

= Q: Does this plugin only work with ACF v6.2 or later? =

**Yes.** Versions lower 6.2 of ACF are not supported.

= Q: Can this plugin function without ACF? =

**No.** Advanced Members is an ACF extension. You must install and activate ACF or ACF Pro. If ACF is not activated, Advanced Members shows an installation and activation request Admin Notice.

= Q: Does this plugin require ACF Pro version? =

**No.** Advanced Members works great with both ACF free and Pro. However, if you use ACF Pro, you can utilize pro fields like Repeater Field and Flexible Content Field for membership forms.

= Q: Is this plugin compatible with other ACF add-on fields? =

**Yes and No.** Advanced Members works great with most ACF Add-on fields, but we haven't tested all of them. If you encounter any issues, feel free to submit a support ticket on WordPress.org.

= Q: Does this plugin support SCF? =

**Yes or No.** Most features will likely work fine. However, Advanced Custom Fields officially does not support SCF(Secure Custom Fields) and has no plans to support it in the future.

= Q: Will this plugin slow down my website? =

**Absolutely not.** Advanced Custom Fields doesn't require extensive coding as an add-on. You can check this plugin's performance on [PluginsTest](https://plugintests.com/plugins/wporg/advanced-members/latest) or [WP Hive](https://wphive.com/plugins/advanced-members/).

== Screenshots ==

1. Edit Membership Forms with ACF
2. Extra Fields for Membership
3. Emails
4. Redirects
5. Menu Visibility
6. Modulized feature control

== Changelog ==

= 1.2.4 =
- Fix: Support custom login redirect by roles

= 1.2.3 =
- Fix: Bypass avatar defaults

= 1.2.2 =
- Fix: Maintain settings when modules are disabled
- Mod: Updated plugins descriptions on readme
- Mod: Conditional menu visibility settings on Menus screen
- Fix: Fix: Prevent password fields from being sanitized by kses
- Mod: Added password confirmation label field setting
- Fix: Form title not appearing

= 1.2.1 =
- Mod: Lightweight reCAPTCHA library
- New: `bypass_empty` password field setting for developers

= 1.2.0 =
- New: Added Local Avatar module
- New: Added reCAPTCHA module
- New: shadcn/ui style

= 1.1.0 =
- New: Form edit link on block
- Mod: Apply 'Hide Label' setting to all ACF fields

= 1.0.0 =
- New: Content Restriction Module
- Fix: Redirection module error

= 0.9.15
- Fix: Password Checker JS not working properly

= 0.9.14 = 
- New: Email setting for override WP core user password changed email.
- New: Settings sanitizer
- New: Show list of pages memers form is inserted(beta)

= 0.9.12 =
- New: Allow multiple forms in one page(beta)
- Fix: Email body content returns single line text
- Mod: Form block select style
- New: force follow redirect url of form argument redirect value
- Mod: Changed assets directory structure
- Mod: Changed admin framework to follow ACF Admin internal post types
- Fix: Misc bug fixes and text modifications

= 0.9.7 =
- New: Supports sorting field groups in form
- Mod: UI of edit form screen changed to 2 columns
- New: Can filter field groups for AMem forms
- Mod: Changed core page label(Login, Registration)
- Mod: Added placeholder text to account deletion text and label
- New: Added form type name to form CSS classes

= 0.9.6 =
- Fix: Rmoved debug code
- Mod: Changed $post to get_the_ID() in amem_is_core_page

= 0.9.5 =
- Mod: Renamed page state title
- New: Warning message for non default account form when render form
- Mod: Improved form settings fields
- Mod: Merged predefined forms and general forms in block

= 0.9.4 =
- Fix: not follow redirect_to query string
- Fix: login button kses stripped
- Fix: uppercase constant names, lowercase function names
- New: Support email field on login form

= 0.9.3 =
- Intitial Release


== Upgrade Notice ==

None
