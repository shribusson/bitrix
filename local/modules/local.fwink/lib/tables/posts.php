<?

namespace Local\Fwink\Tables;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data;
use Bitrix\Main\ORM\EventResult;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\ORM\Event;
use Exception;
use Local\Fwink\Service\PostFolderManager;
use Local\Fwink\Staff\TransferManager;

Loc::loadMessages(__FILE__);

class PostsTable extends Data\DataManager
{
    public static function getTableName(): string
    {
        return 'itrack_chart_post';
    }

    /**
     * @return array
     * @throws Exception
     */
    public static function getMap(): array
    {
        /*return [
            new Fields\IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
                'title' => 'ID'
            ]),
        ];*/

		return  [
			'ID' => array(
				'primary' => true,
				'autocomplete' => true,
				'data_type' => 'integer',
				'title' => Loc::getMessage('local.fwink_tables_post_ID'),
			),
			'ID_PORTAL' => array(
				'data_type' => 'string',
				'title' => Loc::getMessage('local.fwink_tables_post_ID_PORTAL'),
			),
			'NAME_POST' => array(
				'data_type' => 'string',
				'title' => Loc::getMessage('local.fwink_tables_post_NAME_POST'),
                'save_data_modification' => function () {
                    return [
                        function ($value) {
                            return trim($value);
                        }
                    ];
                }
			),
			'CKP_OF_POST' => array(
				'data_type' => 'text',
				'title' => Loc::getMessage('local.fwink_tables_post_CKP_OF_POST'),
			),
			'FUNCTION_OF_POST' => array(
				'data_type' => 'text',
				'title' => Loc::getMessage('local.fwink_tables_post_FUNCTION_OF_POST'),
			),
			'ID_SUPERVISOR_POST' => array(
				'data_type' => 'string',
				'title' => Loc::getMessage('local.fwink_tables_post_ID_SUPERVISOR_POST'),
			),
			'ID_SHIEF_POST_USERB24' => array(
				'data_type' => 'string',
				'title' => Loc::getMessage('local.fwink_tables_post_ID_SHIEF_POST_USERB24'),
			),
            'IS_MANAGER_POST' => array(
                'data_type' => 'boolean',
                'values' => array('N','Y')
            ),
			'ID_JOB_FOLDERB24' => array(
				'data_type' => 'string',
				'title' => Loc::getMessage('local.fwink_tables_post_ID_JOB_FOLDERB24'),
			),
            'SORT' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('local.fwink_tables_post_SORT'),
                'default' => 100
            ),
            'PARENT_POST' => new Fields\Relations\Reference(
                'PARENT_POST',
                '\Local\Fwink\Tables\PostsTable',
                ['=this.ID_SUPERVISOR_POST' => 'ref.ID']
            ),
            (new Fields\Relations\ManyToMany('BLOCKS', BlocksTable::class))->configureTableName('itrack_chart_blockpostrelation')
		];
	}

	public static function onAfterDelete(Event $event)
    {
        $result = new EventResult();
        $primary = $event->getParameter('primary');
        $id = 0;
        if(is_array($primary) && !empty($primary['ID'])) {
            $id = (int)$primary['ID'];
        } elseif (is_numeric($primary)) {
            $id = (int)$primary;
        }
        if($id > 0) {
            $dbStaff = UserbypostTable::query()
                ->setFilter(['ID_POST' => $id])
                ->setSelect(['ID'])
                ->exec();
            while ($staff = $dbStaff->fetch()) {
                UserbypostTable::delete($staff['ID']);
            }
            $dbBlockPost = BlockPostRelationTable::query()->where('POSTS_ID', $id)->setSelect(['BLOCKS_ID','POSTS_ID'])->exec();
            while($arBlockPost = $dbBlockPost->fetch()) {
                BlockPostRelationTable::delete(['POSTS_ID' => $id, 'BLOCKS_ID' => $arBlockPost['BLOCKS_ID']]);
            }
        }
    }
    
    public static function onBeforeUpdate(Event $event)
    {
        $id = $event->getParameter('primary');
        $fields = $event->getParameter('fields');
        $current = self::getRowById($id);
        $transferManager = TransferManager::getInstance();
        if((int)$fields['ID_SHIEF_POST_USERB24'] !== (int)$current['ID_SHIEF_POST_USERB24']) {
            if((int)$fields['ID_SHIEF_POST_USERB24'] > 0) {
                $transferManager->addUser((int)$fields['ID_SHIEF_POST_USERB24']);
            }
            if((int)$current['ID_SHIEF_POST_USERB24'] > 0) {
                $transferManager->addUser((int)$current['ID_SHIEF_POST_USERB24']);
            }
        }
        if($fields['NAME_POST'] !== $current['NAME_POST']) {
            $managerId = isset($fields['ID_SHIEF_POST_USERB24']) ? $fields['ID_SHIEF_POST_USERB24'] : $current['ID_SHIEF_POST_USERB24'];
            if((int)$managerId > 0) {
                $transferManager->addUser((int)$managerId);
            }
            $dbStaff = UserbypostTable::query()
                ->setFilter(['ID_POST' => $current['ID']])
                ->setSelect(['ID','ID_STAFF'])
                ->exec();
            while($arStaff = $dbStaff->fetch()) {
                $transferManager->addUser((int)$arStaff['ID_STAFF']);
            }
        }
    }

    public static function onAfterAdd(Event $event)
    {
        $result = new EventResult();
        $id = $event->getParameter('primary');
        $fields = $event->getParameter('fields');
        if(empty($fields['ID_JOB_FOLDERB24'])) {
            $folderManager = new PostFolderManager();
            $folderManager->createPostFolder($id);
        }
        if(!empty($fields['ID_SHIEF_POST_USERB24'])) {
            $transferManager = TransferManager::getInstance();
            $transferManager->addUser((int)$fields['ID_SHIEF_POST_USERB24']);
        }
    }
}
