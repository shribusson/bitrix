<?php


namespace Local\Fwink;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Local\Fwink\Settings;

/**
 * Добавить комментарий к задачи в портале Битрикс24
 *
 * Class AddMessageToBitrix24Task
 */
class Applications
{
    /**
     * @var string Путь к настройкам приложения. Файл не должен находится в корне сайта.
     */
    //private static $config = __DIR__ . '/../bx24.auth';
	public static $config = __DIR__ . '/../bx24.auth';

    /**
     * @var string Ключ шифрования
     */
    private static $safeKey;

	static $site = false;
	static $portal = false;
	static $app_id = false;
	static $secret = false;
	public static $arLogSync;
	static $debug=true;



		/**
	 * @return string
	 */
	public static function getAppInfo()
	{
		$uniqpatch=$_REQUEST['DOMAIN'];
		if(!empty($uniqpatch)){
			self::$config= __DIR__ . "/../bx24_$uniqpatch.auth";;
		}

		if (!file_exists(self::$config)) {
			return [];
		}
		/*
		 *  [B24_APPLICATION_ID] => local.6011536b7ba593.13415265
    		[B24_APPLICATION_SECRET] => k6ZsfwQsD3ND2A38dHEiJUwFVpSQEYncaA0a9sJIVXOPUrrda6
    		[B24_APPLICATION_SCOPE] => Array
                (
                    [0] => task
                )
            [B24_REDIRECT_URI] => https://bitrix24dev.softmonster.ru/about/externalsitedef
            [DOMAIN] => dev.blaywillie.keenetic.name
            [MEMBER_ID] => ba980428026d6379a5758aaa287bf49a
            [AUTH_ID] => 7e6211600051babc0050cf8d0000000100000354d3cdc23b61138fc22dfe23f3add154
            [REFRESH_ID] => 6ee138600051babc0050cf8d0000000100000392ffa776dcbe473166564804c09969bb
            [KEY] => def502003b1958f0e722007ad276a70437056751ef653547b6695a5137227292edd055c1d125662bb23367e3ed77c8cff631b430238b8506c77449a739c1403324083571705a4d63a7a4c3579b7ab1db9ba449eb228175f822a3b0ff5c258cb2d277e4dc377bc76fd86619eaf149acfda12066a34d3449a593d8e9b19ce57259086114b984574c41afcd7e142bf2dde95c56083ce55882fb7e176c51a4690acfdd440642a315c539b59f0643e0d489b3d30a16e7cb6ebfee1b47ab71420e2e64b3edcb
            [PRIVATE_KEY] => def00000e1091a7ce9926ef86ad6458e64701413869605fb3bf54fc77471f62928ffa5598b36c7a60c6dfc1bd577af4e851009a8e2166216a865ab1243b40285bb62369b
		 * */
		//Получить настройки приложения
		$params = file_get_contents(self::$config);
		$info=json_decode($params, true);
		if (self::$site === false) {
			self::$site = $info['DOMAIN'];
		}
		if (self::$portal === false) {
			self::$portal = $info['DOMAIN'];
		}
		if (self::$app_id === false) {
			self::$app_id = $info['B24_APPLICATION_ID'];
		}
		if (self::$secret === false) {
			self::$secret = $info['B24_APPLICATION_SECRET'];
		}
		if (self::$site && self::$portal && self::$app_id && self::$secret) {
			/*$info = [
				'site' => self::$site,
				'portal' => self::$portal,
				'app_id' => self::$app_id,
				'secret' => self::$secret,
			];*/
		}
		return $info;//return self::$config;
	}

	/**

	 */
	public static function run($objRequest,$arParam,$strType,$configSource,$arLogSync)
	{
		self::$arLogSync=$arLogSync;
		self::$debug && self::$arLogSync[] = 'domain request:'.$_REQUEST['DOMAIN'];
		self::$debug && self::$arLogSync[] = '------------------------request+parameter---------';
		self::$debug && self::$arLogSync[] = $_REQUEST;
		self::$debug && self::$arLogSync[] = $arParam;
		self::$debug && self::$arLogSync[] = 'member_id request:'.$objRequest->member_id;
		self::$debug && self::$arLogSync[] = 'member_id param:'.$arParam['member_id'];



		// install
		if (0 === count($arParam) || empty($objRequest->member_id) || empty($arParam['member_id']))
		{
			$params = [
				//Идентификатор приложения в портале (из настроек приложения в портале)
				'B24_APPLICATION_ID'     => '',
				//Секретное слово приложения в портале (из настроек приложения в портале)
				'B24_APPLICATION_SECRET' => '',
				//Требуемые для работы сущности портала (из настроек приложения в портале)
				'B24_APPLICATION_SCOPE'  => ['crm','placement','user','task','sonet_group','entity','department'],
				//URL приложения после установки (из настроек приложения в портале)
//				'B24_REDIRECT_URI'       => 'https://bitrix24dev.softmonster.ru/about/externalsitedef',
//				'B24_REDIRECT_URI'       => 'https://dev.blaywillie.keenetic.name'.$objRequest->getRequestedPageDirectory(),
				'B24_REDIRECT_URI'       => 'https://crt.blaywillie.keenetic.name'.$_SERVER['PHP_SELF'],

				//Домен портала
				'DOMAIN'                 => $_REQUEST['DOMAIN'],
				//Уникальный идентификатор приложения
				'MEMBER_ID'              => $_REQUEST['member_id'],
				//Токен авторизации
				'AUTH_ID'                => $_REQUEST['AUTH_ID'],
				//Токен обновления
				'REFRESH_ID'             => $_REQUEST['REFRESH_ID'],
			];
			switch ($configSource) {
				// Hidden fields
				case 'json':
					//$this->params = Local\Fwink\Applications::getAppInfo();
					if(!empty($arParam['B24_APPLICATION_ID'])){
						$params['B24_APPLICATION_ID']=$arParam['B24_APPLICATION_ID'];
					}
					if(!empty($arParam['B24_APPLICATION_SECRET'])){
						$params['B24_APPLICATION_SECRET']=$arParam['B24_APPLICATION_SECRET'];
					}
					if(!empty($arParam['B24_APPLICATION_SCOPE'])){
						$params['B24_APPLICATION_SCOPE']=$arParam['B24_APPLICATION_SCOPE'];
					}



					break;
				case 'mysql':
					//$credentials = Local\Fwink\Rest::getAuthInfo();
					//$arparam = Local\Fwink\Rest::getAppInfo();
					//$this->params = array_merge($credentials, $arparam);
					break;
				default:
					//$this->params = Local\Fwink\Applications::getAppInfo();
			}

			self::save($params);

		}

		if (self::check()) {
			//Загружаем настройки для вывода в интерфейсе Битрикс24 ключа доступа
			$params = self::load(); // by safari+docscreen

			self::$debug && self::$arLogSync[] = 'Приложение установлено.';
			self::$debug && self::$arLogSync[] = 'Добавьте в скрипт hook.php ключ доступа:';
			self::$debug && self::$arLogSync[] = $params['KEY'];
			self::$debug && self::writeLogDebugConnect(self::$arLogSync);
			return true;
		} else {
			self::$debug && self::$arLogSync[] = 'Приложение установлено c ошибками.';
			self::$debug && self::writeLogDebugConnect(self::$arLogSync);
			return false;
		}

	}

	public function writeLogDebugConnect($arLogSync)
	{
		if (empty($arLogSync)) {
			return;
		}

		//$pathLogfile = LocalFwink::MODULE_PATCH.LocalFwink::MODULE_ID;
		$pathLogfile = '/local/modules/local.fwink';
		file_put_contents(sprintf("%s%s/debug.json", $_SERVER['DOCUMENT_ROOT'], $pathLogfile)
			, "" . json_encode([
				'time' => date("H:i:s d-m-Y"),
				'domain' => $_REQUEST['DOMAIN']
				, 'inf' => $arLogSync
			])
		);
	}

    /**
     * Шифровать переменную
     *
     * @param string $var Переменная для шифрования
     *
     * @return string Шифрованная переменная
     *
     * @throws \Defuse\Crypto\Exception\BadFormatException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public static function encrypt($var)
    {
        return Crypto::encrypt($var, self::getKey());
    }

    /**
     * Дешифровать переменую
     *
     * @param string $var Переменная для дешифрации
     *
     * @return string Дешифрованная переменная
     *
     * @throws \Defuse\Crypto\Exception\BadFormatException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public static function decrypt($var)
    {
        return Crypto::decrypt($var, self::getKey());
    }

    /**
     * Получить ключ шифрования
     *
     * @return Key Ключ шифрования
     *
     * @throws \Defuse\Crypto\Exception\BadFormatException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public static function getKey()
    {
        if (null === self::$safeKey) {
            $params = self::load();

            //Получить ключ шифрования в бинарно-безопасном виде
            if (null === $params || null === $params['PRIVATE_KEY'] || empty($params['PRIVATE_KEY'])) {
                self::$safeKey = Key::createNewRandomKey()->saveToAsciiSafeString();
            } else {
                self::$safeKey = $params['PRIVATE_KEY'];
            }
        }

        return Key::loadFromAsciiSafeString(self::$safeKey);
    }

    /**
     * Получить объект для работы с Bitrix24
     *
     * @param array $params Параметры для работы с Битрикс24
     *
     * @return \Bitrix24\Bitrix24 Объект для работы с Битрикс24
     *
     * @throws \Bitrix24\Bitrix24Exception
     * @throws \Defuse\Crypto\Exception\BadFormatException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public static function getBX24Instance(array $params)
    {
        $bx24 = new \Bitrix24\Bitrix24(false);

        $bx24->setApplicationScope($params['B24_APPLICATION_SCOPE']);
        $bx24->setApplicationId($params['B24_APPLICATION_ID']);
        $bx24->setApplicationSecret($params['B24_APPLICATION_SECRET']);
        $bx24->setRedirectUri($params['B24_REDIRECT_URI']);
        $bx24->setDomain($params['DOMAIN']);
        $bx24->setMemberId($params['MEMBER_ID']);
        $bx24->setAccessToken($params['AUTH_ID']);
        $bx24->setRefreshToken($params['REFRESH_ID']);

        //Если время жизни токенов истекло
        if ($bx24->isAccessTokenExpire()) {
            //ПОлучитть новый токен доступа
            $temp = $bx24->getNewAccessToken();
            //Обновить токены в объекте
            $params['AUTH_ID'] = $temp['access_token'];
            $params['REFRESH_ID'] = $temp['refresh_token'];
            $bx24->setAccessToken($params['AUTH_ID']);
            $bx24->setRefreshToken($params['REFRESH_ID']);
            //Сохранить обновленные токены
            self::save($params);
        }

        return $bx24;
    }

    /**
     * Добавить комментарий к задаче
     *
     * @param \Bitrix24\Bitrix24 $bx24    Объект для работы с Битрикс24
     * @param    int             $task    Идентификатор задачи
     * @param    string          $message Комментарий
     *
     * @return string
     */
    public static function add(\Bitrix24\Bitrix24 $bx24, $task, $message)
    {
        $str = '';

        try {
            //Проверить есть ли такая задача на портале
            $bx24->call(
                'task.item.getdata',
                [
                    'TASKID' => $task
                ]
            );

            $str .= 'Задача #' . $task . ' на портале ' . $bx24->getDomain() . ' найдена' . PHP_EOL;

            //Добавить комментарий к задаче
            $bx24->call(
                'task.commentitem.add',
                [
                    'TASKID' => $task,
                    'FIELDS' => [
                        'POST_MESSAGE' => $message
                    ]
                ]
            );

            $str .= 'Комментарий к задаче успешно добавлен';
        } catch (Exception $e) {
            $str .= 'Ошибка при добавлении комментация к задаче';
        }

        return $str;
    }

    /**
     * Сохранить настройки в конфигурационный файл
     *
     * @param array $params Настройки
     *
     * @return bool
     *
     * @throws \Defuse\Crypto\Exception\BadFormatException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public static function save(array $params)
    {
		$uniqpatch=!empty($GLOBALS['FWINK']['DOMAIN'])?$GLOBALS['FWINK']['DOMAIN']:$_REQUEST['DOMAIN'];
		if(!empty($uniqpatch)){
			self::$config= __DIR__ . "/../bx24_$uniqpatch.auth";;
		}
        //Ключ для доступа к приложению для добавления комментария
        $params['KEY'] = self::encrypt($params['B24_APPLICATION_ID'] . $params['MEMBER_ID'] . $params['B24_APPLICATION_SECRET']);
        //Ключ шифрования
        $params['PRIVATE_KEY'] = self::$safeKey;
        //Сохраняем данные в файл конфигурации
		$result = json_encode($params, JSON_UNESCAPED_UNICODE);
        return file_put_contents(self::$config, $result) > 0;
    }

    /**
     * Получить настройки из конфигурационного файла
     *
     * @return array Настройки
     */
    public static function load()
    {
		$uniqpatch=!empty($_REQUEST['DOMAIN'])?$_REQUEST['DOMAIN']:$GLOBALS['FWINK']['DOMAIN'];

		if(!empty($uniqpatch)){
			self::$config= __DIR__ . "/../bx24_$uniqpatch.auth";;
		}

        if (!file_exists(self::$config)) {
            return [];
        }
        //Получить настройки приложения
        $params = file_get_contents(self::$config);

		$arparams=json_decode($params, true);
		return $arparams;
    }

    /**
     * Проверка, что приложение установлено из заданого портала Битрикс24.
     *
     * @return bool
     */
    public static function check()
    {
        try {
            $params = self::load();
            $bx24 = self::getBX24Instance($params);

            $result = $bx24->call('app.info');
			self::$debug && self::$arLogSync[] = "bx24->call['result']['CODE'] === params['B24_APPLICATION_ID']----";
			self::$debug && self::$arLogSync[] = ($result['result']['CODE'] === $params['B24_APPLICATION_ID'])?'yes':'no, '.$result['result']['CODE'].' '.$params['B24_APPLICATION_ID'];
			self::$debug && self::$arLogSync[] = "----------------------------";
			self::$debug && self::$arLogSync[] ='result_c='.($result['result']['CODE']);
			self::$debug && self::$arLogSync[] ='params_incom_d='.($params['B24_APPLICATION_ID']);
			/*
			 * at moment call BX24 instance update
			 *
			 * if($params['AUTH_ID']<>$bx24->getAccessToken()
				&&!empty($bx24->getAccessToken())){
				$params['AUTH_ID']=$bx24->getAccessToken();
			}
			if($params['REFRESH_ID']<>$bx24->getRefreshToken()
				&&!empty($bx24->getRefreshToken())){
				$params['REFRESH_ID']=$bx24->getRefreshToken();
			}
			$sucesswrite = \Local\Fwink\Applications::save($params);*/
			self::$debug && self::$arLogSync[] ='hide it new AUTH_ID'.($bx24->getAccessToken());
			self::$debug && self::$arLogSync[] ='hide it new REFRESH_ID'.($bx24->getRefreshToken());

            return $result['result']['CODE'] === $params['B24_APPLICATION_ID'];
        } catch (\Exception $e) {
			echo $e->getMessage();
			self::$debug && self::$arLogSync[] = $e->getMessage();
			self::$debug && self::$arLogSync[] = $e->getFile().':'.$e->getLine();
			return false;
        }
    }
}
