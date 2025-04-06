<?
/** @noinspection PhpCSValidationInspection */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

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
use Local\Fwink\Fields\Config\Staffremote as ConfigStaff;

//use Local\Fwink\Fields\Config\Staff as ConfigStaff;
use Local\Fwink\Helpers\Field as HelpersField;
use Local\Fwink\Helpers\Filter as HelpersFilter;
use Local\Fwink\Helpers\Grid as HelpersGrid;
use Local\Fwink\Helpers\User as HelpersUser;
use Local\Fwink\Staffremote;

use Local\Fwink\Staff;
use Local\Fwink\Tables\StaffTable;
use Local\Fwink\Tables\RolesTable;
use Local\Fwink\Rest;

class StaffListstaff extends CBitrixComponent
{
	/** @var StaffListComponent */
	private const SUPPORTED_ACTIONS = ['delete'];
	private const SUPPORTED_SERVICE_ACTIONS = ['GET_ROW_COUNT'];

	private static $gridId;

	private static $headers;
	private static $filterFields;
	private static $filterPresets;

	private static $moduleNames = ['local.fwink', 'crm'];
	private static $navigation = 'nav-staff';

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
			if (!$moduleLoaded) {
				throw new LoaderException(
					Loc::getMessage('LOCAL_SERVICEDESK_MODULE_LOAD_ERROR', ['#MODULE_NAME#' => $moduleName])
				);
			}
		}
	}

	/**
	 * Initialization entity.
	 * Get properties to show the user.
	 */
	private function dataManager(): void
	{
		//$this->manager = new Staffremote();
		$this->manager = new Staff();
		$this->fields = (new ConfigStaff())->getFields();

		$entityName = $this->manager->getEntityName();

		/** @var \Local\Fwink\Fields\Field $field */
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

		if ($this->arParams['GRID_ID']) {
			self::$gridId = $this->arParams['GRID_ID'];
		}
	}

	/**
	 * Get SELECT for getList.
	 *
	 * @return array
	 */
	private function getFieldsSelect(): array
	{
		$select = [];
		foreach ($this->fieldsShow as $fieldName) {
			/** @var \Local\Fwink\Fields\Field $field */
			$field = $this->fields[$fieldName];
			if ($field) {
				$select[] = $field->getSelect();
			}
		}

		$select = array_merge(...$select);

		return $select ?: ['ID'];
	}

	/**
	 * Get permissions to the entity for the current user.
	 *
	 * @throws \Bitrix\Main\ArgumentException
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
			/** @var \Local\Fwink\Fields\Field $field */
			$field = $this->fields[$fieldName];
			if ($field) {
				self::$headers[] = HelpersGrid::getHeader($field, $fieldName);
				$filter = HelpersGrid::getFilter($field);
				if (!empty($filter)) {
					self::$filterFields[] = $filter;
				}
			}
		}
        $dbPosts = \Local\Fwink\Tables\PostsTable::query()
            ->where('ID_PORTAL',$GLOBALS['FWINK']['ID_PORTAL']) // todo: replace
            ->setSelect(['ID','NAME_POST'])
            ->setOrder(['NAME_POST' => 'ASC'])
            ->exec();
        $posts = [];
        while($arPost = $dbPosts->fetch()) {
            $posts[$arPost['ID']] = $arPost['NAME_POST'];
        }
        self::$filterFields[] = [
            'id' => 'POST',
            'name' => 'Должность',
            'type' => 'list',
            'items' => $posts,
            'params' => [
                'multiple' => 'Y'
            ],
            'default' => true
        ];
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
			$sort = ['ID' => 'ASC'];
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
		$pager->setPageSize(50);
		$pager->initFromUri();
        $rows = [];
        $stores = $this->manager->getList([
            'filter' => $filter,
            'limit' => $pager->getLimit(),
            'offset' => $pager->getOffset(),
            'order' => $sort
        ]);
        $pager->setRecordCount($stores['total']);

        foreach ($stores['items'] as $item) {
            $row = [];
            foreach ($item as $field=>$value) {
                if($field == 'ID') {
                    $row['ID_STAFF'] = $value;
                } elseif($field == 'POSTS') {
                    $arPosts = [];
                    foreach ($value as $postInfo) {
                        $arPosts[] = '<a href="javascript:void(0);" onclick="BX.iTrack.Chart.StaffList.openPost(' . $postInfo['ID_POST'] . ');">' . $postInfo['NAME_POST'] . '</a>';
                    }
                    $row['POST'] = implode(' / ', $arPosts);
                } elseif($field === 'ACTIVE') {
                    $row[$field] = $value ? 'Да' : 'Нет';
                } else {
                    $row[$field] = $value;
                }
            }

            $name = '';
            if(!empty($item['NAME'])) {
                $name .= $item['NAME'].' ';
            }
            if(!empty($item['LAST_NAME'])) {
                $name .= $item['LAST_NAME'].' ';
            }
            if(!empty($item['SECOND_NAME'])) {
                $name .= $item['SECOND_NAME'].' ';
            }
            $photoSrc = !empty($item['PERSONAL_PHOTO']) ? $item['PERSONAL_PHOTO'] : '/local/apps/img/ui-user.svg'; // todo: move picture url to constants
            $userHtml = '<a href="javascript:void(0);" onclick="BX.iTrack.Chart.StaffList.openUser(\''.$item['ID'].'\')" class="postlist_userblock">
                    <div class="postlist_userblock-photo" style="background-image: url('.$photoSrc.')"></div>
                    <div class="postlist_userblock-name">'.$name.'</div></a>';
            $row['FULL_NAME'] = $userHtml;

            $action = [
                /*'TITLE' => 'Открыть',
                'TEXT' => 'Открыть',
                'ONCLICK' => 'BX.iTrack.Chart.PostList.openPost(' . $arPost['raw']['ID'] . ');',
                'DEFAULT' => true*/
            ];

            $rows[] = [
                'id' => $item['ID'],
                'actions' => $action,
                'data' => $row
            ];
        }

        $requestUri = new Uri($request->getRequestedPage());
        $requestUri->addParams([
            'sessid' => bitrix_sessid(),
            'mode' => $request->get('mode'),
            'page' => $request->get('page'),
            'sign' => $request->get('sign')
        ]);

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
            'SIGN' => $request->get('sign')
		];
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
			$from = strrpos($key, HelpersFilter::DATE_FROM);
			if ($from !== false && $value !== '') {
				$rangeValueName = HelpersFilter::trimRange($key);
				if (in_array($rangeValueName, $this->fieldsFilterable, true)) {
				    if($rangeValueName === 'ID_STAFF') {
				        $rangeValueName = 'ID';
                    }
					$rangeValueName = '>=' . $rangeValueName;
					$filter[$rangeValueName] = $value;
				}
			}
			$to = strrpos($key, HelpersFilter::DATE_TO);
			if ($to !== false && $value !== '') {
				$rangeValueName = HelpersFilter::trimRange($key);
				if (in_array($rangeValueName, $this->fieldsFilterable, true)) {
                    if($rangeValueName === 'ID_STAFF') {
                        $rangeValueName = 'ID';
                    }
					$rangeValueName = '<=' . $rangeValueName;
					$filter[$rangeValueName] = $value;
				}
			}
            if ($key === 'POST') {
                $filter['POST']['ID'] = $value;
            } elseif (in_array($key, $this->fieldsFilterable, true)) {
                if($key === 'ID_STAFF') {
                    $key = 'ID';
                }
                if($key === 'FULL_NAME') {
                    $key = 'NAME_SEARCH';
                }
                if(in_array($key, ['FULL_NAME','PERSONAL_PHONE','EMAIL'])) {
                    $filter[$key] = $value . '%';
                } else {
                    $filter[$key] = $value;
                }
			} elseif($key === 'FIND' && !empty($value)) {
			    $filter['NAME_SEARCH'] = $value;
                $filter['POST']['NAME'] = $value;
            }
		}

		if ($this->filter) {
			$filter = array_merge($filter, $this->filter);
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
	 * @throws \Bitrix\Main\ArgumentException
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
			$rows[] = $this->getFields($row);
		}

		return $rows;
	}

	private function getFields($row): array
	{
		$result = [];

		foreach ($this->fieldsShow as $fieldName) {
			/** @var \Local\Fwink\Fields\Field $field */
			$field = $this->fields[$fieldName];
			if ($field) {
				$isRead = $this->access['read'] === true && $field->isReadable() === true;

				$field->setValueFromDb($row);
				if ($isRead) {
					// TODO убрать ID, придумать нормальную логику
					if ($fieldName === 'ID') {
						$result[$fieldName] = $row['ID'];
					} else {
						$result[$fieldName] = $this->getFieldShowHtml($field);
					}
				}
			}
		}

		return $result;
	}

	/**
	 * @param \Local\Fwink\Fields\Field $field
	 *
	 * @return string
	 * @throws \Bitrix\Main\ArgumentException
	 * @throws \Bitrix\Main\Config\ConfigurationException
	 */
	private function getFieldShowHtml($field): string
	{
		$entityName = $this->manager->getEntityName();
		$fieldNameTable = $field->getInfo()->getName();

		$getValue = $field->getValue();
		if (HelpersField::checkUrl($entityName, $fieldNameTable)) {
			$getValue->setShowUrl(true);
		}
		if ($getValue instanceof Local\Fwink\Fields\Value\Type\Url ||
			$getValue instanceof Local\Fwink\Fields\Value\Type\Staff ||
			$getValue instanceof Local\Fwink\Fields\Value\Type\User ||
			$getValue instanceof Local\Fwink\Fields\Value\Type\Observers
		) {
			$templates = $field->getUrlTemplate();
			if ($templates) {
				$getValue->setUrlTemplate($templates);
			}
		}

		$value = $field->getValue()->get();

		return $value ? $field->getShowView()->getHtml() : '[---]';
	}
}
