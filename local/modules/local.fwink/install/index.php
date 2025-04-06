<?
use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Application;
use Bitrix\Main\IO\Directory;

IncludeModuleLangFile(__FILE__);

Class local_fwink extends CModule
{
	var $MODULE_ID = "local.fwink";
	public $MODULE_VERSION;
	public $MODULE_VERSION_DATE;
	public $MODULE_NAME;
	public $MODULE_DESCRIPTION;

	public function __construct()
	{
		include(__DIR__ . DIRECTORY_SEPARATOR . 'version.php');
		$this->MODULE_VERSION = $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		$this->MODULE_NAME = "local.fwink";
		$this->MODULE_DESCRIPTION = "local.fwink";
		$this->PARTNER_NAME = GetMessage('SP_CI_PARTNER_NAME');
		$this->PARTNER_URI = GetMessage('SP_CI_PARTNER_URI');
	}

	function DoInstall()
	{
		RegisterModule($this->MODULE_ID);
		$this->InstallDB();
		return true;
	}

	public function InstallFiles()
	{
		return true;
	}

	function InstallDB()
	{
		/*
		\Local\Fwink\Tables\BlocksTable::getEntity()->createDbTable();
		\Local\Fwink\Tables\PortalTable::getEntity()->createDbTable();
		\Local\Fwink\Tables\PostsTable::getEntity()->createDbTable();
		\Local\Fwink\Tables\RolesTable::getEntity()->createDbTable();
		\Local\Fwink\Tables\StaffTable::getEntity()->createDbTable();
		\Local\Fwink\Tables\StaffremoteTable::getEntity()->createDbTable();
		\Local\Fwink\Tables\UserbypostTable::getEntity()->createDbTable();*/
		return true;
	}

	function DoUninstall()
	{
		$this->UnInstallDB();
		UnRegisterModule($this->MODULE_ID);
	}

	public function UnInstallFiles()
	{

	}

	function UnInstallDB()
	{
		return true;
	}
} ?>
