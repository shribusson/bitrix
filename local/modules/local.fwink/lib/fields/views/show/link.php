<?

namespace Local\Fwink\Fields\Views\Show;

use Local\Fwink\Fields\Views\Base;

class Link extends Base
{
    protected function getNode()
    {
        if ($this->value['URL']) {
            $link = $this->dom->createElement('a', $this->value['TEXT']);
            $link->setAttribute('href', $this->value['URL']);
            $link->setAttribute('title', $this->value['TITLE']);

            if (!empty($this->value['BOLD'])) {
                $link->setAttribute('class', 'no_answer');
            }
            if ($this->value['TARGET_BLANK']) {
                $link->setAttribute('target', '_blank');
            }
        } else {
            $link = $this->dom->createElement('span', $this->value['TEXT']);
        }

        return $link;
    }

    protected function getDefaultAttributes()
    {
        return [];
    }
}
