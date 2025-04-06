<?

namespace Local\Fwink\Fields\Views\Show;

use Bitrix\Main\Localization\Loc;
use Local\Fwink\Fields\Views\Base;
use Local\Fwink\Helpers\Encoding as HelpersEncoding;
use DOMElement;

class StatusInfo extends Base
{
    protected function getNode()
    {
        $divColor = $this->dom->createElement('div');
        $divColor->setAttribute('style', 'background-color: ' . $this->value['COLOR']);

        $div = $this->dom->createElement('div');
        $div->setAttribute('class', 'task-detail-sidebar-status-content');

        $spanValue = $this->dom->createElement('div', $this->value['TITLE']);
        $spanValue->setAttribute('class', 'task-detail-sidebar-status-text');

        $div->appendChild($spanValue);


        if ($this->value['DATE']) {
            $div->appendChild($this->getDate());
        }

        $divColor->appendChild($div);

        return $divColor;
    }

    private function getDate(): DOMElement
    {
        $text = HelpersEncoding::toUtf(Loc::getMessage('LOCAL_FIELDS_VIEWS_SHOW_STATUS_IN'));

        $span = $this->dom->createElement('div', $text . ' ' . $this->value['DATE']);
        $span->setAttribute('class', 'task-detail-sidebar-status-date');

        return $span;
    }

    protected function getDefaultAttributes()
    {
        return [
            'class' => 'task-detail-sidebar-status'
        ];
    }
}
