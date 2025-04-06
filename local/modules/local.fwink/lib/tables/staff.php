<?

namespace Local\Fwink\Tables;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data;
use Bitrix\Main\Type;
use Bitrix\Main\ORM\Fields;
use Exception;

class StaffTable extends Data\DataManager
{
    public static function getTableName(): string
    {
        return 'itrack_chart_staff';
    }

    /**
     * @return array
     * @throws Exception
     */
    public static function getMap(): array
    {
        return [
            new Fields\IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
                'title' => 'ID'
            ]),
			new Fields\StringField('ID_PORTAL', [
				'required' => true,
				'title' => 'ID портала' //Loc::getMessage('LOCAL_ID_PORTAL_TITLE')
			]),
			new Fields\StringField('ID_STAFF', [
				'required' => true,
				'title' => 'Сотрудник' //Loc::getMessage('LOCAL_ID_STAFF_TITLE')
			]),
			new Fields\DateField('DATE_TRAIN_END', [
				'default_value' => new Type\Date(),
				'title' => 'Дата окончания испытательного срока' //Loc::getMessage('LOCAL_DATE_TRAIN_END_TITLE')
			])
        ];
    }
}
