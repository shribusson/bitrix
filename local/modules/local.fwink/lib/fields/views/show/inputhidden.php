<?

namespace Local\Fwink\Fields\Views\Show;

use Local\Fwink\Fields\Views\Base;

class InputHidden extends Base
{
    protected function getNode()
    {
        $input = $this->dom->createElement('input');
        $input->setAttribute('value', $this->value);

        return $input;
    }

    protected function getDefaultAttributes()
    {
        return [
            'type' => 'hidden',
            'name' => 'ELEMENT_ID'
        ];
    }
}
