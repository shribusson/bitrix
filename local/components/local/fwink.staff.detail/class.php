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
use Local\Fwink\Fields\Config\Staffremote as ConfigStaff;
use Local\Fwink\Fields\Field;
use Local\Fwink\Helpers\Encoding as HelpersEncoding;
use Local\Fwink\Helpers\User as UserHelper;
use Local\Fwink\Rest;
use Local\Fwink\Staff;

class StaffDetailComponent extends CBitrixComponent
{
	/** @var StaffDetailComponent */
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
			'PERSONAL_PHOTO'
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
			switch ($this->arParams['ACTION']) {
				case 'list':
					$this->setTemplateData();
					$result = [
						'USER' => $this->arResult['USER'],
						'STATUS' => 'SUCCESS'
					];
					break;
				case 'update':
					$result = $this->update();
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
		$this->manager = new Staff();
		$this->fields = (new ConfigStaff())->getFields();
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
		$this->arResult['ELEMENT_ID'] = $this->elementId;
		$this->arResult['USER'] = $this->getList();

		$this->arResult['PATH_TO_AJAX'] = $this->getPath() . '/ajax.php';
		$this->arResult['SIGNED_PARAMS'] = (new Signer())->sign(
			base64_encode(serialize($this->arParams)),
			'local.fwink.staff.detail'
		);
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
			'filter' => ['ID' => $this->elementId]
		];
		$user = $this->manager->getList($parameters);
		if (is_array($user['items'])) {
		    $result = $user['items'][0];

		    if(!empty($result['POSTS'])) {
		        $users = array_unique(array_column($result['POSTS'], 'PARENT_SHIEF_ID'));
		        $res = Rest::getList('user.get', '', ['FILTER' => ['ID' => $users]]);
		        if(!empty($res)) {
		            foreach($res as $arUser) {
		                if(empty($arUser['PERSONAL_PHOTO'])) {
		                    $arUser['PERSONAL_PHOTO'] = '/local/apps/img/ui-user.svg';
                        }
                        $result['USERS_INFO'][$arUser['ID']] = $arUser;
                    }
                }

		        $dbBlocks = \Local\Fwink\Tables\BlocksTable::query()
                    ->setFilter([
                        'ID_PORTAL' => $GLOBALS['FWINK']['ID_PORTAL']
                    ])
                    ->setSelect(['ID','ID_PARENT_BLOCK','ID_POST','NAME'])
                    ->exec();
		        $arBlocks = $dbBlocks->fetchAll();
                function findParentBlock($blockID, $arBlocks, &$result) {
                    foreach($arBlocks as $block) {
                        if($block['ID'] == $blockID) {
                            $result[] = $block;
                            if(!empty($block['ID_PARENT_BLOCK'])) {
                                findParentBlock($block['ID_PARENT_BLOCK'], $arBlocks, $result);
                            }
                            break;
                        }
                    }
                }

		        function getPostBlocks($postId, $arBlocks) {
                    $result = [];

                    foreach($arBlocks as $arBlock) {
                        if((int)$arBlock['ID_POST'] === (int)$postId) {
                            $result[] = $arBlock;
                            findParentBlock($arBlock['ID_PARENT_BLOCK'], $arBlocks, $result);
                            break;
                        }
                    }

                    $result = array_reverse($result);
                    return $result;
                }
		        foreach($result['POSTS'] as $pkey=>$arPost) {
		            $result['POSTS'][$pkey]['BLOCKS'] = getPostBlocks($arPost['ID_POST'], $arBlocks);
                }
            }

			return $result;
		}

		return $rows;
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
			$result = $this->manager->update($this->elementId, $data);
			if ($result['RESULT']||$result===true) {
				$this->pull();
				$status['STATUS'] = 'SUCCESS';
//				$status['MESSAGE'] = Loc::getMessage('LOCAL_FWINK_MODULE_STAFF_DETAIL_MESSAGE');
			} else {
				$status['STATUS'] = 'ERROR';
//				$status['MESSAGE'] = $result['OB_USER']->LAST_ERROR;
			}
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
/*
			$rsUser = CUser::GetByID($this->elementId);
			$arUser = $rsUser->Fetch();
			if (!empty($arUser['PERSONAL_PHOTO'])) {
				$delete = [
					'del' => 'Y',
					'old_file' => $arUser['PERSONAL_PHOTO']
				];
				$file['PERSONAL_PHOTO'] = array_merge($file['PERSONAL_PHOTO'], $delete, ['MODULE_ID' => 'main']);
			}

			$data = array_merge($data, $file);*/
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
	 * Удаление пользователя.
	 *
	 * @return array
	 * @throws ArgumentException
	 * @throws ArgumentNullException
	 * @throws SystemException
	 */
	private function delete(): array
	{
		global $APPLICATION;
		$status = [];

		if (!empty($this->elementId) && UserHelper::checkUserAccessRole($this->elementId)) {
			$result = $this->manager->delete($this->elementId);
			if ($result['RESULT']) {
				$status['STATUS'] = 'SUCCESS';
				$status['MESSAGE'] = Loc::getMessage('LOCAL_FWINK_MODULE_STAFF_DETAIL_DELETE');
				$status['REDIRECT'] = $this->path;
			} else {
				$status['STATUS'] = 'ERROR';
				$errorText = '';
				foreach ($APPLICATION->ERROR_STACK as $error) {
					$errorText .= $error->msg;
				}
				$status['MESSAGE'] = $errorText;
			}
		}

		return $status;
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
}
