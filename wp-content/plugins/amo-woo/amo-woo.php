<?php
/*
Plugin Name: Интеграция с amocrm
Plugin URI: https://amocrm.ru
Description: Плагин позволяет настроить интеграцию с аккаунтом amocrm
Version: 1.0
Author: Tolyan
Author URI: https://amocrm.ru
*/

require __DIR__ . '/functions.php';
//require __DIR__ . '/AMO_WOO_Api.php';

//add_action('after_setup_theme', function () {
//	require __DIR__ . '/AMO_WOO_Api.php';
//}, 5);

add_action('woocommerce_thankyou', 'amo_woo_thankyou');