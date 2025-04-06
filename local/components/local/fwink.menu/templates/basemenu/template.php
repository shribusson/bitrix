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
?>
<div class="main-buttons ismainuppermenu">
    <div class="main-buttons-inner-container">
        <? foreach ($arResult["MENU"] as $key => $arItem) {
            $itemClass = $arItem["CLASS"];
            if ($arItem["IS_ACTIVE"]) {
                if (isset($arParams["CLASS_ITEM_ACTIVE"]) && mb_strlen($arParams["CLASS_ITEM_ACTIVE"])) {
                    $itemClass .= " ".$arParams["CLASS_ITEM_ACTIVE"];
                } else {
                    $itemClass .= " main-buttons-item-active";
                }
            }
            ?>
            <div class="main-buttons-item <?=$itemClass?>" title="<?=isset($arItem["TITLE"]) ? $arItem["TITLE"] : ""?>">
                <? if (!$arItem["HTML"]) {?>
                    <? if (!empty($arItem["URL"])){?>
                        <a class="main-buttons-item-link<?=$arParams["CLASS_ITEM_LINK"] ? " ".$arParams["CLASS_ITEM_LINK"] : ""?>"
                        href="<?=$arItem["URL"]?>">
                    <?} else {?>
                        <span class="main-buttons-item-link<?=$arParams["CLASS_ITEM_LINK"] ? " ".$arParams["CLASS_ITEM_LINK"] : ""?>">
                    <?}?>

                        <span class="main-buttons-item-icon<?=$arParams["CLASS_ITEM_ICON"] ? " ".$arParams["CLASS_ITEM_ICON"] : ""?>"></span>
                        <span class="main-buttons-item-text<?=$arParams["CLASS_ITEM_TEXT"] ? " ".$arParams["CLASS_ITEM_TEXT"] : ""?>">
                            <span class="main-buttons-item-text-title"><?=$arItem["TEXT"]?></span>
                            <span class="main-buttons-item-text-marker"></span>
                        </span>
                    <? if (!empty($arItem["URL"])) {?>
                        </a>
                    <?} else {?>
                        </span>
                    <?}?>

                    <? if ($arItem["SUB_LINK"]) {?>
                        <a class="main-buttons-item-sublink<?=" ".$arItem["SUB_LINK"]["CLASS"]?>" href="<?=$arItem["SUB_LINK"]["URL"]?>"></a>
                    <?}?>
                <?} else {?>
                    <?=$arItem["HTML"]?>
                <?}?>
            </div>
        <?}?>
    </div>
</div>
