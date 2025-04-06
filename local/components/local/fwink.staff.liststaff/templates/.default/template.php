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

use Bitrix\Main\Grid\Panel\Snippet;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;

$this->addExternalJs('//api.bitrix24.com/api/v1/?v=12082021');
?>

<div class="page-header">
    <div id="uiToolbarContainer" class="ui-toolbar">
        <div class="ui-toolbar-filter-box">
            <?
            $gridID = $arParams['~GRID_ID'] ?? '';
            if ($arParams['~HIDE_FILTER'] !== true && !Bitrix\Main\Grid\Context::isInternalRequest()) {
                $filterParams = $arParams['~FILTER_PARAMS'] ?? [];

                $APPLICATION->IncludeComponent(
                    'bitrix:main.ui.filter',
                    '',
                    [
                        'FILTER_ID' => $gridID,
                        'GRID_ID' => $gridID,
                        'FILTER' => $arResult['FILTER'],
                        'ENABLE_LIVE_SEARCH' => true,
                        'ENABLE_LABEL' => true
                    ],
                    false
                );
            }?>
        </div>
        <div class="ui-toolbar-right-buttons">
            <?php
            if($arResult['ACCESS']['create']){ ?>
            <button class="ui-btn ui-btn-primary ui-btn-icon-add"
                    onclick="BX.iTrack.Chart.StaffList.add()">
                <span class="ui-btn-text">Пригласить сотрудника</span>
            </button>
            <?php } ?>
        </div>
    </div>
</div>
<?php
$APPLICATION->IncludeComponent(
    'bitrix:main.ui.grid',
    'iframe_mode',
    [
        'GRID_ID' => $gridID,
        'HEADERS' => $arResult['HEADERS'] ?? [],
        'SORT' => $arResult['~SORT'] ?? [],
        'SORT_VARS' => $arParams['~SORT_VARS'] ?? [],
        'ROWS' => $arResult['ROWS'],
        'ROW_LAYOUT' => $arParams['~ROW_LAYOUT'] ?? [],
        'AJAX_MODE' => 'N',//$arParams['~AJAX_MODE'] ?: 'Y',
        'FORM_ID' => $arParams['~FORM_ID'] ?? '',
        'TAB_ID' => $arParams['~TAB_ID'] ?? '',
        'AJAX_ID' => $arParams['~AJAX_ID'] ?? '',
        'AJAX_OPTION_JUMP' => 'N',
        'AJAX_OPTION_HISTORY' => 'N',
        'PRESERVE_HISTORY' => $arParams['~PRESERVE_HISTORY'] ?? false,
        'MESSAGES' => $arParams['~MESSAGES'] ?? [],
        'NAV_OBJECT' => $arResult['NAV_OBJECT'],
        //'NAV_STRING' => $navigationHtml,
        'NAV_PARAM_NAME' => 'navpage',
        //'CURRENT_PAGE' => isset($pagination['PAGE_NUM']) ? (int)$pagination['PAGE_NUM'] : 1,
        //'ENABLE_NEXT_PAGE' => isset($pagination['ENABLE_NEXT_PAGE']) ? (bool)$pagination['ENABLE_NEXT_PAGE'] : false,
        'PAGE_SIZES' => [
            /*['NAME' => '5', 'VALUE' => '5'],
            ['NAME' => '10', 'VALUE' => '10'],
            ['NAME' => '20', 'VALUE' => '20'],*/
            ['NAME' => '50', 'VALUE' => '50']
        ],
        'ALLOW_COLUMNS_SORT' => isset($arParams['ALLOW_COLUMNS_SORT']) ? (bool)$arParams['ALLOW_COLUMNS_SORT'] : true,
        'ALLOW_ROWS_SORT' => false,
        'ALLOW_COLUMNS_RESIZE' => true,
        'ALLOW_HORIZONTAL_SCROLL' => true,
        'ALLOW_SORT' => true,
        'ALLOW_PIN_HEADER' => true,
        'ACTION_PANEL' => $actionPanel,
        'SHOW_CHECK_ALL_CHECKBOXES' => isset($arParams['SHOW_CHECK_ALL_CHECKBOXES']) ? (bool)$arParams['SHOW_CHECK_ALL_CHECKBOXES'] : true,
        'SHOW_ROW_CHECKBOXES' => isset($arParams['SHOW_ROW_CHECKBOXES']) ? (bool)$arParams['SHOW_ROW_CHECKBOXES'] : true,
        'SHOW_ROW_ACTIONS_MENU' => isset($arParams['SHOW_ROW_ACTIONS_MENU']) ? (bool)$arParams['SHOW_ROW_ACTIONS_MENU'] : true,
        'SHOW_GRID_SETTINGS_MENU' => isset($arParams['SHOW_GRID_SETTINGS_MENU']) ? (bool)$arParams['SHOW_GRID_SETTINGS_MENU'] : true,
        'SHOW_MORE_BUTTON' => true,
        'SHOW_NAVIGATION_PANEL' => true,
        'SHOW_PAGINATION' => isset($arParams['SHOW_PAGINATION']) ? (bool)$arParams['SHOW_PAGINATION'] : true,
        'ENABLE_COLLAPSIBLE_ROWS' => isset($arParams['ENABLE_COLLAPSIBLE_ROWS']) ? (bool)$arParams['ENABLE_COLLAPSIBLE_ROWS'] : false,
        'SHOW_SELECTED_COUNTER' => isset($arParams['SHOW_SELECTED_COUNTER']) ? (bool)$arParams['SHOW_SELECTED_COUNTER'] : true,
        'SHOW_TOTAL_COUNTER' => isset($arParams['SHOW_TOTAL_COUNTER']) ? (bool)$arParams['SHOW_TOTAL_COUNTER'] : true,
        'SHOW_PAGESIZE' => false,//isset($arParams['SHOW_PAGESIZE']) ? (bool)$arParams['SHOW_PAGESIZE'] : true,
        'SHOW_ACTION_PANEL' => isset($arParams['SHOW_ACTION_PANEL']) ? (bool)$arParams['SHOW_ACTION_PANEL'] : true,
        'TOTAL_ROWS_COUNT_HTML' => $rowCountHtml,
        'TOTAL_ROWS_COUNT' => $arResult['NAV_OBJECT']->getRecordCount()
    ],
    $component,
    ['HIDE_ICONS' => 'Y']
);

?>

<script>
    BX.ready(function(){
        BX.iTrack.Chart.StaffList.init({
            gridId: '<?=$gridID;?>',
            serviceUrl: '<?=$arResult['SERVICE_URL'];?>',
            sign: '<?=$arResult['SIGN'];?>'
        });
    });
</script>
