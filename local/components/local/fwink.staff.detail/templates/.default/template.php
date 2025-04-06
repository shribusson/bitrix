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
use Local\Fwink\Helpers\Folder as HelpersFolder;
use Local\Fwink\Helpers\User as HelpersUser;
use Local\Fwink\Tables\StaffTable;

$this->addExternalJs('//api.bitrix24.com/api/v1/?v=12082021');

$whichdomainsendedrequest=$GLOBALS['FWINK']['DOMAIN'];
$wdsr=strrev($whichdomainsendedrequest);
$randstring=substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'),1,4);
$enc=\Local\Fwink\SimpleEncDecriptStringToNumber::encrypt($whichdomainsendedrequest).$randstring;

if (empty($arResult['USER'])) {
    http_response_code(502);
    die();
}
$documentRoot = Main\Application::getDocumentRoot();
$this->addExternalJs($templateFolder . '/js/component.js');
?>
<div class="staff-detail">
    <div class="staff-detail-header">
        <div class="staff-detail-header__left"></div>
        <div class="staff-detail-header__right">
            <a href="javascript:void(0);" class="staff-detail-header__closebtn js-close-btn">
                Закрыть
            </a>
        </div>
    </div>
    <div class="staff-detail-line">
        <div class="staff-detail-photo">
            <div class="staff-detail-photo__picture">
                <img src="<?=$arResult['USER']['PERSONAL_PHOTO'];?>" alt="<?=$arResult['USER']['NAME'];?>"/>
                <div class="staff-detail-photo__overlay"></div>
                <div class="staff-detail-photo__communications">
                    <a href="javascript:void(0);" class="staff-detail-photo__communications-chat js-chat-link"></a>
                    <a href="javascript:void(0);" class="staff-detail-photo__communications-call js-call-link"></a>
                </div>
                <div class="staff-detail-photo__online">
                    <div class="staff-detail-photo__online-status">
                        <div class="staff-detail-photo__online-status-dot_<?=$arResult['USER']['IS_ONLINE'] == 'Y' ? 'green' : 'red';?>"></div>
                        <div class="staff-detail-photo__online-status-text">
                            <?if($arResult['USER']['IS_ONLINE'] == 'Y'){?>
                                В сети
                            <?} else {?>
                                Не в сети
                            <?}?>
                        </div>
                    </div>
                    <?if($arResult['USER']['IS_ONLINE'] !== 'Y' && !empty($arResult['USER']['LAST_LOGIN'])){?>
                        <div class="staff-detail-photo__online-last">
                            <?
                            $time = new DateTime($arResult['USER']['LAST_LOGIN']);
                            print FormatDate('d F Y\ \в\ H:s', $time->getTimestamp());
                            ?>
                        </div>
                    <?}?>
                </div>
            </div>
        </div>
        <div class="staff-detail-info">
            <div class="staff-detail-info__header">
                <h2 class="staff-detail-info__title">Контактная информация</h2>
                <a class="staff-detail-info__profilelink" href="https://<?=$GLOBALS['FWINK']['DOMAIN'];?>/company/personal/user/<?=$arResult['ELEMENT_ID'];?>/" target="_blank">Перейти в профиль</a>
            </div>
            <div class="staff-detail-info__row">
                <div class="staff-detail-info__row-title">
                    Имя
                </div>
                <div class="staff-detail-info__row-value">
                    <?=$arResult['USER']['LAST_NAME'];?> <?=$arResult['USER']['NAME'];?> <?=$arResult['USER']['SECOND_NAME'];?>
                </div>
            </div>
            <div class="staff-detail-info__row">
                <div class="staff-detail-info__row-title">
                    Контактный email
                </div>
                <div class="staff-detail-info__row-value">
                    <?if(!empty($arResult['USER']['EMAIL'])) {?>
                        <a href="mailto:<?=$arResult['USER']['EMAIL'];?>"><?=$arResult['USER']['EMAIL'];?></a>
                    <?}?>
                </div>
            </div>
            <div class="staff-detail-info__row">
                <div class="staff-detail-info__row-title">
                    Телефон
                </div>
                <div class="staff-detail-info__row-value">
                    <?=$arResult['USER']['PERSONAL_PHONE'];?>
                </div>
            </div>
        </div>
    </div>
    <?foreach($arResult['USER']['POSTS'] as $arPost) {?>
        <div class="staff-detail-line_one">
            <div class="staff-detail-post">
                <a href="javascript:void(0);" onclick="BX.iTrack.Chart.StaffDetail.openPost('<?=$arPost['ID_POST'];?>')">
                    <h2 class="staff-detail-info__title"><?=$arPost['NAME_POST'];?></h2>
                </a>

                <div class="staff-detail-post__department-chain">
                    <?foreach($arPost['BLOCKS'] as $block) {?>
                        <a href="javascript:void(0);" onclick="BX.iTrack.Chart.StaffDetail.openBlock('<?=$block['ID'];?>')">
                            <?=$block['NAME'];?>
                        </a>
                    <?}?>
                </div>
                <div class="staff-detail-info__row">
                    <div class="staff-detail-info__row-title">
                        Функции
                    </div>
                    <div class="staff-detail-info__row-value">
                        <?=$arPost['POST_FUNCTIONS'];?>
                    </div>
                </div>
                <div class="staff-detail-info__row">
                    <div class="staff-detail-info__row-title">
                        ЦКП
                    </div>
                    <div class="staff-detail-info__row-value">
                        <?=$arPost['POST_CKP'];?>
                    </div>
                </div>
                <?if(!empty($arPost['PARENT_SHIEF_ID'])){?>
                    <div class="staff-detail-info__row">
                        <div class="staff-detail-info__row-title">
                            Руководитель
                        </div>
                        <div class="staff-detail-info__row-value">
                            <div class="staff-detail-post__parent">
                                <div class="staff-detail-post__parent-photo">
                                    <a href="javascript:void(0);" onclick="BX.iTrack.Chart.StaffDetail.openUser('<?=$arPost['PARENT_SHIEF_ID'];?>');">
                                        <img src="<?=$arResult['USER']['USERS_INFO'][$arPost['PARENT_SHIEF_ID']]['PERSONAL_PHOTO'];?>" alt="<?=$arResult['USER']['USERS_INFO'][$arPost['PARENT_SHIEF_ID']]['NAME'];?>" />
                                    </a>
                                </div>
                                <div>
                                    <a href="javascript:void(0);" onclick="BX.iTrack.Chart.StaffDetail.openUser('<?=$arPost['PARENT_SHIEF_ID'];?>')" class="staff-detail-post__parent-username">
                                        <div>
                                            <?=$arResult['USER']['USERS_INFO'][$arPost['PARENT_SHIEF_ID']]['LAST_NAME'].' '.$arResult['USER']['USERS_INFO'][$arPost['PARENT_SHIEF_ID']]['NAME'];?>
                                        </div>
                                    </a>
                                    <div class="staff-detail-post__parent-info">
                                        <a class="staff-detail-post__parent-post" href="javascript:void(0);" onclick="BX.iTrack.Chart.StaffDetail.openPost('<?=$arPost['PARENT_ID_POST'];?>')">
                                            <?=$arPost['PARENT_NAME_POST'];?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?}?>
            </div>
        </div>
    <?}?>
</div>

<script>
    BX.iTrack.Chart.StaffDetail.init({
        result: <?=CUtil::PhpToJSObject($arResult)?>,
        ajaxUrl: '<?=CUtil::JSEscape($arResult['PATH_TO_AJAX'])?>',
        signedParams: '<?=CUtil::JSEscape($arResult['SIGNED_PARAMS'])?>',
        link_utm:'<?=$enc;?>',
        elementId: '<?=CUtil::JSEscape($arParams['ELEMENT_ID'])?>'
    });
</script>
