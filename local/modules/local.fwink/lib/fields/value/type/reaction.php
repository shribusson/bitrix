<?

namespace Local\Fwink\Fields\Value\Type;

use Bitrix\Main\Context\Culture;
use Bitrix\Main\ObjectException;
use Bitrix\Main\Type\DateTime;
use Local\Fwink\Fields\Value\Base;
use Local\Fwink\Helpers\Encoding as HelpersEncoding;

class Reaction extends Base
{
    private $dateType = 'short';
    private $dateFull = 'DD.MM.YYYY HH:MI:SS';
    private $dateShort = 'j F Y, HH:MI';

    /**
     * @return array|mixed
     * @throws ObjectException
     */
    public function get()
    {
        $rawValue = $this->getRaw();
        if ($rawValue['REACTION'] instanceof DateTime) {
            switch ($this->dateType) {
                case 'short':
                    $value = FormatDateFromDB(
                        $rawValue['REACTION']->toString(),
                        $this->dateShort
                    );
                    $value = HelpersEncoding::toUtf($value);
                    $value = mb_strtolower($value);
                    break;
                default:
                    $value = $rawValue['REACTION']->toString(new Culture(['FORMAT_DATETIME' => $this->dateFull]));
            }

            return [
                'VALUE' => $value,
                'OVERDUE' => $this->isOverdue($rawValue['REACTION'], $rawValue['DATE_FIRST_REACTION'])
            ];
        }

        return [
            'VALUE' => '[---]',
            'OVERDUE' => false
        ];
    }

    /**
     * @param $deadline
     * @param $closed
     *
     * @return bool
     * @throws ObjectException
     */
    private function isOverdue($deadline, $closed): bool
    {
        $current = (new DateTime())->getTimestamp();
        $deadline = $deadline->getTimestamp();
        $closed = $closed ? $closed->getTimestamp() : '';

        if ($closed) {
            $overdue = $deadline < $closed;
        } else {
            $overdue = $deadline <= $current;
        }

        return $overdue;
    }

    public function setDateType($dateType): void
    {
        if (in_array($dateType, ['short', 'full'])) {
            $this->dateType = $dateType;
        }
    }
}
