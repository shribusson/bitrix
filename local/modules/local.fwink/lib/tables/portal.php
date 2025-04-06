<?

namespace Local\Fwink\Tables;

use Bitrix\Main\ORM\Data;
use Bitrix\Main\ORM\Fields;
use Exception;

class PortalTable extends Data\DataManager
{
    public static function getTableName(): string
    {
        return 'itrack_chart_portal';
    }

    /**
     * @return array
     * @throws Exception
     */
    public static function getMap(): array
    {
		return  [
			'ID' => array(
				'primary' => true,
                'autocomplete' => true,
				'title' => 'ID',
				'data_type' => 'integer',
			),
			'DOMAIN' => array(
				'data_type' => 'string',
				'title' => 'DOMAIN',
			),
			'MEMBER_ID' => array(
				'data_type' => 'string',
				'title' => 'MEMBER_ID',
			),
			'ACCESS_TOKEN' => array(
				'data_type' => 'string',
				'title' => 'ACCESS_TOKEN',
			),
			'REFRESH_TOKEN' => array(
				'data_type' => 'string',
				'title' => 'REFRESH_TOKEN',
			),
			'source' => array(
				'data_type' => 'string',
				'title' => 'SOURCE',
			),
			'consumer' => array(
				'data_type' => 'string',
				'title' => 'CONSUMER',
			),
			'type_of_link' => array(
				'data_type' => 'string',
				'title' => 'TYPE_OF_LINK',
			),
		];
	}
}
