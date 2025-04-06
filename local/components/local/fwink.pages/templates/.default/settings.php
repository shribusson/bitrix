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

$APPLICATION->IncludeComponent(
    'local:fwink.menu',
    'basemenu',
    [
        'SEF_FOLDER' => $arParams['SEF_FOLDER']
    ],
    $component
);
?>

<?php
$APPLICATION->IncludeComponent('local:fwink.settings','',[], false);