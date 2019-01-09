<?php

require __DIR__ . '/AMO_WOO_Api.php';
require __DIR__ . '/settings.php';

$api = new AMO_WOO_Api($login, $hash, $subdomain);

/**
 * @param int $order_id
 */
function amo_woo_thankyou($order_id)
{
	global $api;
	file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/logs.html', '<b>order_id</b><br><pre>' . var_export($order_id, true) . '<pre>', FILE_APPEND);

	$auth_result = $api->auth();
	file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/logs.html', '<b>auth_result</b><br><pre>' . var_export($auth_result, true) . '<pre>', FILE_APPEND);
	$result = $api->amo_woo_add_entity('leads', ['name' => 'order_' . $order_id]);
	file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/logs.html', '<b>result</b><br><pre>' . var_export($result, true) . '<pre>', FILE_APPEND);
}

/**
 * @param WC_Order $order
 */
function amo_woo_order_details_after_order_table(WC_Order $order)
{
	global $api;

	if ($api->auth()) {
		$order_data = $order->get_data();
		$order_id = $order_data['id'];
		$billing_data = $order_data['billing'];

		$contact_data = [
			'name' => $billing_data['first_name'] ? $billing_data['first_name'] : 'order_' . $order_id,
			'custom_fields' => [
				[
					'id'     => 94271,
					'values' => ['value' => $billing_data['phone'], 'enum' => "WORK"],
				],
			],
		];

		try {
			$contact_add_result = $api->amo_woo_add_entity('contacts', $contact_data);

			if ($contact_add_result) {
				$contact_data = $contact_add_result[0];

				$lead_data = [
					'name' => 'order_' . $order_id,
					'contacts_id' => $contact_data['id'],
					'sale' => $order_data['total'],
					'tags' => 'wp_site',
				];

				$api->amo_woo_add_entity('leads', $lead_data);
			}
		} catch (Exception $exception) {
			$api->amo_woo_handle_exception($exception);
		}
	}
}