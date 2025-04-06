<?
/** @noinspection PhpCSValidationInspection */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use Local\Fwink\AccessControl\Exception;
use Local\Fwink\AccessControl\Operations;
use Local\Fwink\Tables\BlocksTable;
use Bitrix\Main\Security\Sign\Signer;

class StaffListcompanyblockComponent extends CBitrixComponent
{
	private static $moduleNames = ['local.fwink', 'crm'];

	private $manager;
	private $access;
	private $fields;
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
		$this->manager = new \Local\Fwink\Blocks();
		$this->fields = (new \Local\Fwink\Fields\Config\Blocks())->getFields();
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
        $this->arResult['access'] = $this->access;
        $this->arResult['PATH_TO_AJAX'] = $this->getPath() . '/ajax.php';
        $this->arResult['SIGNED_PARAMS'] = (new Signer())->sign(
            base64_encode(serialize($this->arParams)),
            'local.fwink.staff.listcompanyblock'
        );
        $this->arResult['SIGNED_PARAMS_EDIT'] = (new Signer())->sign(
            base64_encode(serialize([])),
            'local.fwink.companyblock.edit'
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
