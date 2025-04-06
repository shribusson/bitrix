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
use Local\Fwink\Helpers\Staffremote as HelpersStaff;
use Local\Fwink\Helpers\User as HelpersUser;
//use Local\Fwink\Helpers\Folder as HelpersFolder;
//use Local\Fwink\Helpers\Staffremote as HelpersPosts;
//use Local\Fwink\Helpers\User as HelpersUser;
use Local\Fwink\Tables\UserbypostTable;

class Userbypost extends Manager
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
			'ID_STAFF' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('ID_STAFF'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['ID_STAFF'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'ID_STAFF'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'ID_STAFF',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			'ID_POST' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('ID_POST'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['ID_POST'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'ID_POST'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'ID_POST',
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
        return UserbypostTable::getEntity();
    }
}
