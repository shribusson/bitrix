<?

namespace Local\Fwink\Fields\Views\Edit;

use Local\Fwink\Fields\Views\Base;

class InputText extends Base
{
    protected function getNode()
    {
        return $this->dom->createElement('input');
    }

    protected function getDefaultAttributes()
    {
        return [
            'type' => 'text',
            'name' => $this->field->getName(),
            'value' => $this->value,
            'class' => 'helpdesk_form-control'
        ];
    }
}
