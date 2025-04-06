<?

namespace Local\Fwink\Fields\Value\Type;

use Bitrix\Main\Type\DateTime;
use Local\Fwink\Fields\Value\Base;
use Local\Fwink\Fields\Views\Show\StatusInfo;
use Local\Fwink\Fields\Views\ViewInterface;
use Local\Fwink\Helpers\Encoding as HelpersEncoding;

class Status extends Base
{
    private $fieldNameForTitle;
    private $fieldNameForColor;
    private $dateShort = 'j F Y, HH:MI';

    public function __construct($fieldNames)
    {
        parent::__construct($fieldNames);

        if ($fieldNames['TITLE']) {
            $this->fieldNameForTitle = $fieldNames['TITLE'];
        }

        if ($fieldNames['COLOR']) {
            $this->fieldNameForColor = $fieldNames['COLOR'];
        }
    }

    public function setDataToView(ViewInterface $view): void
    {
        if ($view instanceof StatusInfo) {
            $value['TITLE'] = $this->getTitle();
            $value['COLOR'] = $this->getColor();
            $value['DATE'] = $this->getDate();
        } else {
            $value = $this->getTitle();
        }

        $view->setValue($value);
    }

    public function getTitle()
    {
        $title = $this->getFieldFromRawValue($this->fieldNameForTitle);
        $title = ($title ?? '');

        return $title;
    }

    public function getColor()
    {
        $color = $this->getFieldFromRawValue($this->fieldNameForColor);
        $color = ($color ?? '');

        return $color;
    }

    public function getDate()
    {
        /** @var DateTime $date */
        $date = $this->getRaw()['STATUS_CHANGED_DATE'];
        if ($date) {
            $date = FormatDateFromDB($date->toString(), $this->dateShort);
            $date = HelpersEncoding::toUtf($date);
            $date = mb_strtolower($date);

            return $date;
        }

        return '';
    }
}
