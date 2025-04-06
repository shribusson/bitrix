<?

namespace Local\Fwink\Fields\Views\Edit;

use Local\Fwink\Fields\Views\Base;

class InputFile extends Base
{
    protected function getNode()
    {
        return $this->dom->createElement('input');
    }

    protected function getDefaultAttributes()
    {
        return [
            'type' => 'file',
            'name' => $this->field->getName(),
            'value' => $this->value
        ];
    }
}
