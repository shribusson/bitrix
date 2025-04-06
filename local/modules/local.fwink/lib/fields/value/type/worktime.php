<?

namespace Local\Fwink\Fields\Value\Type;

use Local\Fwink\Fields\Value\Base;
use Local\Fwink\Fields\Views\ViewInterface;

class WorkTime extends Base
{
    public function setDataToView(ViewInterface $view): void
    {
        $rawValue = $this->getRaw();
        $value = \Local\Fwink\Helpers\WorkTime::getTimeFormat($rawValue);
        $view->setValue($value);
    }
}
