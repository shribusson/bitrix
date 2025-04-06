<?php

namespace Local\Fwink\Helpers;

use Local\Fwink\Tables\BlocksTable;
use Local\Fwink\Tables\PostsTable;
use Local\Fwink\Tables\UserbypostTable;

class Tools
{
    public static function createStruct()
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $db = \CIBlockSection::GetTreeList(['IBLOCK_ID' => 3],['*','UF_*']);

        print '<pre>';
        $arBlocks = [];
        while($ar = $db->Fetch()) {

            $dirId = $ar['UF_HEAD'];
            $by = 'ID';$order='ASC';
            $dbUsers = \CUser::GetList($by, $order, ['UF_DEPARTMENT' => $ar['ID'], 'ACTIVE' => 'Y']);

            $parentPost = 0;
            if(!empty($ar['IBLOCK_SECTION_ID']) && !empty($arBlocks[$ar['IBLOCK_SECTION_ID']]['HEAD_POST'])) {
                $parentPost = $arBlocks[$ar['IBLOCK_SECTION_ID']]['HEAD_POST'];
            }
            $arHeadPost = [
                'ID_PORTAL' => 1,
                'NAME_POST' => $ar['NAME'].' - руководитель',
                'ID_SUPERVISOR_POST' => $parentPost,
                'ID_SHIEF_POST_USERB24' => 0
            ];
            $arStaffPostUsers = [];
            while($arUser = $dbUsers->fetch()) {
                if((int)$arUser['ID'] === (int)$dirId) {
                    if(!empty($arUser['WORK_POSITION'])) {
                        $arHeadPost['NAME_POST'] = $arUser['WORK_POSITION'];
                        $arHeadPost['ID_SHIEF_POST_USERB24'] = $arUser['ID'];
                        $arHeadPost['IS_MANAGER_POST'] = 'Y';
                        $headPostId = static::findCreatedPost($arUser['ID'], $arUser['WORK_POSITION']);
                        if($headPostId !== null) {
                            $arHeadPost['ID'] = $headPostId;
                        }
                    }
                } else {
                    $arStaffPostUsers[] = $arUser;
                }
            }

            $staffPosts = [];
            
            if(empty($arHeadPost['ID'])) {
                $addResult = PostsTable::add($arHeadPost);
                if($addResult->isSuccess()) {
                    $headPostId = $addResult->getId();
                } else {
                    print_r($addResult->getErrorMessages());
                    exit;
                }
            }
            
            if((int)$headPostId > 0) {
                //$staffPostId = 0;
                if(!empty($arStaffPostUsers)) {
                    foreach ($arStaffPostUsers as $staffPostUser) {
                        $postName = $ar['NAME'] . ' - сотрудники';
                        if(!empty($staffPostUser['WORK_POSITION'])) {
                            $postName = $staffPostUser['WORK_POSITION'];
                        }
                        $issetPostId = static::findCreatedPost($staffPostUser['ID'], $postName, $staffPosts);
                        if($issetPostId === null) {
                            $arStaffPost = [
                                'ID_PORTAL' => 1,
                                'NAME_POST' => $postName,
                                'ID_SUPERVISOR_POST' => $headPostId
                            ];
                            $staffAddRes = PostsTable::add($arStaffPost);
                            if ($staffAddRes->isSuccess()) {
                                $staffPostId = $staffAddRes->getId();
                                $staffPosts[$staffPostId] = [
                                    'id' => $staffPostId,
                                    'name' => $postName,
                                    'users' => [$staffPostUser['ID']]
                                ];
                            } else {
                                print_r($staffAddRes->getErrorMessages());
                                print_r($ar);
                                print_r($arStaffPost);
                            }
                        } else {
                            if(empty($staffPosts[$issetPostId])) {
                                $staffPosts[$issetPostId] = ['name' => $postName, 'id' => $issetPostId];
                            }
                            $staffPosts[$issetPostId]['users'][] = $staffPostUser['ID'];
                        }
                    }
                }

                $blockFields = [
                    'ID_PORTAL' => 1,
                    'SUBDIVISION' => $ar['ID'],
                    'NAME' => $ar['NAME'],
                    //'ID_POST' => $headPostId,
                    //'ID_STAFF_POST' => $staffPostId,
                    'ID_PARENT_BLOCK' => (!empty($ar['IBLOCK_SECTION_ID']) && !empty($arBlocks[$ar['IBLOCK_SECTION_ID']]['BLOCK'])) ? $arBlocks[$ar['IBLOCK_SECTION_ID']]['BLOCK'] : 0
                ];

                $blockAddResult = BlocksTable::add($blockFields);
                if($blockAddResult->isSuccess()) {
                    $block = BlocksTable::getByPrimary($blockAddResult->getId())->fetchObject();

                    $block->addToPosts(PostsTable::getByPrimary($headPostId)->fetchObject());
                    /*if($staffPostId > 0) {
                        $block->addToPosts(PostsTable::getByPrimary($staffPostId)->fetchObject());
                    }*/
                    if(!empty($staffPosts)) {
                        foreach($staffPosts as $staffPostData) {
                            $block->addToPosts(PostsTable::getByPrimary($staffPostData['id'])->fetchObject());
                        }
                    }
                    $block->save();
                    $arBlocks[$ar['ID']] = [
                        'BLOCK' => $blockAddResult->getId(),
                        'HEAD_POST' => $headPostId
                    ];
                    if(!empty($staffPosts)) {
                        foreach($staffPosts as $staffPostData) {
                            foreach($staffPostData['users'] as $staffId) {
                                $dbCheck = UserbypostTable::query()
                                    ->where('ID_PORTAL', 1)
                                    ->where('ID_STAFF', $staffId)
                                    ->where('ID_POST', $staffPostData['id'])
                                    ->setSelect(['ID'])
                                    ->exec();
                                if($dbCheck->getSelectedRowsCount() === 0) {
                                    $ur = UserbypostTable::add(['ID_PORTAL' => 1, 'ID_STAFF' => $staffId, 'ID_POST' => $staffPostData['id']]);
                                    if(!$ur->isSuccess()) {
                                        print_r($ur->getErrorMessages());
                                    }
                                }
                            }
                        }
                    }
                    /*if(!empty($arStaffPostUsers) && (int)$staffPostId > 0) {
                        foreach($arStaffPostUsers as $staffId) {
                            $ur = UserbypostTable::add(['ID_PORTAL' => 1, 'ID_STAFF' => $staffId, 'ID_POST' => $staffPostId]);
                            if(!$ur->isSuccess()) {
                                print_r($ur->getErrorMessages());
                            }
                        }
                    }*/
                } else {
                    print_r($blockAddResult->getErrorMessages());
                    exit;
                }
            }
        }
        print '</pre>';
    }

    private static function findCreatedPost($userId, $postName, $currentBlockPosts = [])
    {
        $dbPost = UserbypostTable::query()
            ->where('ID_PORTAL', 1)
            ->where('ID_STAFF', $userId)
            ->where('POST.NAME_POST', $postName)
            ->setSelect(['ID_POST'])
            ->exec();
        if($arPost = $dbPost->fetch()) {
            return $arPost['ID_POST'];
        }

        if(!empty($currentBlockPosts)) {
            foreach ($currentBlockPosts as $post) {
                if($post['name'] === $postName) {
                    return $post['id'];
                }
            }
        }

        return null;
    }
}