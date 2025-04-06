<?php

namespace Local\Fwink\Controller;

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Error;
use Bitrix\Main\Loader;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\UserTable;
use Bitrix\Main\Web\Json;
use Local\Fwink\Controller\ActionFilter\Domain;
use Local\Fwink\Staff\TransferManager;
use Local\Fwink\Tables\BlockPostRelationTable;
use Local\Fwink\Tables\BlocksTable;
use Local\Fwink\Tables\PortalTable;
use Local\Fwink\Tables\PostsTable;
use Local\Fwink\Tables\UserbypostTable;

class Staff extends Controller
{
    public function configureActions()
    {
        return [
            'getPostList' => [
                new ActionFilter\Authentication(),
                new ActionFilter\HttpMethod(
                    array(ActionFilter\HttpMethod::METHOD_GET, ActionFilter\HttpMethod::METHOD_POST)
                ),
                new ActionFilter\Csrf(),
            ],
            'changePost' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod(
                        array(ActionFilter\HttpMethod::METHOD_GET, ActionFilter\HttpMethod::METHOD_POST)
                    ),
                    new ActionFilter\Csrf(),
                    new Domain()
                ]
            ]
        ];
    }

    public function getPostListAction($id)
    {
        $result = [];
        $idPortal = 1; // todo: заменить
        $userPosts = [];
        $users = [];
        $dbHeadPosts = \Local\Fwink\Tables\PostsTable::query()
            ->setFilter(['ID_PORTAL' => $idPortal, 'ID_SHIEF_POST_USERB24' => $id])
            ->setSelect([
                'ID_POST' => 'ID',
                'NAME_POST',
                'ID_SHIEF_POST_USERB24',
                'POST_FUNCTIONS' => 'FUNCTION_OF_POST',
                'POST_CKP' => 'CKP_OF_POST',
                'PARENT_ID_POST' => 'ID_SUPERVISOR_POST',
                'PARENT_NAME_POST' => 'PARENT_POST.NAME_POST',
                'PARENT_SHIEF_ID' => 'PARENT_POST.ID_SHIEF_POST_USERB24'
            ])
            ->exec();
        while($headPost = $dbHeadPosts->fetch()) {
            // todo: оптимизировать кучу циклов
            $dbCurrentBlocks = BlockPostRelationTable::query()->where('POSTS_ID',$headPost['ID_POST'])->setSelect(['BLOCKS_ID'])->exec();
            $currentBlocksID = [];
            while($arCurrentBlock = $dbCurrentBlocks->fetch()) {
                $currentBlocksID[] = $arCurrentBlock['BLOCKS_ID'];
            }
            if(!empty($currentBlocksID)) {
                $dbChildBlocks = BlocksTable::query()->whereIn('ID_PARENT_BLOCK', $currentBlocksID)->setSelect(['ID','NAME','POSTS'])->exec();
                while($childBlock = $dbChildBlocks->fetchObject()) {
                    $headPost['CHILDS'][$childBlock->getId()] = [
                        'ID' => $childBlock->getId(),
                        'NAME' => $childBlock->getName(),
                        'POSTS' => []
                    ];
                    foreach ($childBlock->getPosts() as $post) {
                        $postId = (int)$post->getId();
                        $headPost['CHILDS'][$childBlock->getId()]['POSTS'][$postId] = [
                            'ID' => $postId,
                            'NAME' => $post->getNamePost(),
                            'USERS' => []
                        ];
                        $dbPostUsers = \Local\Fwink\Tables\UserbypostTable::query()
                            ->where('ID_POST',$postId)
                            ->where('ACTIVE','Y')
                            ->setSelect(['ID_STAFF'])
                            ->exec();
                        if($dbPostUsers->getSelectedRowsCount() > 0) {
                            while($arPostUser = $dbPostUsers->fetch()) {
                                if((int)$arPostUser['ID_STAFF'] > 0) {
                                    $headPost['CHILDS'][$childBlock->getId()]['POSTS'][$postId]['USERS'][] = $arPostUser['ID_STAFF'];
                                    $users[] = $arPostUser['ID_STAFF'];
                                }
                            }
                        }
                        $managerId = $post->get('ID_SHIEF_POST_USERB24');
                        if((int)$managerId > 0) {
                            $headPost['CHILDS'][$childBlock->getId()]['POSTS'][$postId]['USERS'][] = $managerId;
                            $users[] = $managerId;
                        }
                        if(empty($headPost['CHILDS'][$childBlock->getId()]['POSTS'][$postId]['USERS'])) {
                            unset($headPost['CHILDS'][$childBlock->getId()]['POSTS'][$postId]);
                        }
                    }

                    if(empty($headPost['CHILDS'][$childBlock->getId()]['POSTS'])) {
                        unset($headPost['CHILDS'][$childBlock->getId()]);
                    }
                }
            }

            $userPosts[] = $headPost;
        }

        $dbPost = \Local\Fwink\Tables\UserbypostTable::query()
            ->setFilter(['ID_PORTAL' => $idPortal, 'ID_STAFF' => $id, 'ACTIVE' => 'Y'])
            ->setSelect([
                'ID_STAFF',
                'ID_POST',
                'NAME_POST' => 'POST.NAME_POST',
                'POST_FUNCTIONS' => 'POST.FUNCTION_OF_POST',
                'POST_CKP' => 'POST.CKP_OF_POST',
                'PARENT_ID_POST' => 'POST.ID_SUPERVISOR_POST',
                'PARENT_NAME_POST' => 'POST.PARENT_POST.NAME_POST',
                'PARENT_SHIEF_ID' => 'POST.PARENT_POST.ID_SHIEF_POST_USERB24'
            ])
            ->exec();

        while($userPost = $dbPost->fetch()) {
            $userPosts[] = $userPost;
        }



        $dbBlocks = \Local\Fwink\Tables\BlocksTable::query()
            ->where('ID_PORTAL',$idPortal)
            ->setSelect(['ID','ID_PARENT_BLOCK','POSTS','NAME'])
            ->exec();
        while($obBlock = $dbBlocks->fetchObject()) {
            $arBlock = [
                'ID' => $obBlock->getId(),
                'ID_PARENT_BLOCK' => $obBlock->getIdParentBlock(),
                'NAME' => $obBlock->getName()
            ];
            foreach ($obBlock->getPosts() as $post) {
                $arBlock['POSTS'][] = (int)$post->getId();
            }
            $arBlocks[] = $arBlock;
        }

        function findParentBlock($blockID, $arBlocks, &$result) {
            foreach($arBlocks as $block) {
                if($block['ID'] == $blockID) {
                    $result[] = $block;
                    if(!empty($block['ID_PARENT_BLOCK'])) {
                        findParentBlock($block['ID_PARENT_BLOCK'], $arBlocks, $result);
                    }
                    break;
                }
            }
        }

        function getPostBlocks($postId, $arBlocks) {
            $result = [];

            foreach($arBlocks as $arBlock) {
                if(in_array((int)$postId, $arBlock['POSTS'])) {
                    $result[] = $arBlock;
                    findParentBlock($arBlock['ID_PARENT_BLOCK'], $arBlocks, $result);
                    break;
                }
            }

            $result = array_reverse($result);
            return $result;
        }
        foreach($userPosts as $pkey=>$arPost) {
            $userPosts[$pkey]['BLOCKS'] = getPostBlocks($arPost['ID_POST'], $arBlocks);
            if(empty($arPost['PARENT_SHIEF_ID'])) {
                $ppId = 0;
                $ppName = '';
                $ppUid = 0;
                foreach ($userPosts[$pkey]['BLOCKS'] as $key=>$arBlock) {
                    if($key === (count($userPosts[$pkey]['BLOCKS']) - 1)) {
                        break;
                    }
                    if(!empty($arBlock['POSTS'])) {
                        $dbPost = PostsTable::query()
                            ->whereIn('ID', $arBlock['POSTS'])
                            ->where('IS_MANAGER_POST','Y')
                            ->whereNotNull('ID_SHIEF_POST_USERB24')
                            ->setSelect(['ID','NAME_POST','ID_SHIEF_POST_USERB24'])
                            ->exec();
                        while($arPost = $dbPost->fetch()) {
                            $ppId = (int)$arPost['ID'];
                            $ppName = $arPost['NAME_POST'];
                            $ppUid = (int)$arPost['ID_SHIEF_POST_USERB24'];
                        }
                    }
                }
                if($ppUid > 0) {
                    $userPosts[$pkey]['PARENT_ID_POST'] = $ppId;
                    $userPosts[$pkey]['PARENT_NAME_POST'] = $ppName;
                    $userPosts[$pkey]['PARENT_SHIEF_ID'] = $ppUid;
                    $users[] = $ppUid;
                }
            }
        }

        $result['POSTS'] = $userPosts;

        $users = array_unique(array_merge($users, array_column($userPosts, 'PARENT_SHIEF_ID')));
        if(!empty($users)) {
            $dbUsers = UserTable::query()
                ->whereIn('ID', $users)
                ->setSelect(['*'])
                ->exec();
            if ($dbUsers->getSelectedRowsCount() > 0) {
                while ($arUser = $dbUsers->fetch()) {
                    if (empty($arUser['PERSONAL_PHOTO'])) {
                        $arUser['PERSONAL_PHOTO'] = '/local/apps/img/ui-user.svg';
                    } else {
                        $arUser['PERSONAL_PHOTO'] = \CFile::GetPath($arUser['PERSONAL_PHOTO']);
                    }
                    $result['USERS_INFO'][$arUser['ID']] = $arUser;
                }
            }
        }

        return $result;
    }

    public function changePostAction($data)
    {
        if((int)$data['user']['ID'] > 0 && in_array($data['typeEvent'],['copy','move'])) {
            $oldPostId = (int)$data['oldPostId'];
            $newPostID = (int)$data['newPostId'];
            if ($oldPostId > 0 && $newPostID > 0) {
                if($data['typeEvent'] === 'move') {
                    $dbHeadPost = \Local\Fwink\Tables\PostsTable::query()
                        ->setFilter(['=ID' => $oldPostId])
                        ->setSelect(['ID', 'ID_SHIEF_POST_USERB24'])
                        ->exec();
                    $arHeadPost = $dbHeadPost->fetch();
                    if ((int)$arHeadPost['ID_SHIEF_POST_USERB24'] === (int)$data['user']['ID']) {
                        \Local\Fwink\Tables\PostsTable::update($arHeadPost['ID'], ['ID_SHIEF_POST_USERB24' => null]);
                    } else {
                        $dbPostRef = \Local\Fwink\Tables\UserbypostTable::query()
                            ->setFilter(['=ID_STAFF' => $data['user']['ID'], '=ID_POST' => $oldPostId])
                            ->setSelect(['ID'])
                            ->exec();
                        while ($arPostRef = $dbPostRef->fetch()) {
                            \Local\Fwink\Tables\UserbypostTable::delete($arPostRef['ID']);
                        }
                    }
                }

                $dbNewPost = \Local\Fwink\Tables\PostsTable::query()
                    ->setFilter(['=ID' => $newPostID])
                    ->setSelect(['ID','IS_MANAGER_POST'])
                    ->exec();
                if($arNewPost = $dbNewPost->fetch()) {
                    if($arNewPost['IS_MANAGER_POST'] === 'Y') {
                        \Local\Fwink\Tables\PostsTable::update($arNewPost['ID'], ['ID_SHIEF_POST_USERB24' => $data['user']['ID']]);
                    } else {
                        $dbAlreadyInPost = \Local\Fwink\Tables\UserbypostTable::query()
                            ->where('ID_POST',$arNewPost['ID'])
                            ->where('ID_STAFF',$data['user']['ID'])
                            ->where('ID_PORTAL',$GLOBALS['FWINK']['ID_PORTAL'])
                            ->setSelect(['ID'])
                            ->exec();
                        if($dbAlreadyInPost->getSelectedRowsCount() === 0) {
                            \Local\Fwink\Tables\UserbypostTable::add([
                                'ID_PORTAL' => $GLOBALS['FWINK']['ID_PORTAL'],  // todo: replace globals
                                'ID_STAFF' => $data['user']['ID'],
                                'ID_POST' => $arNewPost['ID'],
                                'ACTIVE' => 'Y'
                            ]);
                        } else {
                            $arAlreadyInPost = $dbAlreadyInPost->fetch();
                            \Local\Fwink\Tables\UserbypostTable::update($arAlreadyInPost['ID'], ['ACTIVE' => 'Y']);
                        }
                    }
                }

                TransferManager::getInstance()->process();
            }
        }
    }
}