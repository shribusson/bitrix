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

<div class="loader" id="loader">
    <div id="cube-loader">
        <div class="caption">
            <div class="loader-description"></div>
            <div class="cube-loader">
                <div class="cube loader-1"></div>
                <div class="cube loader-2"></div>
                <div class="cube loader-4"></div>
                <div class="cube loader-3"></div>
            </div>
        </div>
    </div>
</div>
<div>
    <div class="settings_header">
        <h2>Пользователи с правами на редактирование схемы</h2>
    </div>
    <div class="settings_row">
        <div class="settings_field-tags" id="settings_users_edit"></div>
    </div>
    <div class="settings_row">
        <button class="ui-btn ui-btn-primary js-save-btn">Сохранить</button>
    </div>
</div>

<script>
	BX.ready(function(){
		BX.iTrack.Chart.Settings.init({
			sign: '<?=$arResult['SIGN']?>',
            settings: <?=\CUtil::PhpToJSObject($arResult['SETTINGS'])?>
		});
	});
</script>
