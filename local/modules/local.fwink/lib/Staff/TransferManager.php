<?php

namespace Local\Fwink\Staff;

use Local\Fwink\Rest;
use Local\Fwink\Tables\BlocksTable;
use Local\Fwink\Tables\PostsTable;
use Local\Fwink\Tables\UserbypostTable;

class TransferManager
{
    private static $instance;
    private $users = [];

    private function __construct(){}
    private function __clone() {}
    private function __wakeup() {}

    public static function getInstance() : TransferManager
    {
        return self::$instance ?? (self::$instance = new static());
    }

    public function addUser(int $userId)
    {
        if($userId > 0) {
            $this->users[] = $userId;
            $this->users = array_unique($this->users);
        }
    }

    public function process()
    {
        \LocalFwink::Log('Update users departments start'); // todo: check result
        \LocalFwink::Log(print_r($this->users, true));
        $batch = [];
        foreach($this->users as $userID) {
            // todo: вынести запросы из цикла
            $arPosts = [];
            $dbPosts = UserbypostTable::query()
                ->setFilter([
                    'ID_PORTAL' => $GLOBALS['FWINK']['ID_PORTAL'],
                    'ID_STAFF' => $userID,
                    'ACTIVE' => 'Y'
                ])
                ->setSelect(['ID_POST'])
                ->exec();
            while ($post = $dbPosts->fetch()) {
                $arPosts[] = $post['ID_POST'];
            }
            $dbManagerPosts = PostsTable::query()
                ->setFilter([
                    'ID_PORTAL' => $GLOBALS['FWINK']['ID_PORTAL'],
                    'ID_SHIEF_POST_USERB24' => $userID
                ])
                ->setSelect(['ID'])
                ->exec();
            $arManagerPosts = [];
            while($post = $dbManagerPosts->fetch()) {
                $arPosts[] = $post['ID'];
                $arManagerPosts[] = $post['ID'];
            }
            $arPosts = array_unique($arPosts);
            $arUpdateFields = [];
            $arDepartments = [];
            $arDepartmentUpdate = [];
            if(!empty($arPosts)) {
                /*$dbBlock = BlocksTable::query()
                    ->setFilter([
                        'ID_PORTAL' => $GLOBALS['FWINK']['ID_PORTAL'],
                        'ID_POST' => $arPosts
                    ])
                    ->setSelect(['ID', 'SUBDIVISION', 'ID_POST'])
                    ->exec();
                while($arBlock = $dbBlock->fetch()) {
                    if(!empty($arBlock['SUBDIVISION']) && (int)$arBlock['SUBDIVISION'] > 0) {
                        $arDepartments[] = (int)$arBlock['SUBDIVISION'];
                        if(in_array($arBlock['ID_POST'], $arManagerPosts)) {
                            $arDepartmentUpdate[(int)$arBlock['SUBDIVISION']]['UF_HEAD'] = $userID;
                        }
                    }
                }*/

                $dbPostInfo = PostsTable::query()
                    ->whereIn('ID', $arPosts)
                    ->setSelect(['ID','NAME_POST','BLOCKS'])
                    ->exec();
                $arNamePosts = [];
                while($post = $dbPostInfo->fetchObject()) {
                    $arNamePosts[] = $post->getNamePost();
                    foreach($post->getBlocks() as $block) {
                        $postId = $post->getId();
                        $subdivision = $block->getSubdivision();
                        if(!empty($subdivision) && (int)$subdivision > 0) {
                            $arDepartments[] = (int)$subdivision;
                            if(in_array($postId, $arManagerPosts)) {
                                $arDepartmentUpdate[(int)$subdivision]['UF_HEAD'] = $userID;
                            }
                        }
                    }
                }
                if(!empty($arNamePosts)) {
                    $arUpdateFields['WORK_POSITION'] = implode(', ', $arNamePosts);
                }
            }
            if(!empty($arDepartments)) {
                $arDepartments = array_unique($arDepartments);
                $arUpdateFields['UF_DEPARTMENT'] = $arDepartments;
            } else {
                $arUpdateFields['UF_DEPARTMENT'] = [];
            }

            if(!empty($arUpdateFields)) {
                $arUpdateFields['ID'] = $userID;
                $batch[$userID] = [
                    'method' => 'user.update',
                    'params' => $arUpdateFields
                ];
            }
            \LocalFwink::Log('dept update '.print_r($arDepartmentUpdate, true));
            $headDepartments = Rest::execute('department.get', ['UF_HEAD' => $userID]);
            if(!empty($headDepartments)) {
                \LocalFwink::Log('head dept '.print_r($headDepartments, true));
                foreach($headDepartments as $department) {
                    if(empty($arDepartmentUpdate[(int)$department['ID']])) {
                        if(empty($batch['dept'.$department['ID']])) {
                            $batch['dept' . $department['ID']] = [
                                'method' => 'department.update',
                                'params' => ['ID' => $department['ID'], 'UF_HEAD' => '']
                            ];
                        }
                    } else {
                        unset($arDepartmentUpdate[(int)$department['ID']]);
                    }
                }
            }

            foreach($arDepartmentUpdate as $departmentID => $updateParams) {
                $updateParams['ID'] = $departmentID;
                $batch['dept'.$departmentID] = [
                    'method' => 'department.update',
                    'params' => $updateParams
                ];
            }
        }
        if(!empty($batch)) {
            \LocalFwink::Log('update users batch '.print_r($batch, true));
            $result = Rest::batch($batch);
            \LocalFwink::Log('Update users departments'.PHP_EOL.print_r($result, true)); // todo: check result
        }
    }
}