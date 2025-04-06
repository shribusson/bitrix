<?

namespace Local\Fwink\Fields\Views\Edit;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use Local\Fwink\Fields\Views\Base;
use Local\Fwink\Helpers\Encoding as HelpersEncoding;
use DOMElement;

class InputTime extends Base
{
    private $hours = '';
    private $minutes = '';

    protected function getNode()
    {
        Asset::getInstance()->addCss('/bitrix/css/local.fwink/imputtime.css');
        Asset::getInstance()->addJs('/bitrix/js/local.fwink/typeahead/bootstrap3-typeahead.min.js');
        Asset::getInstance()->addJs('/bitrix/js/local.fwink/typeahead/inputtime.js');

        if ($this->value) {
            $this->hours = floor($this->value / 3600);
            $this->minutes = ($this->value % 3600) / 60;
        }

        $wrapper = $this->dom->createElement('div');
        $hour = $this->getHours();
        $wrapper->appendChild($hour);

        $separator = $this->getSeparator();
        $wrapper->appendChild($separator);

        $minutes = $this->getMinutes();
        $wrapper->appendChild($minutes);

        return $wrapper;
    }

    /**
     * @return DOMElement
     */
    private function getHours(): DOMElement
    {
        $div = $this->dom->createElement('div');
        $div->setAttribute('class', 'hours');
        $input = $this->dom->createElement('input');
        $input->setAttribute('name', $this->field->getName() . '[hours]');
        $input->setAttribute('value', $this->hours);
        $input->setAttribute('class', 'helpdesk_form-control typeahead_hours');
        $input->setAttribute('autocomplete', 'off');
        $input->setAttribute(
            'placeholder',
            HelpersEncoding::toUtf(Loc::getMessage('LOCAL_FIELDS_VIEWS_EDIT_TIME_HOURS'))
        );

        $div->appendChild($input);

        return $div;
    }

    /**
     * @return DOMElement
     */
    private function getSeparator(): DOMElement
    {
        $span = $this->dom->createElement('span', ':');
        $span->setAttribute('class', 'separator');

        return $span;
    }

    /**
     * @return DOMElement
     */
    private function getMinutes(): DOMElement
    {
        $div = $this->dom->createElement('div');
        $div->setAttribute('class', 'minutes');
        $input = $this->dom->createElement('input');
        $input->setAttribute('name', $this->field->getName() . '[minutes]');
        $input->setAttribute('value', $this->minutes);
        $input->setAttribute('class', 'helpdesk_form-control typeahead_minutes');
        $input->setAttribute('autocomplete', 'off');
        $input->setAttribute(
            'placeholder',
            HelpersEncoding::toUtf(Loc::getMessage('LOCAL_FIELDS_VIEWS_EDIT_TIME_MINUTES'))
        );

        $div->appendChild($input);

        return $div;
    }

    protected function getDefaultAttributes()
    {
        return [
            'class' => 'helpdesk_input_time'
        ];
    }
}
