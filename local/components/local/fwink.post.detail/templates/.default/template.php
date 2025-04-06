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

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Local\Fwink\AccessControl\Operations;
use Local\Fwink\Helpers\User as HelpersUser;
use Local\Fwink\SimpleEncDecriptStringToNumber;
use Local\Fwink\Stuffeveryone;

if (empty($arResult['USER']) && $arParams['MODE'] !== 'ADD') {
	http_response_code(404); die();
}

$this->addExternalJs('//api.bitrix24.com/api/v1/?v=12082021');
\Bitrix\Main\UI\Extension::load("ui.dialogs.messagebox");
\CJSCore::Init(['jquery']);

$arResult['RESTDEPARTMENTS'] = array_map(function ($k, $v) {
	return [
		'id' => $k,
		'name' => $v
	];
}, array_keys($arResult['REST']['DEPARTMENTS'] ?: []),
										 array_values($arResult['REST']['DEPARTMENTS'] ?: [])
);

$arID_JOB_FOLDERB24=[];
$strID_JOB_FOLDERB24 = '<option value="">---</option>';
array_walk($arResult['arID_JOB_FOLDERB24'], function ($item) use (&$strID_JOB_FOLDERB24,
    $arResult,
	&$arID_JOB_FOLDERB24) {
	$strselected='';
	if($item['id']==$arResult['RES']['ID_JOB_FOLDERB24']){
		$strselected=' selected';
    }
	$arID_JOB_FOLDERB24[$item['id']]=$item['name'];
	$strID_JOB_FOLDERB24 = $strID_JOB_FOLDERB24.'<option'.$strselected.' value="'.$item['id'].'">'.$item['name'].'</option>';
});

$arUPNames = array_map(function ($a) {
	return $a['NAME'];
}, array_values($arResult['USERSPOST'] ?: []));

$arUPId = array_map(function ($a) {
	return $a['ID'];
}, array_values($arResult['USERSPOST'] ?: []));

$documentRoot = Main\Application::getDocumentRoot();
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

<div class="postdetail screen " data-id="<?= $arResult['ELEMENT_ID']; ?>">
    <input name="ID" type="hidden" value="<?= $arResult['ELEMENT_ID']; ?>">

    <div class="postdetail-header">
        <div id="main_namepost" class="postdetail-header_title"><?= $arResult['RES']['NAME_POST'] ?></div>
        <?
        if ($arResult['ACCESS']['update'] === true) {
            ?>
            <a href="javascript:void(0);" class="postdetail-header_editbtn js-edit-link">
                Редактировать
            </a>
            <?if($arParams['MODE'] !== 'ADD'){?>
                <a href="javascript:void(0);" class="postdetail-header_editbtn js-delete-link">
                    Удалить
                </a>
            <?}?>
        <? } ?>
        <a href="javascript:void(0);" class="postdetail-header_closebtn js-close-btn">
            <?if ($arResult['ACCESS']['update'] === true) :?>Закрыть<?else:?>Отменить<?endif;?>
        </a>
    </div>

    <div class="postdetail-body">
        <? if ($arResult['ACCESS']['update'] === true) {?>
            <input type="hidden" name="signedParamsString" value="<?= $arResult['SIGNED_PARAMS'] ?>"/>
        <? } ?>

        <div class="postdetail-block js-post-name" style="display: none;">
            <div class="postdetail-block_title-wrapper">
                <div class="postdetail-block_title">Название должности</div>
            </div>
            <div class="postdetail-block_inner-row">
                <input name="NAME_POST" class="postdetail-input" onchange="BX.iTrack.Chart.PostDetail.changeTitle(this)"
                   value="<?= $arResult['RES']['NAME_POST']; ?>" disabled placeholder="Название должности">
            </div>
        </div>
        <? if ($arResult['ACCESS']['update'] === true) {?>
            <div class="postdetail-block js-post-ismanager" style="display: none">
                <div class="postdetail-block_title-wrapper">
                    <div class="postdetail-block_title">Руководящая должность?</div>
                </div>
                <div class="postdetail-block_inner-row">
                    <input type="checkbox" name="isManagerPost" value="Y" <?if($arResult['RES']['IS_MANAGER_POST'] === 'Y'){?>checked<?}?>/>
                </div>
            </div>
        <?}?>
        <!--СОТРУДНИКИ-->
        <input name="ID_STAFF" type="hidden" value="<?= implode(',', $arUPId) ?>">
        <div class="postdetail-block js-post-employees" <?if($arResult['RES']['IS_MANAGER_POST'] === 'Y'){?>style="display: none;"<?}?>>
            <div class="postdetail-block_title-wrapper">
                <div class="postdetail-block_title">СОТРУДНИКИ В ДОЛЖНОСТИ</div>
                <div class="js-plus-btn postdetail-block_plusbtn" style="opacity: 0" onclick="BX.iTrack.Chart.PostDetail.changeEmployees();">
                    <img src="data:image/png;base64, iVBORw0KGgoAAAANSUhEUgAAABYAAAAWCAYAAADEtGw7AAAACXBIWXMAABYlAAAWJQFJUiTwAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAABiSURBVHgB7ZVBCoAwDAS3qQ9tX6aPingU/YdGgh/YQvVQshDIYZhTlgBkVPeiehaWn1gwJ5kB83VheMFHCXGIfxQnb9R7/P1yWaqS0T9Nzm09zIfl4ypCPIKY/nmwu7Z06gES7ReqOmuIQgAAAABJRU5ErkJggg==" />
                </div>
            </div>
            <div class="postdetail-block_user-row" id="post_staff_row">
                <?
$cnt = count($arResult['USERSPOST'] ?: []);
                $iter = 0;
                while ($cnt--) {
                    $objElement = $arResult['USERSPOST'][$cnt];
                    ?>
                        <div class="postdetail-block_user-wrapper" data-id="userblock<?=$objElement['ID']?>">
                            <a href="javascript:void(0);" onclick="BX.iTrack.Chart.PostDetail.openUser('<?=$objElement['ID']?>');" class="postdetail-block_user" id="staff-<?= $objElement['ID'] ?>" data-num="<?= $iter; ?>" title="<?=$objElement['NAME']?>">
                                <img title="<?= $objElement['NAME'] ?>" src="<?=$objElement['PHOTO']?>" />
                            </a>
                            <? if ($arResult['ACCESS']['update'] === true) {?>
                                <div class="postdetail-block_user-delete js-user-delete-btn" style="display: none;" data-id="<?=$objElement['ID']?>" onclick="BX.iTrack.Chart.PostDetail.removeEmployee('<?=$objElement['ID']?>');"></div>
                            <?}?>
                        </div>
                    <?
                    $iter++;
                }
                ?>
            </div>
        </div>
        <!--РУКОВОДИТЕЛЬ ТЕКУЩЕГО ПОСТА-->
        <input type="hidden" name="ID_SHIEF_POST_USERB24" value="<?=$arResult['RES']['ID_SHIEF_POST_USERB24']?>">
        <div class="postdetail-block js-post-shief" <?if($arResult['RES']['IS_MANAGER_POST'] !== 'Y'){?>style="display: none;"<?}?>>
            <div class="postdetail-block_title-wrapper">
                <div class="postdetail-block_title">СОТРУДНИКИ В ДОЛЖНОСТИ</div>
                <div class="js-plus-btn postdetail-block_plusbtn" style="opacity: 0" onclick="BX.iTrack.Chart.PostDetail.changeShief();">
                    <img src="data:image/png;base64, iVBORw0KGgoAAAANSUhEUgAAABYAAAAWCAYAAADEtGw7AAAACXBIWXMAABYlAAAWJQFJUiTwAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAABiSURBVHgB7ZVBCoAwDAS3qQ9tX6aPingU/YdGgh/YQvVQshDIYZhTlgBkVPeiehaWn1gwJ5kB83VheMFHCXGIfxQnb9R7/P1yWaqS0T9Nzm09zIfl4ypCPIKY/nmwu7Z06gES7ReqOmuIQgAAAABJRU5ErkJggg==" />
                </div>
            </div>
            <div class="postdetail-block_user-row" id="post_shief_row">
                <?if((int)$arResult['RES']['ID_SHIEF_POST_USERB24'] > 0) {
                    $arShief = $arResult['REST']['USERS'][$arResult['RES']['ID_SHIEF_POST_USERB24']];
                    if(empty($arShief['PERSONAL_PHOTO'])) {
                        $arShief['PERSONAL_PHOTO'] = '/local/apps/img/ui-user.svg';
                    }
                    ?>
                    <a href="javascript:void(0);" onclick="BX.iTrack.Chart.PostDetail.openUser('<?=$arShief['ID']?>');" class="postdetail-block_user" id="shief-<?= $arShief['ID'] ?>" title="<?=$arShief['NAME']?>">
                        <img title="<?= $arShief['NAME'].' '.$arShief['LAST_NAME'] ?>" src="<?=$arShief['PERSONAL_PHOTO']?>" />
                    </a>
                <?}?>
            </div>
        </div>
        <!--ВЫШЕСТОЯЩИЙ ПОСТ-->
        <div class="js-parent-block">
            <?if(!empty($arResult['RES']['ID_SUPERVISOR_POST'])){?>
                <div class="postdetail-block">
                    <div class="postdetail-block_title-wrapper">
                        <div class="postdetail-block_title">РУКОВОДИТЕЛЬ</div>
                    </div>
                    <div class="postdetail-parent_user">
                        <?if(!empty($arResult['SUPERVISOR']['SHIEF_ID'])) {?>
                            <div class="postdetail-parent_photo">
                                <a href="javascript:void(0);" onclick="BX.iTrack.Chart.PostDetail.openUser('<?=$arResult['SUPERVISOR']['SHIEF_ID']?>');">
                                    <img id="svisorimg" src="<?=$arResult['SUPERVISOR']['SHIEF_IMG']?>" />
                                </a>
                            </div>
                            <div>
                                <a href="javascript:void(0);" onclick="BX.iTrack.Chart.PostDetail.openUser('<?=$arResult['SUPERVISOR']['SHIEF_ID'];?>');" class="postdetail-parent_username">
                                    <div id="svisorname"><?=$arResult['SUPERVISOR']['SHIEF_NAME']?></div>
                                </a>
                            </div>
                        <?}?>
                    </div>
                </div>
                <div class="postdetail-block">
                    <div class="postdetail-block_title-wrapper">
                        <div class="postdetail-block_title">ВЫШЕСТОЯЩАЯ ДОЛЖНОСТЬ</div>
                    </div>
                    <input type="hidden" name="ID_SUPERVISOR_POST" id="svisorid" value="<?=$arResult['RES']['ID_SUPERVISOR_POST']?>">
                    <div class="postdetail-parent">
                        <a href="javascript:void(0);" onclick="BX.iTrack.Chart.PostDetail.openPost('<?=$arResult['RES']['ID_SUPERVISOR_POST']?>');" id="svisorfunction" class="postdetail-parent_info-post"><?=$arResult['SUPERVISOR']['NAME']?></a>
                    </div>
                </div>
            <?}?>
        </div>
        <!--ФУНКЦИИ-->
        <div class="postdetail-block">
            <div class="postdetail-block_title-wrapper">
                <div class="postdetail-block_title">ФУНКЦИИ</div>
            </div>
            <div class="js-functions-block postdetail-block_inner-row" onclick="BX.iTrack.Chart.PostDetail.showEditFunctions(this)">
                <textarea name="FUNCTION_OF_POST" class="postdetail-textarea" onchange="BX.iTrack.Chart.PostDetail.closeEditFunctions(this)" id="FUNCTION_OF_POST" style="display:none"><?= $arResult['RES']['FUNCTION_OF_POST'] ?></textarea>
                <div class="js-functions-block-content">
                    <div class="postdetail-textarea"><?= $arResult['RES']['FUNCTION_OF_POST'] ?></div>
                </div>
            </div>
        </div>
        <!--ЦКП-->
        <div class="postdetail-block">
            <div class="postdetail-block_title-wrapper">
                <div class="postdetail-block_title">ЦКП</div>
            </div>
            <div class="js-ckp-block postdetail-block_inner-row" onclick="BX.iTrack.Chart.PostDetail.showEditCkp(this)">
                <textarea name="CKP_OF_POST" class="postdetail-textarea" onchange="BX.iTrack.Chart.PostDetail.closeEditCkp(this)" id="CKP_OF_POST" style="display:none"><?= $arResult['RES']['CKP_OF_POST'] ?></textarea>
                <div class="js-ckp-block-content">
                    <div class="postdetail-textarea"><?= $arResult['RES']['CKP_OF_POST'] ?></div>
                </div>
            </div>
        </div>
	    <div class="postdetail-block">
		    <div class="postdetail-block_title-wrapper">
			    <div class="postdetail-block_title">Сортировка</div>
		    </div>
		    <div class="js-ckp-block postdetail-block_inner-row">
			    <input name="SORT" value="<?=$arResult['RES']['SORT']?>" class="postdetail-input" disabled placeholder="100" />
		    </div>
	    </div>
        <!--Диск-->
        <div class="postdetail-block">
            <div class="postdetail-block_title-wrapper">
                <div class="postdetail-block_title">Папка должности</div>
            </div>
            <div class="postdetail-block_inner-row">
                <?if(!empty($arResult['POST_FOLDER'])) {
                   ?>
                    <a href="<?=$arResult['POST_FOLDER']['link']?>" class="postdetail-block_folder-link" target="_blank" id="team-disk-view"><?=$arResult['POST_FOLDER']['name']?></a>
                    <?
                } else {?>
                    <a href="" class="postdetail-block_folder-link" target="_blank" id="team-disk-view"></a>
                <?}?>
                <select name="ID_JOB_FOLDERB24" class="postdetail-input"
                        value="<?= $arResult['RES']['ID_JOB_FOLDERB24'] ?>"
                        id="team-disk-edit"
                        style="display:none;">
                    <? echo $strID_JOB_FOLDERB24; ?>
                </select>
            </div>
        </div>
    </div>
</div>

<script>
    BX.ready(function(){
        BX.iTrack.Chart.PostDetail.init({
            mode: '<?=$arParams['MODE'] === 'ADD' ? 'add' : 'detail'?>',
            result: <?=CUtil::PhpToJSObject($arResult)?>,
            ajaxUrl: '<?=CUtil::JSEscape($arResult['PATH_TO_AJAX'])?>',
            signedParams: '<?=CUtil::JSEscape($arResult['SIGNED_PARAMS'])?>',
            link_utm: '<?=\Local\Fwink\Service\TokenManager::getInstance()->getPublicToken()?>',
            elementId: '<?=CUtil::JSEscape($arParams['ELEMENT_ID'])?>',
            emptyParentPost: <?=empty($arResult['SUPERVISOR']['SHIEF_ID']) ? 'true' : 'false'?>
        });
    });
</script>