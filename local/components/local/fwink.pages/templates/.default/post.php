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

$APPLICATION->SetTitle(Loc::getMessage('LOCAL_TEMPLATE_STAFF_TITLE'));

//$this->SetViewTarget('above_pagetitle', 100);
$APPLICATION->IncludeComponent(
    'local:fwink.menu',
    'basemenu',
    [
        'SEF_FOLDER' => $arParams['SEF_FOLDER']
    ],
    $component
);
?>

<?
$APPLICATION->IncludeComponent(
	'local:fwink.staff.listpost',
	'',
	[
		'GRID_ID' => 'POST_LIST',
		'URL_TEMPLATES' => $arResult['URL_TEMPLATES'],
		'SEF_FOLDER' => $arResult['SEF_FOLDER']
	],
	$this->getComponent(),
	['HIDE_ICONS' => 'Y']
);


