<?php

namespace Local\Fwink\Controller;

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Error;
use Bitrix\Main\Loader;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Web\Json;
use Local\Fwink\Blocks;
use Local\Fwink\Controller\ActionFilter\Domain;
use Local\Fwink\Service\TokenManager;
use Local\Fwink\Staff\TransferManager;
use Local\Fwink\Tables\BlocksTable;
use Local\Fwink\Tables\PortalTable;
use Local\Fwink\Tables\PostsTable;
use Local\Fwink\Tables\UserbypostTable;

class Block extends Controller
{
    protected function getDefaultPreFilters()
    {
        return [
            new ActionFilter\HttpMethod(
                array(ActionFilter\HttpMethod::METHOD_GET, ActionFilter\HttpMethod::METHOD_POST)
            ),
            new ActionFilter\Csrf(),
            new Domain()
        ];
    }

    public function configureActions()
    {
        return [
            'getList' => [],
            'setWidth' => [],
            'move' => [],
            'externalList' => [
                'prefilters' => []
            ],
            'externalStructure' => [
                'prefilters' => []
            ]
        ];
    }

    public function getListAction($portalId = null)
    {
        if(!$portalId) {
            $portalId = $GLOBALS['FWINK']['ID_PORTAL']; // todo: replace portal id
        }
        $dbBlocks = BlocksTable::query()
            ->setFilter(['ID_PORTAL' => $portalId])
            ->setSelect(['*', 'POSTS'/*'NAME_POST' => 'POST.NAME_POST', 'FUNCTIONS' => 'POST.FUNCTION_OF_POST', 'CKP' => 'POST.CKP_OF_POST', 'SHIEF' => 'POST.ID_SHIEF_POST_USERB24'*/])
            ->exec();
        $arBlocks = [];
        $arUsers = [];
        $arPosts = [];
        while($block = $dbBlocks->fetchObject()) {
            $parentBlockId = $block->getIdParentBlock();
            if(!empty($parentBlockId)) {
                $parentBlockId = (int)$parentBlockId;
            }
            $arBlock = [
                'ID' => $block->getId(),
                'ID_PORTAL' => $block->getIdPortal(),
                'SUBDIVISION' => $block->getSubdivision(),
                'NAME' => $block->getName(),
                'ID_PARENT_BLOCK' => $parentBlockId,
                'COLOR_HEADER' => $block->getColorHeader(),
                'COLOR_BLOCK' => $block->getColorBlock(),
                'COLOR_BY_PARENT' => $block->getColorByParent(),
                'NUMBER' => $block->getNumber(),
                'SORT' => $block->getSort(),
                'IS_HIDE' => $block->getIsHide(),
                'POSTS' => []
            ];

            $posts = $block->getPosts();
            foreach($posts as $post) {
                $postInfo = [
                    'id' => $post->getId(),
                    'name' => $post->getNamePost(),
					'functions' => $post->getFunctionOfPost(),
                    'ckp' => $post->getCkpOfPost(),
                    'SHIEF' => $post->getIdShiefPostUserb24(),
                    'isManager' => $post->getIsManagerPost(),
                    'sort' => $post->getSort(),
                    'users' => []
                ];
                if(empty($postInfo['sort'])) {
                    $postInfo['sort'] = 100;
                }
                if($postInfo['isManager'] === true) {
                    if(!empty($postInfo['SHIEF'])) {
                        $arUsers[] = $postInfo['SHIEF'];
                    }
                    $arBlock['CKP'] = $postInfo['ckp'];
                } else {
                    $arPosts[] = $postInfo['id'];
                }
                $arBlock['POSTS'][] = $postInfo;
            }
            usort($arBlock['POSTS'], static function($a, $b) {
               return ($a['sort'] < $b['sort'] || $a['isManager']) ? -1 : 1;
            });

            /*if(!empty($arBlock['SHIEF'])) {
                $arUsers[] = $arBlock['SHIEF'];
            } else {
                $arPosts[] = $arBlock['ID_POST'];
            }*/
            if((int)$arBlock['ID_PARENT_BLOCK'] === 0) {
                $arBlock['ID_PARENT_BLOCK'] = '';
            }
            $addictParam = $block->getAddictParam();
            if(!empty($addictParam)) {
                try {
                    $additionalParams = Json::decode($addictParam);
                    $arBlock += $additionalParams;
                } catch(\Exception $e) {

                }
            }
            $arBlocks[] = $arBlock;
        }
        $arUserPosts = [];
        if(!empty($arPosts)) {
            $arPosts = array_unique($arPosts);
            $dbUsersByPost = UserbypostTable::query()
                ->setFilter(['ID_PORTAL' => $GLOBALS['FWINK']['ID_PORTAL'], 'ID_POST' => $arPosts,'ACTIVE' => 'Y']) // todo: replace portal id
                ->setSelect(['ID_POST','ID_STAFF'])
                ->exec();
            while($arUP = $dbUsersByPost->fetch()) {
                $arUsers[] = $arUP['ID_STAFF'];
                $arUserPosts[$arUP['ID_POST']][] = $arUP['ID_STAFF'];
            }
        }
        $arUsers = array_unique($arUsers);
        $res = \Local\Fwink\Rest::getList('user.get', '', [
            'FILTER' => [
                'ID' => $arUsers
            ]
        ]);
        $arUsersResult = [];
        foreach($res as $user) {
            if(empty($user['PERSONAL_PHOTO'])) {
                $user['PERSONAL_PHOTO'] = '/local/apps/img/ui-user.svg';
            }
            $arUsersResult[$user['ID']] = $user;
        }
        foreach($arBlocks as &$block) {
            foreach ($block['POSTS'] as $i=>$arPost) {
                if(!empty($arPost['SHIEF'])) {
                    $block['POSTS'][$i]['users'][] = $arUsersResult[$arPost['SHIEF']];
                } else {
                    foreach($arUserPosts[$arPost['id']] as $uId) {
                        $block['POSTS'][$i]['users'][] = $arUsersResult[$uId];
                    }
                }
            }
        }
        return $arBlocks;
    }

    public function setWidthAction($id, $width)
    {
        if ((int)$id > 0 && (int)$width > 0) {
            $dbBlock = BlocksTable::query()
                ->where('ID_PORTAL', $GLOBALS['FWINK']['ID_PORTAL']) // todo: replace portal id
                ->where('ID', $id)
                ->setSelect(['ID','ADDICT_PARAM'])
                ->exec();
            if($dbBlock->getSelectedRowsCount() !== 0) {
                $block = $dbBlock->fetchObject();
                if($block !== null) {
                    $currentValues = $block->getAddictParam();
                    try {
                        $params = [];
                        try {
                            $params = Json::decode($currentValues);
                        } catch(\Exception $e) {

                        }
                        $params['CUSTOM_WIDTH'] = (int)$width;
                        $block->setAddictParam(Json::encode($params));
                        $block->save();
                    } catch(\Exception $e) {

                    }
                }
            } else {
                $this->addError(new Error('wrong block id'));
            }

        } else {
            $this->addError(new Error('wrong request'));
            //http_response_code(401);
        }
    }

    public function moveAction($data)
    {
        if ((int)$data['blockId'] > 0 && (int)$data['newParentId'] > 0 && in_array($data['typeEvent'], ['copy', 'move'])) {
            if($data['typeEvent'] === 'move') {
                $userAuth = TokenManager::getInstance()->getAuth();

                // todo: replace globals
                $isAdmin = \Local\Fwink\Rest::execute('user.admin', [], !empty($userAuth) ? ['access_token' => $userAuth] : false);
                $GLOBALS['FWINK']['admin'] = (bool)$isAdmin === true;
                try {
                    $service = new Blocks();
                    $service->update($data['blockId'], ['ID_PARENT_BLOCK' => $data['newParentId']]);

                    $transferManager = TransferManager::getInstance();
                    $transferManager->process();
                } catch(\Throwable $e) {
                    \LocalFwink::Log($e);
                    $this->addError(new Error('internal error'));
                }
            }
        } else {
            $this->addError(new Error('wrong request'));
        }
    }

    public function externalListAction($token = '')
    {
        if(TokenManager::getInstance()->checkExternalToken($token)) {
            $result = [];

            $dbBlock = BlocksTable::query()
                ->where('ID_PORTAL',1)
                ->setSelect(['ID','NAME','SUBDIVISION','SORT','ID_PARENT_BLOCK','PARENT_BLOCK','POSTS'])
                ->exec();

            while($block = $dbBlock->fetchObject()) {
                $parentBlock = $block->getParentBlock();
                $postsId = [];
                $posts = $block->getPosts();
                foreach ($posts as $post) {
                    $postsId[] = $post->getId();
                }
                $result[] = [
                    'id' => $block->getId(),
                    'title' => $block->getName(),
                    'bxDepartment' => (int)$block->getSubdivision(),
                    'parentId' => (int)$block->getIdParentBlock(),
                    'parentBxDepartment' => $parentBlock ? (int)$parentBlock->getSubdivision() : 0,
                    'posts' => $postsId,
                    'sort' => $block->getSort()
                ];
            }

            return $result;
        } else {
            \Bitrix\Main\Context::getCurrent()->getResponse()->setStatus(401);
            $this->addError(new Error('Access denied'));
        }
    }

    public function externalStructureAction($token) {
        if(TokenManager::getInstance()->checkExternalToken($token)) {
            $blockList = $this->externalListAction($token);
            return $this->formatChilds(0, $blockList);
        } else {
            \Bitrix\Main\Context::getCurrent()->getResponse()->setStatus(401);
            $this->addError(new Error('Access denied'));
        }
    }

    private function formatChilds($parentKey, $items, $currentLevel = 1)
    {
        $subItems = array_filter($items, function ($a) use ($parentKey) {
            return $a['parentId'] === $parentKey;
        });
        if(count($subItems) > 1) {
            usort($subItems, function($a, $b) {
                return (int)$a['sort'] <= (int)$b['sort'] ? -1: 1;
            });
        }
        $result = [];
        foreach($subItems as $subItem) {
            $kids = $this->formatChilds($subItem['id'], $items, $currentLevel + 1);
            if(!empty($kids)) {
                $subItem['children'] = $kids;
            }

            $posts = [];
            if(!empty($subItem['posts'])) {
                $dbPosts = PostsTable::query()
                    ->whereIn('ID', $subItem['posts'])
                    ->setSelect(['ID','NAME_POST','ID_SHIEF_POST_USERB24','IS_MANAGER_POST'])
                    ->exec();
                while ($arPost = $dbPosts->fetch()) {
                    $users = [];
                    if($arPost['IS_MANAGER_POST'] === 'Y') {
                        if(!empty($arPost['ID_SHIEF_POST_USERB24'])) {
                            $users[] = (int)$arPost['ID_SHIEF_POST_USERB24'];
                        }
                    } else {
                        $dbUsersByPost = UserbypostTable::query()
                            ->setFilter(['ID_POST' => $arPost['ID'], 'ACTIVE' => 'Y'])
                            ->setSelect(['ID_POST', 'ID_STAFF'])
                            ->exec();
                        while ($arUP = $dbUsersByPost->fetch()) {
                            if((int)$arUP['ID_STAFF'] > 0) {
                                $users[] = (int)$arUP['ID_STAFF'];
                            }
                        }
                    }
                    $posts[] = [
                        'id' => (int)$arPost['ID'],
                        'title' => $arPost['NAME_POST'],
                        'users' => $users
                    ];
                }
                $subItem['posts'] = $posts;
            }

            $result[] = $subItem;
        }

        return $result;
    }
}