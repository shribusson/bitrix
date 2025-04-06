<?

namespace Local\Fwink\Tables;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectException;
use Bitrix\Main\ORM\Data;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\ORM\Fields\Validators;
use Bitrix\Main\Type;
use Bitrix\Main\UserTable;
use CUser;
use Exception;

class LogTable extends Data\DataManager
{
    public static function getTableName(): string
    {
        return 'itrack_chart_log';
    }

    /**
     * @return array
     * @throws ArgumentException
     * @throws ObjectException
     * @throws Exception
     */
    public static function getMap(): array
    {
        return [
            new Fields\IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true
            ]),
            new Fields\DatetimeField('DATE_CREATED', [
                'default_value' => new Type\DateTime(),
                'title' => Loc::getMessage('LOCAL_LOG_DATE_CREATED')
            ]),
            new Fields\IntegerField('AUTHOR_ID', [
                'title' => Loc::getMessage('LOCAL_LOG_AUTHOR'),
                'default_value' => static function () {
                    return (new CUser)->GetID();
                },
                'validation' => static function () {
                    return [
                        new Validators\ForeignValidator(UserTable::getEntity()->getField('ID'))
                    ];
                }
            ]),
            new Fields\Relations\Reference(
                'AUTHOR',
                UserTable::class,
                ['=this.AUTHOR_ID' => 'ref.ID'],
                ['join_type' => 'left']
            ),
            new Fields\StringField('ENTITY_ELEMENT', [
                'required' => true,
                'title' => Loc::getMessage('LOCAL_LOG_ENTITY_ELEMENT')
            ]),
            new Fields\IntegerField('ELEMENT_ID', [
                'title' => Loc::getMessage('LOCAL_LOG_ELEMENT_ID')
            ]),
            new Fields\StringField('FIELD', [
                'title' => Loc::getMessage('LOCAL_LOG_FIELD'),
                'serialized' => true
            ]),
            new Fields\StringField('OPERATION', [
                'required' => true,
                'title' => Loc::getMessage('LOCAL_LOG_OPERATION')
            ]),
            new Fields\TextField('OLD', [
				'default_value' =>'',
                'title' => Loc::getMessage('LOCAL_LOG_OLD'),
                'serialized' => true
            ]),
            new Fields\TextField('NEW', [
                'title' => Loc::getMessage('LOCAL_LOG_NEW'),
                'serialized' => true
            ])
        ];
    }
}
