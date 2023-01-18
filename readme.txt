=== WooCommerce Domain Names ===
Contributors: magicoli69
Donate link: https://paypal.me/magicoli
Tags: domain, dns, registrar, domain names, domain registration, woocommerce, openprovider
Requires at least: 4.5
Tested up to: 6.1.1
Requires PHP: 5.6
Stable tag: 0.1.6
License: AGPLv3 or later
License URI: https://www.gnu.org/licenses/agpl-3.0.html

WooCommerce products and tools for domain names resellers.

== Description ==

This plugin is designed for domain names resellers. Currently, it only provides the sale part, actual registration has to be done manually.

* Requires [WooCommerce](https://wordpress.org/plugins/woocommerce/).
* Requires [Openprovider](https://openprovider.com/) account.
* Compatible with [WooCommerce Subscriptions](https://woocommerce.com/products/woocommerce-subscriptions/).

= Features =

* "Domain name" product type
* "Domain name" field on product page
* Availability and price check, using Openprovider API
* Price adjustment rules:

  - optional margin to add to gross price
  - optional rounding (e.g. only prices like 10, 15, 20...)
  - optional minimum price

= Status =

This is a work in progress. I develop this plugin for my own needs, because I keep forgetting to bill domain renewals to my customers. However I have the feeling I'm not alone on this path.

The goal is to implement an easy bridge between WooCommerce and Openprovider, although the architecture should allow integration of other registrars and/or commerce platforms.

**Disclaimer**: This is is not (and will probably never be) a full domain management platform. There are already very complete solutions for that.

The current to-do list include:

* Actual domain name registration via registrar API
* Actual domain renewal after subscription renewal payment
* Migrate existing domains from former billing solution
* Reminders before domain expiration
* Basic DNS options
* Handle multiple years registration (and adjust price if applicable)
* Handle renewal price different than registration price if applicable
* Handle promo prices if applicable
* Allow domain transfers


== Installation ==

1. Install and activate [WooCommerce](https://wordpress.org/plugins/woocommerce/) plugin. Optionally install WooCommerce Subscriptions (recommended).
2. Download, install and activate the [latest stable release](https://github.com/magicoli/woocommerce-domain-names/releases/latest).
3. In WooCommerce settings > Domain names, fill Openprovider credentials and save. Setup margin, rounding and minimum price on the same page.
4. Create a product,
  * check "virtual" and "Domain name" in product data options
  * set the base price to 0 or more (will be added to actual domain registration price).

== Frequently Asked Questions ==

= Why is FAQ section empty? =

Because nobody asked anything yet.

== Changelog ==

= 0.1.6 =

= 0.1.5 =
* added "From" before price
* added input-text class to domain name field
* fix fatal error Unknown format specifier “)” fatal error
* fix fatal error Couldn't fetch DOMText
* fix fatal error with recent php versions
* fix price not displayed
* fix empty error reason for active domains
* fix options not included in cart total
* don't try to validate domain if $passed value is already false
* updated external libraries

= 0.1.3.1 =
* renamed as woocommerce-domain-names (hopefully the last rename, to match my other wc plugins naming)
* updated assets

= 0.1.2 =
* added package updater

= 0.1.1 =
* added fr, nl and de translations
* renamed as "WooCommerce Domain Names", which reflects better the actual features

= 0.1.0 =

* "Domain name" product type
* "Domain name" field on product page
* Availability and price check, using Openprovider API
* optional margin to add to gross price
* optional rounding
* optional minimum price
* Openprovider API
* WooCommerce integration
