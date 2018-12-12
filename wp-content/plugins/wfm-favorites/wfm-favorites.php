<?php
/*
Plugin Name: Добавление статей в избранное
Plugin URI: http://страница_с_описанием_плагина_и_его_обновлений
Description: Плагин добавляет авторизованным пользователям ссылку к статьям, позволяющую добавлять статью в избранное.
Version: Номер версии плагина, например: 1.0
Author: Tolyan
Author URI: https://google.com
*/

require __DIR__ . '/functions.php';

add_filter('the_content', 'wfm_favorites_content');
add_action('wp_enqueue_scripts', 'wfm_favorites_scripts');
add_action('wp_ajax_wfm_add', 'wp_ajax_wfm_add');
add_action('wp_ajax_wfm_del', 'wp_ajax_wfm_del');
add_action('wp_ajax_wfm_del_all', 'wp_ajax_wfm_del_all');
add_action('wp_dashboard_setup', 'wfm_favorites_dashboard_widget');

add_action('admin_enqueue_scripts', 'wfm_favorites_admin_scripts');
