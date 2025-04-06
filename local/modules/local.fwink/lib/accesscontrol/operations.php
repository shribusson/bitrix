<?

namespace Local\Fwink\AccessControl;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserGroupTable;
use CUser;
use Local\Fwink\Tables\PortalTable;
use Local\Fwink\Tables\TaskTable;
use Bitrix\Main\Config\Option;

/**
* Класс для работы с операциями доступа для сущностей модуля.
*
* Class Operations
*
* @package Local\Fwink\AccessControl
*/
class Operations
{
	public const OPERATION_CREATE = 'create';
	public const OPERATION_READ = 'read';
	public const OPERATION_UPDATE = 'update';
	public const OPERATION_DELETE = 'delete';

	private static $operationsType = [
		'create' => self::OPERATION_CREATE,
		'read' => self::OPERATION_READ,
		'update' => self::OPERATION_UPDATE,
		'delete' => self::OPERATION_DELETE,
	];

	/**
	* Проверка на доступ ко всем операциям.
	*
	* @param $entityName
	*
	* @return bool
	* @throws ArgumentException
	* @throws ObjectPropertyException
	* @throws SystemException
	*/
	public static function checkAccessAll($entityName): bool
	{
		foreach (self::getOperations() as $type) {
			if (self::checkAccess($type, $entityName) === false) {
				return false;
			}
		}

		return true;
	}

	/**
	* @return array
	*/
	public static function getOperations(): array
	{
		return self::$operationsType;
	}

	/**
	* Проверяем может ли текущий пользователь выполнять операцию типа $operationType для сущности с именем $entityName.
	*
	* @param $operationType
	* @param $entityName
	*
	* @return bool
	* @throws ArgumentException
	* @throws ObjectPropertyException
	* @throws SystemException
	*/
	public static function checkAccess($operationType, $entityName): bool
	{
		/*$entitiesDataClasses = Configuration::getInstance()->get('entitiesDataClasses');

		$name = [];
		foreach ($entitiesDataClasses as $entity) {
			$name[] = (new $entity)->getEntity()->getName();
		}


		if (!in_array($entityName, $name, true) ||
			!in_array($operationType, self::getOperations(), true)
		) {
			return false;
		}
		*/

		if(!empty($GLOBALS['FWINK']['profile']['ID']) && in_array($operationType, ['create','update','delete'])) {
            $usersCanEdit = Option::get('local.fwink','users_can_edit','');
            if(!empty($usersCanEdit)) {
                $users = explode(',', $usersCanEdit);
                return in_array($GLOBALS['FWINK']['profile']['ID'], $users) || $GLOBALS['FWINK']['admin'] === true;
            } else {
                return $GLOBALS['FWINK']['admin'] === true;
            }
        }

        $dbPortalCheck = PortalTable::query()->where('DOMAIN', $GLOBALS['FWINK']['DOMAIN'])->setSelect(['ID'])->exec();
        if($dbPortalCheck->getSelectedRowsCount() > 0) {
            return true;
        }

		return false;
	}
}
