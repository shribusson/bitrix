<?

namespace Local\Fwink\Fields\Value\Type\Settings;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Context\Culture;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Local\Fwink\Fields\Value\Base;
use Local\Fwink\Fields\Views\ViewInterface;
use Local\Fwink\Helpers\Encoding as HelpersEncoding;
use Local\Fwink\SlaException;
use Local\Fwink\Tables\SlaWorkTimeTable;

class Except extends Base
{
    /**
     * @param ViewInterface $view
     *
     */
    public function setDataToView(ViewInterface $view): void
    {
        $rawValue = $this->getRaw();
        $value = $this->getException($rawValue);
        $view->setValue($value);
    }

    private function getException(int $scheduleId): string
    {
        $selectValues = [];
        $parameters = [
            'select' => [
                'DATE'
            ],
            'filter' => [
                'SLA_SCHEDULE_ID' => $scheduleId
            ],
            'order' => ['DATE' => 'ASC'],
            'cache' => ['ttl' => 86400]
        ];

        $result = (new SlaException())->getList($parameters);
        while ($row = $result->fetch()) {
            $date = FormatDateFromDB($row['DATE']->toString(), 'j F Y');
            $date = HelpersEncoding::toUtf($date);
            $selectValues[] = mb_strtolower($date);
        }

        return implode(', ', $selectValues);
    }
}
