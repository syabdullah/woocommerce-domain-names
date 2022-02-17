<?php
/**
 * Plugin Name:     WooCommerce Domain Names
 * Plugin URI:      https://github.com/magicoli/wc-domain-names
 * Description:     WooCommerce domain names products and tools for Openprovider.
 * Author:          Magiiic
 * Author URI:      https://magiiic.com/
 * Text Domain:     wcdnr
 * Domain Path:     /languages
 * Version:         0.1.1
 *
 * @package         WCDNR
 */

define('WCDNR_PLUGIN', plugin_basename(__FILE__));
$plugin_data = get_plugin_data( __FILE__ );
define('WCDNR_PLUGIN_NAME', $plugin_data['Name']);

require_once(__DIR__ . '/includes/classes.php');

if(is_admin()) {
  require_once(__DIR__ . '/admin/wc-admin-classes.php');
}
