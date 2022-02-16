<?php
/**
 * Plugin Name:     WooCommerce Domain Registration
 * Plugin URI:      https://magiiic.com/wordpress/wcdnr/
 * Description:     WooCommerce additions for domain resellers.
 * Author:          Magiiic
 * Author URI:      https://magiiic.com/
 * Text Domain:     wcdnr
 * Domain Path:     /languages
 * Version:         0.1.0
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
