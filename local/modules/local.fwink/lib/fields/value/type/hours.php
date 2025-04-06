<?

namespace Local\Fwink\Fields\Value\Type;

use Local\Fwink\Fields\Value\Base;
use Local\Fwink\Fields\Views\ViewInterface;

class Hours extends Base
{
    public function setDataToView(ViewInterface $view): void
    {
        $rawValue = $this->getRaw();
        $value = $rawValue / 3600;
        $view->setValue($value);
    }
}
