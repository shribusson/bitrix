<?
/** @noinspection PhpUndefinedMethodInspection */
/** @noinspection PhpCSValidationInspection */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\ArgumentTypeException;
use Bitrix\Main\Config\ConfigurationException;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\Security\Sign\Signer;
use Bitrix\Main\SystemException;
use Local\Fwink\AccessControl\Operations;
use Local\Fwink\Fields\Config\Staff as ConfigStaff;
use Local\Fwink\Fields\Config\Posts as ConfigPosts;
use Local\Fwink\Fields\Config\Roles as ConfigRoles;
use Local\Fwink\Fields\Config\Blocks as ConfigBlocks;
use Local\Fwink\Fields\Field;
use Local\Fwink\Helpers\Company as HelpersCompany;
use Local\Fwink\Helpers\Encoding as HelpersEncoding;
use Local\Fwink\Helpers\Field as HelpersField;
use Local\Fwink\Helpers\Folder as HelpersFolder;
use Local\Fwink\Helpers\Posts as HelpersStaff;
use Local\Fwink\Helpers\User as UserHelper;
use Local\Fwink\Staff, Local\Fwink\Posts, Local\Fwink\Rest
	, Local\Fwink\Blocks, Local\Fwink\Roles;
use Local\Fwink\Tables\UserbypostTable as UsersPost;
use Local\Fwink\Tables\PostsTable;

class PostsDetailComponent extends CBitrixComponent
{
	/** @var PostsDetailComponent */
	private static $moduleNames = ['local.fwink', 'pull'];
	private static $fieldsParams = [
		'SHOW' => [
			'DATE_REGISTER',
			'LAST_AUTH',
			'ACTIVE',
			'LOGIN',
			'EMAIL',
			'NAME',
			'LAST_NAME',
			'SECOND_NAME',
			'WORK_POSITION',
			'PERSONAL_PHONE',
			'WORK_NOTES',
			'GROUP_ID',
			'PERSONAL_PHOTO',
            'SORT'
		]
	];
	public $boolUselimitshowFields;
	private $path;
	private $manager;
	private $fields;
	private $fieldsSelect;
	private $fieldsShow;
	private $elementId;
	private $elementName;
	private $companyId;
	private $companyName;

	public function executeComponent(): array
	{
		$result = [];
		try {
			$this->loadModules();
			$this->dataManager();
			$this->accessControl();
			if (is_set($GLOBALS['FWINK']['requestURL']['action'])) {
				$this->arParams['ACTION'] = $GLOBALS['FWINK']['requestURL']['action'];
			}

			switch ($this->arParams['ACTION']) {
				case 'update':
					$result = $this->update();
					break;
                case 'add':
                    $result = $this->add();
                    break;
				case 'delete':
					$result = $this->delete();
					break;
				default:
					$this->setTemplateData();
					$this->includeComponentTemplate();
			}
		} catch (Exception $e) {
			ShowError($e->getMessage());
		}

		return $result;
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
					Loc::getMessage('LOCAL_FWINK_MODULE_LOAD_ERROR', ['#MODULE_NAME#' => $moduleName])
				);
			}
		}
	}

	/**
	 * Initialization entity.
	 * Get properties to show the user.
	 *
	 * @throws ArgumentException
	 * @throws SystemException
	 */
	private function dataManager(): void
	{
		$this->manager = new Posts();
		$this->fields = (new ConfigPosts())->getFields();
		$this->elementId = $this->arParams['ELEMENT_ID'];

		foreach ($this->fields as $fieldName => $field) {

			/*		!!!!!
			 * this condition allows you
			 * to display certain columns
			 * */
			$this->boolUselimitshowFields = False;

			if ($this->boolUselimitshowFields && in_array($fieldName, self::$fieldsParams['SHOW'], true)) {
				$this->fieldsShow[] = $fieldName;
			} else {
				$this->fieldsShow[] = $fieldName;
			}
		}

		$this->fieldsSelect = $this->getFieldsSelect();
	}

	private function getFieldsSelect(): array
	{
		$select = [];
		/** @var Field $field */
		foreach ($this->fields as $fieldName => $field) {

			if ($this->boolUselimitshowFields && in_array($fieldName, $this->fieldsShow, true)) {
				$select[] = $field->getSelect();
			} else {
				$select[] = $field->getSelect();
			}
		}

		$select = array_merge(...$select);

		return $select;
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
	}

	/**
	 * Set Template Data.
	 */
	private function setTemplateData(): void
	{
	    if((int)$this->elementId > 0) {
            $this->arResult['ELEMENT_ID'] = $this->elementId;
            $this->arResult['USER'] = $this->getList();
            $this->arResult['SUPERVISOR'] = $this->getSupervisor();

//		$r=Local\Fwink\Fields\Field;
            $numIDshiefPost = $this->fields['ID_SHIEF_POST_USERB24']->getValue()->getRaw();
            $arUsersPost = [$numIDshiefPost];
            $arUsersPost = array_unique(array_merge(
                $arUsersPost,
                $this->getUsersPost(),
                [
                    $this->arResult['SUPERVISOR']['SHIEF_ID']
                ]
            ));


            $res = $this->getUserByRest($arUsersPost);

//			$res = file_get_contents($_SERVER["DOCUMENT_ROOT"].'local/apps/include/userget.json');
            //$this->arResult['REST']['USER'] = count($res) > 0 ? $res[0] : [];
            $this->arResult['REST']['USERS'] = $res;
            $_tmp = [];
            foreach ($this->arResult['REST']['USERS'] as $key => $value) {
                $_tmp[$value['ID']] = $value;
            }
            $this->arResult['REST']['USERS'] = $_tmp;
            $this->arResult['SUPERVISOR']['SHIEF_NAME'] = $this->arResult['REST']['USERS'][$this->arResult['SUPERVISOR']['SHIEF_ID']]['NAME'] . ' ' . $this->arResult['REST']['USERS'][$this->arResult['SUPERVISOR']['SHIEF_ID']]['LAST_NAME'];
            $this->arResult['SUPERVISOR']['SHIEF_IMG'] = $this->arResult['REST']['USERS'][$this->arResult['SUPERVISOR']['SHIEF_ID']]['PERSONAL_PHOTO'] ?: '/local/apps/img/ui-user.svg';
            //$this->arResult['SUPERVISOR']['DEPARTMENT_NAME'] = 'Department name';
            $_tmpUSERSPOST = [];
            array_walk($this->arResult['USERSPOST'], function ($v, $k) use (&$_tmpUSERSPOST, $_tmp) {
                // print_r($v);
                $strName = empty($_tmp[$v['ID_STAFF']]['NAME']) ?
                    $_tmp[$v['ID_STAFF']]['EMAIL'] :
                    $_tmp[$v['ID_STAFF']]['NAME'] . " " . $_tmp[$v['ID_STAFF']]['LAST_NAME'];
                $_tmpUSERSPOST[] = [
                    'ID' => $v['ID_STAFF'],
                    'NAME' => $strName,
                    'PHOTO' => $_tmp[$v['ID_STAFF']]['PERSONAL_PHOTO'] ?: '/local/apps/img/ui-user.svg'
                ];
            });
            $this->arResult['USERSPOST'] = $_tmpUSERSPOST;

            $numIDsuperviserPost = $this->fields['ID_SUPERVISOR_POST']->getValue()->getRaw();

            if(!empty($this->arResult['RES']['ID_JOB_FOLDERB24'])) {
                $this->arResult['POST_FOLDER'] = $this->getPostFolderInfo();
            }
        }

		$this->arResult['arID_JOB_FOLDERB24'] = $this->getidsDiskBitrix24();

		$res = PostsTable::query()
			->setSelect(["ID", "NAME_POST"])
			->setFilter(['ID_PORTAL' => (int)$GLOBALS['FWINK']['ID_PORTAL']])
			->setOrder(['NAME_POST' => 'asc'])
			->exec()->fetchAll();
		$arDepartments = [];
		array_walk($res, function ($v, $k) use (&$arDepartments) {
			$arDepartments[$v['ID']] = $v['NAME_POST'];
		});
		$this->arResult['REST']['DEPARTMENTS'] = $arDepartments;
		$this->arResult['DEPARTMENT'] = $numIDsuperviserPost;


		$enable = ['NAME_POST','CKP_OF_POST', 'FUNCTION_OF_POST', 'ID_SUPERVISOR_POST', 'ID_SHIEF_POST_USERB24', 'SORT', 'ID_JOB_FOLDERB24'];
		$massive = [];
		foreach ($this->arResult['USER'] as $k => $v) {
			if ($v['CODE'] == 'ID_SHIEF_POST_USERB24') {
				/*
				 * set in visible part detail page Letters name of a shief
				 */
				$numUserID = preg_replace('/[^0-9]/', '', $v['SHOW']);

				if ($numUserID > 0) {
					$v['SHOW'] = empty($this->arResult['REST']['USERS'][$numUserID]['NAME']) ?
						str_replace("rzimin", "abcdef", $this->arResult['REST']['USERS'][$numUserID]['EMAIL']) :
						$this->arResult['REST']['USERS'][$numUserID]['NAME'];
					$v['SHOW'] = '<span>' . $v['SHOW'] . '</span>';
				} else {
					$v['SHOW'] = '<span data-inf-rest-users-count="' . count($this->arResult['REST']['USERS']) . '" data-id="' . $numUserID . '">[----]</span>';
				}
			}

			if (!empty($enable)) {
				if (in_array($v['CODE'], $enable)) {
					$massive[] = $v;
				}
			} else {
				$massive[] = $v;
			}
		}
		$this->arResult['USER'] = $massive;

		$this->arResult['PATH_TO_AJAX'] = $this->getPath() . '/ajax.php';
		$this->arResult['SIGNED_PARAMS'] = (new Signer())->sign(
			base64_encode(serialize($this->arParams)),
			'local.fwink.post.detail'
		);


		$this->setNavChain();
	}

    protected function getPostFolderInfo()
    {
        $result = [];
        $res = Rest::execute('disk.folder.get', ['id' => $this->arResult['RES']['ID_JOB_FOLDERB24']]);
        if($res['ID']) {
           $result = [
               'id' => $res['ID'],
               'name' => $res['NAME'],
               'link' => $res['DETAIL_URL']
           ];
        }
        return $result;
    }

	/**
	 * @return array
	 * @throws ArgumentException
	 * @throws ArgumentNullException
	 * @throws ArgumentOutOfRangeException
	 * @throws ArgumentTypeException
	 * @throws ConfigurationException
	 * @throws ObjectPropertyException
	 * @throws SystemException
	 * @throws \Local\Fwink\AccessControl\Exception
	 */
	private function getList(): array
	{
		$rows = [];
		$parameters = [
			'select' => $this->fieldsSelect,
			'filter' => ['ID' => $this->elementId]
		];
		$user = $this->manager->getList($parameters);
		if ($row = $user->fetch()) {
			foreach ($row as $key => $value) {
				if ($value === null) {
					$row[$key] = '';
				}
			}
			// use with ckeditor !!

			$this->arResult['RES'] = $row;
			$rows = $this->getFields($row);
			$this->elementName = UserHelper::getUserName($row);
			$this->companyId = $row['UF_HELPDESK_COMPANY'];
			$this->companyName = $row['COMPANY_TITLE'];
		}

		return $rows;
	}

	/**
	 * @param $row
	 *
	 * @return array
	 * @throws ArgumentException
	 * @throws ArgumentNullException
	 * @throws ArgumentTypeException
	 * @throws ConfigurationException
	 * @throws ArgumentOutOfRangeException
	 * @throws ObjectPropertyException
	 * @throws SystemException
	 * @throws \Local\Fwink\AccessControl\Exception
	 */
	private function getFields($row): array
	{
		$result = [];


		/** @var Field $field */
		foreach ($this->fields as $fieldName => $field) {
			if (in_array($fieldName, $this->fieldsShow, true)) {
				$html = [];
				$field->setValueFromDb($row);
				if ($this->arResult['ACCESS']['read'] === true && $field->isReadable() === true) {
					$html['CODE'] = $fieldName;
					$html['TITLE'] = $field->getInfo()->getTitle();
					$html['SHOW'] = $this->getFieldShowHtml($field);
				}
				if ($this->arResult['ACCESS']['update'] === true &&
					UserHelper::checkUserAccessRole($this->elementId) &&
					$field->isEditable() === true
				) {
					$html['EDIT'] = $this->getFieldEditHtml($field);
					$html['EDIT'] .= $this->getInputHidden($this->elementId);
				}
				$result[] = $html;
			}
		}

		/*foreach ($row as $fieldName => $field) {
//			if (in_array($fieldName, $this->fieldsShow, true)) {
				$html = [];
//				$field->setValueFromDb($row);
				if ($this->arResult['ACCESS']['read'] === true) {
					$html['CODE'] = $fieldName;
					$html['TITLE'] = $field;
					$html['SHOW'] = $this->getFieldShowHtml_fromDictionary($fieldName,$field);
				}
				if ($this->arResult['ACCESS']['update'] === true &&
					UserHelper::checkUserAccessRole($this->elementId) &&
					$field->isEditable() === true
				) {
					$html['EDIT'] = $this->getFieldEditHtml($field);
					$html['EDIT'] .= $this->getInputHidden($this->elementId);
				}
				$result[] = $html;
//			}
		}*/

		return $result;
	}

	private function getFieldShowHtml($field): string
	{
		$entityName = $this->manager->getEntityName();
		$fieldNameTable = $field->getInfo()->getName();

		$getValue = $field->getValue();
		if (HelpersField::checkUrl($entityName, $fieldNameTable)) {
//            $getValue->setShowUrl(true);
		}
		if ($getValue instanceof Local\Fwink\Fields\Value\Type\Url ||
			$getValue instanceof Local\Fwink\Fields\Value\Type\Staff ||
			$getValue instanceof Local\Fwink\Fields\Value\Type\User
		) {
			$templates = $field->getUrlTemplate();
			if ($templates && $fieldNameTable !== 'FULL_NAME') {
				$getValue->setUrlTemplate($templates);
			}
		}

		$value = $field->getValue()->get();

		return $value ? $field->getShowView()->getHtml() : '<span>[---]</span>';
	}

	/**
	 * @param Field $field
	 *
	 * @return string
	 * @throws ArgumentException
	 * @throws ArgumentNullException
	 * @throws ArgumentOutOfRangeException
	 * @throws ObjectPropertyException
	 * @throws SystemException
	 * @throws \Local\Fwink\AccessControl\Exception
	 */
	private function getFieldEditHtml($field): string
	{
		$valueId = $field->getValue()->get();
		switch ($field->getInfo()->getName()) {
			case 'ACTIVE':
				$value = HelpersStaff::getActiveValues($valueId);
				break;
			case 'UF_HELPDESK_COMPANY':
				$value = HelpersCompany::getSelectValues($valueId);
				break;
			case 'UF_HELPDESK_MANAGER':
				$value = HelpersStaff::getSelectValues($valueId);
				break;
			case 'GROUP_ID':
				$value = HelpersStaff::getSelectValuesGroup($valueId);
				break;
			case 'WORK_NOTES':
				$value = $field->getValue()->getRaw();
				break;
			case 'FUNCTION_OF_POST':
				$value = $field->getValue()->getRaw();
				break;
			case 'CKP_OF_POST':
				$value = $valueId;//$value = $field->getValue()->getRaw();
				break;
			default:
				$value = $valueId;
				break;
		}
		$field->getEditView()->setValue($value);

		return $field->getEditView()->getHtml();
	}

	/**
	 * @param $elementId
	 *
	 * @return string
	 * @throws ArgumentTypeException
	 */
	private function getInputHidden($elementId): string
	{
		$signer = new Signer();
		$params = base64_encode(serialize([
			'ELEMENT_ID' => $elementId,
			'FIELDS' => $this->arParams['FIELDS'],
			'ENTITY' => $this->arParams['ENTITY'],
		]));
		$signedParams = $signer->sign($params, 'local.fwink.post.detail');

		$dom = new DOMDocument();
		$element = $dom->createElement('input');
		$element->setAttribute('type', 'hidden');
		$element->setAttribute('name', 'signedParamsString');
		$element->setAttribute('value', $signedParams);
		$dom->appendChild($element);

		return $dom->saveXML();
	}

	public function getSupervisor($numPortal = 0, $idPostElement = 0)
	{

		$result = [];
		/* custom solution */
		$numIDportal = $numIDportal > 0 ? $numIDportal : (int)$GLOBALS['FWINK']['ID_PORTAL'];
		$idPostElement = $idPostElement > 0 ? $idPostElement : $this->elementId;

		// todo: переделать на орм
		$strSql = "SELECT  post.ID,
				post.ID_PORTAL,
				post.NAME_POST,
				post.ID_SHIEF_POST_USERB24,
				parent.NAME_POST parentname,
				parent.ID_SHIEF_POST_USERB24 parentshief,
                parent_block.NAME parentblockname,    
                parent_block.ID parentblockid
		  FROM  itrack_chart_post post
		  LEFT JOIN itrack_chart_post parent ON parent.ID=post.ID_SUPERVISOR_POST 
          LEFT JOIN itrack_chart_blocks parent_block ON parent_block.ID_POST=post.ID_SUPERVISOR_POST
		  WHERE post.ID_PORTAL='$numIDportal' AND post.ID='" . $idPostElement . "'";

		if ($numIDportal > 0) {
			$arresult = \Bitrix\Main\Application::getConnection()->query($strSql)->fetch();
			$result = [
				'NAME' => $arresult['parentname'],
				'SHIEF_ID' => $arresult['parentshief'],
				'SHIEF_NAME' => '',
                'DEPARTMENT_NAME' => $arresult['parentblockname']
			];
		}

		return $result;
	}

	private function getUsersPost()
	{
		$varID_PORTAL = $GLOBALS['FWINK']['ID_PORTAL'];
		$parameters = [
			'select' => [
				'ID',
				// '*'
				//'ID_PORTAL',
				'ID_STAFF',
				//'NAME_POST'
				// 'SECTION_CODE' => 'SECTION.CODE',
				// 'UF_SECTION_CODE_PATH' => 'SECTION.UF_SECTION_CODE_PATH',
			],
			'filter' => array(
				'ID_POST' => $this->elementId,
				'ID_PORTAL' => $varID_PORTAL,
				"!ID_STAFF" => 0,
				'ACTIVE' => 'Y'
			)
		];
		$arPostTable = UsersPost::getList($parameters)->fetchAll();
		/*(int)$arPostTable['ID_PORTAL']<1&&$arPostTable['ID_PORTAL']=11;
		(int)$arPostTable['CKP_OF_POST']<1&&$arPostTable['CKP_OF_POST']=11;

//			[ID_PORTAL] => 0
//			[NAME_POST] => 7
//			[CKP_OF_POST] => 7

		$parameters = [
			'select' => [
				'ID',
				// 'ID_PORTAL',
				'ID_STAFF',
				// 'ID_POST'
				// '*'
			],
			'filter'  => array(
				// 'ID' => $arPostTable['ID'],
				'ID_PORTAL' => $arPostTable['ID_PORTAL'],
				'ID_POST' => $arPostTable['CKP_OF_POST']
			)
		];
		$arUserPostTable = UsersPost::getList($parameters)->fetchAll();*/
		$this->arResult['USERSPOST'] = $arPostTable;
		$existIDstaff = array_map(function ($sel) {
			if ((int)$sel['ID_STAFF'] > 0) {
				return ($sel['ID_STAFF']);
			}
		}, $arPostTable); //$arUserPostTable

		empty($existIDstaff) && $existIDstaff = [];

		return $existIDstaff;
	}

	public function getUserByRest($arUsersPost = [])
	{
		$res = [];
		if (!empty($arUsersPost)) {
			$res = Rest::execute('user.get', [
				'FILTER' => [
					'ID' => $arUsersPost,//!!! one element!!!
				]
			]);
		}
		return $res;
	}

	public function getDataDiskBitrix24(){
		$res = Rest::execute('disk.storage.getlist', [
			'FILTER' => []
		]);
		$lsFolder = [];
		$res = is_array($res) ? $res : json_decode($res, true);
		foreach ($res as $valres) {
			if ($valres['ENTITY_TYPE'] == 'user') {
				continue;
			}
			$lsFolder[] = [
				'id' => $valres['ID'],
				'name' => $valres['NAME'],
				'parent' => 0,
				'subroot' => 0,
				'main' => 1,
				'ENTITY_TYPE' => $valres['ENTITY_TYPE']
			];
			$subres = Rest::execute('disk.storage.getchildren', [
				'id' => $valres['ID'],
				'FILTER' => ['TYPE' => 'folder']
			]);
			$subres = is_array($subres) ? $subres : json_decode($subres, true);
			foreach ($subres as $valsubres) {
				if ($valsubres['TYPE'] !== 'folder') { // is custom!!
					continue;
				}
				$lsFolder[] = [
					'id' => $valsubres['ID'],
					'name' => $valsubres['NAME'],
					'parent_id' => $valsubres['PARENT_ID'],
					'subroot' => $valsubres['STORAGE_ID'],
					'main' => 0,
					'ENTITY_TYPE' => ''
				];
			}
		}

		$lsFolder = is_array($lsFolder) ? $lsFolder : json_decode($lsFolder, true);

		return $lsFolder;
	}

	public function getidsDiskBitrix24()
	{
		$numIDportal = (int)$numIDportal > 0 ? $numIDportal : (int)$GLOBALS['FWINK']['ID_PORTAL'];
		/*
			TODO
			if file with portal data Disk exist and timestampCreate nearest ONE day
				read file
			else
				make rest request
				make file with portal
				read file
		 */
		$arResults = [];

        $cache = \Bitrix\Main\Data\Cache::createInstance();
        $taggedCache = \Bitrix\Main\Application::getInstance()->getTaggedCache();
        $path = 'chart/disk_folders';
        if($cache->initCache(86400, 'ic_chart_diskfolders'.$GLOBALS['FWINK']['ID_PORTAL'], $path)) {
            $arResults = $cache->getVars();
        } elseif ($cache->startDataCache()) {
            $taggedCache->registerTag('ic_chart_diskfolders_'.$GLOBALS['FWINK']['ID_PORTAL']);
            $arResults = $this->getDataDiskBitrix24();
            $taggedCache->endTagCache();
            $cache->endDataCache($arResults);
        }

		/*$pathfile = $_SERVER['DOCUMENT_ROOT'] . '/local/modules/local.fwink/' . 'result_dFolderjsonPortal_'.$numIDportal.'.json';

		if (file_exists($pathfile)) {
			$strInf = file_get_contents($pathfile);
			$arResults = json_decode($strInf, true);
		}else{
			$arResults=$this->getDataDiskBitrix24();
			$jsonarResults = json_encode($arResults, JSON_UNESCAPED_UNICODE);
			$pathfile = $_SERVER['DOCUMENT_ROOT'] . '/local/modules/local.fwink/' . 'result_dFolderjsonPortal_'.$numIDportal.'.json';
			file_put_contents($pathfile, $jsonarResults);
		}*/

		$arFileMain = $arFile = [];
		array_walk($arResults, function ($v, $k) use (&$arFile, &$arFileMain) {
			if ($v['main']) {
				$arFileMain[$v['id']] = $v;
			} else {
				$arFile[$v['id']] = $v;
			}
		});

		$arResults = [];
		foreach ($arFileMain as $key => $val) {
			$arResults[] = $val;
			$arSubIntoMain = ((array_filter($arFile, function ($arrayValue) use ($val) {
				return $arrayValue['subroot'] == $val['id'];
			})));
			$arSubs = array_map(function ($a) {
				$a['name'] = '---' . $a['name'];
				return $a;
			}, array_values($arSubIntoMain));
			$arResults = array_merge($arResults, $arSubs);
		}

		return $arResults;

	}

	private function setNavChain(): void
	{
		global $APPLICATION;

		if ($this->companyId) {
			$companyTitle = $this->companyName;
			$companyLink = HelpersFolder::get(HelpersFolder::PATH_COMPANY) . $this->companyId . '/';
			$APPLICATION->AddChainItem($companyTitle, $companyLink);
		}

		$title = $this->elementName;
		$APPLICATION->AddChainItem($title);
	}

	/**
	 * @return array
	 * @throws ArgumentException
	 * @throws ArgumentNullException
	 * @throws SystemException
	 */
	private function update(): array
	{
		$status = [];
		$data = $this->getData();

		if (!empty($data) && UserHelper::checkUserAccessRole($this->elementId)) {

			/*042021 herepoint*/

			$result = $this->manager->update($this->elementId, $data);
			if ($result['RESULT']) {
				//$this->pull();
				$status['STATUS'] = 'SUCCESS';
//                $status['MESSAGE'] = Loc::getMessage('LOCAL_FWINK_MODULE_STAFF_DETAIL_MESSAGE');
			} else {
				$status['STATUS'] = 'ERROR';
				$status['MESSAGE'] = $result['OB_USER']->LAST_ERROR;
			}
		}

		return $status;
	}

	private function add(): array
    {
        $status = [];
        $data = $this->getData();

        // todo: check rights
        $data['ID_PORTAL'] = $GLOBALS['FWINK']['ID_PORTAL'];
        $result = $this->manager->add($data);
        if($result->isSuccess()) {
            $status['STATUS'] = 'SUCCESS';
            $staffId = explode(',',$this->request->get('ID_STAFF'));
            if(!empty($staffId)) {
                $staffId = array_unique($staffId);
                foreach ($staffId as $userId) {
                    if((int)$userId > 0) {
                        $arFields = [
                            'ID_PORTAL' => $data['ID_PORTAL'],
                            'ID_POST' => $result->getId(),
                            'ID_STAFF' => $userId
                        ];
                        $addResult = UsersPost::add($arFields);
                    }
                }
            }
        } else {
            $status['STATUS'] = 'ERROR';
            $status['MESSAGE'] = implode(', ', $result->getErrorMessages());
        }

        return $status;
    }

    /**
     * Удаление пользователя.
     *
     * @return array
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws SystemException
     */
    private function delete(): array
    {
        $status = [];
        $id = $this->request['ID'];

        $result = $this->manager->delete($id);
        if($result->isSuccess()) {
            $status['STATUS'] = 'SUCCESS';
        } else {
            $status['STATUS'] = 'ERROR';
            $status['MESSAGE'] = implode(', ', $result->getErrorMessages());
        }

        return $status;
    }

	/**
	 * @return array
	 */
	private function getData(): array
	{
		$data = [];
		/** @var Field $field */
		foreach ($this->fields as $fieldName => $field) {
			$fieldNameTable = $field->getInfo()->getName();
			$value = $this->request[$fieldNameTable];
			if ($value !== null) {
				$data[$fieldNameTable] = $value;
			}
		}

		if ($_FILES['PERSONAL_PHOTO']) {
			$file = [];
			foreach ($_FILES['PERSONAL_PHOTO'] as $key => $val) {
				$file['PERSONAL_PHOTO'][$key] = HelpersEncoding::fromUtf($val);
			}

			$rsUser = CUser::GetByID($this->elementId);
			$arUser = $rsUser->Fetch();
			if (!empty($arUser['PERSONAL_PHOTO'])) {
				$delete = [
					'del' => 'Y',
					'old_file' => $arUser['PERSONAL_PHOTO']
				];
				$file['PERSONAL_PHOTO'] = array_merge($file['PERSONAL_PHOTO'], $delete, ['MODULE_ID' => 'main']);
			}

			$data = array_merge($data, $file);
		}

		return $data;
	}

	/**
	 * Update data users.
	 */
	private function pull(): void
	{
		CPullStack::AddShared([
			'module_id' => 'local.fwink',
			'command' => 'update_staff_detail',
			'params' => [
				'ELEMENT_ID' => $this->elementId
			]
		]);
	}

	/**
	 * @param array $arParams
	 *
	 * @return array
	 */
	public function onPrepareComponentParams($arParams): array
	{
		$this->path = $arParams['SEF_FOLDER'];
		$this->arParams = $arParams;

		return $this->arParams;
	}

	private function getFieldShowHtml_fromDictionary($fieldName, $field): string
	{
//		$entityName = $this->manager->getEntityName();
		$fieldNameTable = $fieldName;

		$getValue = $field->getValue();
		/*if (HelpersField::checkUrl($entityName, $fieldNameTable)) {
			$getValue->setShowUrl(true);
		}*/
		/*if ($getValue instanceof Local\Fwink\Fields\Value\Type\Url ||
			$getValue instanceof Local\Fwink\Fields\Value\Type\Staff ||
			$getValue instanceof Local\Fwink\Fields\Value\Type\User
		) {
			$templates = $field->getUrlTemplate();
			if ($templates && $fieldNameTable !== 'FULL_NAME') {
				$getValue->setUrlTemplate($templates);
			}
		}*/

		$value = $field;

		return $value ? $field->getShowView()->getHtml() : '<span>[---]</span>';
	}
}
