# WooCommerce Domain Names (dev)

![Stable 0.1.0](https://badgen.net/badge/Stable/0.1.0/00aa00)
![WordPress 4.5 - 6.0.2](https://badgen.net/badge/WordPress/4.5%20-%206.0.2/3858e9)
![Requires PHP 5.6](https://badgen.net/badge/PHP/5.6/7884bf)
![License AGPLv3 or later](https://badgen.net/badge/License/AGPLv3%20or%20later/552b55)

WooCommerce domain names products and tools for Openprovider.

## Description

This plugin is designed for domain names resellers. Currently, it only provides the sale part, actual registration has to be done manually.

- Requires [WooCommerce](https://wordpress.org/plugins/woocommerce/).
- Requires [Openprovider](https://openprovider.com/) account.
- Compatible with [WooCommerce Subscriptions](https://woocommerce.com/products/woocommerce-subscriptions/).

### Features

- "Domain name" product type
- "Domain name" field on product page
- Availability and price check, using Openprovider API
- Price adjustment rules:

  - optional margin to add to gross price
  - optional rounding (e.g. only prices like 10, 15, 20...)
  - optional minimum price

### Status

This is a work in progress. I develop this plugin for my own needs, because I keep forgetting to bill domain renewals to my customers. However I have the feeling I'm not alone on this path.

The goal is to implement an easy bridge between WooCommerce and Openprovider, although the architecture should allow integration of other registrars and/or commerce platforms.

- *Disclaimer**: This is is not (and will probably never be) a full domain management platform. There are already very complete solutions for that.

The current to-do list include:

- Actual domain name registration via registrar API
- Actual domain renewal after subscription renewal payment
- Migrate existing domains from former billing solution
- Reminders before domain expiration
- Basic DNS options
- Handle multiple years registration (and adjust price if applicable)
- Handle renewal price different than registration price if applicable
- Handle promo prices if applicable
- Allow domain transfers


## Frequently Asked Questions

### Why is FAQ section empty?

Because nobody asked anything yet.

