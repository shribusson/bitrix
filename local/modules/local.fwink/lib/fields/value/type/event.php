<?

namespace Local\Fwink\Fields\Value\Type;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserTable;
use Local\Fwink\AccessControl\Configuration;
use Local\Fwink\AccessControl\Operations;
use Local\Fwink\Fields\Value\Base;
use Local\Fwink\Fields\Views\ViewInterface;
use Local\Fwink\Helpers\Encoding as HelpersEncoding;
use Exception;

class Event extends Base
{
    /** @var DataManager $entity */
    private $entity;
    private $operation;
    private $field;

    private $fieldNameForOperation;

    public function __construct($fieldNames)
    {
        parent::__construct($fieldNames);

        if ($fieldNames['OPERATION']) {
            $this->fieldNameForOperation = $fieldNames['OPERATION'];
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
            $this->operation = $this->getOperation();
            $this->field = $this->getFieldValue();
            $value = $this->getValue();
        } catch (Exception $e) {
            $value = $e->getMessage();
        }

        $view->setValue($value);
    }

    /**
     * @return UserTable
     * @throws ArgumentException
     * @throws LoaderException
     * @throws SystemException
     */
    private function getEntity()
    {
        $entitiesDataClasses = Configuration::getInstance()->get('entitiesDataClasses');
        $entityName = $this->getEntityName();
        if ($entityName === 'Client' || $entityName === 'Staff') {
            return new UserTable();
        }

        if ($entityName) {
            /** @var DataManager $entityDataClass */
            foreach ($entitiesDataClasses as $entityDataClass) {
                if ($entityName === $entityDataClass::getEntity()->getName()) {
                    return new $entityDataClass();
                    break;
                }
            }
        }
        throw new LoaderException('[---]');
    }

    /**
     * @return string
     */
    private function getEntityName(): string
    {
        $value = $this->getRaw()['FIELD']['ENTITY'];

        return $value ?? '';
    }

    /**
     * @return string
     */
    private function getOperation(): string
    {
        $value = $this->getFieldFromRawValue($this->fieldNameForOperation);

        return $value ?? '';
    }

    /**
     * @return string
     */
    private function getFieldValue(): string
    {
        $value = $this->getRaw()['FIELD']['CODE'];

        return $value ?? '';
    }

    /**
     * Для каждой сущности свой текст.
     *
     * @return string
     * @throws ArgumentException
     * @throws SystemException
     */
    private function getValue(): string
    {
        $entityName = $this->getEntityName();
        $fieldTitle = '';
        switch ($entityName) {
            case 'Comment':
                $message = $this->getMessageComment()[$this->operation];
                break;
            case 'TicketObservers':
                $message = $this->getMessageObservers()[$this->operation];
                break;
            case 'WorkTime':
                if ($this->field) {
                    $fieldTitle = $this->entity::getEntity()->getField($this->field)->getTitle();
                }
                $message = $this->getMessageWorkTime($fieldTitle)[$this->operation];
                break;
            case 'Staff':
            case 'Client':
                if ($this->field) {
                    switch ($this->field) {
                        case 'WORK_NOTES':
                            $fieldTitle = Loc::getMessage('LOCAL_FIELDS_VALUE_TYPE_EVENT_USER_WORK_NOTES');
                            break;
                        case 'UF_HELPDESK_MANAGER':
                            $fieldTitle = Loc::getMessage('LOCAL_FIELDS_VALUE_TYPE_EVENT_USER_MANAGER');
                            break;
                        case 'UF_HELPDESK_COMPANY':
                            $fieldTitle = Loc::getMessage('LOCAL_FIELDS_VALUE_TYPE_EVENT_USER_COMPANY');
                            break;
                        case 'GROUP_ID':
                            $fieldTitle = Loc::getMessage('LOCAL_FIELDS_VALUE_TYPE_EVENT_USER_GROUP');
                            break;
                        default:
                            $fieldTitle = $this->entity::getEntity()->getField($this->field)->getTitle();
                            break;
                    }
                }
                $message = $this->getMessage($fieldTitle)[$this->operation];
                break;
            default:
                if ($this->field) {
                    $fieldTitle = $this->entity::getEntity()->getField($this->field)->getTitle();
                }
                $message = $this->getMessage($fieldTitle)[$this->operation];
                break;
        }

        return HelpersEncoding::toUtf($message);
    }

    /**
     * @return array
     */
    private function getMessageComment(): array
    {
        return [
            Operations::OPERATION_CREATE => Loc::getMessage(
                'LOCAL_FIELDS_VALUE_TYPE_EVENT_COMMENT_OPERATION_CREATE'
            ),
            Operations::OPERATION_UPDATE => Loc::getMessage(
                'LOCAL_FIELDS_VALUE_TYPE_EVENT_COMMENT_OPERATION_UPDATE'
            ),
            Operations::OPERATION_DELETE => Loc::getMessage(
                'LOCAL_FIELDS_VALUE_TYPE_EVENT_COMMENT_OPERATION_DELETE'
            )
        ];
    }

    /**
     * @return array
     */
    private function getMessageObservers(): array
    {
        return [
            Operations::OPERATION_CREATE => Loc::getMessage(
                'LOCAL_FIELDS_VALUE_TYPE_EVENT_OBSERVERS_OPERATION_CREATE'
            ),
            Operations::OPERATION_UPDATE => Loc::getMessage(
                'LOCAL_FIELDS_VALUE_TYPE_EVENT_OBSERVERS_OPERATION_UPDATE'
            ),
            Operations::OPERATION_DELETE => Loc::getMessage(
                'LOCAL_FIELDS_VALUE_TYPE_EVENT_OBSERVERS_OPERATION_DELETE'
            )
        ];
    }

    /**
     * @param string $fieldTitle
     *
     * @return array
     */
    private function getMessageWorkTime(string $fieldTitle = ''): array
    {
        return [
            Operations::OPERATION_CREATE => Loc::getMessage(
                'LOCAL_FIELDS_VALUE_TYPE_EVENT_WORK_TIME_OPERATION_CREATE'
            ),
            Operations::OPERATION_UPDATE => Loc::getMessage(
                'LOCAL_FIELDS_VALUE_TYPE_EVENT_WORK_TIME_OPERATION_UPDATE',
                ['#FIELD#' => $fieldTitle]
            ),
            Operations::OPERATION_DELETE => Loc::getMessage(
                'LOCAL_FIELDS_VALUE_TYPE_EVENT_WORK_TIME_OPERATION_DELETE'
            )
        ];
    }

    /**
     * @param string $fieldTitle
     *
     * @return array
     */
    private function getMessage(string $fieldTitle = ''): array
    {
        return [
            Operations::OPERATION_CREATE => Loc::getMessage(
                'LOCAL_FIELDS_VALUE_TYPE_EVENT_OPERATION_CREATE'
            ),
            Operations::OPERATION_UPDATE => Loc::getMessage(
                'LOCAL_FIELDS_VALUE_TYPE_EVENT_OPERATION_UPDATE',
                ['#FIELD#' => $fieldTitle]
            ),
            Operations::OPERATION_DELETE => Loc::getMessage(
                'LOCAL_FIELDS_VALUE_TYPE_EVENT_OPERATION_DELETE'
            )
        ];
    }
}
