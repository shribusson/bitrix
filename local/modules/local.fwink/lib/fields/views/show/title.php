<?

namespace Local\Fwink\Fields\Views\Show;

use Local\Fwink\Fields\Views\Base;
use DOMElement;

class Title extends Base
{
    protected function getNode()
    {
        if ($this->value['URL']) {
            $link = $this->dom->createElement('a', $this->value['TEXT']);
            $link->setAttribute('href', $this->value['URL']);
            $link->setAttribute('title', $this->value['TITLE']);

            $class = [];
            if (!empty($this->value['BOLD'])) {
                $class[] = 'no_answer';
            }

            if (!empty($this->value['CLOSED'])) {
                $class[] = 'status_final';
            }

            if ($class) {
                $link->setAttribute('class', implode(' ', $class));
            }

            if ($this->value['TARGET_BLANK']) {
                $link->setAttribute('target', '_blank');
            }

            if ($this->value['COUNT'] > 0) {
                $count = $this->getCount();
                $link->appendChild($count);
            }
        } else {
            $link = $this->dom->createElement('span', $this->value['TEXT']);
        }

        return $link;
    }

    private function getCount(): DOMElement
    {
        $span = $this->dom->createElement('span', $this->value['COUNT']);
        $span->setAttribute('class', 'task-title-indicators');

        return $span;
    }

    protected function getDefaultAttributes()
    {
        return [];
    }
}
