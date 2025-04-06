<?

namespace Local\Fwink\Fields\Config;

use Bitrix\Main\Entity\Base;

abstract class Manager
{
    /** @var Base $entity */
    protected $entity;

    public function __construct()
    {
        $this->entity = $this->getEntity();
    }

    abstract protected function getEntity();

    public function getField($fieldName)
    {
        return $this->getFields()[$fieldName];
    }

    abstract public function getFields();
}
