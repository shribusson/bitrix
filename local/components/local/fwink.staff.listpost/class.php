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
use Local\Fwink\Fields\Config\Staff as ConfigStaff;
use Local\Fwink\Fields\Config\Posts as ConfigPosts;
use Local\Fwink\Fields\Field;
use Local\Fwink\Helpers\Field as HelpersField;
use Local\Fwink\Helpers\Filter as HelpersFilter;
use Local\Fwink\Helpers\Grid as HelpersGrid;
use Local\Fwink\Helpers\User as HelpersUser;
use Local\Fwink\Staff;
use Local\Fwink\Posts;
use Local\Fwink\Tables\PostsTable, Local\Fwink\Rest;
use Local\Fwink\Tables\RolesTable;
use Bitrix\Main\Security\Sign\Signer;

class StaffListpostComponent extends CBitrixComponent
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
		$this->manager = new Posts();
		$this->fields = (new ConfigPosts())->getFields();

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
		$select = [
		    ['ID_SHIEF_POST_USERB24']
        ];
		foreach ($this->fieldsShow as $fieldName) {
			/** @var Field $field */
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
        ];

        self::$filterFields[] = [
            'id' => 'STAFF',
            'name' => 'Сотрудник',
            'type' => 'entity_selector',
            'params' => [
                'multiple' => 'Y',
                'dialogOptions' => [
                    'height' => 240,
                    'context' => 'local_fwink_staff_filter',
                    'entities' => [
                        [
                            'id' => 'user',
                            'options' => [
                                'inviteEmployeeLink' => false
                            ],
                        ]
                    ]
                ],
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
			$sort = ['ID' => 'DESC', 'NAME_POST' => 'ASC'];
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

        $postIds = [];
        $userIds = [];
        $diskIds = [];
        $currentPostIds = [];
        foreach ($stores as $arPost) {
            $currentPostIds[] = $arPost['raw']['ID'];
            if((int)$arPost['raw']['ID_SUPERVISOR_POST'] > 0) {
                $postIds[] = (int)$arPost['raw']['ID_SUPERVISOR_POST'];
            }
            if((int)$arPost['raw']['ID_SHIEF_POST_USERB24'] > 0) {
                $userIds[] = (int)$arPost['raw']['ID_SHIEF_POST_USERB24'];
            }
            if((int)$arPost['raw']['ID_JOB_FOLDERB24'] > 0) {
                $diskIds[] = (int)$arPost['raw']['ID_JOB_FOLDERB24'];
            }
        }
        $arPosts = [];
        if(!empty($postIds)) {
            $dbPosts = PostsTable::query()
                ->setFilter(['=ID' => array_unique($postIds), 'ID_PORTAL' => $GLOBALS['FWINK']['ID_PORTAL']]) // todo: replace
                ->setSelect(['ID','NAME_POST','ID_SHIEF_POST_USERB24'])
                ->exec();
            while($post = $dbPosts->fetch()) {
                $arPosts[$post['ID']] = $post;
                if((int)$post['ID_SHIEF_POST_USERB24'] > 0) {
                    $userIds[] = (int)$post['ID_SHIEF_POST_USERB24'];
                }
            }
        }

        $arStaff = [];
        $arSubStaff = [];
        if(!empty($currentPostIds)) {
            $dbStaff = \Local\Fwink\Tables\UserbypostTable::query()
                ->setFilter([
                    '=ID_PORTAL' => $GLOBALS['FWINK']['ID_PORTAL'],
                    '=ID_POST' => $currentPostIds,
                    'ACTIVE' => 'Y'
                ])
                ->setSelect(['ID_POST', 'ID_STAFF'])
                ->exec();
            while($arPostStaff = $dbStaff->fetch()) {
                $arStaff[$arPostStaff['ID_POST']][] = $arPostStaff['ID_STAFF'];
                $userIds[] = $arPostStaff['ID_STAFF'];
            }
            $dbStaffAdd =  \Local\Fwink\Tables\UserbypostTable::query()
                ->setFilter(['=ID_PORTAL' => $GLOBALS['FWINK']['ID_PORTAL'], '=POST.ID_SUPERVISOR_POST' => $currentPostIds])
                ->setSelect(['ID_PARENT_POST' => 'POST.ID_SUPERVISOR_POST', 'ID_STAFF'])
                ->exec();
            while($arPostStaff = $dbStaffAdd->fetch()) {
                $arSubStaff[$arPostStaff['ID_PARENT_POST']][] = $arPostStaff['ID_STAFF'];
                $userIds[] = $arPostStaff['ID_STAFF'];
            }
        }

        $arUsers = [];
        if(!empty($userIds)) {
            // todo: replace rest call
            $res = Rest::getList('user.get', '', [
                'FILTER' => [
                    'ID' => array_unique($userIds),
                ]
            ]);

            if(!empty($res)) {
                foreach($res as $v) {
                    $arUsers[$v['ID']] = $v;
                }
            }
        }

        $arFolders = [];
        if(!empty($diskIds)) {
            $batch = [];
            $diskIds = array_unique($diskIds);
            foreach($diskIds as $folderId) {
                $batch[$folderId] = [
                    'method' => 'disk.folder.get',
                    'params' => ['id' => $folderId]
                ];
            }
            $resFolders = Rest::batch($batch);
            foreach($resFolders['result'] as $batchRes) {
                $arFolders[$batchRes['ID']] = $batchRes;
            }
        }

        $rows = [];
        foreach ($stores as $key=>$arPost) {
            if((int)$arPost['raw']['ID_SUPERVISOR_POST'] > 0 && !empty($arPosts[$arPost['raw']['ID_SUPERVISOR_POST']])) {
                $stores[$key]['format']['ID_SUPERVISOR_POST'] = '<a class="postlist_link" href="javascript:void(0);" onclick="BX.iTrack.Chart.PostList.openPost(' . $arPost['raw']['ID_SUPERVISOR_POST'] . ')">'.$arPosts[$arPost['raw']['ID_SUPERVISOR_POST']]['NAME_POST'].'</a>';
            } else {
                $stores[$key]['format']['ID_SUPERVISOR_POST'] = '';
            }
            if((int)$arPost['raw']['ID_SHIEF_POST_USERB24'] > 0 && !empty($arUsers[$arPost['raw']['ID_SHIEF_POST_USERB24']])) {
                $stores[$key]['format']['STAFF_LIST'] = $this->getUsersHtml([$arPost['raw']['ID_SHIEF_POST_USERB24']], $arUsers);
            } else {
                if(!empty($arStaff[$arPost['raw']['ID']])) {
                    $stores[$key]['format']['STAFF_LIST'] = $this->getUsersHtml($arStaff[$arPost['raw']['ID']], $arUsers);
                } else {
                    $stores[$key]['format']['STAFF_LIST'] = '';
                }
            }
            if(!empty($arSubStaff[$arPost['raw']['ID']])) {
                $stores[$key]['format']['STAFF_SUBORDINATE'] = $this->getUsersHtml($arSubStaff[$arPost['raw']['ID']], $arUsers);
            } else {
                $stores[$key]['format']['STAFF_SUBORDINATE'] = '';
            }
            if(!empty($arPosts[$arPost['raw']['ID_SUPERVISOR_POST']]['ID_SHIEF_POST_USERB24'])) {
                $stores[$key]['format']['STAFF_MANAGER'] = $this->getUsersHtml([$arPosts[$arPost['raw']['ID_SUPERVISOR_POST']]['ID_SHIEF_POST_USERB24']], $arUsers);
            } else {
                $stores[$key]['format']['STAFF_MANAGER'] = '';
            }

            $stores[$key]['format']['NAME_POST'] = '<a class="postlist_link" href="javascript:void(0);" onclick="BX.iTrack.Chart.PostList.openPost(' . $arPost['raw']['ID'] . ')">'.$arPost['raw']['NAME_POST'].'</a>';

            $folderInfo = $arFolders[$arPost['raw']['ID_JOB_FOLDERB24']];
            $stores[$key]['format']['ID_JOB_FOLDERB24'] = '<a href="'.$folderInfo['DETAIL_URL'].'" target="_blank">'.$folderInfo['NAME'].'</a>';



            $action = [[
                'TITLE' => 'Открыть',
                'TEXT' => 'Открыть',
                'ONCLICK' => 'BX.iTrack.Chart.PostList.openPost(' . $arPost['raw']['ID'] . ');',
                'DEFAULT' => true
            ]];
            if($this->access['update'] === true) {
                $action[] = [
                    'TITLE' => 'Удалить',
                    'TEXT' => 'Удалить',
                    'ONCLICK' => 'BX.iTrack.Chart.PostList.delete('.$arPost['raw']['ID'].');'
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
                'local.fwink.post.detail'
            )  ,
            'EDIT_URL' => '/local/components/local/fwink.post.detail/ajax.php'
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
            if(!empty($value)) {
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
                    if ($key === 'NAME_POST' || $key === 'CKP_OF_POST' || $key === 'FUNCTION_OF_POST') {
                        $filter['%' . $key] = $value;
                    } else {
                        $filter[$key] = $value;
                    }
                }
                if ($key === 'STAFF') {
                    $filter['STAFF']['ID'] = $value;
                }

                if ($key === 'FIND') {
                    $filter[] = [
                        'LOGIC' => 'OR',
                        '%NAME_POST' => $value,
                        '%CKP_OF_POST' => $value,
                        '%FUNCTION_OF_POST' => $value
                    ];
                    $filter['STAFF']['NAME'] = $value;
                }
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
			    'format' => $this->getFields($row)
            ];
		}

		return $rows;
	}

	private function getFields($row): array
	{
		$result = [];

		foreach ($this->fieldsShow as $fieldName) {
			/** @var Field $field */
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
	 * @param Field $field
	 *
	 * @return string
	 * @throws ArgumentException
	 * @throws ConfigurationException
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

		return $value ? $field->getShowView()->getHtml() : '<span>-</span>';
	}
}
