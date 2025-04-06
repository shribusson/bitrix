<?

namespace Local\Fwink\Fields\Config;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserTable;
use Local\Fwink\Fields;
use Local\Fwink\Fields\Field;
use Local\Fwink\Helpers\Folder as HelpersFolder;
use Local\Fwink\Helpers\Staff as HelpersStaff;
use Local\Fwink\Helpers\User as HelpersUser;
//use Local\Fwink\Helpers\Folder as HelpersFolder;
use Local\Fwink\Helpers\Roles as HelpersRole;
//use Local\Fwink\Helpers\User as HelpersUser;
use Local\Fwink\Tables\UserrightsTable;


class Userrights extends Manager
{
    /**
     * @return array
     * @throws ArgumentException
     * @throws SystemException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws ObjectPropertyException
     */
    public function getFields(): array
    {
		return [
			'ID' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('ID'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['ID'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'ID'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'ID',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			'ID_USER_USERB24' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('ID_USER_USERB24'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['ID_USER_USERB24'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'ID_USER_USERB24'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'ID_USER_USERB24',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			'ID_ROLE' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('ID_ROLE'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['ID_ROLE'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'ID_ROLE'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'ID_ROLE',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
		];

	}

    /**
     * @return Base
     * @throws ArgumentException
     * @throws SystemException
     */
    protected function getEntity(): Base
    {
        return UserrightsTable::getEntity();
    }
}
