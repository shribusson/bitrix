<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

class CLocalFwinkSettings extends \CBitrixComponent
{
    private static $moduleNames = ['local.fwink'];

    public function executeComponent()
    {
        try {
            $this->loadModules();
            $this->accessControl();
            $this->getResult();
            \Bitrix\Main\UI\Extension::load(['ui.buttons']);
            \Bitrix\Main\UI\Extension::load("ui.buttons.icons");
            \Bitrix\Main\UI\Extension::load("ui.entity-selector");
            \Bitrix\Main\UI\Extension::load("ui.dialogs.messagebox");
            $this->includeComponentTemplate();
        } catch (Exception $e) {
            ShowError($e->getMessage());
        }
    }

    /**
     * @throws \Bitrix\Main\LoaderException
     */
    private function loadModules(): void
    {
        foreach (self::$moduleNames as $moduleName) {
            $moduleLoaded = Loader::includeModule($moduleName);
            if (!$moduleLoaded) {
                throw new LoaderException(
                    Loc::getMessage('LOCAL_SERVICEDESK_MODULE_LOAD_ERROR', ['#MODULE_NAME#' => $moduleName])
                );
            }
        }
    }

    private function accessControl()
    {
        if(!$GLOBALS['FWINK']['admin']) {
            throw new \Exception('Access denied');
        }
    }

    private function getResult()
    {
        $usersCanEdit = Option::get('local.fwink','users_can_edit','');
        if(!empty($usersCanEdit)) {
            $users = explode(',', $usersCanEdit);
        } else {
            $users = [];
        }
        $this->arResult['SETTINGS']['usersCanEdit'] = $users;
        $this->arResult['SIGN'] = \Local\Fwink\Service\TokenManager::getInstance()->getPublicToken();
    }

    /**a
     *
     * @param array $arParams
     *
     * @return array
     */
    public function onPrepareComponentParams($arParams): array
    {
        $this->arParams = $arParams;

        return $this->arParams;
    }
}