<?

namespace Local\Fwink\Fields\Views\Show;

use Local\Fwink\Fields\Views\Base;
use DOMElement;

class User extends Base
{
    protected function getNode()
    {
        $div = $this->dom->createElement('div');

        $photo = $this->getPhoto();
        $name = $this->getName();

        $div->appendChild($photo);
        $div->appendChild($name);

        return $div;
    }

    private function getPhoto(): DOMElement
    {
        $div = $this->dom->createElement('div');
        $div->setAttribute('class', 'media-left');

        if ($this->value['URL']) {
            $link = $this->dom->createElement('a');
            $link->setAttribute('href', $this->value['URL']);
            $link->setAttribute('title', $this->value['TITLE']);
            if ($this->value['TARGET_BLANK']) {
                $link->setAttribute('target', '_blank');
            }
        } else {
            $link = $this->dom->createElement('span');
        }

        $photo = $this->dom->createElement('img');
        $photo->setAttribute('src', $this->value['PHOTO']);
        $photo->setAttribute('class', 'media-object img-radius comment-img');

        $link->appendChild($photo);

        $div->appendChild($link);

        return $div;
    }

    private function getName(): DOMElement
    {
        $div = $this->dom->createElement('div');
        $div->setAttribute('class', 'media-body');

        $header = $this->dom->createElement('h6');
        $header->setAttribute('class', 'media-heading txt-primary m-b-0');

        if ($this->value['URL']) {
            $link = $this->dom->createElement('a', $this->value['TITLE']);
            $link->setAttribute('href', $this->value['URL']);
            $link->setAttribute('title', $this->value['TITLE']);
            if ($this->value['TARGET_BLANK']) {
                $link->setAttribute('target', '_blank');
            }
        } else {
            $link = $this->dom->createElement('span', $this->value['TITLE']);
        }

        $span = $this->dom->createElement(
            'span',
            !empty($this->value['POSITION']) ? $this->value['POSITION'] : '[---]'
        );
        $span->setAttribute('class', 'f-12 text-muted');

        $header->appendChild($link);
        $div->appendChild($header);
        $div->appendChild($span);

        return $div;
    }

    protected function getDefaultAttributes()
    {
        return [
            'class' => 'media'
        ];
    }
}
