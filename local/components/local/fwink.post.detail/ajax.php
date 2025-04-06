<?
/** @noinspection PhpCSValidationInspection */
/** @noinspection PhpIncludeInspection */
define('STOP_STATISTICS', true);
define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC', 'Y');
define('DisableEventsCheck', true);
define('BX_SECURITY_SHOW_MESSAGE', true);
define("NOT_CHECK_PERMISSIONS", true);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

use Bitrix\Main\ArgumentTypeException;
use Bitrix\Main\Security\Sign\BadSignatureException;
use Bitrix\Main\Security\Sign\Signer;
use Bitrix\Main\SystemException;
use Bitrix\Main\Web\Json;
use Bitrix\Main\Web\PostDecodeFilter;
use Local\Fwink\Tables\PortalTable;
use Bitrix\Main\Loader;
use Local\Fwink\Tables\UserbypostTable as UsersPost;
use Local\Fwink\Tables\PostsTable;
use Local\Fwink\Service\TokenManager;

if (!class_exists('LocalFwink')) {
	Loader::includeModule('local.fwink');
}


$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$request->addFilter(new PostDecodeFilter());

$boolchekSign = false;
$signer = new Signer();
try {
	$params = $signer->unsign($request->get('signedParamsString'), 'local.fwink.post.detail');
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
$boolchekSign = true;

$requestURL = $request->getValues();

$customrequestURL = json_decode($request->getValues()['PLACEMENT_OPTIONS'], true);

if (!empty($customrequestURL)) {
	$requestURL = array_merge(
		$requestURL
		, $customrequestURL
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
$thisrequestDomainURL = !empty($requestURL['DOMAIN']) ? $requestURL['DOMAIN'] : $_SERVER['REMOTE_ADDR'];

//-------------start for identifying ID_PORTL
$_SERVER_SERVER_NAME = $_SERVER['SERVER_NAME'];
$idPortal = PortalTable::query()->setSelect([
	// "*"
	'ID'
])->setFilter([
	"source" => $_SERVER_SERVER_NAME,
	"consumer" => $thisrequestDomainURL
])->fetch();
$idPortal = is_array($idPortal) ? $idPortal['ID'] : 0;
//------end for identifying ID_Portal
if((int)$idPortal <= 0) {
    http_response_code(400);die();
}

//$retf = PortalTable::getList($parameters)->fetchAll();

$GLOBALS['FWINK'] = [
	'DOMAIN' => $thisrequestDomainURL,
	'ID_PORTAL' => $idPortal,
	'requestURL' => $requestURL
];
$boolchekSign && $GLOBALS['FWINK']['requestURL']['boolsignedParamsString'] = true;

$userAuth = TokenManager::getInstance()->getAuth();

// todo: replace globals
$isAdmin = \Local\Fwink\Rest::execute('user.admin', [], !empty($userAuth) ? ['access_token' => $userAuth] : false);
$GLOBALS['FWINK']['admin'] = (bool)$isAdmin === true;

$boolusedExternalObj = false;

switch ($request->get('action')) {
	case 'list':
		$params['ACTION'] = 'list';
		break;
	case 'update':
		$params['ACTION'] = 'update';
		$boolusedExternalObj = isset($_REQUEST['ID_STAFF']) ? true : false;
		$boolusedExternalObjSUPERVISOR = isset($_REQUEST['ID_SUPERVISOR_POST']) ? true : false;
		break;
    case 'add':
        $params['ACTION'] = 'add';
        break;
	case 'delete':
		$params['ACTION'] = 'delete';
		break;
	default:
		exit();
}

$arResult = []; //result.STATUS === 'SUCCESS'

$incomingIDpost = (int)$GLOBALS['FWINK']['requestURL']['ID'];
if ($boolusedExternalObj && (int)$incomingIDpost > 0) {
// todo: переделать всю эту х**ту от Руслана
	$incomingIDportal = $GLOBALS['FWINK']['ID_PORTAL'];
	$incomingIDstaff = $GLOBALS['FWINK']['requestURL']['ID_STAFF'];
	$arIDstaff = array_unique(explode(',', $incomingIDstaff));
    $arIDstaff = array_filter($arIDstaff, function($a) {
        return (int)$a > 0;
    });

	$parameters = [
		'select' => ['ID', 'ID_PORTAL', 'ID_STAFF', 'ID_POST'],
		'filter' => [
			'ID_PORTAL' => $incomingIDportal,
			'ID_POST' => $incomingIDpost,
		]
	];
	$arUserPostTable = UsersPost::getList($parameters)->fetchAll();

	$existIDstaff = array_map(function ($sel) {
		if ((int)$sel['ID_STAFF'] > 0) {
			return ($sel['ID_STAFF']);
		}
	}, $arUserPostTable);

	$insertions = array_values(array_diff($arIDstaff, $existIDstaff));
	$deletions = array_values(array_diff($existIDstaff, $arIDstaff));


	if (!empty($insertions)) {
		$arResult['insert'] = [
			'added' => [],
			'error' => []
		];
		$cnt = count($insertions);
		while ($cnt--) {
			if (true) {// (int)$insertions[$cnt]>0) {
				$add = [
					'ID_PORTAL' => $incomingIDportal,
					'ID_STAFF' => $insertions[$cnt],
					'ID_POST' => $incomingIDpost
				];
				$addResult = UsersPost::add($add);
				if ($addResult->isSuccess()) {
					$arResult['insert']['added'][$insertions[$cnt]] = $addResult->getId();
					$arResult['STATUS'] = 'SUCCESS';
				} else {
					if ($e = $GLOBALS["APPLICATION"]->GetException()) {
						$arResult['insert']['error'][$insertions[$cnt]] = ([$e->GetString()]);
						$arResult['STATUS'] = 'ERROR';
						$arResult['MESSAGE'][$insertions[$cnt]] = ([$e->GetString()]);
					} else {
						$arResult['insert']['error'][$insertions[$cnt]] = $addResult->getErrorMessages();
						$arResult['STATUS'] = 'ERROR';
						$arResult['MESSAGE'] = [$e->GetString()];
					}
				}
			}
		}
	} else {
		$arResult['insert'] = [
			'nophing staffs'
		];
	}

	if (!empty($deletions)) {
		$arResult['delete'] = [
			'delete' => [],
			'error' => []
		];
		$arIDs = UsersPost::getList([
			'select' => ['ID'],
			'filter' => [
                'ID_STAFF' => $deletions,
                'ID_POST' => $incomingIDpost,
                'ID_PORTAL' => $GLOBALS['FWINK']['ID_PORTAL']
            ]
		])->fetchAll();
		$arIDs = array_map(function ($id) {
			return ($id['ID']);
		}, $arIDs);
//		print_r($arIDs);

		$cnt = count($arIDs);
		while ($cnt--) {
			// $del=array("ID_STAFF" => (int) $deletions[$cnt]);

			$delResult = UsersPost::delete((int)$arIDs[$cnt]);
			if ($delResult->isSuccess()) {
				$arResult['STATUS'] = 'SUCCESS';
				$arResult['delete']['action'][$arIDs[$cnt]] = 'delete id in table :' . $arIDs[$cnt];
			} else {
				if ($e = $GLOBALS["APPLICATION"]->GetException()) {
					$arResult['delete']['error'][$arIDs[$cnt]] = ([$e->GetString()]);
					$arResult['STATUS'] = 'ERROR';
					$arResult['MESSAGE'] = [$e->GetString()];
				} else {
					$arResult['delete']['error'][$arIDs[$cnt]] = $addResult->getErrorMessages();
					$arResult['STATUS'] = 'ERROR';
					$arResult['MESSAGE'] = [$e->GetString()];
				}
			}
			// print_r($add);
		}
	} else {
		$arResult['delete'] = [
			'nophing staffs'
		];
	}
}


if($boolusedExternalObjSUPERVISOR){
	$incomingIDportal = $GLOBALS['FWINK']['ID_PORTAL'];
	$incomingIDpost = (int)$GLOBALS['FWINK']['requestURL']['ID'];
	$incomingIDpost = ($incomingIDpost < 1) ? 999 : $incomingIDpost;
	if (!class_exists('PostsDetailComponent')) {
		$classname=CBitrixComponent::includeComponentClass("local:fwink.post.detail");
	}
	$thComponentClass = new $classname;
	$resultthis=$thComponentClass->getSupervisor($incomingIDportal,$incomingIDpost);

	$arResult['SUPERVISOR']=$resultthis;
	$strBossName=$thComponentClass->getUserByRest([$resultthis['SHIEF_ID']]);
	$arResult['SUPERVISOR']['SHIEF_NAME']=trim($strBossName[0]['NAME'].' '.$strBossName[0]['LAST_NAME']);
	$arResult['SUPERVISOR']['SHIEF_IMG'] = $strBossName[0]['PERSONAL_PHOTO'] ? : '/local/apps/img/ui-user.svg';
}

$result = $APPLICATION->IncludeComponent(
	'local:fwink.post.detail',
	'',
	$params
);

$incomingIDpost = (int)$GLOBALS['FWINK']['requestURL']['ID'];
if($incomingIDpost>0) {
	$resultafterrecord['AFTERRECORD'] = PostsTable::query()
		->setSelect(['ID','NAME_POST', 'CKP_OF_POST', 'FUNCTION_OF_POST', 'ID_SUPERVISOR_POST', 'ID_SHIEF_POST_USERB24', 'ID_JOB_FOLDERB24'])
		->setFilter([
			'ID_PORTAL' => (int)$GLOBALS['FWINK']['ID_PORTAL'],
			'ID'=>$incomingIDpost
		])
		->setOrder(['NAME_POST' => 'asc'])
		->exec()->fetch();
}
$resultafterrecord['AFTERRECORD']['ID_JOB_FOLDERB24name']=$GLOBALS['FWINK']['requestURL']['ID_JOB_FOLDERB24name'];
$arResult = (array_merge(
	$arResult,
	$resultafterrecord,
	$result
));
//	$resultexec['RESULT']='sucess'; todo custom set be???

//here is make pull to Table
echo Json::encode($arResult);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';
