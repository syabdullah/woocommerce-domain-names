<?php

require_once(__DIR__ . '/functions.php');
require_once(__DIR__ . '/openprovider-api.php');

/**
 * [WCDNR description]
 */
class WCDNR {

  /*
  * Bootstraps the class and hooks required actions & filters.
  */
  public static function init() {
    add_filter( 'product_type_options', __CLASS__ . '::add_product_type_options');
    add_action( 'save_post_product', __CLASS__ . '::save_product_type_options', 10, 3);

    add_action( 'woocommerce_before_add_to_cart_button', __CLASS__ . '::display_custom_field' );
    add_filter( 'woocommerce_add_to_cart_validation', __CLASS__ . '::validate_custom_field', 10, 3 );
    add_filter( 'woocommerce_add_cart_item', __CLASS__ . '::add_cart_item', 20 );
    add_filter( 'woocommerce_add_cart_item_data', __CLASS__ . '::add_custom_field_item_data', 10, 4 );
    add_action( 'woocommerce_before_calculate_totals', __CLASS__ . '::before_calculate_totals', 10, 1 );
    add_filter( 'woocommerce_cart_item_name', __CLASS__ . '::cart_item_name', 10, 3 );
    add_action( 'woocommerce_checkout_create_order_line_item', __CLASS__ . '::add_custom_data_to_order', 10, 4 );
    // add_action( 'admin_init', __CLASS__ . '::create_attributes' );
    add_filter( 'wc_add_to_cart_message', __CLASS__ . '::add_to_cart_message', 10, 2 );
    add_filter( 'woocommerce_get_price_html', __CLASS__ . '::get_price_html', 10, 2 );

    add_action( 'plugins_loaded', __CLASS__ . '::load_plugin_textdomain' );

    add_filter( 'woocommerce_product_add_to_cart_text', __CLASS__ . '::add_to_card_button', 10, 2);
    add_filter( 'woocommerce_product_single_add_to_cart_text', __CLASS__ . '::single_add_to_card_button', 10, 2);
  }

  public static function load_plugin_textdomain() {
		load_plugin_textdomain(
			'wcdnr',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}

  static function add_to_card_button( $text, $product ) {
    if($product->get_meta( '_domainname' ) == 'yes') $text = _x('Register domain', 'An unspecified domain name (in products list)', 'wcdnr');
  	return $text;
  }

  static function single_add_to_card_button( $text, $product ) {
    if($product->get_meta( '_domainname' ) == 'yes') $text = _x('Register domain name', 'The given domain name (on single product page, under name field)', 'wcdnr');
  	return $text;
  }

  static function add_to_cart_message( $message, $product_id ) {
      // make filter magic happen here...
      if(!empty($_POST['wcdnr_domain'])) $message = $_POST['wcdnr_domain'] . ": $message";
      return $message;
  }

  public static function add_product_type_options($product_type_options) {
    $product_type_options["domainname"] = array(
      "id"            => "_domainname",
      "wrapper_class" => "show_if_simple show_if_variable",
      "label"         => __('Domain name', 'wcdnr'),
      "description"   => __('Check to use this product for domain name registration.', 'wcdnr'),
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
  static function display_custom_field() {
    global $post;
    // Check for the custom field value
    // $product = wc_get_product( $post->ID );
    // if($product->get_meta( '_domainname' ) != 'yes') return;

    if(!wcdnr_is_domain_product( wc_get_product( $post->ID ) )) return;
    $value = isset( $_REQUEST['wcdnr_domain'] ) ? sanitize_text_field( $_REQUEST['wcdnr_domain'] ) : '';
    printf(
      '<div class="wcdnr-field wcdnr-field-domain-name">
        <label for="wcdnr_domain">%s</label>
        <abbr class="required" title="required">*</abbr>
        <p class="form-row form-row-wide">
          <input class="input-text" name="wcdnr_domain" value="%s" placeholder="example.org">
        </p>
        <p class=description>%s</p>
      </div>',
      __('Domain name', 'wcdnr'),
      $value,
      __('Actual domain name price will be displayed in the shopping cart, before order validation and payment.', 'wcdnr'),
    );
  }

  static function validate_custom_field( $passed, $product_id, $quantity ) {
    if($passed && wcdnr_is_domain_product( $product_id )) {
      $domain = sanitize_text_field($_POST['wcdnr_domain']);
      if( empty( $domain ) ) {
        wc_add_notice( __('Domain name is required', 'wcdnr'), 'error' );
        $passed = false;
      } else if (wcdnr_validate_domain_name($domain) == false) {
        wc_add_notice( __('Please provide a valid domain name', 'wcdnr'), 'error' );
        $passed = false;
      } else if($passed) {
        global $Openprovider;
        $extension = preg_replace('/^.*\./', '', $domain);
        $name = preg_replace("/\.$extension$/", '', $domain);
        $reply = $Openprovider->request('checkDomainRequest', array(
          'domains' => array(
            array(
              'name' => $name,
              'extension' => $extension,
            ),
          ),
          'withPrice' => true,
        ));
        if ($reply->getFaultCode() != 0) {
          $passed = false;
        } else if ($reply->getValue()[0]['status'] == 'free'){
          wp_cache_set('domain_price_' . $domain, $reply->getValue()[0], 'wcdnr');
        } else {
          $passed = false;
          $notice = sprintf(__('Cannot register %1$s', 'wcdnr'), $domain);
          if(!empty($reply->getValue()[0]['reason'])) {
            $notice .= sprintf(' (%s)', __($reply->getValue()[0]['reason'], 'wcdnr'));
          } else if($reply->getValue()[0]['status'] == 'active') {
            $notice .= sprintf(' (%s)', __('Domain exists', 'wcdnr')) . " [" . $reply->getValue()[0]['status'] . "]";
          }
          wc_add_notice($notice, 'error');
        }
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
  static function add_custom_field_item_data( $cart_item_data, $product_id, $variation_id, $quantity ) {
    $domain = sanitize_text_field($_POST['wcdnr_domain']);

    if( ! empty( $domain ) ) {
      global $Openprovider;
      // Add the item data
      $cart_item_data['domain_name'] = $domain;

      $price = $Openprovider->get_quote($domain);
      if(!$price === false) {
        $product = wc_get_product( $product_id ); // Expanded function
        // $price = $product->get_price(); // Expanded function
        $cart_item_data['domain_price'] = wcdnr_selling_price($price); // Expanded function
      }
    }
    return $cart_item_data;
  }

  public static function add_cart_item( $cart_item ) {
    if( isset( $cart_item['domain_price'] ) ) {
      // $cart_item['data']->adjust_price( $cart_item['domain_price'] );

      // $price = (float) $cart_item_data['data']->get_price( 'edit' );
      // $cart_item_data['data']->set_price( $price + $cart_item_data['domain_price'] );
      // $value['data']->set_price( ( $price ) );
    }
    // error_log('$cart_item_data ' . print_r($cart_item_data, true));
    return $cart_item;
  }
  /**
  * Update the price in the cart
  * @since 1.0.0
  */
  static function before_calculate_totals( $cart ) {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
      return;
    }
    // Iterate through each cart item
    foreach( $cart->get_cart() as $cart_key => $cart_item ) {
      $cached = wp_cache_get('wcdnr_cart_item_processed_' . $cart_key, 'wcdnr');
      if(!$cached) {
        if( is_numeric( $cart_item['domain_price'] &! $cart_item['domain_price_added']) ) {
          $price = (float)$cart_item['data']->get_price( 'edit' );
          $total = $price + $cart_item['domain_price'];
          error_log('adding ' . $cart_item['domain_price']
          . "\n" . 'initial price ' . $price
          . "\n" . 'adjusted price ' . $total);
          // $cart_item['data']->adjust_price( $cart_item['domain_price'] );
          $cart_item['data']->set_price( ( $total ) );
          $cart_item['domain_price_added'] = true;
        }
        wp_cache_set('wcdnr_cart_item_processed_' . $cart_key, true, 'wcdnr');
      }
    }
  }

  /**
  * Display the custom field value in the cart
  * @since 1.0.0
  */
  static function cart_item_name( $name, $cart_item, $cart_item_key ) {
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
  static function add_custom_data_to_order( $item, $cart_item_key, $values, $order ) {
    foreach( $item as $cart_item_key=>$values ) {
      if( isset( $values['domain_name'] ) ) {
        $item->add_meta_data( __( 'Domain name', 'wcdnr' ), $values['domain_name'], true );
      }
    }
  }

  static function get_price_html( $price_html, $product ) {
    if($product->get_meta( '_domainname' ) == 'yes') {
      $price = max($product->get_price(), get_option('wcdnr_domain_minimum_price', 0));
      if( $price == 0 ) {
        $price_html = apply_filters( 'woocommerce_empty_price_html', '', $product );
      } else {
        if ( $product->is_on_sale() && $product->get_price() >= $price ) {
          $price = wc_format_sale_price( wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) ),
          wc_get_price_to_display( $product ) ) . $product->get_price_suffix();
        } else {
          $price = wc_price( $price ) . $product->get_price_suffix();
        }
        $price_html = sprintf('<span class="from">%s </span>', __('From', 'wcdnr')) . $price;
      }
    }
    return $price_html;
  }

  // /**
  //  * Register TLD attribute taxonomy.
  //  */
  // static function create_attributes() {
  //   $attributes = wc_get_attribute_taxonomies();
  //   $slugs = wp_list_pluck( $attributes, 'attribute_name' );
  //   if ( ! in_array( 'tld', $slugs ) ) {
  //     $args = array(
  //       'slug'    => 'tld',
  //       'name'   => __( 'Top-level domain', 'wcdnr' ),
  //       'type'    => 'select',
  //       'order_by' => 'name',
  //       'has_archives'  => false,
  //     );
  //     $result = wc_create_attribute( $args );
  //   } else {
  //     $result = true;
  //   }
  //
  //   if($result && empty(get_terms('pa_tld'))) {
  //     $tlds = [ 'com', 'net', 'org' ];
  //     foreach($tlds as $tld) {
  //       if( ! term_exists( ".$tld", 'pa_tld', [ 'slug' => $tld ] ) ) {
  //         $term_data = wp_insert_term( $tld, 'pa_tld' );
  //         $term_id   = $term_data['term_id'];
  //       } else {
  //         $term_id   = get_term_by( 'name', $tld, 'pa_tld' )->term_id;
  //       }
  //     }
  //   }
  // }

  // function product_add_attributes($product) {
  //   if(is_numeric($product)) {
  //     $product_id = $product;
  //     $product = wc_get_product( $product_id );
  //   }
  //   if(!$product) return;
  //
  //   $attributes = (array) $product->get_attributes();
  //
  //   // 1. If the product attribute is set for the product
  //   if( array_key_exists( 'pa_tld', $attributes ) ) {
  //     foreach( $attributes as $key => $attribute ){
  //       if( $key == 'pa_tld' ){
  //         $options = (array) $attribute->get_options();
  //         $options[] = $term_id;
  //         $attribute->set_options($options);
  //         $attributes[$key] = $attribute;
  //         break;
  //       }
  //     }
  //     $product->set_attributes( $attributes );
  //   }
  //   // 2. The product attribute is not set for the product
  //   else {
  //     $attribute = new WC_Product_Attribute();
  //
  //     $attribute->set_id( sizeof( $attributes) + 1 );
  //     $attribute->set_name( 'pa_tld' );
  //     $attribute->set_options( array( $term_id ) );
  //     $attribute->set_position( sizeof( $attributes) + 1 );
  //     $attribute->set_visible( false );
  //     $attribute->set_variation( true );
  //     $attributes[] = $attribute;
  //
  //     $product->set_attributes( $attributes );
  //   }
  //
  //   $product->save();
  //
  //   // Append the new term in the product
  //   if( ! has_term( $term_name, 'pa_tld', $product_id ))
  //   wp_set_object_terms($product_id, $term_slug, 'pa_tld', true );
  // }
}

WCDNR::init();
