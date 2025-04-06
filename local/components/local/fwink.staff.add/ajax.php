<?
/** @noinspection PhpCSValidationInspection */
/** @noinspection PhpIncludeInspection */
define('STOP_STATISTICS', true);
define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC', 'Y');
define('DisableEventsCheck', true);
define('BX_SECURITY_SHOW_MESSAGE', true);

/*
 * custom for form
 *
 * */
if (empty($_REQUEST['NAME']) and !empty($_REQUEST['GROUP_NAME'])){
	$_REQUEST['NAME']=$_REQUEST['GROUP_NAME'];
}

$authToken = isset($_REQUEST['auth']) ? $_REQUEST['auth'] : '';
if($authToken !== '')
{
	define('NOT_CHECK_PERMISSIONS', true);
}
define("NOT_CHECK_PERMISSIONS", true);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

use Bitrix\Main\ArgumentTypeException;
use Bitrix\Main\Security\Sign\BadSignatureException;
use Bitrix\Main\SystemException;
use Local\Fwink\Tables\PortalTable;
use Bitrix\Main\Loader;
if (!class_exists('LocalFwink')) {
	Loader::includeModule('local.fwink');
}

$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$request->addFilter(new \Bitrix\Main\Web\PostDecodeFilter);
$boolchekSign=false;
$signer = new \Bitrix\Main\Security\Sign\Signer;
try {
	if(!empty($request->get('signedParamsString'))){
		$params = $signer->unsign($request->get('signedParamsString'), 'local.fwink.staff.add');
	}else{
		$params = $signer->unsign($request->get('signedParams'), 'local.fwink.staff.add');
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
	if (empty($requestURL['DOMAIN'])) {
		$whichdomainsendedrequest = (new Local\Fwink\SimpleEncDecriptStringToNumber)->decrypt($requestURL['link_utm']);
		$requestURL['DOMAIN'] = $whichdomainsendedrequest;
	}
}
$thisrequestDomainURL = !empty($requestURL['DOMAIN']) ? $requestURL['DOMAIN'] : $_SERVER['REMOTE_ADDR'];

//-------------start for identifying ID_PORTL
$_SERVER_SERVER_NAME = $_SERVER['SERVER_NAME'];
if (in_array($_SERVER['SERVER_NAME'], [
	'192.168.1.106',
	'192.168.0.107','192.168.1.1',
	'dev.blaywillie.keenetic.name',
])) {
	$_SERVER_SERVER_NAME = 'dev.blaywillie.keenetic.name';
}
if (in_array($thisrequestDomainURL, [
	'192.168.1.106',
	'192.168.0.107','192.168.1.1',
	'dev.blaywillie.keenetic.name',
])) {
	$thisrequestDomainURL = 'dev.blaywillie.keenetic.name';
}
$idPortal = PortalTable::query()->setSelect([
	// "*"
	'ID'
])->setFilter([
	"source" => $_SERVER_SERVER_NAME,
	"consumer" => $thisrequestDomainURL
])->fetch();$idPortal=is_array($idPortal)?$idPortal['ID']:0;
//------end for identifying ID_Portal


//$retf = PortalTable::getList($parameters)->fetchAll();

$GLOBALS['FWINK'] = [
	'DOMAIN' => $thisrequestDomainURL,
	'ID_PORTAL' => $idPortal,
	'requestURL' => $requestURL
];
$boolchekSign&&$GLOBALS['FWINK']['requestURL']['boolsignedParamsString']=true;


switch ($request->get('action')) {
    case 'add':
        $params['ACTION'] = 'add';
        break;
    case 'select':
        $params['ACTION'] = 'select';
        break;
    default:
        exit();
}

$result = $APPLICATION->IncludeComponent(
    'local:fwink.staff.add',
    '',
    $params
);

echo \Bitrix\Main\Web\Json::encode($result);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';
