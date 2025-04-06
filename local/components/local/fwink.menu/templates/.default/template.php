<?
/** @noinspection PhpIncludeInspection */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
 * @var $component
 */
$whichdomainsendedrequest=$GLOBALS['FWINK']['DOMAIN'];
$wdsr=strrev($whichdomainsendedrequest);
$randstring=substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'),1,4);
$enc=\Local\Fwink\SimpleEncDecriptStringToNumber::encrypt($whichdomainsendedrequest).$randstring;
//$dec=\Local\Fwink\SimpleEncDecriptStringToNumber::decrypt($enc);
/*array (
  'mode' => 'pages',
  'page' => 'companyblock',
  'link_utm' => '100101118046098108097121119105108108105101046107101101110101116105099046110097109101EZYK',
  'DOMAIN' => 'dev.blaywillie.keenetic.name',
array (
  'mode' => 'page',
  'page' => 'post',
  'link_utm' => '100101118046098108097121119105108108105101046107101101110101116105099046110097109101CIKY',
  'DOMAIN' => 'dev.blaywillie.keenetic.name',
)
'staff'

)*/
$arResult=array (
	'MENU' =>
		array (
			0 =>
				array (
					'TEXT' => 'Структура компании',
					'URL' => '?mode=pages&page=companyblock&link_utm='.$enc,
					'ID' => 'COMPANY',
					'IS_ACTIVE' => false,
					'COUNTER' => 0,
					'COUNTER_ID' => 'COUNTER_COMPANY',
				),
			1 =>
				array (
					'TEXT' => 'Сотрудники',
					'URL' => '?mode=page&page=staff&link_utm='.$enc,
					'ID' => 'STAFF',
					/*'IS_ACTIVE' => true,  todo:make active interface*/
					'IS_ACTIVE' => false,
					'COUNTER' => 0,
					'COUNTER_ID' => 'COUNTER_STAFF',
				),
			2 =>
				array (
					'TEXT' => 'Должность/Пост',
					'URL' => '?mode=page&page=post&link_utm='.$enc,
					'ID' => 'POST',
					'IS_ACTIVE' => false,
					'COUNTER' => 0,
					'COUNTER_ID' => 'COUNTER_STAFF',
				),
			/*3 =>
				array (
					'TEXT' => '...',
					'URL' => '?mode=page&page=list&link_utm='.$enc,
					'ID' => 'LIST',
					'IS_ACTIVE' => false,
					'COUNTER' => 0,
					'COUNTER_ID' => 'COUNTER_LIST',
				),*/
		),
);

$strings=$GLOBALS['FWINK']['requestURL']['page'];
foreach($arResult['MENU'] as $key=>$value){
	if ((stripos($value['URL'], $strings)) !== false)
	{
		$arResult['MENU'][$key]['IS_ACTIVE']=true;
	}
}

$APPLICATION->IncludeComponent(
    'local:fwink.interface.buttons',
//	'bitrix:main.interface.buttons',
    '',
    [
        'ID' => 'MENU_TICKET',
        'ITEMS' => $arResult['MENU'],
        'DISABLE_SETTINGS' => true
    ],
    $component
);
