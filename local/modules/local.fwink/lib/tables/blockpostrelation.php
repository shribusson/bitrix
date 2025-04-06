<?php

namespace Local\Fwink\Tables;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;

class BlockPostRelationTable extends DataManager
{
    public static function getTableName()
    {
        return 'itrack_chart_blockpostrelation';
    }
    
    public static function getMap()
    {
        return [
            (new IntegerField('BLOCKS_ID'))->configurePrimary(true),
            (new IntegerField('POSTS_ID'))->configurePrimary(true)
        ];
    }
}