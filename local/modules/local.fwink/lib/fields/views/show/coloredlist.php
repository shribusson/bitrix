<?

namespace Local\Fwink\Fields\Views\Show;

class ColoredList extends ColoredValue
{
    protected function getNode()
    {
        $iconColor = $this->dom->createElement('span', $this->value['TITLE']);
        $iconColor->setAttribute('style', 'background-color: ' . $this->value['COLOR']);

        return $iconColor;
    }

    protected function getDefaultAttributes()
    {
        return [
            'class' => 'label label-lg'
        ];
    }
}
