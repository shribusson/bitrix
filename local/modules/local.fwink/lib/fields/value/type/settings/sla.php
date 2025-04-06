<?

namespace Local\Fwink\Fields\Value\Type\Settings;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Local\Fwink\Fields\Value\Base;
use Local\Fwink\Fields\Views\ViewInterface;
use Local\Fwink\Helpers\Encoding as HelpersEncoding;
use Local\Fwink\Tables\CompanyTable;
use Local\Fwink\Tables\PriorityTable;
use Local\Fwink\Tables\TypeTable;

class Sla extends Base
{
    /**
     * @param ViewInterface $view
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function setDataToView(ViewInterface $view): void
    {
        $value = '';
        $rawValue = $this->getRaw();

        if ($rawValue) {
            switch ($this->fieldNameForValue) {
                case 'TYPE_ID':
                    $value = $this->getType($rawValue);
                    break;
                case 'PRIORITY_ID':
                    $value = $this->getPriority($rawValue);
                    break;
                case 'COMPANY_ID':
                    $value = $this->getCompany($rawValue);
                    break;
            }
        }

        $view->setValue($value);
    }

    /**
     * @param array $typeId
     *
     * @return string
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    private function getType(array $typeId): string
    {
        $selectValues = [];
        $parameters = [
            'select' => [
                'TITLE'
            ],
            'filter' => [
                'ID' => $typeId
            ],
            'order' => ['DEFAULT' => 'DESC', 'TITLE' => 'ASC'],
            'cache' => ['ttl' => 86400]
        ];

        $result = TypeTable::getList($parameters);

        while ($type = $result->fetch()) {
            $selectValues[] = HelpersEncoding::toUtf($type['TITLE']);
        }

        return implode(', ', $selectValues);
    }

    /**
     * @param array $priorityId
     *
     * @return string
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    private function getPriority(array $priorityId): string
    {
        $selectValues = [];
        $parameters = [
            'select' => [
                'TITLE'
            ],
            'filter' => [
                'ID' => $priorityId
            ],
            'order' => ['SORT' => 'ASC', 'TITLE' => 'ASC'],
            'cache' => ['ttl' => 86400]
        ];

        $result = PriorityTable::getList($parameters);

        while ($type = $result->fetch()) {
            $selectValues[] = HelpersEncoding::toUtf($type['TITLE']);
        }

        return implode(', ', $selectValues);
    }

    /**
     * @param array $companyId
     *
     * @return string
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    private function getCompany(array $companyId): string
    {
        if (in_array('', $companyId, true)) {
            $selectValues[] = HelpersEncoding::toUtf(
                Loc::getMessage('LOCAL_FIELDS_VALUE_TYPE_SETTINGS_SLA_NO_COMPANY')
            );
        } else {
            $selectValues = [];
        }
        $parameters = [
            'select' => [
                'TITLE'
            ],
            'filter' => [
                'ID' => $companyId
            ],
            'order' => ['TITLE' => 'ASC', 'ID' => 'DESC'],
            'cache' => ['ttl' => 86400]
        ];

        $result = CompanyTable::getList($parameters);

        while ($type = $result->fetch()) {
            $selectValues[] = HelpersEncoding::toUtf($type['TITLE']);
        }

        return implode(', ', $selectValues);
    }
}
