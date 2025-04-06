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
 */

$this->addExternalJs('/local/js/local.fwink/dist/bundle.js');
//$this->addExternalCss('/local/js/local.fwink/dist/bundle.css');

Bitrix\Main\Page\Asset::getInstance()->addString('<link href="/local/js/local.fwink/dist/bundle.css?v='.time().'" rel="stylesheet" />');
$this->addExternalJs('//api.bitrix24.com/api/v1/?v=16082022');
\Bitrix\Main\UI\Extension::load("ui.dialogs.messagebox");
?>

<div id="app">
    <orgstructure sign="<?=\Local\Fwink\Service\TokenManager::getInstance()->getPublicToken()?>" sign-edit="<?=$arResult['SIGNED_PARAMS_EDIT']?>" access='<?=\Bitrix\Main\Web\Json::encode($arResult['access'])?>'></orgstructure>
</div>