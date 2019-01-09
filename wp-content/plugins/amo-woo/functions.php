<?php

require __DIR__ . '/AMO_WOO_Api.php';
require __DIR__ . '/settings.php';

$api = new AMO_WOO_Api($login, $hash, $subdomain);

function amo_woo_thankyou($order_id)
{
	global $api;
	file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/logs.html', '<b>order_id</b><br><pre>' . var_export($order_id, true) . '<pre>', FILE_APPEND);

	$auth_result = $api->auth();
	file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/logs.html', '<b>auth_result</b><br><pre>' . var_export($auth_result, true) . '<pre>', FILE_APPEND);
	$result = $api->amo_woo_add_entity('leads', ['name' => 'order_' . $order_id]);
	file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/logs.html', '<b>result</b><br><pre>' . var_export($result, true) . '<pre>', FILE_APPEND);
}