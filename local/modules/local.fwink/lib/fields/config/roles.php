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
use Local\Fwink\Tables\RolesTable;


class Roles extends Manager
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
			'ID_PORTAL' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('ID_PORTAL'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['ID_PORTAL'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'ID_PORTAL'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'ID_PORTAL',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			'NAME' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('NAME'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['NAME'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'NAME'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'NAME',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			'GROUP_SOCIALNETWORKB24' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('GROUP_SOCIALNETWORKB24'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['GROUP_SOCIALNETWORKB24'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'GROUP_SOCIALNETWORKB24'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'GROUP_SOCIALNETWORKB24',
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
        return RolesTable::getEntity();
    }
}
