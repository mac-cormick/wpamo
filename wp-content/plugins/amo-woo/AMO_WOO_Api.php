<?php

class AMO_WOO_Api {

	/** @var string */
	private $login;

	/** @var string */
	private $hash;

	/** @var string */
	private $subdomain;

	/** @var array */
	private $enities = [
		'leads',
		'companies',
		'contacts',
		'tasks',
		'customers',
		'notes',
	];

	/**
	 * AMO_WOO_Api constructor.
	 *
	 * @param string $login
	 * @param string $hash
	 * @param string $subdomain
	 */
	public function __construct($login, $hash, $subdomain)
	{
		$this->login = $login;
		$this->hash = $hash;
		$this->subdomain = $subdomain;
	}

	/**
	 * @return array
	 */
	public function amo_woo_get_user()
	{
		return [
			'USER_LOGIN' => $this->login,
			'USER_HASH'  => $this->hash,
		];
	}

	public function auth()
	{
		$user = $this->amo_woo_get_user();
		$subdomain = $this->subdomain;

		$link = '/private/api/auth.php?type=json';

		return $this->amo_woo_curl_post($link, $user);
	}

	/**
	 * @param string $link
	 * @param array  $data
	 * @return array
	 */
	public function amo_woo_curl_post($link, $data)
	{
		return $this->amo_woo_curl_send_request($link, $data, true);
	}

	/**
	 * @param string $link
	 * @return array
	 */
	public function amo_woo_curl_get($link)
	{
		return $this->amo_woo_curl_send_request($link);
	}

	/**
	 * @param string $link
	 * @param array  $data
	 * @param bool   $post
	 * @return array
	 */
	private function amo_woo_curl_send_request($link, array $data = [], $post = false)
	{
		$link = 'https://' . $this->subdomain . '.amocrm.ru' . $link;
		file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/logs.html', '<b>link</b><br><pre>' . var_export($link, true) . '<pre>', FILE_APPEND);

		$curl=curl_init();
		curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl,CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');

		try {
			if (empty($link)) {
				throw new Exception('link is empty');
			}
			curl_setopt($curl,CURLOPT_URL, $link);
			if ($post) {
				curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
				if (empty($data)) {
					throw new Exception('empty data for post request');
				}
				curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
				curl_setopt($curl,CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			}
		} catch (Exception $exception) {
			die($this->amo_woo_handle_exception($exception));
		}

		curl_setopt($curl,CURLOPT_HEADER, false);
		curl_setopt($curl,CURLOPT_COOKIEFILE, dirname(__FILE__).'/cookie.txt');
		curl_setopt($curl,CURLOPT_COOKIEJAR, dirname(__FILE__).'/cookie.txt');
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 0);
		$out=curl_exec($curl);
		file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/logs.html', '<b>out</b><br><pre>' . var_export($out, true) . '<pre>', FILE_APPEND);
		$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
		curl_close($curl);

		return $this->amo_woo_curl_request_result($code, $out);
	}

	/**
	 * @param string|int $code
	 * @param mixed $out
	 * @return array
	 */
	private function amo_woo_curl_request_result($code, $out)
	{
		$code=(int)$code;
		$errors=array(
			301=>'Moved permanently',
			400=>'Bad request',
			401=>'Unauthorized',
			403=>'Forbidden',
			404=>'Not found',
			500=>'Internal server error',
			502=>'Bad gateway',
			503=>'Service unavailable'
		);
		try
		{
			#Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
			if($code != 200 && $code != 204)
				throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error', $code);
		}
		catch(Exception $exception)
		{
			die($this->amo_woo_handle_exception($exception));
		}

		$response = json_decode($out, true);

		return $response;
	}

	/**
	 * @param Exception $exception
	 * @return array
	 */
	private function amo_woo_handle_exception(\Exception $exception)
	{
		file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/logs.html', '<b>exception</b><br><pre>' . var_export($exception->getMessage(), true) . '<pre>', FILE_APPEND);
		return [
			'message' => $exception->getMessage(),
		];
	}

	/**
	 * @param string $entity
	 * @param array $data
	 * @return array
	 */
	public function amo_woo_add_entity($entity, array $data = [])
	{
		try {
			if (!in_array($entity, $this->enities)) {
				throw new Exception('unsupported type of entity to add');
			}

			if (empty($data)) {
				throw new Exception('empty data for entity to add');
			}

			$url = '/api/v2/' . $entity;
			$post_data = [
				'add' => [$data],
			];

			return $this->amo_woo_curl_post($url, $post_data);

		} catch (Exception $exception) {
			return $this->amo_woo_handle_exception($exception);
		}
	}
}