<?

namespace Local\Fwink\Tables;

use Bitrix\Main\ORM\Data;
use Bitrix\Main\ORM\Fields;
use Exception;

class RolesTable extends Data\DataManager
{
    public static function getTableName(): string
    {
        return 'itrack_chart_roles';
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
				'primary' => true,
				'autocomplete' =>true,
				'title' => 'ID',
			),
			'ID_PORTAL' => array(
				'data_type' => 'integer',
				'title' => 'ID_PORTAL',
			),
			'NAME' => array(
				'data_type' => 'string',
				'title' => 'NAME',
			),
			'GROUP_SOCIALNETWORKB24' => array(
				'data_type' => 'string',
				'title' => 'GROUP_SOCIALNETWORKB24',
			),
		];
	}
}
