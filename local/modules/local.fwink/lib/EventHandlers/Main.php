<?php

namespace Local\Fwink\EventHandlers;

use Local\Fwink\Staff\TransferManager;
use Local\Fwink\Tables\UserbypostTable;

class Main
{
    public static function onEpilog()
    {
        $transferManager = TransferManager::getInstance();
        $transferManager->process();
    }

	public static function onAfterUserUpdate(&$arFields)
    {
        if($arFields['RESULT']) {
            if(!empty($arFields['ACTIVE'])) {
                $dbRel = UserbypostTable::query()
                    ->where('ID_STAFF', $arFields['ID'])
                    ->setSelect(['ID','ACTIVE'])
                    ->exec();
                while($arUserRel = $dbRel->fetch()) {
                    if($arFields['ACTIVE'] === 'N' && $arUserRel['ACTIVE'] === 'Y') {
                        UserbypostTable::update($arUserRel['ID'], ['ACTIVE' => 'N']);
                    } elseif ($arFields['ACTIVE'] === 'Y' && $arUserRel['ACTIVE'] === 'N') {
                        UserbypostTable::update($arUserRel['ID'], ['ACTIVE' => 'Y']);
                    }
                }

				if($arFields['ACTIVE'] === 'N') {
					$dbMgr = \Local\Fwink\Tables\PostsTable::query()
						->setSelect(['ID'])
						->where('ID_SHIEF_POST_USERB24',$arFields['ID'])
						->exec();
					while($ar = $dbMgr->fetch()) {
						\Local\Fwink\Tables\PostsTable::update($ar['ID'], ['ID_SHIEF_POST_USERB24' => NULL]);
					}
				}
            }
            if(!empty($arFields['UF_DEPARTMENT'])) {
                
            }
        }
    }
}