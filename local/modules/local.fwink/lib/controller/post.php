<?php

namespace Local\Fwink\Controller;

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Error;
use Local\Fwink\Controller\ActionFilter\Domain;
use Local\Fwink\Service\TokenManager;
use Local\Fwink\Staff\TransferManager;
use Local\Fwink\Tables\BlocksTable;
use Local\Fwink\Tables\PortalTable;
use Local\Fwink\Tables\PostsTable;
use Local\Fwink\Tables\UserbypostTable;

class Post extends Controller
{
    public function configureActions()
    {
        return [
            'move' => [],
            'list' => [
                '+prefilters' => [
                    new Domain()
                ]
            ],
            'externalList' => [
                'prefilters' => []
            ]
        ];
    }

    public function moveAction($data)
    {
        if ((int)$data['post']['id'] > 0 && (int)$data['newBlockId'] > 0 && (int)$data['oldBlockId'] > 0 && in_array($data['typeEvent'], ['copy', 'move'])) {
            if($data['typeEvent'] === 'move') {
                $userAuth = TokenManager::getInstance()->getAuth();

                // todo: replace globals
                $isAdmin = \Local\Fwink\Rest::execute('user.admin', [], !empty($userAuth) ? ['access_token' => $userAuth] : false);
                $GLOBALS['FWINK']['admin'] = (bool)$isAdmin === true;
                if($isAdmin) { // replace to correct rights check
                    try {
                        $post = PostsTable::getByPrimary($data['post']['id'])->fetchObject();
                        if ($post) {
                            $oldBlock = BlocksTable::getByPrimary($data['oldBlockId'])->fetchObject();
                            if($oldBlock) {
                                $oldBlock->removeFromPosts($post);
                                $oldBlock->save();
                            }
                            $newBlock = BlocksTable::getByPrimary($data['newBlockId'])->fetchObject();
                            if($newBlock) {
                                $newBlock->addToPosts($post);
                                $newBlock->save();
                            }
                        }

                        $transferManager = TransferManager::getInstance();
                        $transferManager->process();
                    } catch (\Throwable $e) {
                        $this->addError(new Error('internal error'));
                    }
                } else {
                    $this->addError(new Error('Forbidden'));
                }
            }
        } else {
            $this->addError(new Error('wrong request'));
        }
    }

    public function listAction()
    {
        $result = [];
        $dbPost = PostsTable::query()
            ->where('ID_PORTAL', $GLOBALS['FWINK']['ID_PORTAL'])
            ->setSelect(['ID','NAME_POST'])
            ->setOrder(['NAME_POST' => 'ASC', 'SORT' => 'ASC'])
            ->exec();
        while($arPost = $dbPost->fetch()) {
            $result[] = [
                'id' => $arPost['ID'],
                'name' => $arPost['NAME_POST']
            ];
        }
        return $result;
    }

    public function externalListAction($token = '')
    {
        if(TokenManager::getInstance()->checkExternalToken($token)) {
            $result = [];
            $dbPost = PostsTable::query()
                ->setSelect(['ID','NAME_POST','ID_SHIEF_POST_USERB24','IS_MANAGER_POST','BLOCKS'])
                ->where('ID_PORTAL', 1)
                ->exec();
            while($post = $dbPost->fetchObject()) {
                $users = [];
                if($post->get('IS_MANAGER_POST') === 'Y') {
                    if(!empty($post->get('ID_SHIEF_POST_USERB24'))) {
                        $users[] = (int)$post->get('ID_SHIEF_POST_USERB24');
                    }
                } else {
                    $dbUsersByPost = UserbypostTable::query()
                        ->setFilter(['ID_POST' => $post->getId(), 'ACTIVE' => 'Y'])
                        ->setSelect(['ID_POST', 'ID_STAFF'])
                        ->exec();
                    while ($arUP = $dbUsersByPost->fetch()) {
                        if((int)$arUP['ID_STAFF'] > 0) {
                            $users[] = (int)$arUP['ID_STAFF'];
                        }
                    }
                }

                $departments = [];
                $blocks = $post->getBlocks();
                foreach ($blocks as $block) {
                    $departments[] = $block->getId();
                }

                $result[] = [
                    'id' => $post->getId(),
                    'title' => $post->getNamePost(),
                    'users' => $users,
                    'departments' => $departments
                ];
            }
            return $result;
        } else {
            \Bitrix\Main\Context::getCurrent()->getResponse()->setStatus(401);
            $this->addError(new Error('Access denied'));
        }
    }

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
}