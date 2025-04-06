<?

namespace Local\Fwink\Fields\Views\Edit;

use Bitrix\Main\Localization\Loc;
use CJSCore;
use Local\Fwink\Fields\Views\Base;
use Local\Fwink\Helpers\Encoding as HelpersEncoding;

class InputDateTime extends Base
{
    protected function getNode()
    {
        return $this->dom->createElement('input');
    }

    protected function getDefaultAttributes()
    {
        CJSCore::Init(['date']);
        return [
            'type' => 'datetime',
            'name' => $this->field->getName(),
            'value' => $this->value,
            'class' => 'helpdesk_form-control',
            'placeholder' => HelpersEncoding::toUtf(Loc::getMessage('LOCAL_FIELDS_VIEWS_PLACE_HOLDER')),
            'readonly' => 'readonly',
            'onclick' => 'BX.calendar({node: this, field: this, bTime: true, bHideTime: false});'
        ];
    }
}
