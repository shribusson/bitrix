<?

namespace Local\Fwink\Tables;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data;
use Bitrix\Main\ORM\Fields;
use Local\Fwink\AccessControl\Configuration;
use Exception;

class TaskTable extends Data\DataManager
{
    public static function getTableName(): string
    {
        return 'itrack_chart_task';
    }

    /**
     * @return array
     * @throws ArgumentException
     * @throws Exception
     */
    public static function getMap(): array
    {
        $entitiesDataClasses = Configuration::getInstance()->get('entitiesDataClasses');

        return [
            new Fields\IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true
            ]),
            new Fields\IntegerField('GROUP_ID', [
                'required' => true,
                'title' => Loc::getMessage('LOCAL_TASK_GROUP')
            ]),
            new Fields\IntegerField('TASK_OPERATION_ID', [
                'required' => false,
                'title' => Loc::getMessage('LOCAL_TASK_OPERATION')
            ]),
            new Fields\Relations\Reference(
                'TASK',
                $entitiesDataClasses['taskOperation'],
                ['=this.TASK_OPERATION_ID' => 'ref.ID']
            ),
        ];
    }
}
