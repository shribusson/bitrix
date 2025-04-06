<?

namespace Local\Fwink\Fields\Views\Edit;

use Local\Fwink\Fields\Views\Base;

class Textarea extends Base
{
    protected function getNode()
    {
        return $this->dom->createElement('textarea', $this->value);
    }

    protected function getDefaultAttributes()
    {
        return [
            'name' => $this->field->getName(),
            'class' => 'helpdesk_form-control'
        ];
    }
}
