<?

namespace Local\Fwink;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Authentication\ApplicationPasswordTable;
use Bitrix\Main\DB\Result;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserTable;
use CUser;
use Local\Fwink\AccessControl\Exception;
use Local\Fwink\AccessControl\Operations;
use Local\Fwink\Helpers\User as HelpersUser;
use Local\Fwink\Tables\BlocksTable;
use Local\Fwink\Tables\PostsTable;

class Blocks extends ProtectedDataManager
{
    /**
     * @param int $id
     *
     * @return mixed
     * @throws ArgumentException
     * @throws Exception
     * @throws SystemException
     * @throws ObjectPropertyException
     */
    public function delete(int $id)
    {
        $resultCheckingOperation = Operations::checkAccess(Operations::OPERATION_DELETE, $this->entityName);
        if ($resultCheckingOperation) {
            $result = BlocksTable::delete($id);
            if($result->isSuccess()) {
				//BlockPostRelationTable::delete(['BLOCKS_ID' => $id]);
                /*$dbPostRelation = BlockPostRelationTable::query()->where('BLOCKS_ID', $id)->setSelect(['POSTS_ID'])->exec();
                while($relation = $dbPostRelation->fetch()) {

                }*/
            }
        } else {
            throw new Exception(Operations::OPERATION_DELETE, $this->entityName);
        }

        return $result;
    }

    /**
     * @param array $data
     *
     * @return mixed
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws Exception
     * @throws SystemException
     * @throws ObjectPropertyException
     */
    public function add(array $data)
    {
        $resultCheckingOperation = Operations::checkAccess(Operations::OPERATION_CREATE, $this->entityName); // todo
        if ($resultCheckingOperation) {
            $data['ID_PORTAL'] = $GLOBALS['FWINK']['ID_PORTAL'];
            if((int)$data['ID_PARENT_BLOCK'] === 0) {
                unset($data['ID_PARENT_BLOCK']);
            }
            $posts = $data['POSTS'];
            unset($data['POSTS']);
            /*if($data['IS_HIDE'] === 'Y') {
                $data['IS_HIDE'] = true;
            } else {
                $data['IS_HIDE'] = false;
            }*/
            $result = BlocksTable::add($data);
            if(!empty($posts) && $result->isSuccess()) {
				$object = BlocksTable::getByPrimary($result->getId())->fetchObject();
                foreach($posts as $postId) {
                    if((int)$postId > 0) {
                        $post = PostsTable::getByPrimary($postId)->fetchObject();
                        $object->addToPosts($post);
                    }
                }
                $object->save();
            }
        } else {
            throw new Exception(Operations::OPERATION_CREATE, $this->entityName);
        }

        return $result;
    }

    /**
     * @param int $id
     * @param array $data
     *
     * @return mixed
     * @throws ArgumentException
     * @throws Exception
     * @throws SystemException
     * @throws ObjectPropertyException
     */
    public function update(int $id, array $data)
    {
        $resultCheckingOperation = Operations::checkAccess(Operations::OPERATION_UPDATE, $this->entityName);
        if ($resultCheckingOperation) {
            if((int)$data['ID_PARENT_BLOCK'] === 0) {
                unset($data['ID_PARENT_BLOCK']);
            }
            $posts = $data['POSTS'];
            unset($data['POSTS']);
            /*if($data['IS_HIDE'] === 'Y') {
                $data['IS_HIDE'] = true;
            } else {
                $data['IS_HIDE'] = false;
            }*/
			$result=BlocksTable::update(
				(int)$id,
				$data
			);
            if(!empty($posts) && $result->isSuccess()) {
                $object = $result->getObject();
                $object->removeAllPosts();
                foreach($posts as $postId) {
                    if((int)$postId > 0) {
                        $post = PostsTable::getByPrimary($postId)->fetchObject();
                        $object->addToPosts($post);
                    }
                }
                $object->save();
            }
           // $result['OB_USER'] = new CUser();
           // $result['RESULT'] = $result['OB_USER']->Update($id, $data);
        } else {
            throw new Exception(Operations::OPERATION_UPDATE, $this->entityName);
        }
        return $result;
    }

    /**
     * @param array $parameters
     *
     * @return Result|mixed
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws Exception
     * @throws SystemException
     * @throws ObjectPropertyException
     */
    public function getList(array $parameters = [])
    {
        $resultCheckingOperation = Operations::checkAccess(Operations::OPERATION_READ, $this->entityName);
        if ($resultCheckingOperation) {
            $result = BlocksTable::getList($parameters);
        } else {
            throw new Exception(Operations::OPERATION_READ, $this->entityName);
        }

        return $result;
    }

    /**
     * @param $group
     *
     * @return bool
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    private function checkFilterGroup($group): bool
    {
        $access = false;
        $groupStaffId = HelpersUser::getGroupsOfStaff();
        if (is_array($group)) {
            foreach ($group as $id) {
                if (in_array($id, $groupStaffId, true)) {
                    $access = true;
                } else {
                    return false;
                }
            }
        } elseif (in_array($group, $groupStaffId, true)) {
            return true;
        }

        return $access;
    }

    /**
     * @param array $filter
     *
     * @return int|mixed
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws SystemException
     * @throws ObjectPropertyException
     */
    public function getCount(array $filter = array())
    {
        return BlocksTable::getCount($filter);
    }

    /**
     * @return Base
     * @throws ArgumentException
     * @throws SystemException
     */
    protected function getEntity(): Base
    {
        return BlocksTable::getEntity();
    }
}
