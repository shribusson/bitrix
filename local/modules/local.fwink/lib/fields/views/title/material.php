<?

namespace Local\Fwink\Fields\Views\Title;

use Local\Fwink\Fields\Views\Base;
use Local\Fwink\Helpers\Encoding as HelpersEncoding;

class Material extends Base
{
    protected function getNode()
    {
        $label = $this->dom->createElement('label', HelpersEncoding::toUtf($this->field->getTitle()));

        if ($this->field->isRequired()) {
            $required = $this->dom->createElement('span', ' *');
            $required->setAttribute('style', 'color: red;');
            $label->appendChild($required);
        }

        return $label;
    }

    protected function getDefaultAttributes()
    {
        return [
            'for' => $this->field->getName(),
            'class' => 'block'
        ];
    }
}
