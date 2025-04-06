<?php

if (!class_exists('LocalFwink')) {
	\Bitrix\Main\Loader::includeModule('local.fwink');
}
// $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

$boolExcept=false;
if($GLOBALS['FWINK']['requestURL']['page']=='settings'){
	$boolExcept=true;
}



global $APPLICATION;
$APPLICATION->ShowHead();

CUtil::InitJSCore(['ajax']);

if (!$boolExcept) {
	CJSCore::Init(array('date', 'popup', 'ajax', 'tooltip', 'sidepanel'));


	CJSCore::Init(['window', 'core', 'jquery']);
	$_1979739807123 = Bitrix\Main\Page\Asset::getInstance();
	$_1979739807123->addJs('/bitrix/js/local.fwink/jquery-ui.js');
	$_1979739807123->addJs('/bitrix/js/local.fwink/mustache.js');
	$_1979739807123->addJs('/bitrix/js/local.fwink/main.js');
	$_1979739807123->addCss('/bitrix/css/local.fwink/themify-icons/themify-icons.css');
	$_1979739807123->addCss('/bitrix/css/local.fwink/grid.css');
	$_1979739807123->addCss('/bitrix/css/local.fwink/main.css');
}

$APPLICATION->IncludeComponent(
	'local:fwink.pages',
	'',
	[
		'COMPONENT_TEMPLATE' => '',
		'SEF_URL_TEMPLATES' => [
			'list' => '',
			'detail' => '#ELEMENT_ID#/',
			'add' => 'add/'
		],
		'SEF_MODE' => 'Y'
	]
);
