<?
/** @noinspection PhpCSValidationInspection */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Security\Sign\Signer;
use Bitrix\Main\UserGroupTable;
use Local\Fwink\AccessControl\Operations;
use Local\Fwink\Fields\Config\Staff as ConfigStaff;
use Local\Fwink\Helpers\Folder as HelpersFolder;
use Local\Fwink\Helpers\Staff as HelpersStaff;
use Local\Fwink\Helpers\User as HelpersUser;
use Local\Fwink\Staff;
use Local\Fwink\Tables\PostsTable;
use Local\Fwink\Tables\BlocksTable as WorkTable;

class CompanyblockEditComponent extends CBitrixComponent
{
	/** @var CompanyblockEditComponent */
	private static $moduleNames = ['local.fwink', 'pull'];

	public $arMassiveFields = [];
	private $path;
	private $manager;
	private $access;
	private $fields;
	private $fieldsShow;

	public function executeComponent(): array
	{
		$result = [];
		try {
			$this->loadModules();
			$this->dataManager();

			switch ($this->arParams['ACTION']) {
				case 'add':
					$actionResult = $this->add();
					break;
				case 'edit':
                    $actionResult = $this->edit();
					break;
                case 'delete':
                    $actionResult = $this->delete();
                    break;
                case 'applyChildColors':
                    $actionResult = $this->applyChildColors();
                    break;
				default:
					$this->setTemplateData();
					$this->includeComponentTemplate();
			}
		} catch (Exception $e) {
			ShowError($e->getMessage());
		}

		if($actionResult instanceof \Bitrix\Main\Result) {
		    if($actionResult->isSuccess()) {
		        $result = [
		            'status' => 'success',
                    'data' => $actionResult->getData()
                ];
            } else {
		        $result = [
		            'status' => 'error',
                    'message' => implode(', ', $actionResult->getErrorMessages())
                ];
            }
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
					Loc::getMessage('LOCAL_SERVICEDESK_MODULE_LOAD_ERROR', ['#MODULE_NAME#' => $moduleName])
				);
			}
		}
	}

	private function add()
	{
        $result = new \Bitrix\Main\Result();

        $data = $this->getData();
        $posts = $this->request['POSTS'];
        $posts = array_filter($posts, static function($a) {return (int)$a > 0;});
        if(!empty($posts)) {
            $data['POSTS'] = $posts;
        }
        $headId = 0;
        if(!empty($data['POSTS'])) {
            $dbPost = PostsTable::query()
                ->setFilter(['ID_PORTAL' => $GLOBALS['FWINK']['ID_PORTAL'], '=ID' => $data['POSTS']])
                ->setSelect(['ID','ID_SHIEF_POST_USERB24'])
                ->exec();
            if($dbPost->getSelectedRowsCount() > 0) {
                while($arPost = $dbPost->fetch()) {
                    if (!empty($arPost['ID_SHIEF_POST_USERB24'])) {
                        $headId = $arPost['ID_SHIEF_POST_USERB24'];
                        break;
                    }
                }
            } else {
                $result->addError(new \Bitrix\Main\Error('Неправильная должность', 'wrong_post'));
            }
        }
        if($result->isSuccess()) {
            try {
                $data['SUBDIVISION'] = $this->getDepartmentId($data['NAME'], $data['ID_PARENT_BLOCK'], $headId, $data['SORT']);
                $additionalFields = [];
                if(!empty($this->request['CUSTOM_WIDTH'])) {
                    $additionalFields['CUSTOM_WIDTH'] = $this->request['CUSTOM_WIDTH'];
                } else {
                    $additionalFields['CUSTOM_WIDTH'] = '';
                }
                try {
                    $data['ADDICT_PARAM'] = \Bitrix\Main\Web\Json::encode($additionalFields);
                } catch(\Exception $e) {

                }
                $addResult = $this->manager->add($data);
                if(!$addResult->isSuccess()) {
                    $result->addError(new \Bitrix\Main\Error('Ошибка создания блока'));
                    if((int)$data['SUBDIVISION'] > 0) {
                        $delDepartment = \Local\Fwink\Rest::execute('department.delete', ['ID' => $data['SUBDIVISION']]);
                    }
                } else {
                    // todo: add event to link users
                }
            } catch(\Exception $e) {
                if((int)$data['SUBDIVISION'] > 0) {
                    $delDepartment = \Local\Fwink\Rest::execute('department.delete', ['ID' => $data['SUBDIVISION']]);
                }
                $result->addError(new \Bitrix\Main\Error('Внутренняя ошибка'.$e));
            }
        }

		return $result;
	}

	private function edit()
    {
        $result = new \Bitrix\Main\Result();

        $data = $this->getData();

        if((int)$data['ID'] > 0) {
            $dbBlock = \Local\Fwink\Tables\BlocksTable::query()
                ->setFilter(['ID_PORTAL' => $GLOBALS['FWINK']['ID_PORTAL'], '=ID' => $data['ID']])
                ->setSelect(['*'])
                ->exec();
            if($arBlock = $dbBlock->fetch()) {
                $updateFields = [];
                foreach($data as $code=>$val) {
                    if($val !== $arBlock[$code] || $code === 'COLOR_BLOCK') {
                        $updateFields[$code] = $val;
                    }
                }
                $posts = $this->request['POSTS'];
                if(!empty($posts)) {
                    $updateFields['POSTS'] = $posts;
                }
                if(!isset($data['IS_HIDE']) && $arBlock['IS_HIDE'] === 'Y') {
                    $updateFields['IS_HIDE'] = 'N';
                }
                if(!isset($data['COLOR_BY_PARENT']) && $arBlock['COLOR_BY_PARENT'] === 'Y') {
                    $updateFields['COLOR_BY_PARENT'] = 'N';
                }
                $additionalFields = [];
                if(!empty($this->request['CUSTOM_WIDTH'])) {
                    $additionalFields['CUSTOM_WIDTH'] = $this->request['CUSTOM_WIDTH'];
                } else {
                    $additionalFields['CUSTOM_WIDTH'] = '';
                }
                try {
                    $updateFields['ADDICT_PARAM'] = \Bitrix\Main\Web\Json::encode($additionalFields);
                } catch(\Exception $e) {

                }
                if(!empty($updateFields)) {
                    $updateResult = $this->manager->update($data['ID'], $updateFields);
                    if($updateResult->isSuccess()) {
                        if(!empty($updateFields['ID_PARENT_BLOCK'])) {
                            // TODO: event to move
                            // изменить родителя связанного департамента на нового
                            $dbNewParent = \Local\Fwink\Tables\BlocksTable::query()
                                ->setFilter(['ID_PORTAL' => $GLOBALS['FWINK']['ID_PORTAL'], '=ID' => $data['ID_PARENT_BLOCK']])
                                ->setSelect(['ID','SUBDIVISION'])
                                ->exec();
                            if($arNewParent = $dbNewParent->fetch()) {
                                \Local\Fwink\Rest::execute('department.update',['ID' => $arBlock['SUBDIVISION'], 'PARENT' => $arNewParent['SUBDIVISION']]);
                            }
                        }
                        if(!empty($updateFields['NAME'])) {
                            \Local\Fwink\Rest::execute('department.update', ['ID' => $arBlock['SUBDIVISION'], 'NAME' => $updateFields['NAME']]);
                        }
                    } else {
                        LocalFwink::Log(print_r($updateResult->getErrorMessages(), true));
                        $result->addError(new \Bitrix\Main\Error('Ошибка обновления блока'));
                    }
                }
            } else {
                $result->addError(new \Bitrix\Main\Error('Блок не найден'));
            }
        } else {
            $result->addError(new \Bitrix\Main\Error('Некорректный ид блока'));
        }

        return $result;
    }

    private function applyChildColors()
    {
        $result = new \Bitrix\Main\Result();
        $id = $this->request['ID'];
        $color = $this->request['color'];
        if(!empty($id)) {
            $dbBlock = \Local\Fwink\Tables\BlocksTable::query()
                ->setFilter(['ID_PORTAL' => $GLOBALS['FWINK']['ID_PORTAL'], '=ID' => $id])
                ->setSelect(['ID', 'COLOR_BLOCK'])
                ->exec();
            if($arBlock = $dbBlock->fetch()) {
                if(empty($color) || $color === $arBlock['COLOR_BLOCK']) {
                    $color = $arBlock['COLOR_BLOCK'];
                }
                $this->updateChildColor($id, $color);
            } else {
                $result->addError(new \Bitrix\Main\Error('Блок не найден'));
            }
        } else {
            $result->addError(new \Bitrix\Main\Error('Некорректный ид блока'));
        }
        return $result;
    }

    private function updateChildColor($parentId, $color)
    {
        $dbChilds = \Local\Fwink\Tables\BlocksTable::query()
            ->setFilter(['=ID_PARENT_BLOCK' => $parentId])
            ->setSelect(['ID'])
            ->exec();
        if($dbChilds->getSelectedRowsCount() > 0) {
            $childs = $dbChilds->fetchCollection();
            $arIds = [];
            if($childs !== null) {
                foreach ($childs as $child) {
                    $arIds[] = $child->getId();
                    $child->setColorBlock($color);
                    //$child->setColorByParent('Y');
                }

                $childs->save();
                $this->updateChildColor($arIds, $color);
            }
        }
    }

    private function delete()
    {
        $result = new \Bitrix\Main\Result();
        $id = $this->request['ID'];
        if(!empty($id)) {
            $dbBlock = \Local\Fwink\Tables\BlocksTable::query()
                ->setFilter(['ID_PORTAL' => $GLOBALS['FWINK']['ID_PORTAL'], '=ID' => $id])
                ->setSelect(['ID','ID_PARENT_BLOCK','SUBDIVISION','PARENT_DEPARTMENT' => 'PARENT_BLOCK.SUBDIVISION'])
                ->exec();
            if($arBlock = $dbBlock->fetch()) {
                $deleteResult = $this->manager->delete($id);
                if($deleteResult->isSuccess()) {
                    try {
                        $dbChildren = \Local\Fwink\Tables\BlocksTable::query()
                            ->setFilter(['ID_PORTAL' => $GLOBALS['FWINK']['ID_PORTAL'], 'ID_PARENT_BLOCK' => $id])
                            ->setSelect(['ID', 'SUBDIVISION'])
                            ->exec();
                        while ($arChildren = $dbChildren->fetch()) {
                            $upd = \Local\Fwink\Tables\BlocksTable::update($arChildren['ID'], ['ID_PARENT_BLOCK' => $arBlock['ID_PARENT_BLOCK']]);
                            if ($upd->isSuccess()) {
                                \Local\Fwink\Rest::execute('department.update', ['ID' => $arChildren['SUBDIVISION'], 'PARENT' => $arBlock['PARENT_DEPARTMENT']]);
                            }
                        }
                        \Local\Fwink\Rest::execute('department.delete', ['ID' => $arBlock['SUBDIVISION']]);
                    } catch(\Exception $e) {
                        $result->addError(new \Bitrix\Main\Error('Ошибка удаления блока'));
                    }
                } else {
                    $result->addError(new \Bitrix\Main\Error('Ошибка удаления блока '));
                }
            } else {
                $result->addError(new \Bitrix\Main\Error('Блок не найден'));
            }
        } else {
            $result->addError(new \Bitrix\Main\Error('Некорректный ид блока'));
        }
        return $result;
    }

	private function getData(): array
	{
		$data = [];

		/** @var \Local\Fwink\Fields\Field $field */
		foreach ($this->fields as $fieldName => $field) {
			$fieldName = $field->getInfo()->getName();
			if(isset($this->request[$fieldName])) {
				$data[$fieldName] = $this->request[$fieldName];
			}
		}

		return $data;
	}

	private function getDepartmentId($name, $parentBlock = 0, $headId = 0, $sort = 0)
    {
        $parentDepartment = 0;
        if((int)$parentBlock > 0) {
            $dbParent = WorkTable::query()
                ->setFilter(['ID_PORTAL' => $GLOBALS['FWINK']['ID_PORTAL'], '=ID' => $parentBlock])
                ->setSelect(['ID', 'SUBDIVISION'])
                ->exec();
            if($arParent = $dbParent->fetch()) {
                if((int)$arParent['SUBDIVISION'] > 0) {
                    $checkParent = \Local\Fwink\Rest::execute('department.get', ['ID' => $arParent['SUBDIVISION']]);
                    if(!empty($checkParent)) {
                        $parentDepartment = (int)$arParent['SUBDIVISION'];
                    }
                }
            }
        }
        $fields = [
            'NAME' => $name
        ];
        if($parentDepartment > 0) {
            $fields['PARENT'] = $parentDepartment;
        }
        if((int)$headId > 0) {
            $fields['UF_HEAD'] = $headId;
        }
        if((int)$sort >= 0) {
            $fields['SORT'] = $sort;
        }
        $newDepartment = \Local\Fwink\Rest::execute('department.add', $fields);
        return $newDepartment;
    }

	/**
	 * Update data users.
	 */
	private function pull(): void
	{
		\CPullStack::AddShared([
			'module_id' => 'local.fwink',
			'command' => 'update_staff_list'
		]);
	}

	/**
	 * Set Template Data.
	 */
	private function setTemplateData(): void
	{
        if(!empty($this->arParams['ELEMENT_ID'])) {
            $this->arResult['ID'] = $this->arParams['ELEMENT_ID'];
        }
		$this->arResult['POSTS'] = $this->getPosts();
		$this->arResult['BLOCKS'] = $this->getBlocks();
		$this->arResult['FIELDS'] = $this->getArMassiveFields();

		$this->arResult['PATH_TO_AJAX'] = $this->getPath() . '/ajax.php';
		$this->arResult['SIGNED_PARAMS'] = (new Signer())->sign(
			base64_encode(serialize($this->arParams)),
			'local.fwink.companyblock.edit'
		);
	}

	private function getPosts()
	{

		$res = PostsTable::query()
            ->setFilter(['ID_PORTAL' => $GLOBALS['FWINK']['ID_PORTAL']]) // todo: replace
            ->setSelect(["ID", "NAME_POST"])
            ->setOrder(["NAME_POST" => "ASC"])
            ->exec()->fetchAll();
		$arPosts = [];
		array_walk($res, function ($v, $k) use (&$arPosts) {
			$arPosts[$v['ID']] = $v['NAME_POST'];
		});

		return $arPosts;
	}

	private function getBlocks()
	{
	    $filter = ['ID_PORTAL' => $GLOBALS['FWINK']['ID_PORTAL']]; // todo: replace
	    if(!empty($this->arParams['ELEMENT_ID'])) {
	        $filter['!ID'] = $this->arParams['ELEMENT_ID'];
        }
		$res = WorkTable::query()
            ->setFilter($filter)
            ->setSelect(["ID", "NAME"])
            ->setOrder(['NAME' => 'ASC'])
            ->exec()->fetchAll();
		$arBlocks = [];
		array_walk($res, function ($v, $k) use (&$arBlocks) {
			$arBlocks[$v['ID']] = $v['NAME'];
		});

		return $arBlocks;
	}


	/**
	 * @return array
	 */
	public function getArMassiveFields(): array
	{
	    $currentValues = [];

        $this->arMassiveFields = [
            'NAME' => [
                'title' => 'Имя',
                'type' => 'string',
                'required' => true,
                'value' => ''
            ],
            'ID_PARENT_BLOCK' => [
                'title' => 'Родительский блок',
                'type' => 'list',
                'values' => $this->arResult['BLOCKS'],
                'value' => !empty($GLOBALS['FWINK']['requestURL']['parent_id']) ? $GLOBALS['FWINK']['requestURL']['parent_id'] : '',
                'disabled' => !empty($GLOBALS['FWINK']['requestURL']['parent_id'])
            ],
            'POSTS' => [
                'title' => 'Связанная должность',
                'type' => 'list',
                'multiple' => 'Y',
                'values' => $this->arResult['POSTS'],
                'value' => []
            ],
            'COLOR_HEADER' => [
                'title' => 'Цвет заголовка блока',
                'type' => 'color',
                'value' => ''
            ],
            'COLOR_BLOCK' => [
                'title' => 'Цвет фона блока',
                'type' => 'color',
                'value' => ''
            ],
            'COLOR_BY_PARENT' => [
                'title' => 'Цвет фона из родительского блока',
                'type' => 'boolean',
                'value' => 'N',
                'parent_value' => ''
            ],
            'NUMBER' => [
                'title' => 'Номер блока',
                'type' => 'string',
                'value' => ''
            ],
            'SORT' => [
                'title' => 'Сортировка (чем меньше, тем левее)',
                'type' => 'string',
                'value' => ''
            ],
            'IS_HIDE' => [
                'title' => 'Скрытый блок',
                'type' => 'boolean',
                'value' => ''
            ]
        ];

	    if(!empty($this->arParams['ELEMENT_ID'])) {
	        $dbBlock = WorkTable::query()
                ->setFilter(['=ID' => $this->arParams['ELEMENT_ID']])
                ->setSelect(['*','POSTS','PARENT_BLOCK'/*,'PARENT_COLOR' => 'PARENT_BLOCK.COLOR_BLOCK'*/])
                ->exec();
	        if($block = $dbBlock->fetchObject()) {
                foreach ($this->arMassiveFields as $fCode=>$arField) {
                    switch($fCode) {
                        case 'POSTS':
                            $value = [];
                            foreach($block->getPosts() as $post) {
                                $value[] = $post->getId();
                            }
                            $this->arMassiveFields[$fCode]['value'] = $value;
                            break;
                        case 'ID_PARENT_BLOCK':
                            $value = $block->get($fCode);
                            $this->arMassiveFields[$fCode]['value'] = !empty($value) ? $value : (!empty($GLOBALS['FWINK']['requestURL']['parent_id']) ? $GLOBALS['FWINK']['requestURL']['parent_id'] : ''); // todo: replace
                            break;
                        case 'COLOR_BY_PARENT':
                            $value = $block->get($fCode);
                            $parentBlock = $block->getParentBlock();
                            if($parentBlock) {
                                $this->arMassiveFields[$fCode]['parent_value'] = $block->getParentBlock()->getColorBlock();
                            }
                            if(!empty($value)) {
                                $this->arMassiveFields[$fCode]['value'] = $value;
                            }
                            break;
                        default:
                            $this->arMassiveFields[$fCode]['value'] = $block->get($fCode);
                            break;
                    }
                }
            }
        }


	    $additionalFields = [];
	    if(!empty($currentValues['ADDICT_PARAM'])) {
	        try {
                $additionalFields = \Bitrix\Main\Web\Json::decode($currentValues['ADDICT_PARAM']);
            } catch(\Bitrix\Main\ArgumentException $e) {
	            // todo: log?
            }
        }
        $this->arMassiveFields['CUSTOM_WIDTH'] = [
            'title' => 'Ширина блока (по-умолчанию: 300)',
            'type' => 'int',
            'value' => $additionalFields['CUSTOM_WIDTH']
        ];
		return $this->arMassiveFields;
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

	private function dataManager(): void
	{
		$this->manager = new \Local\Fwink\Blocks();
		$this->fields = (new \Local\Fwink\Fields\Config\Blocks())->getFields();
	}

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

	private function getFields(): array
	{
		$result = [];

		/** @var \Local\Fwink\Fields\Field $field */
		foreach ($this->fields as $fieldName => $field) {
			if (in_array($fieldName, $this->fieldsShow, true)) {
				/** @noinspection DegradedSwitchInspection */
				switch ($field->getInfo()->getName()) {
					case 'GROUP_ID':
						$value = HelpersStaff::getSelectValuesGroup();
						break;
					case 'SELECT_USER':
						$value = HelpersUser::getSelectValuesMultiple();
						break;
					default:
						$value = [];
						break;
				}

				if (!empty($value)) {
					$field->getEditView()->setValue($value);
				}
				$result[$fieldName]['CODE'] = $fieldName;
				$result[$fieldName]['NAME'] = $field->getTitleView()->getHtml();
				$result[$fieldName]['VALUE'] = $field->getEditView()->getHtml();
			}
		}

		return $result;
	}
}
