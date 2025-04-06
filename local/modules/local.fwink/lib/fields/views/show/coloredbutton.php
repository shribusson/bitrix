<?

namespace Local\Fwink\Fields\Views\Show;

use Local\Fwink\Fields\Views\Base;

class ColoredButton extends Base
{
    protected function getNode()
    {
        $button = $this->dom->createElement('button');
        $buttonText = $this->dom->createTextNode($this->value['TITLE']);
        $button->setAttribute('style', 'opacity: 1; background-color: ' . $this->value['COLOR']);
        $button->setAttribute('disabled', 'disabled');
        $button->appendChild($buttonText);

        return $button;
    }

    protected function getDefaultAttributes()
    {
        return [
            'class' => 'btn helpdesk_btn-status'
        ];
    }
}
