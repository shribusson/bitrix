<?

namespace Local\Fwink\Fields\Views\Show;

use Local\Fwink\Fields\Views\Base;

class Deadline extends Base
{
    protected function getNode()
    {
        if ($this->value['OVERDUE']) {
            $div = $this->dom->createElement('div');
            $div->setAttribute('class', 'task-detail-sidebar-item-delay-message');

            $spanIco = $this->dom->createElement('span');
            $spanIco->setAttribute('class', 'task-detail-sidebar-item-delay-message-icon');

            $spanDate = $this->dom->createElement('span', $this->value['VALUE']);
            $spanDate->setAttribute('class', 'task-detail-sidebar-item-delay-message-text');

            $div->appendChild($spanIco);
            $div->appendChild($spanDate);
        } else {
            $div = $this->dom->createElement('span', $this->value['VALUE']);
        }

        return $div;
    }

    protected function getDefaultAttributes(): array
    {
        return [];
    }
}
