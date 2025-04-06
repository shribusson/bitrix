<?
/** @noinspection PhpCSValidationInspection */
/** @noinspection PhpIncludeInspection */
define('STOP_STATISTICS', true);
define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC', 'Y');
define('DisableEventsCheck', true);
define('BX_SECURITY_SHOW_MESSAGE', true);

$authToken = isset($_REQUEST['auth']) ? $_REQUEST['auth'] : '';
if($authToken !== '')
{
	define('NOT_CHECK_PERMISSIONS', true);
}
define("NOT_CHECK_PERMISSIONS", true);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
if (!class_exists('LocalFwink')) {
	\Bitrix\Main\Loader::includeModule('local.fwink');
}
use Bitrix\Main\ArgumentTypeException;
use Bitrix\Main\Security\Sign\BadSignatureException;
use Bitrix\Main\Security\Sign\Signer;
use Bitrix\Main\SystemException;
use Bitrix\Main\Web\Json;
use Bitrix\Main\Web\PostDecodeFilter;
use Local\Fwink\Rest;
use Local\Fwink\Tables\PortalTable;
use Bitrix\Main\Loader;
if (!class_exists('LocalFwink')) {
	Loader::includeModule('local.fwink');
}

$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$request->addFilter(new PostDecodeFilter());

$boolchekSign=false;
$signer = new Signer();
try {
    $params = $signer->unsign($request->get('signedParamsString'), 'local.fwink.staff.detail');
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

//$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
//echo "<pre>"; print_r ($cur_ppl); echo "</pre>";
// $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
//echo "<pre>"; print_r ($cur_deal); echo "</pre>";
$requestURL = $request->getValues();

$customrequestURL = json_decode($request->getValues()['PLACEMENT_OPTIONS'], true);

if (!empty($customrequestURL)) {
	$requestURL = array_merge(
		$requestURL
		, $customrequestURL
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
    case 'list':
        $params['ACTION'] = 'list';
        break;
    case 'update':
        $params['ACTION'] = 'update';
        break;
    case 'updatePassword':
        $params['ACTION'] = 'updatePassword';
        break;
    case 'delete':
        $params['ACTION'] = 'delete';
        break;
    default:
        exit();
}

$result = $APPLICATION->IncludeComponent(
    'local:fwink.staff.detail',
    '',
    $params
);

echo Json::encode($result);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';
