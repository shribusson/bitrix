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
use Local\Fwink\Fields\Config\Staffremote as ConfigStaff;
use Local\Fwink\Helpers\Folder as HelpersFolder;
use Local\Fwink\Helpers\Staff as HelpersStaff;
use Local\Fwink\Helpers\User as HelpersUser;
use Local\Fwink\Staffremote;
use Local\Fwink\Tables\StaffremoteTable as WorkTable;

class StaffAddComponent extends CBitrixComponent
{
    /** @var StaffAddComponent */
    private static $moduleNames = ['local.fwink', 'pull'];

    private $path;
    /** @var Staffremote $manager */
    private $manager;
    private $access;
    private $fields;
    private $fieldsShow;

    public function executeComponent(): array
    {
        $result = [];
        try {
            $this->loadModules();
            //$this->setParams();
            $this->dataManager();
            $this->accessControl();

            switch ($this->arParams['ACTION']) {
                case 'add':
                    $result = $this->add();
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
	 * @return array
	 */
	public function getArMassiveFields(): array
	{
	    $dbPosts = \Local\Fwink\Tables\PostsTable::query()
            ->registerRuntimeField('block', new \Bitrix\Main\ORM\Fields\Relations\Reference('block', '\Local\Fwink\Tables\BlocksTable', ['this.ID' => 'ref.ID_POST']))
            ->setFilter([
				'ID_PORTAL' => $GLOBALS['FWINK']['ID_PORTAL'],
				//'!block.ID' => false, 
				//'!block.SUBDIVISION' => false
			])
            ->setOrder(['NAME_POST' => 'ASC', 'SORT' => 'ASC'])
            ->setSelect(['*','block.*'])
            ->exec();
	    $arPosts = [];
	    while($arPost = $dbPosts->fetch()) {
	        $arPosts[$arPost['ID']] = $arPost['NAME_POST'];
        }

		$this->arMassiveFields = [
		    'NAME' => [
		        'title' => 'Имя',
                'type' => 'string',
                'required' => true
            ],
            'LAST_NAME' => [
                'title' => 'Фамилия',
                'type' => 'string',
                'required' => true
            ],
            'EMAIL' => [
                'title' => 'Email',
                'type' => 'string',
                'required' => true
            ],
            'POST' => [
                'title' => 'Должность',
                'type' => 'list',
                'values' => $arPosts,
                'required' => false
            ],
            'DATE_TRAIN_END' => [
                'title' => 'Дата окночания испытательного срока',
                'type' => 'date',
                'required' => false
            ]
        ];

		return $this->arMassiveFields;
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
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ArgumentNullException
     */
    private function setParams(): void
    {
        //$this->path = HelpersFolder::get(HelpersFolder::PATH_STAFF);
    }

    private function dataManager(): void
    {
        $this->manager = new \Local\Fwink\Staff();
        $this->fields = (new \Local\Fwink\Fields\Config\Staffremote())->getFields();
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

    private function add(): array
    {
        $status = [];
        $result = new \Bitrix\Main\Result();

        $data = $this->getData();
        $needFields = $this->getArMassiveFields();
        foreach ($needFields as $code=>$arField) {
            if($arField['required'] && empty($data[$code])) {
                $result->addError(new \Bitrix\Main\Error('Не заполнено обязательное поле '.$arField['title'], 'empty_req_'.$code));
            }
        }
        if($result->isSuccess()) {
            $result = $this->manager->add($data);
            if($result->isSuccess()) {
                $status['status'] = 'success';
                $status['data'] = $result->getData();
            } else {
                $status['status'] = 'error';
                $status['message'] = implode(', ', $result->getErrorMessages());
            }
        } else {
            $status['status'] = 'error';
            $status['message'] = implode(', ', $result->getErrorMessages());
        }

        return $status;
    }

    private function getData(): array
    {
        $data = [];

        /** @var \Local\Fwink\Fields\Field $field */
        foreach ($this->fields as $fieldName => $field) {
            $fieldName = $field->getInfo()->getName();
            $value = $this->request[$fieldName];
            if ($value !== null && $value !== '') {
                $data[$fieldName] = $value;
            }
        }

        return $data;
    }

    /**
     * Set Template Data.
     */
    private function setTemplateData(): void
    {
		$this->arResult['MASSIVES']=$this->getArMassiveFields();
        $this->arResult['PATH_TO_AJAX'] = $this->getPath() . '/ajax.php';
        $this->arResult['SIGNED_PARAMS'] = (new Signer())->sign(
            base64_encode(serialize($this->arParams)),
            'local.fwink.staff.add'
        );
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
