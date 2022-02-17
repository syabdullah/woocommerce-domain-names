<?php
/*
 * @package         WCDNR
 */

class WCDNR_Admin {

  /*
  * Bootstraps the class and hooks required actions & filters.
  *
  */
  public static function init() {
    add_action( 'woocommerce_settings_tabs_wcdnr', __CLASS__ . '::settings_tab' );
    add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
    add_filter( 'plugin_action_links_' . WCDNR_PLUGIN, __CLASS__ . '::add_action_links' );

    add_action( 'woocommerce_update_options_wcdnr', __CLASS__ . '::update_settings' );
    add_filter( "woocommerce_admin_settings_sanitize_option_wcdnr_openprovider_hash", __CLASS__ . '::check_credentials', 10, 3 );
    add_filter( "woocommerce_admin_settings_sanitize_option_wcdnr_openprovider_username", __CLASS__ . '::check_credentials', 10, 3 );
  }

  public static function add_action_links ( $actions ) {
    $actions = array_merge( $actions, array(
      '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=wcdnr' ) . '">' . __('Settings', 'wcdnr') . '</a>',
    ));
    return $actions;
  }

  /*
  * Add a new settings tab to the WooCommerce settings tabs array.
  *
  * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
  * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
  */
  public static function add_settings_tab( $settings_tabs ) {
    $settings_tabs['wcdnr'] = __( 'Domain Names', 'wcdnr' );
    return $settings_tabs;
  }

  /*
  * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
  *
  * @uses woocommerce_admin_fields()
  * @uses self::get_settings()
  */
  public static function settings_tab() {
    woocommerce_admin_fields( self::get_settings() );
  }


  /**
  * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
  *
  * @uses woocommerce_update_options()
  * @uses self::get_settings()
  */
  public static function update_settings() {
    woocommerce_update_options( self::get_settings() );
  }


  /*
  * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
  *
  * @return array Array of settings for @see woocommerce_admin_fields() function.
  */
  public static function get_settings() {

    $settings = array(
      'section_openprovider' => array(
        'name'     => __( 'Openprovider connection', 'wcdnr' ),
        'type'     => 'title',
        'desc'     => (get_option('wcdnr_openprovider_ok'))
        ? ''
        : sprintf(
          __('Find your credentials in Openprovider dashboard %s', 'wcdnr'),
          sprintf(
            '<a href="%s" target=_blank>%s</a>',
            'https://cp.openprovider.eu/account/dashboard.php',
            join(' -> ', array(
              // __('Openprovider dashboard', 'wcdnr'),
              __('Account', 'wcdnr'),
              __('Account overview', 'wcdnr'),
              __('Contact persons', 'wcdnr'),
            ))
          ),
        ),
        'id'       => 'wcdnr_section_openprovider'
      ),
      array(
        'name' => __( 'Username', 'wcdnr' ),
        'type' => 'text',
        // 'custom_attributes' => array( 'required' => 'required' ),
        // 'desc' => __( 'This is some helper text', 'wcdnr' ),
        'id'   => 'wcdnr_openprovider_username'
      ),
      array(
        'name' => __( 'Hash', 'wcdnr' ),
        'type' => 'password',
        // 'custom_attributes' => array( 'required' => 'required' ),
        // 'desc' => __( 'This is some helper text', 'wcdnr' ),
        'id'   => 'wcdnr_openprovider_hash'
      ),
      'section_openprovider_end' => array(
        'type' => 'sectionend',
        'id' => 'wcdnr_section_openprovider_end'
      ),
    );
    if(get_option('wcdnr_openprovider_ok')) {
      $settings = array_merge($settings, array(
        'section_title' => array(
          'name'     => __( 'Prices', 'wcdnr' ),
          'type'     => 'title',
          'id'       => 'wcdnr_section_prices',
        ),
        array(
          'id' => 'wcdnr_domain_margin',
          'name' => __('Minimum margin (%)', 'wcdnr'),
          'type' => 'number',
          'custom_attributes' => array( 'min' => 0, 'size' => 2 ),
          'default' => 0,
        ),
        array(
          'id' => 'wcdnr_domain_minimum_price',
          'name' => sprintf(__('Minimum price (%s)', 'wcdnr'), get_woocommerce_currency_symbol()),
          'type' => 'number',
          'custom_attributes' => array( 'min' => 0, 'size' => 2 ),
          'default' => 0,
        ),
        array(
          'id' => 'wcdnr_domain_rounding',
          'name' => sprintf(__('Rounding (%s)', 'wcdnr'), get_woocommerce_currency_symbol()),
          'type' => 'number',
          'custom_attributes' => array( 'min' => 0, 'size' => 2 ),
          'default' => 0,
        ),
        'section_end' => array(
          'type' => 'sectionend',
          'id' => 'wcdnr_section_end'
        ),
      ));
    }
    return apply_filters( 'wcdnr_settings', $settings );
  }

  public static function check_credentials($value, $option, $raw_value) {
    $cached = wp_cache_get('wcdnr_check_credentials', 'wcdnr');
    if($cached == 'success') return $value;
    else if($cached) return;

    $username = $_REQUEST['wcdnr_openprovider_username'];
    $hash = $_REQUEST['wcdnr_openprovider_hash'];
    if(empty($username . $hash)) {
      update_option('wcdnr_openprovider_ok', false);
      update_option('wcdnr_openprovider_username', '');
      update_option('wcdnr_openprovider_hash', '');
      wp_cache_set('wcdnr_check_credentials', 'fail', 'wcdnr');
      WC_Admin_Settings::add_error(sprintf(__('%s needs Openprovider credentials to function properly.', 'wcdnr'), WCDNR_PLUGIN_NAME));
      return $value;
    }

    global $Openprovider;

    $request = new OP_Request;
    $request->setCommand('createCustomerRequest')
    ->setAuth(array('username' => $username, 'hash' => $hash));
    $reply = $Openprovider->process($request); // prod

    if ($reply->getFaultCode() == 196) {
      update_option('wcdnr_openprovider_ok', false);
      wp_cache_set('wcdnr_check_credentials', 'fail', 'wcdnr');
      WC_Admin_Settings::add_error("Openprovider credentials: " . $reply->getFaultString());
      return;
    } else {
      update_option('wcdnr_openprovider_ok', true);
      wp_cache_set('wcdnr_check_credentials', 'success', 'wcdnr');
      return $value;
    }
  }
}

WCDNR_Admin::init();
