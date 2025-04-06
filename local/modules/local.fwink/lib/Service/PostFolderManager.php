<?php

namespace Local\Fwink\Service;

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Local\Fwink\Rest;
use Local\Fwink\Tables\PostsTable;

class PostFolderManager
{
    protected $rootPostFolder;

    public function __construct()
    {
        Loader::includeModule('disk');
        $this->rootPostFolder = 343249;
    }

    public function createPostFolder($postId)
    {
        $post = PostsTable::getByPrimary($postId)->fetchObject();
        if($post) {
            $parentId = $this->getParentFolderId($post);
            if ($parentId === 0) {
                $parentId = $this->rootPostFolder;
            }
            $name = $post->getNamePost();
            $name = \Bitrix\Disk\Internals\Path::correctFilename($name);
            $createResult = Rest::execute('disk.folder.addsubfolder', ['id' => $parentId, 'data' => ['NAME' => $name]]);
            if (!empty($createResult['ID'])) {
                $post->setIdJobFolderb24((int)$createResult['ID']);
                $post->save();
            }
            $taggedCache = Application::getInstance()->getTaggedCache()->clearByTag('ic_chart_diskfolders_' . $GLOBALS['FWINK']['ID_PORTAL']);
        }
    }

    protected function getParentFolderId($post)
    {
        $post->fill('PARENT_POST');
        $parentPost = $post->getParentPost();
        $postFolder = 0;
        if($parentPost) {
            $postFolder = $post->getParentPost()->getIdJobFolderb24();
            if(empty($postFolder)) {
                $parentId = $this->getParentFolderId($parentPost);
                if($parentId === 0) {
                    $parentId = $this->rootPostFolder;
                }
                $name = $parentPost->getNamePost();
                $name = \Bitrix\Disk\Internals\Path::correctFilename($name);
                $createResult = Rest::execute('disk.folder.addsubfolder', ['id' => $parentId, 'data' => ['NAME' => $name]]);
                if(!empty($createResult['ID'])) {
                    $postFolder = $createResult['ID'];
                    $parentPost->setIdJobFolderb24((int)$postFolder);
                    $parentPost->save();
                }
            }
        }
        return (int)$postFolder;
    }
}