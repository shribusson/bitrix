<?

namespace Local\Fwink\Fields\Views\Edit;

use Bitrix\Main\Page\Asset;
use Local\Fwink\Fields\Views\Base;

class InputSwithces extends Base
{
    protected function getNode()
    {
        Asset::getInstance()->addJs('/bitrix/js/local.fwink/switchery/js/switchery.js');
        Asset::getInstance()->addCss('/bitrix/js/local.fwink/switchery/css/switchery.css');

        return $this->dom->createElement('input');
    }

    protected function getDefaultAttributes()
    {
        $defaultAttributes = [
            'type' => 'checkbox',
            'name' => $this->field->getName(),
            'id' => $this->field->getName(),
            'value' => '1',
            'class' => 'js-single'
        ];

        if ((bool)$this->value) {
            $defaultAttributes['checked'] = 'checked';
        }

        return $defaultAttributes;
    }
}
