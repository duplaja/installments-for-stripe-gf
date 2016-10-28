=== Installments for Gravity Forms and Stripe ===
Contributors: duplaja
Donate link: https://www.wptechguides.com/donate/
Tags: Gravity Forms, GF, Stripe, installments, layaway, subscriptions
Requires at least: 4.0.1
Tested up to: 4.6.1
Stable tag: trunk
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Installments for Gravity Forms and Stripe enables you to set up subscription payments that autocancel after x times paid / user per feed.

== Description ==

**Note: You MUST have Gravity Forms and the Official Stripe Add-On installed for this plugin to work.** 

Installments for Gravity Forms and Stripe allows you to set up installment payments or limited length subscription payments. Stripe itself does not have the option to auto-cancel, so we will do this through the plugin here.

To do this, set up your Stripe feed like normal in Gravity Forms. Once your feed is created, you can optionally set a number of payments after which the subscription will be auto-canceled on the Installments settings page. To remove the auto-cancel condition from a Stripe feed, simply set the number of times to charge to 0 and then save your changes.

Use Cases

* You want a subscription rate to be valid for one year only, so you charge $25 / month and auto-expire after 12 payments. You could then choose to let them resubscribe at a discount (or markup).
* You have a more expensive offering, like a trip that costs $2000. You can offer  the option for your customer to pay it off at a rate of $400 / month, for 5 months. (using a $400 monthly subscription that auto-cancels after 5 payments)

Features

* Auto-cancels select Stripe subscription feeds on a per customer per feed basis after x times.
* Can be enabled for any number of subscription feeds.
* Sends an e-mail to the admin account detailing the feed name and the entry number of the customer once the total amount is payed and the account is cancelled.
* Can be deactivated safely without affecting existing feeds (they just will not auto-cancel anymore).

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/installments-for-stripe-gf` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Head over to the Installments for Stripe GF settings page, found on the Dashboard sidebar on the Forms submenu.

== Frequently Asked Questions ==

= What do I need for this plugin ro run? =

You need the Gravity Forms plugin as well as the official Gravity Forms Stripe plugin.

= How do I remove an auto-cancel setting from a feed? =

Just change the times to charge to 0, and then save the existing settings.

== Screenshots ==

1. Installments for Stripe Gravity Forms settings page / control panel.

== Dependencies and Liscencing ==

Depends on Gravity Forms, Gravity Forms Stripe Plugin, and Stripe payment systems

== Changelog ==

= 1.0 =
* Initial Plugin Release

== Upgrade Notice ==

= 1.0 =
* Initial Plugin Release
