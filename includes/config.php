<?php
// Deliberately basic and unsafe config for training purposes
// NOTE: Do NOT use this in production.

# SET THE BASE URL FOR THE WEBSITE
$base_url = 'http://localhost/InsecureCarDealer/';

if (!function_exists('base_url')) {
  function base_url(string $path = ''): string {
    global $base_url;
    $base = rtrim($base_url ?: '/', '/');
    if ($path === '' || $path === '/') {
      return $base . '/';
    }
    return $base . '/' . ltrim($path, '/');
  }
}
 
$s = session_status();
if ($s === PHP_SESSION_NONE) { @session_start(); }

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'car_shop3';
 
$conn = @mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
$DB_CONNECTED = (bool)$conn;
$DB_ERROR = $DB_CONNECTED ? '' : mysqli_connect_error();