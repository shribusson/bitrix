<?

namespace Local\Fwink\Tables;

use Bitrix\Main\ORM\Data;
use Bitrix\Main\ORM\Fields;
use Exception;

class UserrightsTable extends Data\DataManager
{
    public static function getTableName(): string
    {
        return 'itrack_chart_userrights';
    }

    /**
     * @return array
     * @throws Exception
     */
    public static function getMap(): array
    {
		return  [
			'ID' => array(
				'data_type' => 'integer',
				'title' => 'ID',
				'primary' => true,
				'autocomplete' => true
			),
			'ID_USER_USERB24' => array(
				'data_type' => 'integer',
				'title' => 'ID_USER_USERB24',
			),
			'ID_ROLE' => array(
				'data_type' => 'string',
				'title' => 'ID_ROLE',
			),
		];
	}
}
