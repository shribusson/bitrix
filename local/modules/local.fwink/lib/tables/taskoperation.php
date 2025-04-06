<?

namespace Local\Fwink\Tables;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data;
use Bitrix\Main\ORM\Fields;
use Exception;

class TaskOperationTable extends Data\DataManager
{
    public static function getTableName(): string
    {
        return 'itrack_chart_task_operation';
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
                'autocomplete' => true
            ]),
            new Fields\TextField('ENTITY', [
                'required' => true,
                'title' => Loc::getMessage('LOCAL_TASK_OPERATION_ENTITY')
            ]),
            new Fields\TextField('OPERATION', [
                'required' => true,
                'title' => Loc::getMessage('LOCAL_TASK_OPERATION_OPERATION')
            ])
        ];
    }
}
