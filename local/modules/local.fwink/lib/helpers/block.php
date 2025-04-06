<?php

namespace Local\Fwink\Helpers;

use Local\Fwink\Tables\BlockPostRelationTable;
use Bitrix\Main\ORM\Fields;
use Local\Fwink\Tables\BlocksTable;
use Local\Fwink\Tables\PostsTable;

class Block
{
    public static function getManagerPostId($blockId)
    {
        $dbPost = BlockPostRelationTable::query()
            ->registerRuntimeField(new Fields\Relations\Reference(
                'POST',
                PostsTable::class,
                ['=this.POSTS_ID' => 'ref.ID']
            ))
            /*->registerRuntimeField(new Fields\Relations\Reference(
                'BLOCK',
                BlocksTable::class,
                ['=this.BLOCKS_ID' => 'ref.ID']
            ))*/
            ->where('BLOCKS_ID', $blockId)
            ->where('POST.IS_MANAGER_POST','Y')
            ->setSelect(['POSTS_ID'])
            ->exec();
        if($dbPost->getSelectedRowsCount() > 0) {
            $arPost = $dbPost->fetch();
            return $arPost['POSTS_ID'];
        } else {
            $dbBlock = BlocksTable::query()->where('ID',$blockId)->setSelect(['ID','ID_PARENT_BLOCK'])->whereNotNull('ID_PARENT_BLOCK')->exec();
            if($arBlock = $dbBlock->fetch()) {
                return self::getManagerPostId($arBlock['ID_PARENT_BLOCK']);
            } else {
                return 0;
            }
        }
    }
}