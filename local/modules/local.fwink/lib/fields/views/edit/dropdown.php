<?

namespace Local\Fwink\Fields\Views\Edit;

use Local\Fwink\Fields\Views\Base;

class Dropdown extends Base
{

    protected function getNode()
    {
        $value = $this->convertToUtf($this->value);

        $dropdown = $this->dom->createElement('div');
        $dropdown->setAttribute('class', '');

        $selectedValue = $this->getSelectedValue($value);

        $id = uniqid('dropdown_', true);
        $dropdownButton = $this->dom->createElement('button', $selectedValue['TEXT']);
        $dropdownButton->setAttribute('type', 'button');

        if (count($value) > 1) {
            $dropdownButton->setAttribute('class', 'btn helpdesk_btn-status dropdown-toggle waves-effect waves-light');
        } else {
            $dropdownButton->setAttribute('class', 'btn helpdesk_btn-status waves-effect waves-light');
        }

        $dropdownButton->setAttribute('id', $id);
        $dropdownButton->setAttribute('style', 'background-color: ' . $selectedValue['ATTRIBUTES']['data-color']);
        $dropdownButton->setAttribute('data-toggle', 'dropdown');
        $dropdownButton->setAttribute('aria-haspopup', 'true');
        $dropdownButton->setAttribute('aria-expanded', 'true');
        $dropdownButton->setAttribute('data-offset', '0, -2');
        $dropdownButton->setAttribute('data-flip', 'false');

        $dropdownMenu = $this->dom->createElement('div');
        $dropdownMenu->setAttribute('class', 'dropdown-menu color');
        $dropdownMenu->setAttribute('aria-labelledby', $id);
        $dropdownMenu->setAttribute('data-dropdown-in', 'fadeIn');
        $dropdownMenu->setAttribute('data-dropdown-out', 'fadeOut');

        foreach ($value as $valueItem) {
            if ($valueItem['VALUE'] !== $selectedValue['VALUE']) {
                $dropdownItem = $this->dom->createElement('button', $valueItem['TEXT']);
                $dropdownItem->setAttribute('type', 'button');
                $dropdownItem->setAttribute('name', $this->field->getName());
                $dropdownItem->setAttribute('value', $valueItem['VALUE']);
                $dropdownItem->setAttribute('class', 'btn helpdesk_btn-status dropdown-item waves-light waves-effect');
                $dropdownItem->setAttribute('style', 'background-color: ' . $valueItem['ATTRIBUTES']['data-color']);
                $dropdownMenu->appendChild($dropdownItem);
            }
        }

        $dropdown->appendChild($dropdownButton);
        $dropdown->appendChild($dropdownMenu);

        return $dropdown;
    }

    private function getSelectedValue($value)
    {
        return array_pop(array_filter($value, static function ($valueParams) {
            return $valueParams['SELECTED'];
        }));
    }

    protected function getDefaultAttributes()
    {
        return [
            'class' => 'dropdown open'
        ];
    }
}
