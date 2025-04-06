<?

namespace Local\Fwink\Fields\Value\Type;

use Local\Fwink\Fields\Value\Base;
use Local\Fwink\Fields\Views\ViewInterface;
use Local\Fwink\Helpers\Client as HelpersClient;

class ColorActiveUser extends Base
{
    public function setDataToView(ViewInterface $view): void
    {
        $val = $this->getValue();
        $value['TITLE'] = HelpersClient::getColor()[$val]['TITLE'];
        $value['COLOR'] = HelpersClient::getColor()[$val]['COLOR'];
        $value = $this->convertToUtf($value);

        $view->setValue($value);
    }

    public function getValue()
    {
        return $this->getFieldFromRawValue('VALUE');
    }
}
