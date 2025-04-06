<?

namespace Local\Fwink\Tables;

use Bitrix\Main\Entity\EventResult;
use Bitrix\Main\ORM\Data;
use Bitrix\Main\ORM\Event;
use Bitrix\Main\ORM\Fields;
use Exception;
use Local\Fwink\Staff\TransferManager;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class BlocksTable extends Data\DataManager
{
    public static function getTableName(): string
    {
        return 'itrack_chart_blocks';
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
				'autocomplete' => true,
				'title' => 'ID',
			),
			'ID_PORTAL' => array(
				'data_type' => 'string',
				'title' => 'ID_PORTAL',
			),
			'SUBDIVISION' => array(
				'data_type' => 'string',
				'title' => Loc::getMessage('local.fwink_tables_block_SUBDIVISION'),
                'required' => true
			),
			'NAME' => array(
				'data_type' => 'string',
				'title' => Loc::getMessage('local.fwink_tables_block_NAME'),
                'required' => true
			),
            (new Fields\Relations\ManyToMany('POSTS', PostsTable::class))->configureTableName('itrack_chart_blockpostrelation'),
			'ID_POST' => array(
				'data_type' => 'string',
				'title' => 'ID_POST',
			),
            'ID_STAFF_POST' => array(
                'data_type' => 'integer',
                'title' => 'ID_STAFF_POST'
            ),
			'ID_PARENT_BLOCK' => array(
				'data_type' => 'string',
				'title' => Loc::getMessage('local.fwink_tables_block_ID_PARENT_BLOCK'),
			),
			'COLOR_HEADER' => array(
				'data_type' => 'string',
				'title' => Loc::getMessage('local.fwink_tables_block_COLOR_HEADER'),
			),
			'COLOR_BLOCK' => array(
				'data_type' => 'string',
				'title' => Loc::getMessage('local.fwink_tables_block_COLOR_BLOCK'),
			),
            'COLOR_BY_PARENT' => array(
                'data_type' => 'boolean',
                'values' => array('N','Y'),
                'title' => Loc::getMessage('local.fwink_tables_block_COLOR_BY_PARENT')
            ),
			'NUMBER' => array(
				'data_type' => 'string',
				'title' => Loc::getMessage('local.fwink_tables_block_NUMBER'),
			),
            'SORT' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('local.fwink_tables_block_SORT')
            ),
            'IS_HIDE' => array(
                'data_type' => 'boolean',
                'values' => array('N','Y'),
                'title' => Loc::getMessage('local.fwink_tables_block_IS_HIDE')
            ),
			'ADDICT_PARAM' => array(
				'data_type' => 'string',
				'title' => Loc::getMessage('local.fwink_tables_block_ADDICT_PARAM'),
			),
            'POST' => new Fields\Relations\Reference(
                'POST',
                '\Local\Fwink\Tables\PostsTable',
                ['=this.ID_POST' => 'ref.ID']
            ),
            'PARENT_BLOCK' => new Fields\Relations\Reference(
                'PARENT_BLOCK',
                __CLASS__,
                ['=this.ID_PARENT_BLOCK' => 'ref.ID']
            )
		];
	}

	public static function onBeforeAdd(Event $event)
    {
        $fields = $event->getParameter('fields');
        $result = new EventResult();
        self::checkParentColor($fields, $result);
        return $result;
    }

    public static function onBeforeUpdate(Event $event)
    {
        $fields = $event->getParameter('fields');
        $result = new EventResult();
        self::checkParentColor($fields, $result);
        return $result;
    }

    public static function onAfterAdd(Event $event)
    {
        $id = $event->getParameter('primary');
        $fields = $event->getParameter('fields');
        self::findEmployees($id);
        if(!empty($fields['COLOR_BLOCK'])) {
            self::updateChildColor($id, $fields['COLOR_BLOCK']);
        }
    }

    public static function onAfterUpdate(Event $event)
    {
        $id = $event->getParameter('primary');
        $fields = $event->getParameter('fields');
        self::findEmployees($id);
        if(!empty($fields['COLOR_BLOCK'])) {
            self::updateChildColor($id, $fields['COLOR_BLOCK']);
        }
		if(!empty($fields['ID_PARENT_BLOCK'])) {
            self::updatePostManagers($id, $fields['ID_PARENT_BLOCK']);
        }
    }

    public static function onBeforeDelete(Event $event)
    {
        $id = $event->getParameter('primary');
        self::findEmployees($id);
    }

    private static function findEmployees($primary)
    {
        $id = 0;
        if(is_array($primary) && !empty($primary['ID'])) {
            $id = (int)$primary['ID'];
        } elseif (is_numeric($primary)) {
            $id = (int)$primary;
        }
        if($id > 0) {
            $dbRow = self::query()
                ->setFilter(['=ID' => $id])
                ->setSelect(['ID', 'POSTS', 'MANAGER_ID' => 'POST.ID_SHIEF_POST_USERB24'])
                ->exec();
            if ($row = $dbRow->fetchObject()) {
                foreach ($row->getPosts() as $post) {
                    $transferManager = TransferManager::getInstance();
                    $dbStaff = UserbypostTable::query()
                        ->setFilter([
                            '=ID_POST' => $post->getId()
                        ])
                        ->setSelect(['ID_STAFF'])
                        ->exec();
                    while ($staff = $dbStaff->fetch()) {
                        $transferManager->addUser((int)$staff['ID_STAFF']);
                    }
                    if($post->getIsManagerPost()) {
                        $managerId = $post->getIdShiefPostUserb24();
                        if (!empty($managerId)) {
                            $transferManager->addUser((int)$managerId);
                        }
                    }
                }
            }
        }
    }

    private static function updateChildColor($primary, $color)
    {
        $id = 0;
        if(is_array($primary) && !empty($primary['ID'])) {
            $id = (int)$primary['ID'];
        } elseif (is_numeric($primary)) {
            $id = (int)$primary;
        }
        if($id > 0) {
            $dbChilds = self::query()
                ->setFilter(['=ID_PARENT_BLOCK' => $id, ['LOGIC' => 'OR', 'COLOR_BLOCK' => false, 'COLOR_BY_PARENT' => 'Y']])
                ->exec();
            $childs = $dbChilds->fetchCollection();
            if($childs !== null) {
                foreach ($childs as $child) {
                    $child->setColorBlock($color);
                }
                $childs->save();
            }
        }
    }

    private static function checkParentColor($fields, EventResult $result)
    {
        if($fields['COLOR_BY_PARENT'] === 'Y' && !empty($fields['ID_PARENT_BLOCK'])) {
            $dbParent = self::query()
                ->setFilter(['=ID' => $fields['ID_PARENT_BLOCK']])
                ->setSelect(['ID','COLOR_BLOCK'])
                ->exec();
            if($arParent = $dbParent->fetch()) {
                if(!empty($arParent['COLOR_BLOCK']) && $fields['COLOR_BLOCK'] !== $arParent['COLOR_BLOCK']) {
                    $result->modifyFields(['COLOR_BLOCK' => $arParent['COLOR_BLOCK']]);
                }
            }
        }
    }

	/**
     * Метод обновляет вышестоящий пост у постов, привязанных напрямую к текущему блоку на менеджерский пост, привязанный к родительскому блоку
     * для не руководящих постов не привязанных к текущему руководящему так же обновляет на пост из вышестоящего блока
     *
     * @param $currentBlockId
     * @param $parentBlockId
     * @return void
     */
    private static function updatePostManagers($primary, $parentBlockId)
    {
        $currentBlockId = 0;
        if(is_array($primary) && !empty($primary['ID'])) {
            $currentBlockId = (int)$primary['ID'];
        } elseif (is_numeric($primary)) {
            $currentBlockId = (int)$primary;
        }
        if($currentBlockId > 0) {
            $newManagerPost = Block::getManagerPostId($parentBlockId);
            if ((int)$newManagerPost > 0) {
                \LocalFwink::Log('[newManagerPost] ' . $newManagerPost);
                $dbManagerPost = BlockPostRelationTable::query()
                    ->registerRuntimeField(new Fields\Relations\Reference(
                        'POST',
                        PostsTable::class,
                        ['=this.POSTS_ID' => 'ref.ID']
                    ))
                    ->where('BLOCKS_ID', $currentBlockId)
                    ->where('POST.IS_MANAGER_POST', 'Y')
                    ->setSelect(['POSTS_ID'])
                    ->exec();
                if ($dbManagerPost->getSelectedRowsCount() > 0) {
                    while ($arManagerPost = $dbManagerPost->fetch()) {
                        PostsTable::update($arManagerPost['POSTS_ID'], ['ID_SUPERVISOR_POST' => $newManagerPost]);
                        $dbNotManagerPost = BlockPostRelationTable::query()
                            ->registerRuntimeField(new Fields\Relations\Reference(
                                'POST',
                                PostsTable::class,
                                ['=this.POSTS_ID' => 'ref.ID']
                            ))
                            ->where('BLOCKS_ID', $currentBlockId)
                            ->whereNot('POST.IS_MANAGER_POST', 'Y')
                            ->whereNot('ID_SUPERVISOR_POST', $arManagerPost['POSTS_ID'])
                            ->setSelect(['POSTS_ID'])
                            ->exec();
                        while ($ar = $dbNotManagerPost->fetch()) {
                            PostsTable::update($ar['POSTS_ID'], ['ID_SUPERVISOR_POST' => $newManagerPost]);
                        }
                    }
                } else {
                    \LocalFwink::Log('not managers');
                    $dbPost = BlockPostRelationTable::query()
                        ->where('BLOCKS_ID', $currentBlockId)
                        ->setSelect(['POSTS_ID'])
                        ->exec();
                    while ($arPost = $dbPost->fetch()) {
                        \LocalFwink::Log(print_r($arPost, true));
                        PostsTable::update($arPost['POSTS_ID'], ['ID_SUPERVISOR_POST' => $newManagerPost]);
                    }
                }
            }
        }
    }
}
