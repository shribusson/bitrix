<?
/** @noinspection PhpCSValidationInspection */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Config\ConfigurationException;
use Bitrix\Main\Context;
use Bitrix\Main\Grid;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Filter;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\Web\Json;
use Bitrix\Main\Web\Uri;
use Local\Fwink\AccessControl\Exception;
use Local\Fwink\AccessControl\Operations;
use Local\Fwink\Helpers\Field as HelpersField;
use Local\Fwink\Helpers\Filter as HelpersFilter;
use Local\Fwink\Helpers\Grid as HelpersGrid;
use Bitrix\Main\Security\Sign\Signer;

class LFBlockList extends \CBitrixComponent
{
    private const SUPPORTED_ACTIONS = ['delete'];
    private const SUPPORTED_SERVICE_ACTIONS = ['GET_ROW_COUNT'];

    private static $gridId;

    private static $headers;
    private static $filterFields;
    private static $filterPresets;

    private static $moduleNames = ['local.fwink', 'crm'];
    private static $navigation = 'nav-block';

    private $manager;
    private $access;
    private $fields;
    private $fieldsSelect;
    private $fieldsShow;
    private $fieldsSortable;
    private $fieldsFilterable;

    private $filter;

    public function executeComponent()
    {
        try {
            $this->loadModules();
            $this->dataManager();
            $this->accessControl();
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
        }
    }

    private function dataManager(): void
    {
        $this->manager = new \Local\Fwink\Blocks();
        $this->fields = (new \Local\Fwink\Fields\Config\Blocks())->getFields();

        $entityName = $this->manager->getEntityName();

        /** @var Field $field */
        foreach ($this->fields as $fieldName => $field) {
            if ($field->getGridList()->isShow()) {
                $fieldNameTable = $field->getInfo()->getName();
                $displayField = HelpersField::checkShow($entityName, $fieldNameTable);
                if ($displayField !== false) {
                    $this->fieldsShow[] = $fieldName;
                    $this->fieldsSortable[] = $field->getGridList()->getSort('CODE');
                    $this->fieldsFilterable[] = $fieldNameTable;
                }
            }
        }

        $this->fieldsSelect = $this->getFieldsSelect();

        if ($this->arParams['FILTER']) {
            $this->filter = $this->arParams['FILTER'];
        }
        $this->filter['ID_PORTAL'] = (int)$GLOBALS['FWINK']['ID_PORTAL']; // todo: replace globals

        if ($this->arParams['GRID_ID']) {
            self::$gridId = $this->arParams['GRID_ID'];
        }
    }

    private function getFieldsSelect(): array
    {
        return [
            'ID',
            'SUBDIVISION',
            'NAME',
            'ID_PARENT_BLOCK',
            'COLOR_HEADER',
            'COLOR_BLOCK',
            'COLOR_BY_PARENT',
            'NUMBER',
            'SORT',
            'IS_HIDE',
            'ADDICT_PARAM'
        ];
    }

    /**
     * Get permissions to the entity for the current user.
     *
     * @throws ArgumentException
     */
    private function accessControl(): void
    {
        $result = [];
        $operations = Operations::getOperations();
        $entityName = $this->manager->getEntityName();
        foreach ($operations as $operation) {
            $result[$operation] = Operations::checkAccess($operation, $entityName);
        }

        $this->access = $result;
    }

    /**
     * Set Template Data.
     */
    private function setTemplateData(): void
    {
        $this->initPresets();
        $this->initHeader();
        $this->initGrid();
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

    private function initPresets(): void
    {

    }

    /**
     * Initialization table headers and filter.
     */
    private function initHeader(): void
    {
        foreach ($this->fieldsShow as $fieldName) {
            /** @var Field $field */
            $field = $this->fields[$fieldName];
            if ($field) {
                self::$headers[] = HelpersGrid::getHeader($field, $fieldName);
                $filter = HelpersGrid::getFilter($field);
                if (!empty($filter)) {
                    self::$filterFields[] = $filter;
                }
            }
        }
        self::$headers[] = [
            'id' => 'POST_LIST',
            'type' => 'text',
            'name' => 'Связанные должности',
            'default' => true
        ];
        /*self::$headers[] = [
            'id' => 'STAFF_LIST',
            'type' => 'text',
            'name' => 'Сотрудники в должности',
            'default' => true,
        ];
        self::$headers[] = [
            'id' => 'STAFF_MANAGER',
            'type' => 'text',
            'name' => 'Руководитель',
            'default' => true,
        ];
        self::$headers[] = [
            'id' => 'STAFF_SUBORDINATE',
            'type' => 'text',
            'name' => 'Подчиненные',
            'default' => true,
        ];*/
    }

    private function initGrid(): void
    {
        $context = Context::getCurrent();
        $request = $context->getRequest();

        $grid = new Grid\Options(self::$gridId);

        // region Sort
        $gridSort = $grid->getSorting();
        $sort = array_filter(
            $gridSort['sort'],
            function ($field) {
                return in_array($field, $this->fieldsSortable, true);
            },
            ARRAY_FILTER_USE_KEY
        );
        if (empty($sort)) {
            $sort = ['ID' => 'ASC', 'NAME' => 'ASC'];
        }
        // endregion

        // region Filter
        $gridFilter = new Filter\Options(self::$gridId, self::$filterPresets);
        $gridFilterValues = $gridFilter->getFilter(self::$filterFields);
        $filter = $this->filterProcessing($gridFilterValues);
        // endregion

        $this->processGridActions($filter);
        $this->processServiceActions($filter);

        // region Pagination
        $gridNav = $grid->GetNavParams();
        $pager = new PageNavigation(self::$navigation);
        $pager->setPageSize($gridNav['nPageSize']);
        $pager->setRecordCount($this->manager->getCount($filter));
        $pager->initFromUri();

        // endregion
        $stores = $this->getStores([
            'filter' => $filter,
            'select' => $this->fieldsSelect,
            'limit' => $pager->getLimit(),
            'offset' => $pager->getOffset(),
            'order' => $sort
        ]);

        $requestUri = new Uri($request->getRequestedPage());
        $requestUri->addParams([
            'sessid' => bitrix_sessid(),
            'mode' => $request->get('mode'),
            'page' => $request->get('page'),
            'sign' => $request->get('sign')
        ]);

        $parentBlockIds = [];
        $currentBlockIds = [];
        foreach ($stores as $arBlock) {
            $currentBlockIds[] = $arBlock['raw']['ID'];
            if(!empty($arBlock['raw']['ID_PARENT_BLOCK'])) {
                $parentBlockIds[] = $arBlock['raw']['ID_PARENT_BLOCK'];
            }
        }

        $arParentBlocks = [];
        if(!empty($parentBlockIds)) {
            $dbParentBlocks = \Local\Fwink\Tables\BlocksTable::query()
                ->whereIn('ID',$parentBlockIds)
                ->setSelect(['ID','NAME'])
                ->exec();
            while($arPBlock = $dbParentBlocks->fetch()) {
                $arParentBlocks[$arPBlock['ID']] = $arPBlock;
            }
        }

        $arPosts = [];
        if(!empty($currentBlockIds)) {
            $dbPostRelation = \Local\Fwink\Tables\BlockPostRelationTable::query()
                ->registerRuntimeField(new \Bitrix\Main\ORM\Fields\Relations\Reference(
                    'POST',
                    '\Local\Fwink\Tables\PostsTable',
                    ['=this.POSTS_ID' => 'ref.ID']
                ))
                ->whereIn('BLOCKS_ID',$currentBlockIds)
                ->setSelect(['BLOCKS_ID','POSTS_ID','POST_NAME' => 'POST.NAME_POST'])
                ->exec();
            while($arPostRelation = $dbPostRelation->fetch()) {
                $arPosts[$arPostRelation['BLOCKS_ID']][] = [
                    'ID' => $arPostRelation['POSTS_ID'],
                    'NAME' => $arPostRelation['POST_NAME']
                ];
            }
        }

        $rows = [];
        foreach ($stores as $key => $arBlock) {

            $stores[$key]['format']['NAME'] = '<a class="blocklist_link" href="javascript:void(0);" onclick="BX.iTrack.Chart.BlockList.openBlock(' . $arBlock['raw']['ID'] . ')">'.$arBlock['raw']['NAME'].'</a>';

            if(!empty($arBlock['raw']['ID_PARENT_BLOCK'])) {
                $stores[$key]['format']['ID_PARENT_BLOCK'] = '<a class="blocklist_link" href="javascript:void(0);" onclick="BX.iTrack.Chart.BlockList.openBlock(' . $arBlock['raw']['ID_PARENT_BLOCK'] . ')">'.$arParentBlocks[$arBlock['raw']['ID_PARENT_BLOCK']]['NAME'].'</a>';
            }

            $colorFields = ['COLOR_HEADER','COLOR_BLOCK'];
            foreach($colorFields as $fName) {
                if (!empty($arBlock['raw'][$fName])) {
                    $stores[$key]['format'][$fName] = '<div class="blocklist_color" style="background-color: '.$arBlock['raw'][$fName].'">'.$arBlock['raw'][$fName].'</div>';
                }
            }

            if(!empty($arPosts[$arBlock['raw']['ID']])) {
                $fValue = [];
                foreach($arPosts[$arBlock['raw']['ID']] as $postData) {
                    $fValue[] = '<a class="blocklist_link" href="javascript:void(0);" onclick="BX.iTrack.Chart.BlockList.openPost(' . $postData['ID'] . ')">'.$postData['NAME'].'</a>';
                }
                $stores[$key]['format']['POST_LIST'] = implode(', ', $fValue);
            }

            $stores[$key]['format']['COLOR_BY_PARENT'] = $stores[$key]['raw']['COLOR_BY_PARENT'] === 'Y' ? 'Да' : 'Нет';
            $stores[$key]['format']['IS_HIDE'] = $stores[$key]['raw']['IS_HIDE'] === 'Y' ? 'Да' : 'Нет';

            $action = [[
                'TITLE' => 'Открыть',
                'TEXT' => 'Открыть',
                'ONCLICK' => 'BX.iTrack.Chart.BlockList.openBlock(' . $arBlock['raw']['ID'] . ');',
                'DEFAULT' => true
            ]];
            if ($this->access['update'] === true) {
                $action[] = [
                    'TITLE' => 'Удалить',
                    'TEXT' => 'Удалить',
                    'ONCLICK' => 'BX.iTrack.Chart.PostList.delete(' . $arBlock['raw']['ID'] . ');'
                ];
            }

            $rows[] = [
                'id' => $stores[$key]['raw']['ID'],
                'actions' => $action,
                'data' => $stores[$key]['format']
            ];
        }

        $this->arResult = [
            'GRID_ID' => self::$gridId,
            'STORES' => $stores,
            'ROWS' => $rows,
            'HEADERS' => self::$headers,
            'PAGINATION' => [
                'PAGE_NUM' => $pager->getCurrentPage(),
                'ENABLE_NEXT_PAGE' => $pager->getCurrentPage() < $pager->getPageCount(),
                'URL' => $request->getRequestedPage(),
            ],
            'NAV_OBJECT' => $pager,
            'SORT' => $sort,
            'FILTER' => self::$filterFields,
            'FILTER_PRESETS' => self::$filterPresets,
            'ENABLE_LIVE_SEARCH' => false,
            'DISABLE_SEARCH' => true,
            'SERVICE_URL' => $requestUri->getUri(),
            'HIDE_FILTER' => (bool)$this->arParams['HIDE_FILTER'],
            'ACCESS' => $this->access,
            'SIGN' => $request->get('sign'),
            'SIGNED_PARAMS_EDIT' => (new Signer())->sign(
                base64_encode(serialize([])),
                'local.fwink.comapnyblock.edit'
            ),
            'EDIT_URL' => '/local/components/local/fwink.companyblock.edit/ajax.php'
        ];
    }

    /**
     * create html for users list
     *
     * @param array $users
     * @param array $usersInfo
     * @return string
     */
    private function getUsersHtml(array $users = [], array $usersInfo = []) : string
    {
        $str = '';
        foreach ($users as $userId) {
            $user = $usersInfo[$userId];
            $name = '';
            if(!empty($user['LAST_NAME'])) {
                $name = $user['LAST_NAME'].' ';
            }
            if(!empty($user['NAME'])) {
                $name .= $user['NAME'].' ';
            }
            if(!empty($user['SECOND_NAME'])) {
                $name .= $user['SECOND_NAME'].' ';
            }
            $photoSrc = !empty($user['PERSONAL_PHOTO']) ? $user['PERSONAL_PHOTO'] : '/local/apps/img/ui-user.svg'; // todo: move picture url to constants
            $class = count($users) > 1 ? 'postlist_userblock__inline' : 'postlist_userblock';
            $str .= '<a href="javascript:void(0);" onclick="BX.iTrack.Chart.PostList.openUser(\''.$userId.'\')" class="'.$class.'" title="'.$name.'">
                    <div class="postlist_userblock-photo" style="background-image: url('.$photoSrc.')"></div>';
            if(count($users) === 1) {
                $str .= '<div class="postlist_userblock-name">'.$name.'</div>';
            }
            $str .= '</a>';
        }
        return $str;
    }

    /**
     * Filter processing.
     *
     * @param $gridFilterValues
     *
     * @return array
     */
    private function filterProcessing($gridFilterValues): array
    {
        $filter = [];
        foreach ($gridFilterValues as $key => $value) {
            if (!empty($value)) {
                $from = strrpos($key, HelpersFilter::DATE_FROM);
                if ($from !== false && $value !== '') {
                    $rangeValueName = HelpersFilter::trimRange($key);
                    if (in_array($rangeValueName, $this->fieldsFilterable, true)) {
                        $rangeValueName = '>=' . $rangeValueName;
                        $filter[$rangeValueName] = $value;
                    }
                }
                $to = strrpos($key, HelpersFilter::DATE_TO);
                if ($to !== false && $value !== '') {
                    $rangeValueName = HelpersFilter::trimRange($key);
                    if (in_array($rangeValueName, $this->fieldsFilterable, true)) {
                        $rangeValueName = '<=' . $rangeValueName;
                        $filter[$rangeValueName] = $value;
                    }
                }

                if (in_array($key, $this->fieldsFilterable, true)) {
                    if ($key === 'NAME') {
                        $filter['%' . $key] = $value;
                    } else {
                        $filter[$key] = $value;
                    }
                }

                if ($key === 'FIND') {
                    $filter[] = [
                        'LOGIC' => 'OR',
                        '%NAME' => $value,
                        '%POSTS.NAME_POST' => $value
                        //'%FUNCTION_OF_POST' => $value
                    ];
                }
            }

            if ($this->filter) {
                $filter += $this->filter;
            }
        }

        return $filter;
    }

    /**
     * @param $currentFilter
     *
     */
    private function processGridActions($currentFilter): void
    {
        if (!check_bitrix_sessid()) {
            return;
        }

        $context = Context::getCurrent();
        $request = $context->getRequest();

        $action = $request->get('action_button_' . self::$gridId);

        if (!in_array($action, self::SUPPORTED_ACTIONS, true)) {
            return;
        }

        $allRows = $request->get('action_all_rows_' . self::$gridId) === 'Y';
        if ($allRows) {
            $dbStores = $this->manager->getList([
                'filter' => $currentFilter,
                'select' => ['ID'],
            ]);
            $storeIds = [];
            foreach ($dbStores as $store) {
                $storeIds[] = $store['ID'];
            }
        } else {
            $storeIds = $request->get('ID');
            if (!is_array($storeIds)) {
                $storeIds = [];
            }
        }

        if (empty($storeIds)) {
            return;
        }

        switch ($action) {
            case 'delete':
                foreach ($storeIds as $storeId) {
                    $this->manager->delete($storeId);
                }
                break;

            default:
                break;
        }
    }

    /**
     * @param $currentFilter
     *
     * @throws ArgumentException
     */
    private function processServiceActions($currentFilter): void
    {
        global $APPLICATION;

        if (!check_bitrix_sessid()) {
            return;
        }

        $context = Context::getCurrent();
        $request = $context->getRequest();

        $params = $request->get('PARAMS');

        if (empty($params['GRID_ID']) || $params['GRID_ID'] !== self::$gridId) {
            return;
        }

        $action = $request->get('ACTION');

        if (!in_array($action, self::SUPPORTED_SERVICE_ACTIONS, true)) {
            return;
        }

        $APPLICATION->RestartBuffer();
        header('Content-Type: application/json');

        switch ($action) {
            case 'GET_ROW_COUNT':
                $count = $this->manager->getCount($currentFilter);
                echo Json::encode([
                    'DATA' => [
                        'TEXT' => Loc::getMessage('LOCAL_SERVICEDESK_USER_LIST_GRID_ROW_COUNT',
                            ['#COUNT#' => $count])
                    ]
                ]);
                break;
            default:
                break;
        }
        die;
    }

    private function getStores(array $parameters = []): array
    {
        $rows = [];
        $result = $this->manager->getList($parameters);
        while ($row = $result->fetch()) {
            $rows[] = [
                'raw' => $row,
                'format' => $row
            ];
        }

        return $rows;
    }
}