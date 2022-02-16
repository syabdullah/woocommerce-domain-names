# WooCommerce Domain Registration
* Contributors: magicoli69
* Donate link: https://paypal.me/magicoli
* Tags: domain, dns, registrar, domain names, domain registration, woocommerce, openprovider
* Requires at least: 4.5
* Tested up to: 5.9
* Requires PHP: 5.6
* Stable tag: 0.1.0
* License: AGPLv3 or later
* License URI: https://www.gnu.org/licenses/agpl-3.0.html

Domain registration for resellers using WooCommerce and Openprovider registrar.

## Description

This plugin is designed for domain resellers. Currently, it only provides the sale part, actual registration has to be done manually.

* Requires [WooCommerce](https://wordpress.org/plugins/woocommerce/) and an [Openprovider](https://openprovider.com/) account.
* Compatible with [WooCommerce Subscriptions](https://woocommerce.com/products/woocommerce-subscriptions/).

### Features

* "Domain name" product type
* "Domain name" field on product page
* Availability and price check, using Openprovider API
* Price adjustment rules:

  - optional margin to add to gross price
  - optional rounding (e.g. only prices like 10, 15, 20...)
  - optional minimum price

### Status

This is a work in progress. I develop this plugin for my own needs, because I keep forgetting to bill domain renewals to my customers. However I have the feeling I'm not alone on this path.

The goal is to implement an easy bridge between WooCommerce and Openprovider, although the architecture should allow integration of other registrars and/or commerce platforms.

**Disclaimer**: This is is not (and will probably never be) a full domain management platform. There are already very complete solutions for that.

The current to-do list include:

* Actual domain registration via registrar API
* Actual domain renewal after subscription renewal payment
* Migrate existing domains from former billing solution
* Reminders before domain expiration
* Basic DNS options
* Handle multiple years registration (and adjust price if applicable)
* Handle renewal price different than registration price if applicable
* Handle promo prices if applicable
* Allow domain transfers


## Frequently Asked Questions

### Why is FAQ section empty?

Because nobody asked anything yet.

