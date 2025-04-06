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
use Local\Fwink\Tables\PostsTable;

class Posts extends Manager
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
                        'DEFAULT' => false
                    ],
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
			'NAME_POST' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('NAME_POST'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['NAME_POST'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'NAME_POST'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'NAME_POST',
						'ORDER' => 'ASC',
						'DEFAULT' => true
					],
                    'FILTER' => [
                        'CODE' => 'NAME_POST',
                        'TYPE' => 'string',
                        'DEFAULT' => true
                    ],
				]
			]),
			'CKP_OF_POST' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('CKP_OF_POST'),
				'EDIT_VIEW' => new Fields\Views\Edit\Editor(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['CKP_OF_POST'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'CKP_OF_POST'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'CKP_OF_POST',
						'ORDER' => 'ASC',
						'DEFAULT' => true
					],
                    'FILTER' => [
                        'CODE' => 'CKP_OF_POST',
                        'TYPE' => 'string',
                        'DEFAULT' => false
                    ],
				]
			]),
			/*'CKP_OF_POST' => new Field([
				'INFO' => [
					'NAME' => 'CKP_OF_POST',
					'TITLE' => 'ЦКП of поста',
					'REQUIRED' => false
				],
				'EDIT_VIEW' => new Fields\Views\Edit\Editor(),
				'SHOW_VIEW' => new Fields\Views\Show\CollapsedText(),
				'SELECT' => [
					/*'ID',
					'TEXT' => *//*'CKP_OF_POST'
				],
				'VALUE' => new Fields\Value\Type\StringValue([
					'VALUE' => 'ID',
					'CONTENT' => 'TEXT',
				])
			]),*/
			'FUNCTION_OF_POST' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('FUNCTION_OF_POST'),
				'EDIT_VIEW' => new Fields\Views\Edit\Editor(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['FUNCTION_OF_POST'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'FUNCTION_OF_POST'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'FUNCTION_OF_POST',
						'ORDER' => 'ASC',
						'DEFAULT' => true
					],
                    'FILTER' => [
                        'CODE' => 'FUNCTION_OF_POST',
                        'TYPE' => 'string',
                        'DEFAULT' => false
                    ],
				]
			]),
			'ID_SUPERVISOR_POST' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('ID_SUPERVISOR_POST'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['ID_SUPERVISOR_POST'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'ID_SUPERVISOR_POST'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'ID_SUPERVISOR_POST',
						'ORDER' => 'ASC',
						'DEFAULT' => true
					]
				]
			]),
			'ID_SHIEF_POST_USERB24' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('ID_SHIEF_POST_USERB24'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['ID_SHIEF_POST_USERB24'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'ID_SHIEF_POST_USERB24'
				]),
				'GRID_LIST' => [
					'SHOW' => false,
					'SORT' => [
						'CODE' => 'ID_SHIEF_POST_USERB24',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
            'IS_MANAGER_POST' => new Field([
                'ENTITY_FIELD' => $this->entity->getField('IS_MANAGER_POST'),
                'EDIT_VIEW' => new Fields\Views\Edit\InputCheckbox(),
                'SHOW_VIEW' => new Fields\Views\Show\Span(),
                'SELECT' => ['IS_MANAGER_POST'],
                'VALUE' => new Fields\Value\Base([
                    'VALUE' => 'IS_MANAGER_POST'
                ]),
                'GRID_LIST' => [
                    'SHOW' => false,
                    'SORT' => [
                        'CODE' => 'IS_MANAGER_POST',
                        'ORDER' => 'ASC',
                        'DEFAULT' => false
                    ]
                ]
            ]),
            /*'STAFF_LIST' => new Field([
                'INFO' => [
                    'NAME' => 'STAFF_LIST',
                    'TITLE' => 'Сорудники',
                    'REQUIRED' => false
                ],
                'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
                'SHOW_VIEW' => new Fields\Views\Show\Span(),
                'SELECT' => ['ID_SHIEF_POST_USERB24'],
                'GRID_LIST' => [
                    'SHOW' => true,
                    'SORT' => [
                        'CODE' => 'STAFF_LIST',
                        'ORDER' => 'ASC',
                        'DEFAULT' => true
                    ]
                ]
            ]),*/
			'ID_JOB_FOLDERB24' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('ID_JOB_FOLDERB24'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['ID_JOB_FOLDERB24'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'ID_JOB_FOLDERB24'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'ID_JOB_FOLDERB24',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
            'SORT' => new Field([
                'ENTITY_FIELD' => $this->entity->getField('SORT'),
                'EDIT_VIEW' => new Fields\Views\Edit\InputNumber(),
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
                        'DEFAULT' => false
                    ]
                ]
            ])
		];

	}

    /**
     * @return Base
     * @throws ArgumentException
     * @throws SystemException
     */
    protected function getEntity(): Base
    {
        return PostsTable::getEntity();
    }
}
