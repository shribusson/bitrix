<?

namespace Local\Fwink\Fields\Config;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Local\Fwink\Fields;
use Local\Fwink\Fields\Field;
use Local\Fwink\Tables\StaffremoteTable;

class Staffremote extends Manager
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
					'SHOW' => false,
					'SORT' => [
						'CODE' => 'ID',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			'ID_PORTAL' => new Field([
			    'INFO' => [
			        'NAME' => 'ID_PORTAL',
                    'TITLE' => 'Ид портала',
                    'REQUIRED' => true
                ],
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
			'ID_STAFF' => new Field([
                'INFO' => [
                    'NAME' => 'ID_STAFF',
                    'TITLE' => 'Ид',
                    'REQUIRED' => true
                ],
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
					],
                    'FILTER' => [
                        'CODE' => 'ID_STAFF',
                        'TYPE' => 'string',
                        'DEFAULT' => true
                    ],
				]
			]),
            'FULL_NAME' => new Field([
                'INFO' => [
                    'NAME' => 'FULL_NAME',
                    'TITLE' => 'Имя и фамилия',
                    'REQUIRED' => true
                ],
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
                        'CODE' => 'FULL_NAME',
                        'TYPE' => 'string',
                        'DEFAULT' => true
                    ],
                ]
            ]),
            'NAME' => new Field([
                'INFO' => [
                    'NAME' => 'NAME',
                    'TITLE' => 'Имя',
                    'REQUIRED' => true
                ],
                'ENTITY_FIELD' => $this->entity->getField('NAME'),
                'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
                'SHOW_VIEW' => new Fields\Views\Show\Span(),
                'SELECT' => ['NAME'],
                'VALUE' => new Fields\Value\Base([
                    'VALUE' => 'NAME'
                ]),
                'GRID_LIST' => [
                    'SHOW' => false,
                    'SORT' => [
                        'CODE' => 'NAME',
                        'ORDER' => 'ASC',
                        'DEFAULT' => false
                    ],
                    'FILTER' => [
                        'CODE' => 'NAME',
                        'TYPE' => 'string',
                        'DEFAULT' => true
                    ],
                ]
            ]),
            'LAST_NAME' => new Field([
                'INFO' => [
                    'NAME' => 'LAST_NAME',
                    'TITLE' => 'Фамилия',
                    'REQUIRED' => true
                ],
                'ENTITY_FIELD' => $this->entity->getField('LAST_NAME'),
                'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
                'SHOW_VIEW' => new Fields\Views\Show\Span(),
                'SELECT' => ['LAST_NAME'],
                'VALUE' => new Fields\Value\Base([
                    'VALUE' => 'LAST_NAME'
                ]),
                'GRID_LIST' => [
                    'SHOW' => false,
                    'SORT' => [
                        'CODE' => 'LAST_NAME',
                        'ORDER' => 'ASC',
                        'DEFAULT' => false
                    ],
                    'FILTER' => [
                        'CODE' => 'LAST_NAME',
                        'TYPE' => 'string',
                        'DEFAULT' => true
                    ],
                ]
            ]),
            'SECOND_NAME' => new Field([
                'INFO' => [
                    'NAME' => 'SECOND_NAME',
                    'TITLE' => 'Отчество',
                    'REQUIRED' => false
                ],
                'ENTITY_FIELD' => $this->entity->getField('SECOND_NAME'),
                'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
                'SHOW_VIEW' => new Fields\Views\Show\Span(),
                'SELECT' => ['SECOND_NAME'],
                'VALUE' => new Fields\Value\Base([
                    'VALUE' => 'SECOND_NAME'
                ]),
                'GRID_LIST' => [
                    'SHOW' => false,
                    'SORT' => [
                        'CODE' => 'SECOND_NAME',
                        'ORDER' => 'ASC',
                        'DEFAULT' => false
                    ],
                    'FILTER' => [
                        'CODE' => 'SECOND_NAME',
                        'TYPE' => 'string',
                        'DEFAULT' => true
                    ]
                ]
            ]),
			'DATE_TRAIN_END' => new Field([
                'INFO' => [
                    'NAME' => 'DATE_TRAIN_END',
                    'TITLE' => 'Дата окончания испытательного срока',
                    'REQUIRED' => false
                ],
				'ENTITY_FIELD' => $this->entity->getField('DATE_TRAIN_END'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['DATE_TRAIN_END'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'DATE_TRAIN_END'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						/*'CODE' => 'DATE_TRAIN_END',*/
						'ORDER' => 'ASC',
						'DEFAULT' => false
					],
                    'FILTER' => [
                        'CODE' => 'DATE_TRAIN_END',
                        'TYPE' => 'date',
                        'DEFAULT' => true
                    ],
				]
			]),
			'ACTIVE' => new Field([
                'INFO' => [
                    'NAME' => 'ACTIVE',
                    'TITLE' => 'Активность',
                    'REQUIRED' => false
                ],
				'ENTITY_FIELD' => $this->entity->getField('ACTIVE'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['ACTIVE'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'ACTIVE'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'ACTIVE',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					],
                    'FILTER' => [
                        'CODE' => 'ACTIVE',
                        'TYPE' => 'list',
                        'ITEMS' => ['Y' => 'Да', 'N' => 'Нет','ALL' => 'Все'],
                        'DEFAULT' => true
                    ],
				]
			]),
			'EMAIL' => new Field([
                'INFO' => [
                    'NAME' => 'EMAIL',
                    'TITLE' => 'Email',
                    'REQUIRED' => true
                ],
				'ENTITY_FIELD' => $this->entity->getField('EMAIL'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['EMAIL'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'EMAIL'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'EMAIL',
						'ORDER' => 'ASC',
						'DEFAULT' => true
					],
                    'FILTER' => [
                        'CODE' => 'EMAIL',
                        'TYPE' => 'string',
                        'DEFAULT' => true
                    ],
				]
			]),
            'PERSONAL_PHONE' => new Field([
                'INFO' => [
                    'NAME' => 'PERSONAL_PHONE',
                    'TITLE' => 'Телефон',
                    'REQUIRED' => true
                ],
                'ENTITY_FIELD' => $this->entity->getField('PERSONAL_PHONE'),
                'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
                'SHOW_VIEW' => new Fields\Views\Show\Span(),
                'SELECT' => ['PERSONAL_PHONE'],
                'VALUE' => new Fields\Value\Base([
                    'VALUE' => 'PERSONAL_PHONE'
                ]),
                'GRID_LIST' => [
                    'SHOW' => true,
                    'SORT' => [
                        'CODE' => 'PERSONAL_PHONE',
                        'ORDER' => 'ASC',
                        'DEFAULT' => false
                    ],
                    'FILTER' => [
                        'CODE' => 'PERSONAL_PHONE',
                        'TYPE' => 'string',
                        'DEFAULT' => true
                    ]
                ]
            ]),
            'POST' => new Field([
                'INFO' => [
                    'NAME' => 'POST',
                    'TITLE' => 'Должность',
                    'REQUIRED' => true
                ],
                'ENTITY_FIELD' => $this->entity->getField('PERSONAL_PROFESSION'),
                'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
                'SHOW_VIEW' => new Fields\Views\Show\Span(),
                'SELECT' => ['PERSONAL_PROFESSION'],
                'VALUE' => new Fields\Value\Base([
                    'VALUE' => 'PERSONAL_PROFESSION'
                ]),
                'GRID_LIST' => [
                    'SHOW' => true,
                    'SORT' => [
                        /*'CODE' => 'POST',*/
                        'ORDER' => 'ASC',
                        'DEFAULT' => true
                    ],
                    /*'FILTER' => [
                        'CODE' => 'PERSONAL_PHONE',
                        'TYPE' => 'string',
                        'DEFAULT' => true
                    ]*/
                ]
            ]),
			/*'LAST_LOGIN' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('LAST_LOGIN'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['LAST_LOGIN'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'LAST_LOGIN'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'LAST_LOGIN',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),*/
			/*'DATE_REGISTER' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('DATE_REGISTER'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['DATE_REGISTER'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'DATE_REGISTER'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'DATE_REGISTER',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),*/
			'PERSONAL_PROFESSION' => new Field([
                'INFO' => [
                    'NAME' => 'PERSONAL_PROFESSION',
                    'TITLE' => 'Профессия',
                    'REQUIRED' => false
                ],
				'ENTITY_FIELD' => $this->entity->getField('PERSONAL_PROFESSION'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['PERSONAL_PROFESSION'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'PERSONAL_PROFESSION'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'PERSONAL_PROFESSION',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			'PERSONAL_WWW' => new Field([
                'INFO' => [
                    'NAME' => 'PERSONAL_WWW',
                    'TITLE' => 'Веб-сайт',
                    'REQUIRED' => false
                ],
				'ENTITY_FIELD' => $this->entity->getField('PERSONAL_WWW'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['PERSONAL_WWW'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'PERSONAL_WWW'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'PERSONAL_WWW',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			'PERSONAL_ICQ' => new Field([
                'INFO' => [
                    'NAME' => 'PERSONAL_ICQ',
                    'TITLE' => 'ICQ',
                    'REQUIRED' => false
                ],
				'ENTITY_FIELD' => $this->entity->getField('PERSONAL_ICQ'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['PERSONAL_ICQ'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'PERSONAL_ICQ'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'PERSONAL_ICQ',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			'PERSONAL_GENDER' => new Field([
                'INFO' => [
                    'NAME' => 'PERSONAL_GENDER',
                    'TITLE' => 'Пол',
                    'REQUIRED' => false
                ],
				'ENTITY_FIELD' => $this->entity->getField('PERSONAL_GENDER'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['PERSONAL_GENDER'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'PERSONAL_GENDER'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'PERSONAL_GENDER',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			'PERSONAL_BIRTHDATE' => new Field([
                'INFO' => [
                    'NAME' => 'PERSONAL_BIRTHDATE',
                    'TITLE' => 'День рождения',
                    'REQUIRED' => false
                ],
				'ENTITY_FIELD' => $this->entity->getField('PERSONAL_BIRTHDATE'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['PERSONAL_BIRTHDATE'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'PERSONAL_BIRTHDATE'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'PERSONAL_BIRTHDATE',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			/*'PERSONAL_PHOTO' => new Field([
                'INFO' => [
                    'NAME' => 'PERSONAL_PHOTO',
                    'TITLE' => 'День рождения',
                    'REQUIRED' => false
                ],
				'ENTITY_FIELD' => $this->entity->getField('PERSONAL_PHOTO'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['PERSONAL_PHOTO'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'PERSONAL_PHOTO'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'PERSONAL_PHOTO',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),*/
			'PERSONAL_FAX' => new Field([
                'INFO' => [
                    'NAME' => 'PERSONAL_FAX',
                    'TITLE' => 'Факс',
                    'REQUIRED' => false
                ],
				'ENTITY_FIELD' => $this->entity->getField('PERSONAL_FAX'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['PERSONAL_FAX'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'PERSONAL_FAX'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'PERSONAL_FAX',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			'PERSONAL_MOBILE' => new Field([
                'INFO' => [
                    'NAME' => 'PERSONAL_MOBILE',
                    'TITLE' => 'Мобильный телефон',
                    'REQUIRED' => false
                ],
				'ENTITY_FIELD' => $this->entity->getField('PERSONAL_MOBILE'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['PERSONAL_MOBILE'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'PERSONAL_MOBILE'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'PERSONAL_MOBILE',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			/*'PERSONAL_PAGER' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('PERSONAL_PAGER'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['PERSONAL_PAGER'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'PERSONAL_PAGER'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'PERSONAL_PAGER',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),*/
			'PERSONAL_STREET' => new Field([
                'INFO' => [
                    'NAME' => 'PERSONAL_STREET',
                    'TITLE' => 'Улица',
                    'REQUIRED' => false
                ],
				'ENTITY_FIELD' => $this->entity->getField('PERSONAL_STREET'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['PERSONAL_STREET'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'PERSONAL_STREET'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'PERSONAL_STREET',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			/*'PERSONAL_MAILBOX' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('PERSONAL_MAILBOX'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['PERSONAL_MAILBOX'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'PERSONAL_MAILBOX'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'PERSONAL_MAILBOX',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),*/
			'PERSONAL_CITY' => new Field([
                'INFO' => [
                    'NAME' => 'PERSONAL_CITY',
                    'TITLE' => 'Город',
                    'REQUIRED' => false
                ],
				'ENTITY_FIELD' => $this->entity->getField('PERSONAL_CITY'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['PERSONAL_CITY'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'PERSONAL_CITY'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'PERSONAL_CITY',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			'PERSONAL_STATE' => new Field([
                'INFO' => [
                    'NAME' => 'PERSONAL_STATE',
                    'TITLE' => 'Область',
                    'REQUIRED' => false
                ],
				'ENTITY_FIELD' => $this->entity->getField('PERSONAL_STATE'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['PERSONAL_STATE'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'PERSONAL_STATE'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'PERSONAL_STATE',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			'PERSONAL_ZIP' => new Field([
                'INFO' => [
                    'NAME' => 'PERSONAL_ZIP',
                    'TITLE' => 'Индекс',
                    'REQUIRED' => false
                ],
				'ENTITY_FIELD' => $this->entity->getField('PERSONAL_ZIP'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['PERSONAL_ZIP'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'PERSONAL_ZIP'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'PERSONAL_ZIP',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			'PERSONAL_COUNTRY' => new Field([
                'INFO' => [
                    'NAME' => 'PERSONAL_COUNTRY',
                    'TITLE' => 'Страна',
                    'REQUIRED' => false
                ],
				'ENTITY_FIELD' => $this->entity->getField('PERSONAL_COUNTRY'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['PERSONAL_COUNTRY'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'PERSONAL_COUNTRY'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'PERSONAL_COUNTRY',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			/*'PERSONAL_NOTES' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('PERSONAL_NOTES'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['PERSONAL_NOTES'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'PERSONAL_NOTES'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'PERSONAL_NOTES',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),*/
			/*'WORK_COMPANY' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('WORK_COMPANY'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['WORK_COMPANY'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'WORK_COMPANY'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'WORK_COMPANY',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),*/
			/*'WORK_DEPARTMENT' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('WORK_DEPARTMENT'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['WORK_DEPARTMENT'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'WORK_DEPARTMENT'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'WORK_DEPARTMENT',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),*/
			/*'WORK_POSITION' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('WORK_POSITION'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['WORK_POSITION'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'WORK_POSITION'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'WORK_POSITION',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),*/
			/*'WORK_WWW' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('WORK_WWW'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['WORK_WWW'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'WORK_WWW'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'WORK_WWW',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			'WORK_PHONE' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('WORK_PHONE'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['WORK_PHONE'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'WORK_PHONE'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'WORK_PHONE',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			'WORK_FAX' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('WORK_FAX'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['WORK_FAX'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'WORK_FAX'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'WORK_FAX',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			'WORK_PAGER' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('WORK_PAGER'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['WORK_PAGER'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'WORK_PAGER'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'WORK_PAGER',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			'WORK_STREET' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('WORK_STREET'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['WORK_STREET'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'WORK_STREET'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'WORK_STREET',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			'WORK_MAILBOX' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('WORK_MAILBOX'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['WORK_MAILBOX'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'WORK_MAILBOX'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'WORK_MAILBOX',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			'WORK_CITY' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('WORK_CITY'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['WORK_CITY'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'WORK_CITY'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'WORK_CITY',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			'WORK_STATE' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('WORK_STATE'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['WORK_STATE'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'WORK_STATE'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'WORK_STATE',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			'WORK_ZIP' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('WORK_ZIP'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['WORK_ZIP'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'WORK_ZIP'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'WORK_ZIP',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			'WORK_COUNTRY' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('WORK_COUNTRY'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['WORK_COUNTRY'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'WORK_COUNTRY'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'WORK_COUNTRY',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			'WORK_PROFILE' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('WORK_PROFILE'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['WORK_PROFILE'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'WORK_PROFILE'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'WORK_PROFILE',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			'WORK_LOGO' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('WORK_LOGO'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['WORK_LOGO'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'WORK_LOGO'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'WORK_LOGO',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			'WORK_NOTES' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('WORK_NOTES'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['WORK_NOTES'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'WORK_NOTES'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'WORK_NOTES',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),
			'ADMIN_NOTES' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('ADMIN_NOTES'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['ADMIN_NOTES'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'ADMIN_NOTES'
				]),
				'GRID_LIST' => [
					'SHOW' => true,
					'SORT' => [
						'CODE' => 'ADMIN_NOTES',
						'ORDER' => 'ASC',
						'DEFAULT' => false
					]
				]
			]),*/
			'TIME_ZONE_OFFSET' => new Field([
				'ENTITY_FIELD' => $this->entity->getField('TIME_ZONE_OFFSET'),
				'EDIT_VIEW' => new Fields\Views\Edit\InputText(),
				'SHOW_VIEW' => new Fields\Views\Show\Span(),
				'SELECT' => ['TIME_ZONE_OFFSET'],
				'VALUE' => new Fields\Value\Base([
					'VALUE' => 'TIME_ZONE_OFFSET'
				]),
				'GRID_LIST' => [
					'SHOW' => false,
					'SORT' => [
						'CODE' => 'TIME_ZONE_OFFSET',
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
        return StaffremoteTable::getEntity();
    }
}
