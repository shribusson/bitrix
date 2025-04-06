<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

$module_id = 'local.fwink';

Loc::loadMessages($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/options.php");
Loc::loadMessages(__FILE__);

if ($APPLICATION->GetGroupRight($module_id) < "S") {
	$APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
}

\Bitrix\Main\Loader::includeModule($module_id);

$request = \Bitrix\Main\HttpApplication::getInstance()->getContext()->getRequest();

$listtip_resurs = function () {

	$arListType = ['B24', 'BUS'];

	$arType['empty'] = '--';
	foreach ($arListType as $arItem) {
		// $arType[$arItem["FIELD_NAME"]] = $arItem["EDIT_FORM_LABEL"];
		$arType[$arItem] = $arItem;
	}
	return $arType;
};


$localPath = $_SERVER["DOCUMENT_ROOT"] . '/local/modules/' . $module_id;
/*		$dirIterator = new RecursiveDirectoryIterator($localPath, RecursiveDirectoryIterator::SKIP_DOTS);
		$iterator = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::SELF_FIRST);

		foreach ($iterator as $object){
			// $destPath = $rootPath.DIRECTORY_SEPARATOR.$iterator->getSubPathName();
			// ($object->isDir()) ? mkdir($destPath) : copy($object, $destPath);
			print_r($object);
		}*/

$files = scandir($localPath);

foreach ($files as $key => $value) {
	if ((stripos($value, '.auth')) !== false && (stripos($value, '.json')) === false) {
		$path = realpath($localPath . DIRECTORY_SEPARATOR . $value);
		if (!is_dir($path)) {
			$results[] = $path;
		} else if ($value != "." && $value != "..") {
		}
	}
}

$listtip_resurs = function () {

	$arListType = ['B24', 'BUS'];
	$arType['empty'] = '--';
	foreach ($arListType as $arItem) {
		// $arType[$arItem["FIELD_NAME"]] = $arItem["EDIT_FORM_LABEL"];
		$arType[$arItem] = $arItem;
	}
	return $arType;
	// return [];
};

$arInf = [];
foreach ($results as $fl) {
	$flinf = file_get_contents($fl);
	$objinf = json_decode($flinf, true);
	$arInf[$objinf['DOMAIN']] = $objinf;
}

foreach ($arInf as $k => $v) {
	if (empty($v['DOMAIN'])) {
		continue;
	}
	$araddTab[] = array(
		'DIV' => $k,
		'TAB' => $k,
		'OPTIONS' => array(
			array($k . 'APPLICATION_RESOURCE_TYPE', 'tip resurs2', '', array('selectbox', $listtip_resurs())),
			array($k . 'B24_REDIRECT_URI', 'B24_REDIRECT_URI', $v['B24_REDIRECT_URI'], array('text', 100)),
			array($k . 'DOMAIN', 'app url2 DOMAIN', $v['DOMAIN'], array('text', 100)),
			array($k . 'B24_APPLICATION_ID', 'B24_APPLICATION_ID', $v['B24_APPLICATION_ID'], array('text', 100)),
			array($k . 'B24_APPLICATION_SECRET', 'B24_APPLICATION_SECRET', $v['B24_APPLICATION_SECRET'], array('text', 100)),
			array($k . 'B24_APPLICATION_SCOPE', 'B24_APPLICATION_SCOPE', $v['B24_APPLICATION_SCOPE'], array('text', 100)),),
	);
}


$aTabsDef = array(
	array(
		'DIV' => 'OSNOVNOE',
		'TAB' => Loc::getMessage('LOCAL_FWINK_TAB_OSNOVNOE'),
		'OPTIONS' => array(
			array('app_id', Loc::getMessage('LOCAL_FWINK_OPTION_APP_ID_TITLE'), '', array('text', 0))
        )
	),
	array(
		'DIV' => 'LOGI',
		'TAB' => Loc::getMessage('LOCAL_FWINK_TAB_LOGI'),
		'OPTIONS' => array(
			array('filelog', Loc::getMessage('LOCAL_FWINK_OPTION_LOGI_RAZRESHIT_TITLE'), '', array('checkbox', "Y")),),
	),
	array(
		'DIV' => 'CONNECT',
		'TAB' => 'CONNECT',//Loc::getMessage('LOCAL_FWINK_TAB_AGENT1'),
		'OPTIONS' => array(
			array('tip_resurs1', Loc::getMessage('LOCAL_FWINK_OPTION_TIP_RESURS1_TITLE'), '', array('selectbox', $listtip_resurs())),
			array('back_url1', Loc::getMessage('LOCAL_FWINK_OPTION_BACK_URL1_TITLE'), '', array('text', 0)),
			array('app_url1', Loc::getMessage('LOCAL_FWINK_OPTION_APP_URL1_TITLE'), '', array('text', 0)),
			array('app_id1', Loc::getMessage('LOCAL_FWINK_OPTION_APP_ID1_TITLE'), '', array('text', 0)),
			array('app_secret1', Loc::getMessage('LOCAL_FWINK_OPTION_APP_SECRET1_TITLE'), '', array('text', 0)),
			array('app_store1', Loc::getMessage('LOCAL_FWINK_OPTION_APP_STORE1_TITLE'), '', array('text', 0)),),
	),
	array(
		'DIV' => 'INSERT',
	),
	array(
		"DIV" => "rights",
		"TAB" => Loc::getMessage("MAIN_TAB_RIGHTS"),
		"TITLE" => Loc::getMessage("MAIN_TAB_TITLE_RIGHTS"),
		"OPTIONS" => array()
	)
);
$aTabs = [];
foreach ($aTabsDef as $key => $val) {

	if ($val['DIV'] == 'INSERT') {
		foreach ($araddTab as $araddTabItem) {
			$aTabs[] = $araddTabItem;
		}
	} else {
		$aTabs[] = $val;
	}
}

#Сохранение

if ($request->isPost() && $request['Apply'] && check_bitrix_sessid()) {

	foreach ($aTabs as $aTab) {
		if ((stripos($aTab['DIV'], '.')) !== false)
			continue;

		foreach ($aTab['OPTIONS'] as $arOption) {
			if (!is_array($arOption))
				continue;

			if ($arOption['note'])
				continue;

			$optionName = $arOption[0];

			$optionValue = $request->getPost($optionName);

			if ($optionValue == 'empty') {
				Option::delete($module_id, array(
					"name" => $optionName
				));
			}
			if ($optionValue != 'empty') {
				Option::set($module_id, $optionName, is_array($optionValue) ? implode(",", $optionValue) : $optionValue);
			}


		}
	}
}

$tabControl = new CAdminTabControl('tabControl', $aTabs);

?>
<? $tabControl->Begin(); ?>
<form method='post'
      action='<? echo $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialcharsbx($request['mid']) ?>&amp;lang=<?= $request['lang'] ?>'
      name='local_fwink_settings'>

	<? foreach ($aTabs as $aTab):
		if ($aTab['OPTIONS']):?>
			<? $tabControl->BeginNextTab(); ?>
			<? __AdmSettingsDrawList($module_id, $aTab['OPTIONS']); ?>

		<? endif;
	endforeach; ?>

	<?
	$tabControl->BeginNextTab();

	require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/admin/group_rights.php");

	$tabControl->Buttons(); ?>

    <input type="submit" name="Apply" value="<? echo GetMessage('MAIN_SAVE') ?>">
    <input type="hidden" name="Update" value="Y">
    <input type="reset" name="reset" value="<? echo GetMessage('MAIN_RESET') ?>">
	<?= bitrix_sessid_post(); ?>
</form>
<? $tabControl->End(); ?>

<script type="text/javascript">

(function (window) {
console.log('..init options js');




var stepperBlock = BX.findChildByClassName(BX('adm-workarea'),'adm-detail-content-btns');
BX.append(
					BX.create('input', {
						attrs: {
							'type': 'submit',
							// 'className': 'crm-volume-header-link',
							'value': 'Add resource'
						},
						text: 'Add resource',
						events: {"click": function(e){
									console.log('..exec func');
										return BX.PreventDefault(e);
									}}
					}),
					stepperBlock
				);



})(window);

</script>
