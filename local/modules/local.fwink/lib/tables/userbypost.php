<?

namespace Local\Fwink\Tables;

use Bitrix\Main\ORM\Data;
use Bitrix\Main\ORM\Event;
use Bitrix\Main\ORM\EventResult;
use Bitrix\Main\ORM\Fields;
use Exception;
use Local\Fwink\Staff\TransferManager;

class UserbypostTable extends Data\DataManager
{
    public static function getTableName(): string
    {
        return 'itrack_chart_userbypost';
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
			'ID_PORTAL' => array(
				'data_type' => 'integer',
				'title' => 'ID_PORTAL',
			),
			'ID_STAFF' => array(
				'data_type' => 'integer',
				'title' => 'ID_STAFF',
			),
			'ID_POST' => array(
				'data_type' => 'integer',
				'title' => 'ID_POST',
			),
			'ACTIVE' => array(
                'data_type' => 'boolean',
                'values' => array('N','Y'),
                'default_value' => 'Y'
            ),
            'POST' => new Fields\Relations\Reference(
                'POST',
                '\Local\Fwink\Tables\PostsTable',
                ['=this.ID_POST' => 'ref.ID']
            )
		];
	}

	public static function onAfterUpdate(Event $event)
    {
        $result = new EventResult();
        $id = $event->getParameter('primary');
        $fields = $event->getParameter('fields');
		if($fields['ACTIVE'] === 'Y') {
        	$transferManager = TransferManager::getInstance();
        	$transferManager->addUser((int)$fields['ID_STAFF']);
		}
    }

    public static function onAfterAdd(Event $event)
    {
        $result = new EventResult();
        $id = $event->getParameter('primary');
        $fields = $event->getParameter('fields');
		if($fields['ACTIVE'] === 'Y') {
        	$transferManager = TransferManager::getInstance();
        	$transferManager->addUser((int)$fields['ID_STAFF']);
		}
    }

    public static function onBeforeDelete(Event $event)
    {
        $result = new EventResult();
        $id = $event->getParameter('primary');
        $current = self::getRowById($id);
        $transferManager = TransferManager::getInstance();
        $transferManager->addUser((int)$current['ID_STAFF']);
    }
}
