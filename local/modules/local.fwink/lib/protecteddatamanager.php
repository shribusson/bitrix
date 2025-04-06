<?

namespace Local\Fwink;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Local\Fwink\AccessControl\Exception;
use Local\Fwink\AccessControl\Operations;

/**
 * Класс выполняет операции к сущностям $entityTable с проверкой прав для текущего пользователя.
 */
abstract class ProtectedDataManager
{
    protected $entityTable;
    protected $entityName;
    protected $entityDataClass;

    public function __construct()
    {
        $this->entityTable = $this->getEntity();
        $this->entityName = $this->entityTable->getName();
        $this->entityDataClass = $this->entityTable->getDataClass();
    }

    abstract protected function getEntity();

    /**
     * @param int $id
     *
     * @return mixed
     * @throws ArgumentException
     * @throws Exception
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getByPrimary(int $id)
    {
        $resultCheckingOperation = Operations::checkAccessAll($this->entityName);

        if ($resultCheckingOperation) {
            $result = $this->entityDataClass::getByPrimary($id);
        } else {
            throw new Exception('FULL', $this->entityName);
        }

        return $result;
    }

    /**
     * @param array $data
     *
     * @return mixed
     * @throws ArgumentException
     * @throws Exception
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function add(array $data)
    {
        $resultCheckingOperation = Operations::checkAccess(Operations::OPERATION_CREATE, $this->entityName);

        if ($resultCheckingOperation) {
            $result = $this->entityDataClass::add($data);
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
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function update(int $id, array $data)
    {
        $resultCheckingOperation = Operations::checkAccess(Operations::OPERATION_UPDATE, $this->entityName);

        if ($resultCheckingOperation) {
            $result = $this->entityDataClass::update($id, $data);
        } else {
            throw new Exception(Operations::OPERATION_UPDATE, $this->entityName);
        }

        return $result;
    }

    /**
     * @param int $id
     *
     * @return mixed
     * @throws ArgumentException
     * @throws Exception
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function delete(int $id)
    {
        $resultCheckingOperation = Operations::checkAccess(Operations::OPERATION_DELETE, $this->entityName);

        if ($resultCheckingOperation) {
            $result = $this->entityDataClass::delete($id);
        } else {
            throw new Exception(Operations::OPERATION_DELETE, $this->entityName);
        }

        return $result;
    }

    /**
     * @param int $id
     *
     * @return mixed
     * @throws ArgumentException
     * @throws Exception
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getById(int $id)
    {
        $resultCheckingOperation = Operations::checkAccess(Operations::OPERATION_READ, $this->entityName);

        if ($resultCheckingOperation) {
            $parameters = [
                'filter' => [
                    'ID' => $id
                ]
            ];
            $result = $this->entityDataClass::getList($parameters);
        } else {
            throw new Exception(Operations::OPERATION_READ, $this->entityName);
        }

        return $result;
    }

    /**
     * @param int $id
     *
     * @return mixed
     * @throws ArgumentException
     * @throws Exception
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getRowById(int $id)
    {
        $resultCheckingOperation = Operations::checkAccess(Operations::OPERATION_READ, $this->entityName);

        if ($resultCheckingOperation) {
            $result = $this->entityDataClass::getRowById($id);
        } else {
            throw new Exception(Operations::OPERATION_READ, $this->entityName);
        }

        return $result;
    }

    /**
     * @param array $parameters
     *
     * @return mixed
     * @throws ArgumentException
     * @throws Exception
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getList(array $parameters = [])
    {
        $resultCheckingOperation = Operations::checkAccess(Operations::OPERATION_READ, $this->entityName);

        if ($resultCheckingOperation) {
            $result = $this->entityDataClass::getList($parameters);
        } else {
            throw new Exception(Operations::OPERATION_READ, $this->entityName);
        }

        return $result;
    }

    /**
     * @param array $filter
     *
     * @return mixed
     */
    public function getCount(array $filter = array())
    {
        return $this->entityDataClass::getCount($filter);
    }

    /**
     * @return mixed
     */
    public function getEntityName()
    {
        return $this->entityName;
    }
}
