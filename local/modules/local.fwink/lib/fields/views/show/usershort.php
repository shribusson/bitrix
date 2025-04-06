<?

namespace Local\Fwink\Fields\Views\Show;

use Local\Fwink\Fields\Views\Base;
use DOMElement;

class UserShort extends Base
{
    protected function getNode()
    {
        $div = $this->dom->createElement('div');
        $name = $this->getName();
        $div->appendChild($name);

        return $div;
    }

    private function getName(): DOMElement
    {
        if ($this->value['URL'] && $this->value['TITLE']) {
            $link = $this->dom->createElement('a', $this->value['TITLE']);
            $link->setAttribute('href', $this->value['URL']);
            $link->setAttribute('title', $this->value['TITLE']);
            if ($this->value['TARGET_BLANK']) {
                $link->setAttribute('target', '_blank');
            }
        } else {
            $this->value['TITLE'] = $this->value['TITLE'] ?: '[---]';
            $link = $this->dom->createElement('span', $this->value['TITLE']);
        }

        return $link;
    }

    protected function getDefaultAttributes()
    {
        return [];
    }
}
