<?php
/**
 * Plugin Name:     WooCommerce Domain Names
 * Plugin URI:      https://github.com/magicoli/woocommerce-domain-names
 * Description:     WooCommerce domain names products and tools for Openprovider.
 * Author:          Magiiic
 * Author URI:      https://magiiic.com/
 * Text Domain:     wcdnr
 * Domain Path:     /languages
 * Version:         0.1.3.1
 *
 * @package         WCDNR
 *
 * Icon1x: https://github.com/magicoli/woocommerce-domain-names/raw/master/assets/icon-128x128.png
 * Icon2x: https://github.com/magicoli/woocommerce-domain-names/raw/master/assets/icon-256x256.png
 * BannerHigh: https://github.com/magicoli/woocommerce-domain-names/raw/master/assets/banner-1544x500.jpg
 * BannerLow: https://github.com/magicoli/woocommerce-domain-names/raw/master/assets/banner-772x250.jpg
 */

define('WCDNR_PLUGIN', plugin_basename(__FILE__));
$plugin_data = get_plugin_data( __FILE__ );
define('WCDNR_PLUGIN_NAME', $plugin_data['Name']);

require_once(__DIR__ . '/includes/classes.php');

if(file_exists(plugin_dir_path( __FILE__ ) . 'lib/package-updater.php'))
include_once plugin_dir_path( __FILE__ ) . 'lib/package-updater.php';

if(is_admin()) {
  require_once(__DIR__ . '/admin/wc-admin-classes.php');
}
