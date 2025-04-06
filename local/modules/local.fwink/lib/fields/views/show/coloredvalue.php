<?

namespace Local\Fwink\Fields\Views\Show;

use Local\Fwink\Fields\Views\Base;

class ColoredValue extends Base
{
    protected function getNode()
    {
        $div = $this->dom->createElement('div', $this->value['TITLE']);
        $iconColor = $this->dom->createElement('i');
        $iconColor->setAttribute('class', 'ticket-priority__color');
        $iconColor->setAttribute('style', 'background-color: ' . $this->value['COLOR']);
        $div->appendChild($iconColor);

        return $div;
    }

    protected function getDefaultAttributes()
    {
        return [
            'class' => 'ticket-priority'
        ];
    }
}
