<?

namespace Local\Fwink\Fields\Value\Type;

use Bitrix\Main\Context\Culture;
use Bitrix\Main\Type\DateTime;
use Local\Fwink\Fields\Value\Base;
use Local\Fwink\Helpers\Encoding as HelpersEncoding;

class Date extends Base
{
    private $dateType = 'short';
    private $dateFull = 'DD.MM.YYYY HH:MI:SS';
    private $dateShort = 'j F Y, HH:MI';

    public function get()
    {
        $rawValue = $this->getRaw();
        $value = '';
        if ($rawValue instanceof DateTime) {
            switch ($this->dateType) {
                case 'short':
                    $value = FormatDateFromDB(
                        $rawValue->toString(),
                        $this->dateShort
                    );
                    $value = HelpersEncoding::toUtf($value);
                    $value = mb_strtolower($value);
                    break;
                default:
                    $value = $rawValue->toString(new Culture(['FORMAT_DATETIME' => $this->dateFull]));
            }
        }

        return $value;
    }

    public function setDateType($dateType): void
    {
        if (in_array($dateType, ['short', 'full'])) {
            $this->dateType = $dateType;
        }
    }
}
