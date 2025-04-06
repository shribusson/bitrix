<?

namespace Local\Fwink\Fields\Views\Show;

use Local\Fwink\AccessControl\Operations;
use Local\Fwink\Fields\Views\Base;
use DOMElement;

class LogValue extends Base
{
    protected function getNode()
    {
        $span = $this->dom->createElement('span');

        if ($this->value['OPERATION'] === Operations::OPERATION_UPDATE) {
            $old = $this->getValue($this->value['OLD']);
            $new = $this->getValue($this->value['NEW']);

            $marker = $this->dom->createElement('i');
            $marker->setAttribute('class', 'ti-arrow-right log');

            $span->appendChild($old);
            $span->appendChild($marker);
            $span->appendChild($new);
        } else {
            $new = $this->getValue($this->value['NEW']);
            $span->appendChild($new);
        }

        return $span;
    }

    private function getValue($value): DOMElement
    {
        if ($value['URL']) {
            $link = $this->dom->createElement('a', $value['TEXT']);
            $link->setAttribute('href', $value['URL']);
            $link->setAttribute('title', $value['TITLE']);
        } else {
            $link = $this->dom->createElement('span', $value['TEXT']);
        }

        return $link;
    }

    protected function getDefaultAttributes()
    {
        return [];
    }
}
