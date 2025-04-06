<?

namespace Local\Fwink\Fields\Value\Type\Settings;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Local\Fwink\Fields\Value\Base;
use Local\Fwink\Fields\Views\ViewInterface;
use Local\Fwink\Helpers\Encoding as HelpersEncoding;
use Local\Fwink\Tables\SlaWorkTimeTable;

class Worktime extends Base
{
    private $fieldNameForWeekDay;

    /**
     * Worktime constructor.
     *
     * @param $fieldNames
     */
    public function __construct($fieldNames)
    {
        parent::__construct($fieldNames);

        if ($fieldNames['WEEK_DAY']) {
            $this->fieldNameForWeekDay = $fieldNames['WEEK_DAY'];
        }
    }

    /**
     * @param ViewInterface $view
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function setDataToView(ViewInterface $view): void
    {
        $rawValue = $this->getRaw();
        $value = $this->getWorkTime($rawValue);
        $view->setValue($value);
    }

    /**
     * @param int $scheduleId
     *
     * @return string
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    private function getWorkTime(int $scheduleId): string
    {
        $selectValues = '';
        $parameters = [
            'select' => [
                'ACTIVE',
                'START',
                'FINISH',
            ],
            'filter' => [
                'SLA_SCHEDULE_ID' => $scheduleId,
                'WEEK_DAY_ID' => $this->getWeekDay()
            ],
            'order' => ['WEEK_DAY_ID' => 'ASC'],
            'cache' => ['ttl' => 86400]
        ];

        $result = SlaWorkTimeTable::getList($parameters);
        while ($row = $result->fetch()) {
            $name = $this->getName($row);
            $selectValues = HelpersEncoding::toUtf($name);
        }

        return $selectValues;
    }

    /**
     * @return int
     */
    public function getWeekDay(): int
    {
        return $this->fieldNameForWeekDay ?: 0;
    }

    /**
     * @param $row
     *
     * @return string
     */
    private function getName($row): string
    {
        if (!empty($row['ACTIVE'])) {
            return $this->getTime($row['START']) . ' - <br>' . $this->getTime($row['FINISH']);
        }

        return '[---]';
    }

    /**
     * @param $value
     *
     * @return string
     */
    private function getTime($value): string
    {
        $hours = str_pad(floor($value / 3600), 2, '0', STR_PAD_LEFT);
        $minutes = str_pad(($value % 3600) / 60, 2, '0', STR_PAD_LEFT);

        return $hours . ':' . $minutes;
    }
}
