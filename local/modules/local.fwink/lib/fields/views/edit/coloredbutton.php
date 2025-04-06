<?

namespace Local\Fwink\Fields\Views\Edit;

use Local\Fwink\Fields\Views\Base;

class ColoredButton extends Base
{
    protected function getNode()
    {
        $value = $this->convertToUtf($this->value);

        $div = $this->dom->createElement('div');
        foreach ($value as $valueItem) {
            if (empty($valueItem['SELECTED'])) {
                $color = $valueItem['ATTRIBUTES']['data-color'];
                $buttonItem = $this->dom->createElement('button', $valueItem['BUTTON']);
                $buttonItem->setAttribute('type', 'button');
                $buttonItem->setAttribute('name', $this->field->getName());
                $buttonItem->setAttribute('value', $valueItem['VALUE']);
                $buttonItem->setAttribute('class', 'ui-btn status-btn');
                $buttonItem->setAttribute('style', 'background-color: ' . $color . '; border-color: ' . $color);
                $div->appendChild($buttonItem);
            }
        }

        return $div;
    }

    protected function getDefaultAttributes()
    {
        return [];
    }
}
