<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * Bitrix vars
 *
 * @global CMain $APPLICATION
 * @global CUser $USER
 * @global CDatabase $DB
 * @global CUserTypeManager $USER_FIELD_MANAGER
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @var $component
 */

use Bitrix\Main\Localization\Loc;
use Local\Fwink\Helpers\Folder as HelpersFolder;

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$requestURL = $request->getValues();

$customrequestURL=json_decode($request->getValues()['PLACEMENT_OPTIONS'],true);

if(is_string($customrequestURL)){
	$url = parse_url($customrequestURL);
	$urls_parsed=($url['query']);
	$split_parameters = explode('&', $urls_parsed);
	$split_parameters>0&&$customrequestURL=[];

	for($i = 0; $i < count($split_parameters); $i++) {
		$final_split = explode('=', $split_parameters[$i]);
		$customrequestURL[$final_split[0]] = $final_split[1];
	}
}
if(!empty($customrequestURL)&&is_array($customrequestURL)) {
	$requestURL = array_merge(
		$requestURL
		, $customrequestURL
	);
}
$componentNamechoise=$requestURL['page'];
$componentNames='nullcomponentexcept';
$componentParams = [];
switch ($componentNamechoise) {
	// Hidden fields
	case 'companyblock':
		$componentNames='local:fwink.companyblock.edit';
		$componentParams['MODE'] = 'ADD';
		break;
	case 'post':
		$componentNames='local:fwink.post.detail';
		$componentParams['MODE'] = 'ADD';
		break;
	case 'staff':
		$componentNames='local:fwink.staff.add';
		break;
	/*case 'install':
		break;
	case 'placement':
		break;*/
	default:
//		die('null component, throw is except'.__FILE__.__LINE__);
		break;
//		http_response_code(400);

}


if ($arResult['ACCESS']['create'] === false) {
    http_response_code(401);
    die();
}

$APPLICATION->IncludeComponent($componentNames
	, '.default'
	, $componentParams
	, false);
