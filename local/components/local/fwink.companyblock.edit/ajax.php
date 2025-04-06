<?
/** @noinspection PhpCSValidationInspection */
/** @noinspection PhpIncludeInspection */
define('STOP_STATISTICS', true);
define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC', 'Y');
define('DisableEventsCheck', true);
define('NO_AGENT_CHECK', true);
define('BX_SECURITY_SHOW_MESSAGE', true);

$authToken = isset($_REQUEST['auth']) ? $_REQUEST['auth'] : '';
if($authToken !== '')
{
	define('NOT_CHECK_PERMISSIONS', true);
}
define("NOT_CHECK_PERMISSIONS", true);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
$_debugMode = false;
use Bitrix\Main\ArgumentTypeException;
use Bitrix\Main\Security\Sign\BadSignatureException;
use Bitrix\Main\SystemException;
use Local\Fwink\Service\TokenManager;
use Local\Fwink\Tables\BlocksTable as WorkTable;
use Local\Fwink\Tables\PortalTable;
use Bitrix\Main\Loader;
if (!class_exists('LocalFwink')) {
	Loader::includeModule('local.fwink');
}


$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$request->addFilter(new \Bitrix\Main\Web\PostDecodeFilter);

if (!class_exists('LocalFwink')) {
	\Bitrix\Main\Loader::includeModule('local.fwink');
}

$requestURL = $request->getValues();

$customrequestURL = json_decode($request->getValues()['PLACEMENT_OPTIONS'], true);

if (!empty($customrequestURL)) {
	$requestURL = array_merge(
		$requestURL
		, $customrequestURL
	);
}
if (count($_REQUEST)>0) {
	$requestURL = array_merge(
		$requestURL
		, $_REQUEST
	);
}

if (!empty($requestURL['link_utm'])) {
    $tokenManager = TokenManager::getInstance();
    if($tokenManager->decode($requestURL['link_utm'])) {
        $requestURL['DOMAIN'] = $tokenManager->getDomain();
        $requestURL['AUTH_ID'] = $tokenManager->getAuth();
		/*$request->set('DOMAIN', $tokenManager->getDomain());
$request->set('AUTH_ID', $tokenManager->getAuth());*/
		$requestValues = $request->getValues();
		$requestValues['DOMAIN'] = $tokenManager->getDomain();
		$requestValues['AUTH_ID'] = $tokenManager->getAuth();
		$request->set($requestValues);
    }
}
if (!empty($requestURL['sign'])) {
    $tokenManager = TokenManager::getInstance();
    if($tokenManager->decode($requestURL['sign'])) {
        $requestURL['DOMAIN'] = $tokenManager->getDomain();
        $requestURL['AUTH_ID'] = $tokenManager->getAuth();
		/*$request->set('DOMAIN', $tokenManager->getDomain());
$request->set('AUTH_ID', $tokenManager->getAuth());*/
		$requestValues = $request->getValues();
		$requestValues['DOMAIN'] = $tokenManager->getDomain();
		$requestValues['AUTH_ID'] = $tokenManager->getAuth();
		$request->set($requestValues);
    }
}
$thisrequestDomainURL = !empty($requestURL['DOMAIN']) ? $requestURL['DOMAIN'] : $_SERVER['REMOTE_ADDR'];

//-------------start for identifying ID_PORTL
$_SERVER_SERVER_NAME = $_SERVER['SERVER_NAME'];
$idPortal = PortalTable::query()
    ->setSelect(['ID'])
    ->setFilter([
	    "source" => $_SERVER_SERVER_NAME,
	    "consumer" => $thisrequestDomainURL
    ])->fetch();
$idPortal=is_array($idPortal)?$idPortal['ID']:0;
if((int)$idPortal <= 0) {
    http_response_code(400);die();
}
//------end for identifying ID_Portal

$boolchekSign=false;
$signer = new \Bitrix\Main\Security\Sign\Signer;
try {
    if(!empty($request->get('signedParamsString'))){
        $params = $signer->unsign($request->get('signedParamsString'), 'local.fwink.companyblock.edit');
    }else{
        $params = $signer->unsign($request->get('signedParams'), 'local.fwink.companyblock.edit');
    }
    $serialize = base64_decode($params);
    if (CheckSerializedData($serialize)) {
        $params = unserialize($serialize, ['allowed_classes' => false]);
    } else {
        throw new SystemException('Error Serialized');
    }
} catch (BadSignatureException $e) {
    die();
} catch (ArgumentTypeException $e) {
    die();
} catch (SystemException $e) {
    die();
}
$boolchekSign=true;


//$retf = PortalTable::getList($parameters)->fetchAll();

$GLOBALS['FWINK'] = [
	'DOMAIN' => $thisrequestDomainURL,
	'ID_PORTAL' => $idPortal,
	'requestURL' => $requestURL
];
$boolchekSign&&$GLOBALS['FWINK']['requestURL']['boolsignedParamsString']=true;

$userAuth = TokenManager::getInstance()->getAuth();

// todo: replace globals
$isAdmin = \Local\Fwink\Rest::execute('user.admin', [], !empty($userAuth) ? ['access_token' => $userAuth] : false);
$GLOBALS['FWINK']['admin'] = (bool)$isAdmin === true;


switch ($request->get('action')) {
    case 'add':
        $params['ACTION'] = 'add';
        break;
    case 'edit':
        $params['ACTION'] = 'edit';
        break;
    case 'delete':
        $params['ACTION'] = 'delete';
        break;
    case 'applyChildColors':
        $params['ACTION'] = 'applyChildColors';
        break;
    default:
        exit();
}

$result = $APPLICATION->IncludeComponent(
    'local:fwink.companyblock.edit',
    '',
    $params
);

echo \Bitrix\Main\Web\Json::encode($result);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';

