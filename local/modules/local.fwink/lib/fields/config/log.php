<?

namespace Local\Fwink\Fields\Config;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Local\Fwink\Fields;
use Local\Fwink\Fields\Field;
use Local\Fwink\Helpers\Log as HelpersLog;
use Local\Fwink\Helpers\User as HelpersUser;
use Local\Fwink\Tables\LogTable;

class Log extends Manager
{
    /**
     * @return array
     * @throws ArgumentException
     * @throws SystemException
     * @throws ArgumentNullException
     */
    public function getFields(): array
    {
        return [
            'ID' => new Field([
                'ENTITY_FIELD' => $this->entity->getField('ID'),
                'SHOW_VIEW' => new Fields\Views\Show\Link(),
                'SELECT' => ['ID'],
                'VALUE' => new Fields\Value\Base([
                    'VALUE' => 'ID',
                ]),
                'GRID_LIST' => [
                    'SHOW' => true,
                    'WIDTH' => '70',
                    'SORT' => [
                        'CODE' => 'ID',
                        'ORDER' => 'DESC',
                        'DEFAULT' => false
                    ]
                ]
            ]),
            'DATE_CREATED' => new Field([
                'ENTITY_FIELD' => $this->entity->getField('DATE_CREATED'),
                'SHOW_VIEW' => new Fields\Views\Show\Span(),
                'SELECT' => [
                    'DATE_CREATED'
                ],
                'VALUE' => new Fields\Value\Type\Date([
                    'VALUE' => 'DATE_CREATED'
                ]),
                'GRID_LIST' => [
                    'SHOW' => true,
                    'WIDTH' => '180',
                    'SORT' => [
                        'CODE' => 'DATE_CREATED',
                        'ORDER' => 'DESC',
                        'DEFAULT' => true
                    ],
                    'FILTER' => [
                        'CODE' => 'DATE_CREATED',
                        'TYPE' => 'date',
                        'DEFAULT' => true,
                        'EXCLUDE' => [
                            'TOMORROW',
                            'CURRENT_WEEK',
                            'CURRENT_MONTH',
                            'CURRENT_QUARTER',
                            'LAST_WEEK',
                            'LAST_MONTH',
                            'LAST_7_DAYS',
                            'LAST_30_DAYS',
                            'LAST_60_DAYS',
                            'LAST_90_DAYS',
                            'PREV_DAYS',
                            'NEXT_DAYS',
                            'QUARTER',
                            'NEXT_WEEK',
                            'NEXT_MONTH'
                        ]
                    ],
                ]
            ]),
            'AUTHOR' => new Field([
                'ENTITY_FIELD' => $this->entity->getField('AUTHOR_ID'),
                'SHOW_VIEW' => new Fields\Views\Show\Div(),
                'SELECT' => [
                    'ID',
                    'AUTHOR_ID',
                    'AUTHOR_NAME' => 'AUTHOR.NAME',
                    'AUTHOR_LAST_NAME' => 'AUTHOR.LAST_NAME',
                    'AUTHOR_LOGIN' => 'AUTHOR.LOGIN',
                    'AUTHOR_AVATAR' => 'AUTHOR.PERSONAL_PHOTO',
                    'AUTHOR_POSITION' => 'AUTHOR.WORK_POSITION',
                ],
                'VALUE' => new Fields\Value\Type\User([
                    'VALUE' => 'AUTHOR_ID',
                    'USER_ID' => 'AUTHOR_ID',
                    'USER_NAME' => 'AUTHOR_NAME',
                    'USER_LAST_NAME' => 'AUTHOR_LAST_NAME',
                    'USER_LOGIN' => 'AUTHOR_LOGIN',
                    'USER_AVATAR' => 'AUTHOR_AVATAR',
                    'USER_POSITION' => 'AUTHOR_POSITION',
                    'ROW_ID' => 'ID',
                ]),
                'URL_TEMPLATES' => '',
                'GRID_LIST' => [
                    'SHOW' => true,
                    'WIDTH' => '200',
                    'SORT' => [
                        'CODE' => 'AUTHOR.LAST_NAME',
                        'ORDER' => 'ASC',
                        'DEFAULT' => true
                    ],
                    'FILTER' => [
                        'CODE' => 'AUTHOR_ID',
                        'TYPE' => 'list',
                        'DEFAULT' => true,
                        'ITEMS' => HelpersUser::getListFilter(),
                        'PARAMS' => [
                            'multiple' => 'Y'
                        ]
                    ],
                ]
            ]),
            'ENTITY_ELEMENT' => new Field([
                'ENTITY_FIELD' => $this->entity->getField('ENTITY_ELEMENT'),
                'SHOW_VIEW' => new Fields\Views\Show\Div(),
                'SELECT' => ['ENTITY_ELEMENT'],
                'VALUE' => new Fields\Value\Type\Entity([
                    'VALUE' => 'ENTITY_ELEMENT'
                ]),
                'GRID_LIST' => [
                    'SHOW' => true,
                    'SORT' => [
                        'CODE' => 'ENTITY_ELEMENT',
                        'ORDER' => 'ASC',
                        'DEFAULT' => true
                    ],
                    'FILTER' => [
                        'CODE' => 'ENTITY_ELEMENT',
                        'TYPE' => 'list',
                        'DEFAULT' => true,
                        'ITEMS' => HelpersLog::getEntityElementFilter(),
                        'PARAMS' => [
                            'multiple' => 'Y'
                        ]
                    ],
                ]
            ]),
            'ELEMENT_ID' => new Field([
                'ENTITY_FIELD' => $this->entity->getField('ELEMENT_ID'),
                'SHOW_VIEW' => new Fields\Views\Show\Link(),
                'SELECT' => ['ENTITY_ELEMENT', 'ELEMENT_ID'],
                'VALUE' => new Fields\Value\Type\EntityElement([
                    'ENTITY_ELEMENT' => 'ENTITY_ELEMENT',
                    'ELEMENT_ID' => 'ELEMENT_ID'
                ]),
                'URL_TEMPLATES' => '',
                'GRID_LIST' => [
                    'SHOW' => true,
                    'SORT' => [
                        'CODE' => 'ELEMENT_ID',
                        'ORDER' => 'ASC',
                        'DEFAULT' => true
                    ]
                ]
            ]),
            'EVENT' => new Field([
                'INFO' => [
                    'NAME' => 'FULL_NAME',
                    'TITLE' => Loc::getMessage('LOCAL_FIELDS_CONFIG_LOG_EVENT'),
                    'REQUIRED' => false
                ],
                'SHOW_VIEW' => new Fields\Views\Show\Div(),
                'SELECT' => ['OPERATION', 'FIELD'],
                'VALUE' => new Fields\Value\Type\Event([
                    'OPERATION' => 'OPERATION',
                    'FIELD' => 'FIELD'
                ]),
                'GRID_LIST' => [
                    'SHOW' => true,
                    'SORT' => [
                        'CODE' => 'OPERATION',
                        'ORDER' => 'ASC',
                        'DEFAULT' => true
                    ]
                ]
            ]),
            'VALUE' => new Field([
                'INFO' => [
                    'NAME' => 'FULL_NAME',
                    'TITLE' => Loc::getMessage('LOCAL_FIELDS_CONFIG_LOG_VALUE'),
                    'REQUIRED' => false
                ],
                'SHOW_VIEW' => new Fields\Views\Show\LogValue(),
                'SELECT' => ['ENTITY_ELEMENT', 'OPERATION', 'OLD', 'NEW'],
                'VALUE' => new Fields\Value\Type\LogValue([
                    'ENTITY_ELEMENT' => 'ENTITY_ELEMENT',
                    'OPERATION' => 'OPERATION',
                    'OLD' => 'OLD',
                    'NEW' => 'NEW'
                ]),
                'GRID_LIST' => [
                    'SHOW' => true,
                    'SORT' => [
                        'CODE' => 'OPERATION',
                        'ORDER' => 'ASC',
                        'DEFAULT' => true
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
        return LogTable::getEntity();
    }
}
