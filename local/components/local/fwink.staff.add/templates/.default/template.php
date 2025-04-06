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

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

$this->addExternalJs('//api.bitrix24.com/api/v1/?v=12082021');
\Bitrix\Main\UI\Extension::load("ui.dialogs.messagebox");

$whichdomainsendedrequest=$GLOBALS['FWINK']['DOMAIN'];
$wdsr=strrev($whichdomainsendedrequest);
$randstring=substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'),1,4);
$enc=\Local\Fwink\SimpleEncDecriptStringToNumber::encrypt($whichdomainsendedrequest).$randstring;
\CJSCore::Init(['date']);
$this->addExternalJs($templateFolder . '/js/component.js');
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

<div class="postdetail screen">
    <div class="postdetail-header">
        <div class="postdetail-header_title">Пригласить сотрудника</div>
        <a href="javascript:void(0);" class="postdetail-header_closebtn js-close-btn">
            Закрыть
        </a>
    </div>
    <div class="postdetail-body">
        <form method="post" name="staff_new_form" id="staff_new_form"
              action="<?=htmlspecialcharsbx($arResult['PATH_TO_AJAX'])?>" enctype="multipart/form-data">
            <input type="hidden" name="ajax_request" value="Y">
            <input type="hidden" name="save" value="Y">
            <input type="hidden" name="signedParams" value="<?=$arResult['SIGNED_PARAMS']?>" />
            <input type="hidden" name="sessid" value="<?=bitrix_sessid()?>" />
            <?php foreach ($arResult['MASSIVES'] as $code=>$arValue){ ?>
                <div class="postdetail-block">
                    <div class="postdetail-block_title-wrapper">
                        <div class="postdetail-block_title"><?=$arValue['title']?></div>
	                    <?php
	                    if($code === 'POST') {
	                    ?>
	                        <div class="postdetail-block_add-post">
		                        <a href="javascript:void(0);" onclick="BX.iTrack.Chart.StaffNew.createPost();">Создать должность</a>
	                        </div>
	                    <?php
                        }
	                    ?>
                    </div>
                    <div class="js-functions-block postdetail-block_inner-row">
                        <?php switch($arValue['type']){
                            case 'string':
                                ?><input class="postdetail-input" name="<?=$code?>" <?if($arValue['required']){?>required<?}?>/><?
                                break;
                            case 'date':
                                ?><input class="postdetail-input" name="<?=$code?>" onclick="BX.calendar({node: this, field: this, bTime: false});" <?if($arValue['required']){?>required<?}?>/><?
                                break;
                            case 'list':
                                ?>
                                <select class="postdetail-input" name="<?=$code?>" <?if($arValue['required']){?>required<?}?>>
                                    <option>Выбрать</option>
                                    <?foreach ($arValue['values'] as $valId=>$valTitle){?>
                                        <option value="<?=$valId?>"><?=$valTitle?></option>
                                    <?}?>
                                </select>
                                <?
                                break;
                        } ?>
                    </div>
                </div>
            <?php
			}
			?>
            <div class="postdetail-block">
                <button class="ui-btn ui-btn-primary" id="save-btn">Пригласить</button>
            </div>
        </form>
    </div>
</div>

<?
$jsMessage = [
	'error' => Loc::getMessage('LOCAL_TEMPLATE_STAFF_ADD_JS_ERROR')
];
?>

<script>
    BX.ready(function() {
        BX.iTrack.Chart.StaffNew.init({
            result: <?=CUtil::PhpToJSObject($arResult)?>,
            ajaxUrl: '<?=CUtil::JSEscape($arResult['PATH_TO_AJAX'])?>',
            signedParams: '<?=CUtil::JSEscape($arResult['SIGNED_PARAMS'])?>',
            link_utm: '<?=$enc?>',
	        sign: '<?=\Local\Fwink\Service\TokenManager::getInstance()->getPublicToken()?>',
            message: <?=CUtil::PhpToJSObject($jsMessage)?>
        });
    });
</script>
