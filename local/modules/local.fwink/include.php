<?

use \Bitrix\Main\Config\Option,
	\Bitrix\Main\Localization\Loc,
	\Bitrix\Main\Page\Asset;
use Bitrix\Main\Error,
	Bitrix\Main\Result
	, Bitrix\Main\Request;
use iTrack\Custom\Exceptions\BitrixAppException;

use Local\Fwink\Page, Local\Fwink\Applications;

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Json;
use Local\Fwink\Rest;
use Local\Fwink\Tables\PortalTable;
use Local\Fwink\SimpleEncDecriptStringToNumber;

Loc::loadMessages(__FILE__);

if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/bitrix/vendor/autoload.php')) {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/vendor/autoload.php');
}

$eventManager = \Bitrix\Main\EventManager::getInstance();
$eventManager->addEventHandler('main', 'OnEpilog', ['\Local\Fwink\EventHandlers\Main','onEpilog']);

class LocalFwink
{
	const MODULE_ID = 'local.fwink';
	const MODULE_PATCH = '/local/modules/';
	public $request;
	public $result;
	public $response;
	public $params;
	public $mode;
	public $configSource='json';
	public $arLogSync=[];
	public $debug=true;

	/*
	 *
	 */
	public function __construct()
	{

	}

	public function exec()
	{
		$_debugMode = false;
		$request = Application::getInstance()->getContext()->getRequest();
		$requestURL = $request->getValues();

		$customrequestURL = json_decode($request->getValues()['PLACEMENT_OPTIONS'], true);
		if ($customrequestURL['debug'] == 'yes') {
			$_debugMode = true;
		}

		if (!empty($customrequestURL) && is_array($customrequestURL)) {
			$requestURL = array_merge(
				$requestURL
				, $customrequestURL
			);
		}

		if (!empty($requestURL['link_utm'])) {
			if (empty($requestURL['DOMAIN'])) {
				$whichdomainsendedrequest = (new SimpleEncDecriptStringToNumber)->decrypt($requestURL['link_utm']);
				$requestURL['DOMAIN'] = $whichdomainsendedrequest;
			}
		}

		if(!empty($requestURL['sign'])) {
			$tokenManager = \Local\Fwink\Service\TokenManager::getInstance();
			if($tokenManager->decode($requestURL['sign'])) {
				$requestURL['DOMAIN'] = $tokenManager->getDomain();
				$requestURL['AUTH_ID'] = $tokenManager->getAuth();

				$requestValues = $request->getValues();
				$requestValues['DOMAIN'] = $tokenManager->getDomain();
				$requestValues['AUTH_ID'] = $tokenManager->getAuth();
				$request->set($requestValues);
				/*$request->set('DOMAIN', $tokenManager->getDomain());
$request->set('AUTH_ID', $tokenManager->getAuth());*/
			}
		}

		$thisrequestDomainURL = !empty($requestURL['DOMAIN']) ? $requestURL['DOMAIN'] : $_SERVER['REMOTE_ADDR'];


//-------------start for identifying ID_PORTL
		$_SERVER_SERVER_NAME = $_SERVER['SERVER_NAME'];

		$idPortal = Local\Fwink\Tables\PortalTable::query()->setSelect([
			// "*"
			'ID'
		])->setFilter([
			"source" => $_SERVER_SERVER_NAME,
			"consumer" => $thisrequestDomainURL
		])->fetch();
		$idPortal = is_array($idPortal) ? $idPortal['ID'] : 0;
//------end for identifying ID_Portal

		$GLOBALS['FWINK'] = [
			'DOMAIN' => $thisrequestDomainURL,
			'ID_PORTAL' => is_array($idPortal) ? $idPortal[0]['ID'] : $idPortal,
			'requestURL' => $requestURL
		];

		if (empty($requestURL['DOMAIN'])) {
			if (!$requestURL['access'] == 'whateverdomaintokenfordemo') {
				http_response_code(400);
				exit();
			}
		}

		$profile = Rest::execute('profile', [], !empty($requestURL['AUTH_ID']) ? ['access_token' => $requestURL['AUTH_ID']] : false);
		$isAdmin = false;
		if(!empty($profile)) {
			$GLOBALS['FWINK']['profile'] = $profile; // todo: globals!!!
			$isAdmin = (bool)$profile['ADMIN'] === true;
		}
		$GLOBALS['FWINK']['admin'] = $isAdmin;
// $mode=$placement['mode']
		$mode = '';

		switch ($mode) {
			case 'pages':
				break;
			case 'settings':
				break;
			case 'event':
				break;
			case 'install':
				break;
			case 'placement':
				break;
			default:
				$app = new Local\Fwink\Pages($request);
				$app->exec();
				die();
		}
		http_response_code(400);
	}

	public static function Log($incoming)
	{
		$fLog=self::MODULE_PATCH.self::MODULE_ID.'/log.txt' ;
		$boolwriteLog = Option::get(self::MODULE_ID, "filelog");
		if ($boolwriteLog == 'Y') {
			file_put_contents($_SERVER['DOCUMENT_ROOT'] . $fLog, "\n". date('d.m.Y H:i:s') ."-------------\n", FILE_APPEND);
			file_put_contents($_SERVER['DOCUMENT_ROOT'] . $fLog, $incoming, FILE_APPEND);
		}
	}
}
?>
