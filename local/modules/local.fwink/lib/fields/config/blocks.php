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
use Local\Fwink\Helpers\Blocks as HelpersBlocks;
//use Local\Fwink\Helpers\User as HelpersUser;
use Local\Fwink\Tables\BlocksTable;


class Blocks extends Manager
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
						'DEFAULT' => true
					],
                    'FILTER' => [
                        'CODE' => 'ID',
                        'TYPE' => 'number',
                        'DEFAULT' => true
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
					'SHOW' => false,
					'SORT' => [
						'CODE' => 'ID_PORTAL',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			'SUBDIVISION' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('SUBDIVISION'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['SUBDIVISION'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'SUBDIVISION'
				]),
				'GRID_LIST' => [
					'SHOW' => false,
					'SORT' => [
						'CODE' => 'SUBDIVISION',
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
						'DEFAULT' => true
					],
                    'FILTER' => [
                        'CODE' => 'NAME',
                        'TYPE' => 'string',
                        'DEFAULT' => true
                    ]
				]
			]),
			/*'ID_POST' => new Field([
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
            'ID_STAFF_POST' => new Field([
                'ENTITY_FIELD' => $this->entity->getField('ID_STAFF_POST'),
                'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
                'SHOW_VIEW' => new Fields\Views\Show\Span(),
                'SELECT' => ['ID_STAFF_POST'],
                'VALUE' => new Fields\Value\Base([
                    'VALUE' => 'ID_STAFF_POST'
                ]),
                'GRID_LIST' => [
                    'SHOW' => true,
                    'SORT' => [
                        'CODE' => 'ID_STAFF_POST',
                        'ORDER' => 'ASC',
                        'DEFAULT' => false
                    ]
                ]
            ]),*/
			'ID_PARENT_BLOCK' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('ID_PARENT_BLOCK'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['ID_PARENT_BLOCK'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'ID_PARENT_BLOCK'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'ID_PARENT_BLOCK',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			'COLOR_HEADER' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('COLOR_HEADER'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['COLOR_HEADER'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'COLOR_HEADER'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'COLOR_HEADER',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			'COLOR_BLOCK' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('COLOR_BLOCK'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['COLOR_BLOCK'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'COLOR_BLOCK'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'COLOR_BLOCK',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
            'COLOR_BY_PARENT' => new Field([
                'ENTITY_FIELD' => $this->entity->getField('COLOR_BY_PARENT'),
                'EDIT_VIEW' => new Fields\Views\Edit\InputCheckbox(),
                'SHOW_VIEW' => new Fields\Views\Show\Span(),
                'SELECT' => ['COLOR_BY_PARENT'],
                'VALUE' => new Fields\Value\Base([
                    'VALUE' => 'COLOR_BY_PARENT'
                ]),
                'GRID_LIST' => [
                    'SHOW' => true,
                    'SORT' => [
                        'CODE' => 'COLOR_BY_PARENT',
                        'ORDER' => 'ASC',
                        'DEFAULT' => false
                    ]
                ]
            ]),
			'NUMBER' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('NUMBER'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['NUMBER'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'NUMBER'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'NUMBER',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
            'SORT' => new Field([
                'ENTITY_FIELD' => $this->entity->getField('SORT'),
                'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
                'SHOW_VIEW' => new Fields\Views\Show\Span(),
                'SELECT' => ['SORT'],
                'VALUE' => new Fields\Value\Base([
                    'VALUE' => 'SORT'
                ]),
                'GRID_LIST' => [
                    'SHOW' => true,
                    'SORT' => [
                        'CODE' => 'SORT',
                        'ORDER' => 'ASC',
                        'DEFAULT' => true
                    ]
                ]
            ]),
            'IS_HIDE' => new Field([
                'ENTITY_FIELD' => $this->entity->getField('IS_HIDE'),
                'EDIT_VIEW' => new Fields\Views\Edit\InputCheckbox(),
                'SHOW_VIEW' => new Fields\Views\Show\Span(),
                'SELECT' => ['IS_HIDE'],
                'VALUE' => new Fields\Value\Base([
                    'VALUE' => 'IS_HIDE'
                ]),
                'GRID_LIST' => [
                    'SHOW' => true,
                    'SORT' => [
                        'CODE' => 'IS_HIDE',
                        'ORDER' => 'ASC',
                        'DEFAULT' => false
                    ],
                    'FILTER' => [
                        'CODE' => 'IS_HIDE',
                        'TYPE' => 'list',
                        'DEFAULT' => true,
                        'ITEMS' => ['Y' => 'Да', 'N' => 'Нет']
                    ],
                ]
            ]),
			'ADDICT_PARAM' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('ADDICT_PARAM'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['ADDICT_PARAM'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'ADDICT_PARAM'
				]),
				'GRID_LIST' => [
					'SHOW' => false,
					'SORT' => [
						'CODE' => 'ADDICT_PARAM',
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
        return BlocksTable::getEntity();
    }
}
