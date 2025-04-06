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
//use Local\Fwink\Helpers\User as HelpersUser;
use Local\Fwink\Helpers\Staffremote as HelpersUser;

use Local\Fwink\Tables\StaffremoteTable;
use Local\Fwink\Rest;

class Staffremote extends ProtectedDataManager
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
            $result['RESULT'] = CUser::Delete($id);
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
        $accessGroup = false;
        $resultCheckingOperation = Operations::checkAccess(Operations::OPERATION_CREATE, $this->entityName);
        if ($resultCheckingOperation) {
            //todo!!!! 04.2021 HelpersUser is NOT b_user but staff remote
			if(empty($data['GROUP_ID'])){
				$data['GROUP_ID']=false;
			}
        	if (is_string($data['GROUP_ID'])) {
                $role = HelpersUser::getRole((new CUser)->GetID());
                switch ($role) {
                    case 'Admin':
                        $accessGroup = in_array($data['GROUP_ID'], HelpersUser::getGroupsOfStaff(), true);
                        break;
                    case 'TeamLead':
                        $accessGroup = in_array($data['GROUP_ID'], HelpersUser::getGroupsOfPersonal(), true);
                        break;
                    case 'Personal':
                        $accessGroup = false;
                        break;
                }
            }
			$accessGroup=true;
        	/*
        	 * very custom !!!!
        	 * */
            if ($accessGroup) {
                $data['GROUP_ID'] = [
                    $data['GROUP_ID']
                ];
              /*
              $data['ACTIVE'] = 'N';
                $data['LOGIN'] = $data['EMAIL'] ?? ApplicationPasswordTable::generatePassword();
                $data['PASSWORD'] = $data['CONFIRM_PASSWORD'] = ApplicationPasswordTable::generatePassword();

                $result['OB_USER'] = new CUser();
                $result['ID'] = $result['OB_USER']->Add($data);
              */

				$crm_user_id = 0;
				$arExec = [];
				$arExec = array_merge($arExec, $data);
				if (is_set($arExec['NAME'])) {
					$result = Rest::execute(
						'user.add',
						$arExec
					);
					if ($result[0]['ID']) {
//						$crm_user_id = $res[0]['ID'];
					}
				}
				$result['ID'] = $crm_user_id;

            } else {
                throw new Exception(Operations::OPERATION_CREATE, $this->entityName);
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

		$result['RESULT']='Ok';
        if ($resultCheckingOperation) {
			$arExec=['ID'=>$id];
			$arExec=array_merge($arExec,$data);
			$result = Rest::execute(
				'user.update',
				$arExec
			);
//            $result['OB_USER'] = new CUser();
//            $result['RESULT'] = $result['OB_USER']->Update($id, $data);
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
        	/*
        	 * way custom??? $groupStaffId
        	 * */
//            $groupStaffId = HelpersUser::getGroupsOfStaff();

            if ($parameters['filter']['Bitrix\Main\UserGroupTable:USER.GROUP_ID'] &&
                $this->checkFilterGroup($parameters['filter']['Bitrix\Main\UserGroupTable:USER.GROUP_ID'])) {
//                $result = UserTable::getList($parameters);
				$result = StaffremoteTable::getList($parameters);
            } else {
//                $filter = ['Bitrix\Main\UserGroupTable:USER.GROUP_ID' => $groupStaffId];
				$filter=[];
                if (isset($parameters['filter'])) {
                    $parameters['filter'] = array_merge($parameters['filter'], $filter);
                } else {
                    $parameters['filter'] = $filter;
                }
                /*$result = UserTable::getList($parameters);
                moment of histOry)
                 */
				$parameters['filter']=['ID'=>959];
				//$result = StaffTable::getList($parameters);
				//$result= \Local\Fwink\Integration::getlistUsers($parameters);
				$var=1;

            }
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
        if ($filter['Bitrix\Main\UserGroupTable:USER.GROUP_ID'] &&
            $this->checkFilterGroup($filter['Bitrix\Main\UserGroupTable:USER.GROUP_ID'])) {
            return UserTable::getCount($filter);
        }
        $filter = array_merge(
            $filter,
            ['Bitrix\Main\UserGroupTable:USER.GROUP_ID' => HelpersUser::getGroupsOfStaff()]
        );
        return UserTable::getCount($filter);
    }

    /**
     * @return Base
     * @throws ArgumentException
     * @throws SystemException
     */
    protected function getEntity(): Base
    {
        return StaffremoteTable::getEntity();
    }
}
