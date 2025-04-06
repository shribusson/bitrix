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
?>

<?
CJSCore::Init(['color_picker','jquery']);
\Bitrix\Main\UI\Extension::load("ui.dialogs.messagebox");
$this->addExternalJs('//api.bitrix24.com/api/v1/?v=12082021');
$this->addExternalJs($templateFolder . '/js/component.js');
$this->addExternalJs('/local/js/local.fwink/libs/select2/js/select2.min.js');
$this->addExternalCss('/local/js/local.fwink/libs/select2/css/select2.min.css');
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
        <div class="postdetail-header_title">
            <?=$arParams['MODE'] === 'ADD' ? 'Новый блок' : 'Изменение блока';?>
        </div>
        <a href="javascript:void(0);" class="postdetail-header_editbtn js-edit-btn">
            Сохранить
        </a>
        <?if($arParams['MODE'] !== 'ADD'){?>
            <a href="javascript:void(0);" class="postdetail-header_editbtn js-delete-btn">
                Удалить
            </a>
        <?}?>
        <a href="javascript:void(0);" class="postdetail-header_closebtn js-close-btn">
            Закрыть
        </a>
    </div>
    <div class="postdetail-body">
        <form method="post" name="block-form" id="block-form"
              action="<?=htmlspecialcharsbx($arResult['PATH_TO_AJAX'])?>" enctype="multipart/form-data">
            <input type="hidden" name="ajax_request" value="Y">
            <input type="hidden" name="save" value="Y">
            <input type="hidden" name="signedParams" value="<?=$arResult['SIGNED_PARAMS'];?>" />
            <input type="hidden" name="sessid" value="<?=bitrix_sessid()?>" />
            <? foreach ($arResult['FIELDS'] as $code=>$arValue){ ?>
                <div class="postdetail-block">
                    <div class="postdetail-block_title-wrapper">
                        <div class="postdetail-block_title"><?=$arValue['title'];?></div>
	                    <?php if($code === 'POSTS'){ ?>
		                    <div class="postdetail-block_add-post">
			                    <a href="javascript:void(0);" onclick="BX.iTrack.Chart.CompanyBlockNew.createPost();">Создать должность</a>
		                    </div>
	                    <?php } ?>
                    </div>
                    <div class="js-functions-block postdetail-block_inner-row">
                        <?switch($arValue['type']){
                            case 'string':
                                ?><input class="postdetail-input" name="<?=$code;?>" value="<?=$arValue['value'];?>"<?if($arValue['required']){?>required<?}?>/><?
                                break;
                            case 'int':
                                ?><input type="number" min="200" class="postdetail-input" name="<?=$code;?>" value="<?=$arValue['value'];?>"<?if($arValue['required']){?>required<?}?>/><?
                                break;
                            case 'color':
                                $disabled = '';
                                if($code === 'COLOR_BLOCK' && $arResult['FIELDS']['COLOR_BY_PARENT']['value'] === 'Y') {
                                    $disabled = 'disabled';
                                }
                                ?><input class="postdetail-input js-color-input"
                                         name="<?=$code;?>"
                                        value="<?=$arValue['value'];?>"
                                        <?if($arValue['required']){?> required<?}?>
                                        <?if(!empty($arValue['value'])){?>style="background: <?=$arValue['value'];?>"<?}?>
                                        <?=$disabled;?>
                                        />
                                <?if($code === 'COLOR_BLOCK') {?>
                                    <br /><br /><button class="ui-btn ui-btn-primary ui-btn-xs" onclick="BX.iTrack.Chart.CompanyBlockNew.applyColorToChilds();">Применить цвет к дочерним блокам</button>
                                <?}?>
                                <?
                                break;
                            case 'date':
                                ?><input class="postdetail-input" name="<?=$code;?>" value="<?=$arValue['value'];?>" onclick="BX.calendar({node: this, field: this, bTime: false});" <?if($arValue['required']){?>required<?}?>/><?
                                break;
                            case 'list':
                                if($arValue['multiple'] === 'Y'){
                                    $options = $arValue['value'];
                                    if(!is_array($options)) {
                                        $options = [$options];
                                    }
                                    if(empty($options)) {
                                        $options = [0];
                                    }
                                    foreach($options as $index=>$optionValue) {
                                        ?>
                                        <select class="postdetail-input" data-field="<?=$code;?>" name="<?=$code;?>[<?=$index;?>]" <?if($arValue['required']){?>required<?}?> <?if($arValue['disabled']){?>disabled<?}?>>
                                            <option value="0">Выбрать</option>
                                            <?foreach ($arValue['values'] as $valId=>$valTitle){?>
                                                <option value="<?=$valId;?>" <?if((int)$valId === (int)$optionValue){?>selected<?}?>><?=$valTitle;?></option>
                                            <?}?>
                                        </select><br /><br />
                                        <?
                                    }
                                    if(!$arValue['disabled']) {
                                        ?>
                                        <button class="ui-btn ui-btn-primary ui-btn-xs" onclick="BX.iTrack.Chart.CompanyBlockNew.addMoreOption('<?=$code;?>');">Добавить</button>
                                        <?
                                    }
                                } else {
                                    ?>
                                    <select class="postdetail-input" name="<?=$code;?>" <?if($arValue['required']){?>required<?}?> <?if($arValue['disabled']){?>disabled<?}?>>
                                        <option value="0">Выбрать</option>
                                        <?foreach ($arValue['values'] as $valId=>$valTitle){?>
                                            <option value="<?=$valId;?>" <?if((int)$valId === (int)$arValue['value']){?>selected<?}?>><?=$valTitle;?></option>
                                        <?}?>
                                    </select>
                                    <?
                                }
                                break;
                            case 'boolean':
                                ?>
                                <input type="checkbox" name="<?=$code;?>" value="Y" <?if($arValue['value'] == 'Y'){?>checked<?}?> />
                                <?
                                break;
                        } ?>
                    </div>
                </div>
            <?}?>
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
        BX.iTrack.Chart.CompanyBlockNew.init({
            mode: '<?=$arParams['MODE'] === 'ADD' ? 'add' : 'edit';?>',
            result: <?=CUtil::PhpToJSObject($arResult)?>,
            ajaxUrl: '<?=CUtil::JSEscape($arResult['PATH_TO_AJAX'])?>',
            signedParams: '<?=CUtil::JSEscape($arResult['SIGNED_PARAMS'])?>',
            link_utm: '<?=\Local\Fwink\Service\TokenManager::getInstance()->getPublicToken();?>',
            message: <?=CUtil::PhpToJSObject($jsMessage)?>
        });
    });
</script>
