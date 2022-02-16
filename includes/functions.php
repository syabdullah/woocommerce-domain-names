<?php

/**
 * Check if WooCommerce is activated
 */
if ( ! function_exists( 'is_woocommerce_activated' ) ) {
	function is_woocommerce_activated() {
		if ( class_exists( 'woocommerce' ) ) { return true; } else { return false; }
	}
}

function wcdnr_validate_domain_name($domain_name) {
	if (! preg_match("/^([a-z\d](-*[a-z\d])*\.)+(([a-z\d](-*[a-z\d])*))*$/i", $domain_name)) return false; //valid chars check
	if (! preg_match("/^.{1,253}$/", $domain_name)) return false; //overall length check
	if (! preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name)) return false; //length of each label
	return true;
}

function wcdnr_is_domain_product($product_id) {
	return (wc_get_product( $product_id )->get_meta( '_domainname' ) == 'yes');
}

function wcdnr_product_picker_array() {
  $args = array(
    // 'category' => array( 'hoodies' ),
    'orderby'  => 'name',
  );
  $get_products = wc_get_products($args);
  if(!$get_products) return [ '' => __('No products found')];

  $products = array('' => __('Select a product', 'wcdnr'));
  foreach($get_products as $product) {
    $products[$product->id] = $product->get_formatted_name();
  }
  return $products;
}

/**
 * [wcdnr_XMLRequest description]
 * @param  string $host               [description]
 * @param  string $method                   [description]
 * @param  array $request                  [description]
 * @return array             received xml response
 */
function wcdnr_XMLRequest($host, $method, $request) {
  $xml_request  = xmlrpc_encode_request($method, array($request));

  $context = stream_context_create(array('http' => array(
    'method'  => 'POST',
    'header'  => 'Content-Type: text/xml' . "\r\n",
    'timeout' => 3, // most of the time below 1 sec, but leave some time for slow ones
    'content' =>  $xml_request
  )));

  $response = @file_get_contents($host, false, $context);
  if($response === false) return false;

  $xml_array = xmlrpc_decode($response);
  if(empty($xml_array)) return;
  if (is_array($xml_array) &! xmlrpc_is_fault($xml_array)) return $xml_array;

  return false;
}
