<?

namespace Local\Fwink\Fields\Views\Show;

use Local\Fwink\Fields\Views\Base;
use DOMElement;

class Observers extends Base
{
    protected function getNode()
    {
        $divMain = $this->dom->createElement('div');

        if (is_array($this->value)) {
            foreach ($this->value as $value) {
                $div = $this->dom->createElement('div');
                $div->setAttribute('class', 'task-detail-sidebar-info-user task-detail-sidebar-info-user-employee');

                $photo = $this->getPhoto($value);
                $name = $this->getName($value);

                $div->appendChild($photo);
                $div->appendChild($name);

                $divMain->appendChild($div);
            }
        } else {
            $span = $this->dom->createElement('span', '[---]');
            $divMain->appendChild($span);
        }

        return $divMain;
    }

    private function getPhoto($value): DOMElement
    {
        $link = $this->dom->createElement('a');
        $link->setAttribute('href', $value['URL']);
        $link->setAttribute('title', $value['TITLE']);
        $link->setAttribute('class', 'task-detail-sidebar-info-user-photo');

        if ($value['PHOTO']) {
            $style = "background: url('{$value['PHOTO']}') center no-repeat; background-size: 35px;";
            $link->setAttribute('style', $style);
        }

        return $link;
    }

    private function getName($value): DOMElement
    {
        $div = $this->dom->createElement('div');
        $div->setAttribute('class', 'task-detail-sidebar-info-user-title');

        $link = $this->dom->createElement('a', $value['TITLE']);
        $link->setAttribute('href', $value['URL']);
        $link->setAttribute('title', $value['TITLE']);
        $link->setAttribute('class', 'task-detail-sidebar-info-user-name task-detail-sidebar-info-user-name-link');

        $position = $this->dom->createElement('div', $value['POSITION']);
        $position->setAttribute('class', 'task-detail-sidebar-info-user-pos');

        $div->appendChild($link);
        $div->appendChild($position);

        return $div;
    }

    protected function getDefaultAttributes()
    {
        return [
            'class' => 'task-detail-sidebar-info-user-wrap'
        ];
    }
}
