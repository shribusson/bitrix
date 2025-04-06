<?

namespace Local\Fwink\Fields\Value\Type;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserTable;
use Local\Fwink\AccessControl\Configuration;
use Local\Fwink\Fields\Value\Base;
use Local\Fwink\Fields\Views\Show\Link;
use Local\Fwink\Fields\Views\ViewInterface;
use Local\Fwink\Helpers\Folder as HelpersFolder;
use Local\Fwink\Tables\ClientTable;
use Local\Fwink\Tables\StaffTable;
use Exception;

class EntityElement extends Base
{
    /** @var DataManager $entity */
    private $entity;
    private $element;
    private $fieldNameForEntity;
    private $fieldNameForElementId;
    private $targetBlank = false;

    /**
     * EntityElement constructor.
     *
     * @param $fieldNames
     */
    public function __construct($fieldNames)
    {
        parent::__construct($fieldNames);

        if ($fieldNames['ENTITY_ELEMENT']) {
            $this->fieldNameForEntity = $fieldNames['ENTITY_ELEMENT'];
        }

        if ($fieldNames['ELEMENT_ID']) {
            $this->fieldNameForElementId = $fieldNames['ELEMENT_ID'];
        }
    }

    /**
     * @param ViewInterface $view
     *
     */
    public function setDataToView(ViewInterface $view): void
    {
        try {
            $this->entity = $this->getEntity();
            $this->element = $this->getElement();
            if ($this->element && $view instanceof Link) {
                $value['URL'] = $this->getUrl();
                $value['TEXT'] = $this->getUrlText();
                $value['TITLE'] = $this->getUrlText();
                $value['TARGET_BLANK'] = $this->targetBlank;
            } else {
                $value['TEXT'] = '[' . $this->getElementId() . ']';
            }
        } catch (Exception $e) {
            $value['TEXT'] = '[' . $e->getMessage() . ']';
        }

        $view->setValue($value);
    }

    /**
     * @return mixed
     * @throws LoaderException
     * @throws ArgumentException
     * @throws SystemException
     */
    private function getEntity()
    {
        $entitiesDataClasses = Configuration::getInstance()->get('entitiesDataClasses');
        $entityName = $this->getEntityName();
        if ($entityName) {
            /** @var DataManager $entityDataClass */
            foreach ($entitiesDataClasses as $entityDataClass) {
                if ($entityName === $entityDataClass::getEntity()->getName()) {
                    return new $entityDataClass();
                    break;
                }
            }
        }
        throw new LoaderException($this->getElementId());
    }

    /**
     * @return mixed|string
     */
    private function getEntityName()
    {
        $entityName = $this->getFieldFromRawValue($this->fieldNameForEntity);
        return $entityName ?? '';
    }

    /**
     * @return mixed|string
     */
    private function getElementId()
    {
        $elementId = $this->getFieldFromRawValue($this->fieldNameForElementId);
        return $elementId ?? '';
    }

    /**
     * @return array
     * @throws ArgumentException
     * @throws SystemException
     * @throws ObjectPropertyException
     */
    private function getElement(): array
    {
        $element = [];
        if ($this->entity instanceof ClientTable ||
            $this->entity instanceof StaffTable
        ) {
            $parameters = [
                'select' => ['ID', 'LOGIN', 'NAME', 'LAST_NAME'],
                'filter' => ['ID' => $this->getElementId()],
                'cache' => ['ttl' => 86400]
            ];
            $result = UserTable::getList($parameters);
            if ($row = $result->fetch()) {
                $element = [
                    'ID' => $row['ID'],
                    'TITLE' => \Local\Fwink\Helpers\User::getUserName($row)
                ];
                $element = $this->convertToUtf($element);
            }
        } else {
            $parameters = [
                'select' => ['ID', 'TITLE'],
                'filter' => ['ID' => $this->getElementId()],
                'cache' => ['ttl' => 86400]
            ];
            $result = $this->entity::getList($parameters);
            if ($row = $result->fetch()) {
                $element = $this->convertToUtf($row);
            }
        }

        return $element;
    }

    /**
     * @return string
     * @throws ArgumentException
     * @throws SystemException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public function getUrl(): string
    {
        $entityName = $this->entity::getEntity()->getName();
        return HelpersFolder::getPathEntity($entityName, $this->getElementId());
    }

    /**
     * @return string
     */
    public function getUrlText(): string
    {
        return $this->element['TITLE'] ?: '';
    }
}
