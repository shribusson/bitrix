<?

namespace Local\Fwink\Fields\Views\Edit;

use Local\Fwink\Fields\Views\Base;

class InputCheckbox extends Base
{
    protected function getNode()
    {
        return $this->dom->createElement('input');
    }

    protected function getDefaultAttributes()
    {
        $defaultAttributes = [
            'type' => 'checkbox',
            'name' => $this->field->getName(),
            'value' => 'Y',
        ];

        if ((bool)$this->value) {
            $defaultAttributes['checked'] = 'checked';
        }

        return $defaultAttributes;
    }
}
