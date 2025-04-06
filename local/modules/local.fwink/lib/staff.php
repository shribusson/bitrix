<?

namespace Local\Fwink;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Authentication\ApplicationPasswordTable;
use Bitrix\Main\DB\Result;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\Error;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\Date;
use Bitrix\Main\UserTable;
use CUser;
use Local\Fwink\AccessControl\Exception;
use Local\Fwink\AccessControl\Operations;
use Local\Fwink\Helpers\User as HelpersUser;
use Local\Fwink\Tables\BlockPostRelationTable;
use Local\Fwink\Tables\BlocksTable;
use Local\Fwink\Tables\PostsTable;
use Local\Fwink\Tables\StaffremoteTable;
use Local\Fwink\Tables\StaffTable;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\ORM\Query\Filter\Condition;
use Local\Fwink\Tables\UserbypostTable;

class Staff extends ProtectedDataManager
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
			//$result['RESULT'] = CUser::Delete($id);
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
		// todo : rights
		$result = new \Bitrix\Main\Result();
		if ($resultCheckingOperation) {
			//todo

            $restData = [
                'NAME' => $data['NAME'],
                'LAST_NAME' => $data['LAST_NAME'],
                'EMAIL' => $data['EMAIL'],
            ];

			if(!empty($data['POST'])) {
                $dbBlocks = BlockPostRelationTable::query()
                    ->registerRuntimeField(new \Bitrix\Main\ORM\Fields\Relations\Reference(
                        'BLOCK',
                        '\Local\Fwink\Tables\BlocksTable',
                        ['this.BLOCKS_ID' => 'ref.ID']
                    ))
                    ->where('POSTS_ID',$data['POST'])
                    ->setSelect(['BLOCKS_ID','DEPARTMENT' => 'BLOCK.SUBDIVISION'])
                    ->exec();
			    while($arBlock = $dbBlocks->fetch()) {
                    $restData['UF_DEPARTMENT'][] = $arBlock['DEPARTMENT'];
                }

                $dbPost = PostsTable::query()->where('ID', $data['POST'])->setSelect(['ID','NAME_POST','IS_MANAGER_POST'])->exec();
                if($arPost = $dbPost->fetch()) {
                    $restData['WORK_POSITION'] = $arPost['NAME_POST'];
                }
            } else {
			    // todo: add to default department
            }

			try {

                $restResult = Rest::execute(
                    'user.add',
                    $restData,
                    ['access_token' => $GLOBALS['FWINK']['requestURL']['AUTH_ID']]
                );
                if ((int)$restResult > 0) {
                    $result->setData(['id' => $restResult]);
                    $addStaffResult = StaffTable::add([
                        'ID_PORTAL' => $GLOBALS['FWINK']['ID_PORTAL'],
                        'ID_STAFF' => $restResult,
                        'DATE_TRAIN_END' => !empty($data['DATE_TRAIN_END']) ? new Date($data['DATE_TRAIN_END']) : new Date()
                    ]);
                    if(!$addStaffResult->isSuccess()) {
                        \LocalFwink::Log('Error add staff: '.print_r($addStaffResult->getErrorMessages(), true));
                        $result->addError(new Error('Вннутренняя ошибка регистрации пользователя, код STA-002'));
                    }
                    if($arPost) {
                        if($arPost['IS_MANAGER_POST'] === 'Y') {
                            $updPostResult = PostsTable::update($arPost['ID'], ['ID_SHIEF_POST_USERB24' => $restResult]);
                            if (!$updPostResult->isSuccess()) {
                                \LocalFwink::Log('Error add staff to post: ' . print_r($updPostResult->getErrorMessages(), true));
                                $result->addError(new Error('Внутренняя ошибка регистрации пользователя, код STA-003'));
                            }
                        } else {
                            $addToPostResult = UserbypostTable::add([
                                'ID_PORTAL' => $GLOBALS['FWINK']['ID_PORTAL'],
                                'ID_STAFF' => $restResult,
                                'ID_POST' => $data['POST']
                            ]);
                            if (!$addToPostResult->isSuccess()) {
                                \LocalFwink::Log('Error add staff to post: ' . print_r($addStaffResult->getErrorMessages(), true));
                                $result->addError(new Error('Внутренняя ошибка регистрации пользователя, код STA-004'));
                            }
                        }
                    }
                } else {
                    $result->addError(new Error('Ошибка приглашения пользователя на портал, код STA-001'));
                }
            } catch(\Exception $e) {
                \LocalFwink::Log('Error add user to portal: '.print_r($e, true));
                if((int)$e->getCode() === 400) {
                    $result->addError(new Error('Ошибка приглашения пользователя на портал, '.$e->getMessage()));
                } else {
                    $result->addError(new Error('Ошибка приглашения пользователя на портал, код STA-000'));
                }
            }
		} else {
			$result->addError(new Error(Operations::OPERATION_CREATE, $this->entityName));
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
			/*$result['OB_USER'] = new CUser();
			$result['RESULT'] = $result['OB_USER']->Update($id, $data);*/
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
		    $filter = ['USER_TYPE' => 'employee'];
		    if(!empty($parameters['filter'])) {
                if(!empty($parameters['filter']['POST'])) {
                    $postFilter = $parameters['filter']['POST'];
                    unset($parameters['filter']['POST']);

                    $dbPostQuery = PostsTable::query()
                        ->where('ID_PORTAL',$GLOBALS['FWINK']['ID_PORTAL']) // todo: replace
                        ->setSelect(['ID','ID_SHIEF_POST_USERB24']);
                    if(!empty($postFilter['ID'])) {
                        $dbPostQuery->whereIn('ID',$postFilter['ID']);
                    }
                    if(!empty($postFilter['NAME'])) {
                        $dbPostQuery->whereLike('NAME_POST',$postFilter['NAME']);
                    }
                    $arPostUsers = [];
                    $postIDs = [];
                    $dbPost = $dbPostQuery->exec();
                    while($arPost = $dbPost->fetch()) {
                        $postIDs[] = $arPost['ID'];
                        if(!empty($arPost['ID_SHIEF_POST_USERB24'])) {
                            $arPostUsers[] = (int)$arPost['ID_SHIEF_POST_USERB24'];
                        }
                    }

                    if(!empty($postIDs)) {
                        $dbPostQuery = UserbypostTable::query()
                            ->whereIn('ID_POST', $postIDs)
                            ->setSelect(['ID_STAFF']);
                        if(empty($parameters['filter']['ACTIVE']) || $parameters['filter'] === 'Y') {
                            $dbPostQuery->where('ACTIVE','Y');
                        }
                        $dbPostUsers = $dbPostQuery->exec();
                        while($arPostUser = $dbPostUsers->fetch()) {
                            $arPostUsers[] = (int)$arPostUser['ID_STAFF'];
                        }
                    }
                    $arPostUsers = array_unique($arPostUsers);
                    if(!empty($arPostUsers)) {
                        $filter['ID'] = $arPostUsers;
                    }
                }
		        $filter = $filter + $parameters['filter'];
            }
		    if(empty($filter['ACTIVE'])) {
		        $filter['ACTIVE'] = true;
            } else {
                if($filter['ACTIVE'] === 'ALL') {
                    unset($filter['ACTIVE']);
                } else {
                    $filter['ACTIVE'] = $filter['ACTIVE'] === 'Y';
                }
            }
		    $sortField = array_keys($parameters['order'])[0];
		    if($sortField == 'ID_STAFF') {
		        $sortField = 'ID';
            }
		    $sortOrder = array_values($parameters['order'])[0];
            $requestRes = Rest::execute('user.get', [
                'sort' => $sortField,
                'order' => $sortOrder,
                'FILTER' => $filter,
                'start' => !empty($parameters['offset']) ? $parameters['offset'] : 0
            ], false, true, false);
		    $result = [];
		    if(!empty($requestRes['result'])) {
		        $ids = [];
		        foreach($requestRes['result'] as $user) {
		            $ids[] = $user['ID'];
                }
		        if(!empty($ids)) {
                    $userPosts = [];
                    $dbHeadPosts = \Local\Fwink\Tables\PostsTable::query()
                        ->setFilter(['ID_PORTAL' => (int)$GLOBALS['FWINK']['ID_PORTAL'], 'ID_SHIEF_POST_USERB24' => $ids])
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
                        $userPosts[$headPost['ID_SHIEF_POST_USERB24']][] = $headPost;
                    }

                    $dbPost = \Local\Fwink\Tables\UserbypostTable::query()
                        ->setFilter(['ID_PORTAL' => (int)$GLOBALS['FWINK']['ID_PORTAL'], 'ID_STAFF' => $ids, 'ACTIVE' => 'Y']) // todo: replace global
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
                        $userPosts[$userPost['ID_STAFF']][] = $userPost;
                    }

                    $dbUserAdditional = StaffTable::query()
                        ->setFilter(['ID_PORTAL' => (int)$GLOBALS['FWINK']['ID_PORTAL'], 'ID_STAFF' => $ids])
                        ->setSelect(['ID_STAFF','DATE_TRAIN_END'])
                        ->exec();
                    $userAdditionals = [];
                    while($userAdditional = $dbUserAdditional->fetch()) {
                        $userAdditionals[$userAdditional['ID_STAFF']] = $userAdditional;
                    }
                    foreach($requestRes['result'] as $key=>$user) {
                        if(empty($user['PERSONAL_PHOTO'])) {
                            $requestRes['result'][$key]['PERSONAL_PHOTO'] = '/local/apps/img/ui-user.svg';
                        }
                        $requestRes['result'][$key]['POSTS'] = $userPosts[$user['ID']];
                        $requestRes['result'][$key]['ADDITIONALS'] = $userAdditionals[$user['ID']];
                    }
                    $result = [
                        'items' => $requestRes['result'],
                        'total' => $requestRes['total']
                    ];
                }
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
