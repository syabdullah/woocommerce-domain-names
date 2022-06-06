<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Openprovider\Api\Rest\Client\Auth\Model\AuthLoginRequest;
use Openprovider\Api\Rest\Client\Base\Configuration;
use Openprovider\Api\Rest\Client\Client;
use GuzzleHttp\Client as HttpClient;

function wcdnr_get_op_client($credentials = []) {
  $cached = wp_cache_get('openprovider_client', 'wcdnr');
  if($cached) return $cached;

  // Create new http client.
  $httpClient = new HttpClient();

  // Create new configuration.
  $configuration = new Configuration();

  // Build api client for using created client & configuration.
  $client = new Client($httpClient, $configuration);

  $username = (isset($credentials['username'])) ? $credentials['username'] : get_option('wcdnr_openprovider_username');
  $password = (isset($credentials['password'])) ? $credentials['password'] : get_option('wcdnr_openprovider_password');
  // $hash = (isset($credentials['hash'])) ? $credentials['hash'] : get_option('wcdnr_openprovider_hash');

  $loginResult = $client->getAuthModule()->getAuthApi()->login(
    new AuthLoginRequest([
      'username' => $username,
      'password' => $password,
      // 'hash' => $hash,
    ])
  );

  if($loginResult) {
    error_log("Connected");
    $configuration->setAccessToken($loginResult->getData()->getToken());
    return $client;
  } else {
    error_log("Not connected");
    return false;
  }
  // Set token to configuration (it will update the $client).
  // error_log('client ' . print_r($client, true));

}

//
// // Use this client for API calls.
// $result = $client->getTldModule()->getTldServiceApi()->getTld('com');
// error_log(print_r($result, true));
