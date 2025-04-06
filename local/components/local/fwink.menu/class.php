<?
/** @noinspection PhpCSValidationInspection */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use Local\Fwink\AccessControl\Operations;
use Local\Fwink\Helpers\Folder as HelpersFolder;
use Local\Fwink\Helpers\Staff as HelpersStaff;
use Local\Fwink\Helpers\Ticket as HelpersTicket;

class MenuComponent extends CBitrixComponent
{
    private static $moduleNames = ['local.fwink'];

    public function executeComponent()
    {
        try {
            $this->loadModules();
            $this->setTemplateData();
            $this->includeComponentTemplate();
        } catch (Exception $e) {
            ShowError($e->getMessage());
        }
    }

    /**
     * @throws LoaderException
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

    /**
     * Set Template Data.
     */
    private function setTemplateData(): void
    {
        $menuItems = [];

        $currentDirectory = $this->arParams['SEF_FOLDER'];
        $enc = \Local\Fwink\Service\TokenManager::getInstance()->getPublicToken();

        $menuItems = [
            [
                'TEXT' => 'Структура компании',
                'URL' => '?mode=page&page=companyblock&sign='.$enc,
                'ID' => 'COMPANY',
                'IS_ACTIVE' => false,
                'COUNTER' => 0,
                'COUNTER_ID' => 'COUNTER_COMPANY',
            ],
            [
                'TEXT' => 'Департаменты',
                'URL' => '?mode=page&page=block&sign='.$enc,
                'ID' => 'DEPARTMENTS',
                /*'IS_ACTIVE' => true,  todo:make active interface*/
                'IS_ACTIVE' => false,
            ],
            [
                'TEXT' => 'Сотрудники',
                'URL' => '?mode=page&page=staff&sign='.$enc,
                'ID' => 'STAFF',
                /*'IS_ACTIVE' => true,  todo:make active interface*/
                'IS_ACTIVE' => false,
                'COUNTER' => 0,
                'COUNTER_ID' => 'COUNTER_STAFF',
            ],
            [
                'TEXT' => 'Должности',
                'URL' => '?mode=page&page=post&sign='.$enc,
                'ID' => 'POST',
                'IS_ACTIVE' => false,
                'COUNTER' => 0,
                'COUNTER_ID' => 'COUNTER_STAFF',
            ],
        ];
        if($GLOBALS['FWINK']['admin']) { // todo: replace global
            $menuItems[] = [
                'TEXT' => 'Настройки',
                'URL' => '?mode=page&page=settings&sign='.$enc,
                'ID' => 'SETTINGS',
                'IS_ACTIVE' => false
            ];
        }

        $strings=$GLOBALS['FWINK']['requestURL']['page'];
        foreach($menuItems as $key=>$value){
            if ((stripos($value['URL'], 'page='.$strings)) !== false) {
                $menuItems[$key]['IS_ACTIVE']=true;
            }
        }

        $this->arResult['MENU'] = $menuItems;
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
