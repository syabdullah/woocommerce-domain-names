<?php

require_once(__DIR__ . '/functions.php');

/**
 * [WCDNR description]
 */
class WCDNR {

  /*
  * Bootstraps the class and hooks required actions & filters.
  */
  public static function init() {
    add_filter("product_type_options", __CLASS__ . '::add_product_type_options');
    add_action("save_post_product", __CLASS__ . '::save_product_type_options', 10, 3);

    add_action( 'woocommerce_before_add_to_cart_button', __CLASS__ . '::display_custom_field' );
    add_filter( 'woocommerce_add_to_cart_validation', __CLASS__ . '::validate_custom_field', 10, 3 );
    add_filter( 'woocommerce_add_cart_item_data', __CLASS__ . '::add_custom_field_item_data', 10, 4 );
    add_action( 'woocommerce_before_calculate_totals', __CLASS__ . '::before_calculate_totals', 10, 1 );
    add_filter( 'woocommerce_cart_item_name', __CLASS__ . '::cart_item_name', 10, 3 );
    add_action( 'woocommerce_checkout_create_order_line_item', __CLASS__ . '::add_custom_data_to_order', 10, 4 );
  }

  public static function add_product_type_options($product_type_options) {
    $product_type_options["domainname"] = array(
      "id"            => "_domainname",
      "wrapper_class" => "show_if_simple show_if_variable",
      "label"         => "Domain name",
      "description"   => "Check to use this product for domain registration.",
      "default"       => "no",
    );
    return $product_type_options;
  }

  public static function save_product_type_options($post_ID, $product, $update) {
    update_post_meta($product->ID, "_domainname", isset($_POST["_domainname"]) ? "yes" : "no");
  }

  /**
  * Display custom field on the front end
  * @since 1.0.0
  */
  function display_custom_field() {
    global $post;
    // Check for the custom field value
    // $product = wc_get_product( $post->ID );
    // if($product->get_meta( '_domainname' ) != 'yes') return;

    if(!wcdnr_is_domain_product( wc_get_product( $post->ID ) )) return;

    $value = isset( $_POST['wcdnr_domain'] ) ? sanitize_text_field( $_POST['wcdnr_domain'] ) : '';
    printf(
      '<div class="wcdnr-domain-name">
      <label for="wcdnr_domain">%s</label>
      <abbr class="required" title="required">*</abbr>
      <input name="wcdnr_domain" value="%s"></div>',
      __('Domain name', 'wcdnr'),
      $value,
    );
  }

  function validate_custom_field( $passed, $product_id, $quantity ) {
    if(wcdnr_is_domain_product( $product_id )) {
      if( empty( $_POST['wcdnr_domain'] ) ) {
        wc_add_notice( __('Domain name is required', 'wcdnr'), 'error' );
        $passed = false;
      } else if (wcdnr_validate_domain_name($_POST['wcdnr_domain']) == false) {
        wc_add_notice( __('Please provide a valid domain name', 'wcdnr'), 'error' );
        $passed = false;
      }
    }
    return $passed;
  }

  /**
  * Add the text field as item data to the cart object
  * @since 1.0.0
  * @param Array $cart_item_data Cart item meta data.
  * @param Integer $product_id Product ID.
  * @param Integer $variation_id Variation ID.
  * @param Boolean $quantity Quantity
  */
  function add_custom_field_item_data( $cart_item_data, $product_id, $variation_id, $quantity ) {
    if( ! empty( $_POST['wcdnr_domain'] ) ) {
      // Add the item data
      $cart_item_data['domain_name'] = $_POST['wcdnr_domain'];
      /**
      * TODO: Recalculate price according to tld extension
      */
      // $product = wc_get_product( $product_id ); // Expanded function
      // $price = $product->get_price(); // Expanded function
      // $cart_item_data['total_price'] = $price + 100; // Expanded function
    }
    return $cart_item_data;
  }

  /**
  * Update the price in the cart
  * @since 1.0.0
  */
  function before_calculate_totals( $cart_obj ) {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
      return;
    }
    // Iterate through each cart item
    foreach( $cart_obj->get_cart() as $key=>$value ) {
      if( isset( $value['total_price'] ) ) {
        $price = $value['total_price'];
        $value['data']->set_price( ( $price ) );
      }
    }
  }

  /**
  * Display the custom field value in the cart
  * @since 1.0.0
  */
  function cart_item_name( $name, $cart_item, $cart_item_key ) {
    if( isset( $cart_item['domain_name'] ) ) {
      $name = sprintf(
      '%s <span class=wcdnr-domain-name>%s</span>',
      $name,
      esc_html( $cart_item['domain_name'] ),
      );
    }
    return $name;
  }

  /**
  * Add custom field to order object
  */
  function add_custom_data_to_order( $item, $cart_item_key, $values, $order ) {
    foreach( $item as $cart_item_key=>$values ) {
      if( isset( $values['domain_name'] ) ) {
        $item->add_meta_data( __( 'Domain name', 'wcdnr' ), $values['domain_name'], true );
      }
    }
  }
}

WCDNR::init();
