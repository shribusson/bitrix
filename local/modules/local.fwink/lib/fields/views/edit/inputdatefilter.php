<?

namespace Local\Fwink\Fields\Views\Edit;

use CJSCore;
use Local\Fwink\Fields\Views\Base;

class InputDateFilter extends Base
{
    protected function getNode()
    {
        $row = $this->dom->createElement('div');
        $row->setAttribute('class', 'row');

        $divTo = $this->dom->createElement('div');
        $divTo->setAttribute('class', 'col-sm-12 col-md-6 m-b-5');

        $inputTo = $this->dom->createElement('input');
        $inputTo->setAttribute('type', 'datetime');
        $inputTo->setAttribute('name', $this->field->getName() . '_FROM');
        $inputTo->setAttribute('value', $this->value['FROM']);
        $inputTo->setAttribute('class', 'helpdesk_form-control');
        $inputTo->setAttribute('readonly', 'readonly');
        $inputTo->setAttribute('onclick', 'BX.calendar({node: this, field: this, bTime: true, bHideTime: true});');

        $divTo->appendChild($inputTo);

        $divFrom = $this->dom->createElement('div');
        $divFrom->setAttribute('class', 'col-sm-12 col-md-6');

        $inputFrom = $this->dom->createElement('input');
        $inputFrom->setAttribute('type', 'datetime');
        $inputFrom->setAttribute('name', $this->field->getName() . '_TO');
        $inputFrom->setAttribute('value', $this->value['TO']);
        $inputFrom->setAttribute('class', 'helpdesk_form-control');
        $inputFrom->setAttribute('readonly', 'readonly');
        $inputFrom->setAttribute('onclick', 'BX.calendar({node: this, field: this, bTime: true, bHideTime: true});');

        $divFrom->appendChild($inputFrom);

        $row->appendChild($divTo);
        $row->appendChild($divFrom);

        return $row;
    }

    protected function getDefaultAttributes()
    {
        CJSCore::Init(['date']);
        return [];
    }
}
