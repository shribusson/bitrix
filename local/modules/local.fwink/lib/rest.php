<?php
/**
 *    Rest
 *
 * @mail support@local.online
 * @link local.online
 */

namespace Local\Fwink;

use Bitrix\Main,
	Bitrix\Main\DB\Exception,
	Bitrix\Main\Config\Option,
	Local\Fwink\Settings;
use Bitrix\Main\Loader;
use LocalFwink;

class Rest
{
	const MODULE_ID = 'local.fwink';
	protected static $MANUAL_RUN = false;
	static $site = false;
	static $portal = false;
	static $app_id = false;
	static $secret = false;


	public static function setBulkRun()
	{
		self::$MANUAL_RUN = true;
	}

	public static function isBulkRun()
	{
		return self::$MANUAL_RUN;
	}

	/**
	 * Get Bitrix24 application info
	 */

	public static function getAppInfo()
	{
		$params_app_portal = Applications::load();
		self::$site = $params_app_portal['B24_REDIRECT_URI'];
		self::$portal = $params_app_portal['DOMAIN'];
		self::$app_id = $params_app_portal['B24_APPLICATION_ID'];
		self::$secret = $params_app_portal['B24_APPLICATION_SECRET'];

		$info = array_merge(
			[
				'site' => self::$site,
				'portal' => self::$portal,
				'app_id' => self::$app_id,
				'secret' => self::$secret,
			]
			, $params_app_portal
		);
		return $info;

		//-----------------------
		$info = false;
		if (self::$site === false) {
			self::$site = Settings::get("site");
		}
		if (self::$portal === false) {
			self::$portal = Settings::get("portal");
		}
		if (self::$app_id === false) {
			self::$app_id = Settings::get("app_id");
		}
		if (self::$secret === false) {
			self::$secret = Settings::get("secret");
		}
		if (self::$site && self::$portal && self::$app_id && self::$secret) {
			$info = [
				'site' => self::$site,
				'portal' => self::$portal,
				'app_id' => self::$app_id,
				'secret' => self::$secret,
			];
		}
		return $info;
	}

	/**
	 * Save file with auth data
	 *
	 * @param $info
	 *
	 * @return bool|int
	 */

	public static function saveAuthInfo($info)
	{
		$res = Settings::save("credentials", $info, true);
		return $res;
	}

	/**
	 * Read auth data
	 *
	 * @return bool|mixed
	 */

	public static function getAuthInfo()
	{
		$params_app_portal = Applications::load();
		$info = [
			'auth' => $params_app_portal['AUTH_ID'],
			'access_token' => $params_app_portal['AUTH_ID'],
			'refresh_token' => $params_app_portal['REFRESH_ID'],
			'domain' => $params_app_portal['DOMAIN'],
		];
		return $info;
	}

	/**
	 * Get link for application authentication
	 *
	 * @return bool|string
	 */
	public static function getAuthLink()
	{
		$app_info = self::getAppInfo();
		if (!$app_info) {
			return false;
		}
		$link = $app_info['portal'] . '/oauth/authorize/?client_id=' . $app_info['app_id'] . '&response_type=code';
		return $link;
	}

	/**
	 * Limits control
	 */

	public static function controlLimits()
	{
		$delay = 0;
		// Get values
		$last_exec = (int)Settings::get('rest_last_exec');
		$count_exec = (int)Settings::get('rest_count_exec');
		// Waiting for end of executions
		$current_exec = microtime(true);
		if ($current_exec < $last_exec) {
			$diff = $last_exec - $current_exec;
			$delay += $diff * 1000000;
			$current_exec = $last_exec;
		}
		// Update limits
		$diff = $current_exec - $last_exec;
		$count_exec -= $diff * 2;
		$count_exec = $count_exec >= 0 ? $count_exec : 0;
		$count_exec++;
		// Calc delay
		if ($count_exec > 30) {
			$diff = 1;
			$delay += $diff * 1000000;
			$current_exec += $diff;
			$count_exec -= $diff * 1;
		}
		// Save values
		Settings::save('rest_last_exec', $current_exec);
		Settings::save('rest_count_exec', $count_exec);
		// Delay
		if ($delay) {
			LocalFwink::Log('(controlLimits) delay ' . $delay);
			usleep($delay);
		}
	}

	/**
	 * Get auth token
	 */

	public static function restToken($code)
	{
		$app_info = self::getAppInfo();
		if (!$code || !$app_info) {
			return false;
		}

		$query_url = 'https://oauth.bitrix.info/oauth/token/';
		$query_data = http_build_query($queryParams = array(
			'grant_type' => 'authorization_code',
			'client_id' => $app_info['app_id'],
			'client_secret' => $app_info['secret'],
			'code' => $code,
		));
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_HEADER => 0,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $query_url . '?' . $query_data,
		));
		$result = curl_exec($curl);
		curl_close($curl);
		$cred = json_decode($result, true);

		if (!$cred['error']) {
			// Save new auth credentials
			self::saveAuthInfo($cred);
		}

		return $cred;
	}


	/**
	 * Refresh access token
	 *
	 * @param array $refresh_token
	 * @return bool|mixed
	 */

	public static function refreshToken($refresh_token)
	{
		$app_info = self::getAppInfo();
		if (!isset($refresh_token) || !$app_info) {
			return false;
		}
		if (Applications::check()) {
			//Загружаем настройки для вывода в интерфейсе Битрикс24 ключа доступа
//			$params = \Local\Fwink\Applications::load();
			$result = 'Приложение установлено.<br>';
//			$result .= 'Добавьте в скрипт hook.php ключ доступа:<br>';
//			$result .= $params['KEY'];


		} else {
			$result = 'Приложение установлено c ошибками.<br>';
//			echo "<pre>"; print_r (\Local\Fwink\Applications::$arLogSync); echo "</pre>";
		}
		//-----------
		self::controlLimits();

		$query_url = 'https://oauth.bitrix.info/oauth/token/';
		$query_data = http_build_query($queryParams = array(
			'grant_type' => 'refresh_token',
			'client_id' => $app_info['app_id'],
			'client_secret' => $app_info['secret'],
			'refresh_token' => $refresh_token,
		));
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_HEADER => 0,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $query_url . '?' . $query_data,
		));
		$result = curl_exec($curl);
		curl_close($curl);
		$resp = json_decode($result, true);

		if ($resp['error']) {
			throw new Exception($resp['error_description'], $resp['error']);
		}

		return $resp;
	}

	public static function executecustom($method, array $params = [], $cred = false, $auth_refresh = true, $only_res = true, $err_repeate = true)
	{
		if (!class_exists('LocalFwink', false)) {
			Loader::includeModule('local.fwink');
		}
//		\LocalFwink::Log('(rest execute) method '.$method);
		if (!empty($GLOBALS['FWINK']['requestURL']['AUTH_ID'])) {
			$paramsdomain = $GLOBALS['FWINK']['requestURL'];
//			echo "<pre>262"; print_r ($paramsdomain); echo "</pre>";
//			die(__FILE__.':'.__LINE__);
		} else {
			$paramsdomain = Applications::load();
//			echo "<pre>266:"; print_r ($paramsdomain); echo "</pre>";
//			die(__FILE__.':'.__LINE__);
		}
		$c = curl_init('http://' . $GLOBALS['FWINK']['DOMAIN'] . '/rest/' . $method);
		$params["auth"] = $paramsdomain['AUTH_ID'];
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLOPT_POST, true);
		curl_setopt($c, CURLOPT_POSTFIELDS, http_build_query($params));
		$result = curl_exec($c);
		curl_close($c);
		if (curl_errno($c)) {
			$error_msg = curl_error($c);
			print_r($error_msg);
		}
//		var_dump($response);
		$resp = json_decode($result, true);
		file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/bitrix/execute_curl.json'
			, "," . json_encode([
				'time' => date("H:i:s d-m-Y")
				, 'query_data' => $query_data = []
				, 'params' => $params
				, 'cred' => $cred = []
			]), FILE_APPEND);

		// Error to the log
		if ($resp['error'] || $resp['error_description']) {
			LocalFwink::Log('(rest execute) query "' . $method . '" error: ' . $resp['error_description'] . ' [' . $resp['error'] . ']');
			// If token expired then refresh it
			$query_url = 'https://oauth.bitrix.info/oauth/token/';
			$query_data = http_build_query($queryParams = array(
				'grant_type' => 'refresh_token',
				'client_id' => $paramsdomain['B24_APPLICATION_ID'],
				'client_secret' => $paramsdomain['B24_APPLICATION_SECRET'],
				'refresh_token' => $paramsdomain['REFRESH_ID'],
			));
//			echo "<pre>301:"; print_r ($query_data); echo "</pre>";

			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_HEADER => 0,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_URL => $query_url . '?' . $query_data,
			));
			$result = curl_exec($curl);
			curl_close($curl);
			$resprefresh = json_decode($result, true);
			/*{"access_token":"d28666600051ed000050cf8d0000000100000361cb8a90b433e01297da2e8276c794d8","expires":1617331922,"expires_in":3600,"scope":"app","domain":"oauth.bitrix.info","server_endpoint":"https:\/\/oauth.bitrix.info\/rest\/","status":"L","client_endpoint":"http:\/\/dev.blaywillie.keenetic.name\/rest\/","member_id":"ba980428026d6379a5758aaa287bf49a","user_id":1,"refresh_token":"c2058e600051ed000050cf8d00000001000003e16fbf34c7a8113a9c1961532ad8a38d"}*/
//			throw new Exception(__FILE__);
//			echo "<pre>312:"; print_r ($resprefresh); echo "</pre>";


			$paramsdomain['AUTH_ID'] = $resprefresh['access_token'];
			$paramsdomain['REFRESH_ID'] = $resprefresh['refresh_token'];
			if (empty($paramsdomain)) {
				$arErrorInform = [
					'expired_token' => 'expired token, cant get new auth? Check access oauth server.',
					'invalid_token' => 'invalid token, need reinstall application',
					'invalid_grant' => 'invalid grant, check out define C_REST_CLIENT_SECRET or C_REST_CLIENT_ID',
					'invalid_client' => 'invalid client, check out define C_REST_CLIENT_SECRET or C_REST_CLIENT_ID',
					'QUERY_LIMIT_EXCEEDED' => 'Too many requests, maximum 2 query by second',
					'ERROR_METHOD_NOT_FOUND' => 'Method not found! You can see the permissions of the application: CRest::call(\'scope\')',
					'NO_AUTH_FOUND' => 'Some setup error b24, check in table "b_module_to_module" event "OnRestCheckAuth"',
					'INTERNAL_SERVER_ERROR' => 'Server down, try later'
				];
				throw new Exception('exceptstate by LINE330:' . $resprefresh);
			}
			Applications::save($paramsdomain);
//			echo "<pre>316:"; print_r ($paramsdomain); echo "</pre>";
			if ($resprefresh["access_token"] and !empty($resprefresh['access_token'])) {
				// Save new custom auth credentials
				self::saveAuthInfo($resprefresh);
				// Execute again
				$resp = self::execute($method, $params, $paramsdomain, false, false);
			}



			if ($resp['error'] || $resp['error_description']) {
				LocalFwink::Log('(rest execute) query "' . $method . '" exception');
				throw new Exception($resp['error_description'], $resp['error']);
			}
		}

		if ($only_res) {
			$result = $resp['result'];
		} else {
			$result = $resp;
		}

		return $result;

	}

	/**
	 * Send rest query to Bitrix24.
	 *
	 * @param $method - Rest method, ex: methods
	 * @param array $params - Method params, ex: []
	 * @param array $cred - Authorize data, ex: Array('domain' => 'https://test.bitrix24.com', 'access_token' => '7inpwszbuu8vnwr5jmabqa467rqur7u6')
	 * @param boolean $auth_refresh - If authorize is expired, refresh token
	 *
	 * @return mixed
	 */

	public static function execute($method, array $params = [],
								   $cred = false, $auth_refresh = true, $only_res = true, $err_repeate = false)
	{
		LocalFwink::Log('(rest execute) method ' . $method);
		//---------start custom solution for localhost with SNI wrror

		if (!empty($GLOBALS['FWINK']['requestURL'])) {
			$paramsdomain = $GLOBALS['FWINK']['requestURL'];
		} else {
			$paramsdomain = Applications::load();
		}

		$app_info = self::getAppInfo();

		if (!$app_info) {
			return false;
		}
		if (!$cred) {
			$cred = self::getAuthInfo();
		}

		self::controlLimits();

		// Command to the REST server
		$query_url = 'https://'.$app_info['portal'] . '/rest/' . $method;
		$query_data = http_build_query(array_merge($params, ['auth' => $cred["access_token"]]));
//	    \LocalFwink::Log('(rest execute) query_data '.$query_data);
		$curl = curl_init();
		curl_setopt_array($curl, [
			CURLOPT_POST => 1,
			CURLOPT_HEADER => 0,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_SSL_VERIFYPEER => 1,
			CURLOPT_URL => $query_url,
			CURLOPT_POSTFIELDS => $query_data,
            CURLOPT_TIMEOUT => 10
		]);
        LocalFwink::Log('(rest execute) url ' . $query_url.' data:'.print_r($query_data, true));
		$result = curl_exec($curl);
		curl_close($curl);
		$resp = json_decode($result, true);

		file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/bitrix/execute_curl.json'
			, "," . json_encode([
				'time' => date("H:i:s d-m-Y")
				, 'query_data' => $query_data
				, 'params' => $params
				, 'cred' => $cred
                ,'resp' => $resp
			]), FILE_APPEND);


		// Error to the log
		if ($resp['error'] || $resp['error_description']) {
			LocalFwink::Log('(rest execute) query "' . $method . '" error: ' . $resp['error_description'] . ' [' . $resp['error'] . ']');
			// If token expired then refresh it
			if (in_array($resp['error'], array('expired_token', 'invalid_token'))) {
				if ($auth_refresh) {
					// Try to get new access token
					$i = 0;
					do {
						if ($i > 0) {
							sleep(1);
						}
						try {
							$cred = self::refreshToken($cred['refresh_token']);
						} catch (\Exception $e) {
							LocalFwink::Log('(rest execute) query "' . $method . '" refresh token error: ' . $e->getMessage() . ' [' . $e->getCode() . ']');
						}
						$i++;
					} while (!$cred["access_token"] && $i <= 3);
					if (is_array($cred)) {
						foreach ($cred as $k => $value) {
							$cred_log[$k] = mb_strimwidth($value, 0, 8, '***');
						}
					}
					LocalFwink::Log('(rest execute) query "' . $method . '" repeat result: ' . print_r($cred_log, true));
					if ($cred["access_token"]) {
						// Save new auth credentials
						self::saveAuthInfo($cred);
						// Execute again
						$resp = self::execute($method, $params, $cred, false, false);
					}
				}
			} // Other errors
			else {
				if ($err_repeate) {
					$i = 0;
					while (($resp['error'] || $resp['error_description']) && $i < 2) {
						sleep(1);
						// Execute again
						try {
							$resp = self::execute($method, $params, $cred, $auth_refresh, false, false);
						} catch (\Exception $e) {
							LocalFwink::Log('(rest execute) query "' . $method . '" repeat error: ' . $e->getMessage() . ' [' . $e->getCode() . ']');
						}
						$i++;
					}
				}
				// Return exception
				if ($resp['error'] || $resp['error_description']) {
					LocalFwink::Log('(rest execute) query "' . $method . '" exception');
					throw new Exception($resp['error_description'], $resp['error']);
				}
			}
		}

		// Get results
		if ($only_res) {
			$result = $resp['result'];
		} else {
			$result = $resp;
		}

		return $result;
	}

	public static function executeGetFlat($method, array $params = [], $cred = false)
	{
		LocalFwink::Log('(rest execute) method ' . $method);
		$app_info = self::getAppInfo();
		if (!$app_info) {
			return false;
		}
		if (!$cred) {
			$cred = self::getAuthInfo();
		}
		// Command to the REST server
		$query_url = $app_info['portal'] . '/rest/' . $method;
		$query_data = http_build_query(array_merge($params, ['auth' => $cred["access_token"]]));
		return $query_url . '?' . $query_data;
	}


	/**
	 * Batch request
	 */

	public static function batch(array $req_list, $cred = false)
	{
		$result = [];
		if (!empty($req_list)) {
			$req_limit = 50;
			$req_count = ceil(count($req_list) / $req_limit);
			for ($i = 0; $i < $req_count; $i++) {
				$req_list_f = [];
				$j = 0;
				foreach ($req_list as $id => $item) {
					if ($j >= $i * $req_limit && $j < ($i + 1) * $req_limit) {
						$params = isset($item['params']) ? http_build_query($item['params']) : '';
						$req_list_f[$id] = $item['method'] . '?' . $params;
					}
					$j++;
				}
				if (!empty($req_list_f)) {
					$resp = self::execute('batch', [
						"halt" => false,
						"cmd" => $req_list_f,
					], $cred);
					$result = array_merge($result, $resp);
				}
			}
		}
		return $result;
	}


	/**
	 * Universal list
	 */

	public static function getList($method, $sub_array = '', $params = [], $limit = 0)
	{
		$list = [];
		$resp = self::execute($method, $params, false, true, false);
		$count = $resp['total'];
		if ($count) {
			$req_list = [];
			$req_count = ceil($count / 50);
			for ($i = 0; $i < $req_count; $i++) {
				$next = $i * 50;
				$params['start'] = $next;
				$req_list[$i] = [
					'method' => $method,
					'params' => $params,
				];
			}
			$resp = self::batch($req_list);
			foreach ($resp['result'] as $step_list) {
				if ($sub_array) {
					$step_list = $step_list[$sub_array];
				}
				if (is_array($step_list)) {
					foreach ($step_list as $item) {
						if (!$limit || $i < $limit) {
							$list[] = $item;
							$i++;
						}
					}
				}
			}
		}
		return empty($list)?$resp:$list;
	}


	/**
	 * Send request on background
	 */

	public static function sendBgrRequest($uri, $data)
	{
		$app_info = self::getAppInfo();
		$site = $app_info['site'];
		$url_info = parse_url($site);
		$is_https = $url_info['scheme'] == 'https' ? true : false;
		$server = $url_info['host'];
		$query_url = ($is_https ? 'https://' : 'http://') . $server . $uri;
		$query_data = http_build_query($data);
		if ($server) {
			$success = false;
			for ($i = 1; $i <= 3 && !$success; $i++) {
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_POST => 1,
					CURLOPT_HEADER => 0,
					CURLOPT_RETURNTRANSFER => 1,
					CURLOPT_SSL_VERIFYPEER => 0,
					CURLOPT_URL => $query_url,
					CURLOPT_POSTFIELDS => $query_data,
					CURLOPT_FRESH_CONNECT => true,
					CURLOPT_TIMEOUT => 1 * $i,
				));
				curl_exec($curl);
				$info = curl_getinfo($curl);
				$success = (int)$info['http_code'] < 300;
				LocalFwink::Log('(Rest::sendBgrRequest) response ' . (!$success ? 'failure' : 'success'));
				if (!$success) {
					LocalFwink::Log('(Rest::sendBgrRequest) info ' . print_r($info, true));
				}
				curl_close($curl);
			}
		}
	}

	/*
	 * $res = $CB24->method($_REQUEST, 'crm.contact.list.json', array(
		"select" => array("ID", "LAST_NAME", "NAME"),
		//		"filter" => array("ID" => $arContactsID),
			)
		);
	 */
	public static function debugB24query($auth, $method, $params)
	{
		// выполнить метод rest api
		$c = curl_init('http://' . $auth['DOMAIN'] . '/rest/' . $method);
		$params["auth"] = $auth['AUTH_ID'];
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLOPT_POST, true);
		curl_setopt($c, CURLOPT_POSTFIELDS, http_build_query($params));
		$response = curl_exec($c);
		if (curl_errno($c)) {
			$error_msg = curl_error($c);
			print_r($error_msg);
		}
//		var_dump($response);
//		$response=json_decode($response,true);

		return $response;

	}

}
