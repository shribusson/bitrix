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
use Local\Fwink\Tables\PostsTable;
use Local\Fwink\Tables\UserbypostTable;

class Posts extends ProtectedDataManager
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
			$result = PostsTable::delete($id);
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
		$resultCheckingOperation = Operations::checkAccess(Operations::OPERATION_CREATE, $this->entityName);
		if ($resultCheckingOperation) {
            if(!empty($data['NAME_POST'])) {
                $data['NAME_POST'] = trim($data['NAME_POST']);
            }
			$result = PostsTable::add($data);
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
		$resultexec['RESULT'] = 'sucess';
		if ($resultCheckingOperation) {
            if(!empty($data['NAME_POST'])) {
                $data['NAME_POST'] = trim($data['NAME_POST']);
            }
			$result = PostsTable::update(
				(int)$id,
				$data
			);
			$resultexec['RESULT'] = $result->isSuccess();
			if($e = $GLOBALS["APPLICATION"]->GetException()){
				$resultexec['RESULT']=[$e->GetString()];
			}
//            $result['OB_USER'] = new CUser();
//            $result['RESULT'] = $result['OB_USER']->Update($id, $data);
		} else {
			throw new Exception(Operations::OPERATION_UPDATE, $this->entityName);
		}

		return $resultexec;
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
            $parameters['filter'] = $this->prepareFilter($parameters['filter']);
            $result = PostsTable::getList($parameters);

		} else {
			throw new Exception(Operations::OPERATION_READ, $this->entityName);
		}

		return $result;
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
        $filter = $this->prepareFilter($filter);
		return PostsTable::getCount($filter);
	}

	/**
	 * @return Base
	 * @throws ArgumentException
	 * @throws SystemException
	 */
	protected function getEntity(): Base
	{
		return PostsTable::getEntity();
	}

    protected function prepareFilter($paramsFilter = [])
    {
        $filter = [
            'ID_PORTAL' => (int)$GLOBALS['FWINK']['ID_PORTAL'] // todo: replace globals
        ];
        if (isset($paramsFilter)) {
            // лютая логика, чтоб найти пост по сотруднику
            if(!empty($paramsFilter['STAFF'])) {
                $staffData = $paramsFilter['STAFF'];
                unset($paramsFilter['STAFF']);

                $additionalFilter = $this->getStaffFilter($staffData);
                if(!empty($additionalFilter['BY_NAME'])) {
                    $byNameFilter = $additionalFilter['BY_NAME'];
                    unset($additionalFilter['BY_NAME']);
                    if (!empty($paramsFilter)) {
                        foreach ($paramsFilter as $key => $value) {
                            if (is_numeric($key)) {
                                if ($value['LOGIC'] === 'OR') {
                                    $paramsFilter[$key][] = $byNameFilter;
                                    break;
                                }
                            }
                        }
                    } else {
                        $paramsFilter[] = $byNameFilter;
                    }
                }
                $paramsFilter[] = $additionalFilter;
            }
            $paramsFilter = array_merge($paramsFilter, $filter);
        } else {
            $paramsFilter = $filter;
        }
        return $paramsFilter;
    }

    /**
     * Подготавливает массив фильтра по сотрудникам. Т.к. в контексте реста мы не храним со своей стороны данные пользователей
     * при поиске по имени сначлаа ищем польщователей по имени ч помощью рест апи, потом находим посты, к которым этот пользователь привязан
     * и вставляем в фильтр по пстам уже массив ид
     *
     * @param $data
     * @return array
     */
    protected function getStaffFilter($data = [])
    {
        $filter = [];

        if(!empty($data['ID'])) {
            $subFilter = [
                'LOGIC' => 'OR',
                'ID_SHIEF_POST_USERB24' => $data['ID']
            ];
            $dbPosts = UserbypostTable::query()
                ->where('ID_PORTAL',$GLOBALS['FWINK']['ID_PORTAL']) // todo: replace
                ->whereIn('ID_STAFF',$data['ID'])
                ->where('ACTIVE','Y')
                ->setSelect(['ID_POST'])
                ->exec();
            $postIDs = [];
            while($arPosts = $dbPosts->fetch()) {
                $postIDs[] = $arPosts['ID_POST'];
            }
            if(!empty($postIDs)) {
                $subFilter['ID'] = $postIDs;
            }
            $filter[] = $subFilter;
        }

        // не объеденить в один запрос по UserbypostTable, т.к. поиск по имени пользваотеля должен быть параллельно с явным указанием ид пользователя
        // плюс там хитрая логика OR/AND в результирующем запросе
        if(!empty($data['NAME'])) {
            $requestRes = Rest::execute('user.search', ['FILTER' => ['FIND' => $data['NAME']]]);
            $ids = [];
            if(!empty($requestRes)) {
                foreach($requestRes as $user) {
                    $ids[] = $user['ID'];
                }
            }
            if(!empty($ids)) {
                $subFilter = [
                    'LOGIC' => 'OR',
                    'ID_SHIEF_POST_USERB24' => $ids
                ];
                $dbPosts = UserbypostTable::query()
                    ->where('ID_PORTAL',$GLOBALS['FWINK']['ID_PORTAL']) // todo: replace
                    ->whereIn('ID_STAFF',$ids)
                    ->where('ACTIVE','Y')
                    ->setSelect(['ID_POST'])
                    ->exec();
                $postIDs = [];
                while($arPosts = $dbPosts->fetch()) {
                    $postIDs[] = $arPosts['ID_POST'];
                }
                if(!empty($postIDs)) {
                    $subFilter['ID'] = $postIDs;
                }
                $filter['BY_NAME'] = $subFilter;
            }
        }

        return $filter;
    }
}
