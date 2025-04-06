<?

namespace Local\Fwink\Fields\Value\Type;

use Local\Fwink\Fields\Value\Base;
use Local\Fwink\Fields\Views\ViewInterface;
use Local\Fwink\Helpers\Encoding as HelpersEncoding;
use Local\Fwink\Helpers\WorkTime as HelpersWorkTime;

class Time extends Base
{
    /**
     * @param ViewInterface $view
     */
    public function setDataToView(ViewInterface $view): void
    {
        $rawValue = $this->getRaw();
        $value = HelpersWorkTime::getFormat($rawValue);
        $value = HelpersEncoding::toUtf($value);

        $view->setValue($value);
    }
}
