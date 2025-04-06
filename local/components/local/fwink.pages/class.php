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
use Local\Fwink\Staffremote;

class StaffComponent extends CBitrixComponent
{
    /** @var StaffComponent */
    private static $moduleNames = ['local.fwink'];
    private $defaultUrlTemplates404 = [];
    private $componentVariables = [];
    private $page = '';
	private $mode = '';

    private $manager;

    public function executeComponent()
    {
        try {
            $this->loadModules();
            $this->setParams();
            $this->dataManager();
            $this->accessControl();
            $this->setSefDefaultParams();
            $this->getResult();
            \Bitrix\Main\UI\Extension::load(['ui.buttons']);
            \Bitrix\Main\UI\Extension::load("ui.buttons.icons");
            $this->includeComponentTemplate($this->page);
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

    /**
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ArgumentNullException
     */
    private function setParams(): void
    {
        //$this->arParams['SEF_FOLDER'] = HelpersFolder::get(HelpersFolder::PATH_STAFF);
    }

    private function dataManager(): void
    {
        $this->manager = new Staffremote();
    }

    private function accessControl(): void
    {
        $result = [];
        $operations = Operations::getOperations();
        $entityName = $this->manager->getEntityName();
        foreach ($operations as $operation) {
            $result[$operation] = Operations::checkAccess($operation, $entityName);
        }

        $this->arResult['ACCESS'] = $result;

        if ($this->arResult['ACCESS']['read'] === false) {
            throw new LoaderException(
                Loc::getMessage('LOCAL_SERVICEDESK_MODULE_ACCESS_DENIED')
            );
        }
    }

    /**
     * Определяет переменные шаблонов и шаблоны путей.
     */
    private function setSefDefaultParams(): void
    {
        $this->defaultUrlTemplates404 = [
            'list' => '',
            'add' => '/add/',
            'detail' => '/#ELEMENT_ID#/'
        ];
        $this->componentVariables = ['ELEMENT_ID'];
    }

    /**
     * Получение результатов.
     */
    private function getResult(): void
    {

//		$requestURL = Bitrix\Main\Context::getCurrent()->getRequest()->getRequestedPage();
//		$currentPageUrl = mb_substr($requestURL, mb_strlen($folder404));
		$requestURL = $this->request->getValues();

		$customrequestURL=json_decode($this->request->getValues()['PLACEMENT_OPTIONS'],true);
		if(is_string($customrequestURL)){
			$url = parse_url($customrequestURL);
			$urls_parsed=($url['query']);
			$split_parameters = explode('&', $urls_parsed);
			$split_parameters>0&&$customrequestURL=[];

			for($i = 0; $i < count($split_parameters); $i++) {
				$final_split = explode('=', $split_parameters[$i]);
				$customrequestURL[$final_split[0]] = $final_split[1];
			}
		}
		if(!empty($customrequestURL)&&is_array($customrequestURL)) {
			$requestURL = array_merge(
				$requestURL
				, $customrequestURL
			);
		}
		/*
		 * todo
		 * fwink.pages unto fwink.staff/fwink.pages
		 * engine method from complex cmp,eg
		 * ---
		 * {"mode":"pages","page":"companyblock","element_id":"959"}
		 * */

        $urlTemplates = [];
        if ($this->arParams['SEF_MODE'] === 'Y') {
            $variables = [];
            $urlTemplates = \CComponentEngine::makeComponentUrlTemplates(
                $this->defaultUrlTemplates404,
                $this->arParams['SEF_URL_TEMPLATES']
            );
            $variableAliases = \CComponentEngine::makeComponentVariableAliases(
                $this->defaultUrlTemplates404,
                $this->arParams['VARIABLE_ALIASES']
            );
            $this->page = \CComponentEngine::parseComponentPath(
                $this->arParams['SEF_FOLDER'],
                $urlTemplates,
                $variables
            );
//			if(!$this->page){$this->page='add';}
			if(!$this->page){
				$this->page=$requestURL['page'];
				$this->mode=$requestURL['mode'];
				if(is_numeric($requestURL['element_id']))
				{
					$this->page='detail';
					$variables=['ELEMENT_ID'=>$requestURL['element_id']];
				}
				if(isset($requestURL['ADD'])||isset($requestURL['add']))
				{
					$this->page='add';
				}

			}


            if (strlen($this->page) <= 0) {
                if(is_numeric($requestURL['element_id']))
                {
					$this->page='detail';
					$variables=['ELEMENT_ID'=>$requestURL['element_id']];
				}else {
					$this->page = 'list';
				}
            }
            \CComponentEngine::initComponentVariables(
                $this->page,
                $this->componentVariables,
                $variableAliases,
                $variables
            );
        } else {
            $variables = [];
            $variableAliases = \CComponentEngine::makeComponentVariableAliases(
                [],
                $this->arParams['VARIABLE_ALIASES']
            );

            \CComponentEngine::initComponentVariables(
                false,
                $this->componentVariables,
                $variableAliases,
                $variables
            );
			/*
			public const PATH_COMPANY = '?mode=pages&page=companyblock';
			public const PATH_STAFF = '?mode=page&page=staff';
			public const PATH_POST = '?mode=page&page=post';
			public const PATH_LIST = '?mode=page&page=list';

			?mode=pages&page=companyblock&element_id=959

			todo
			is this a orient for add page
			:before: 	for this->page=list local/components/local/fwink.staff/templates/.default/list.php
						this->page=deatal local/components/local/fwink.staff/templates/.default/detail.php
			:succeseed in code: $this->includeComponentTemplate($this->page)
									forward to local/components/local/fwink.staff.listext/class.php

			*/
            $this->page = '';
            if ((int)$variables['ELEMENT_ID'] > 0) {
                $this->page = 'detail';
            } elseif (isset($variables['ADD'])||isset($variables['add'])) {
                $this->page = 'add';
            } else {
                $this->page = 'list';
            }
        }

        $this->arResult['SEF_FOLDER'] = $this->arParams['SEF_FOLDER'];//  ?mode=page&page=staff
        $this->arResult['URL_TEMPLATES'] = $urlTemplates;
        $this->arResult['VARIABLES'] = $variables;
        $this->arResult['ALIASES'] = $variableAliases;
    }

    /**
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
