<?

namespace Local\Fwink\Fields\Views\Show;

use Local\Fwink\Fields\Views\Base;

class Role extends Base
{
    protected function getNode()
    {
        $div = $this->dom->createElement('span');
        foreach ($this->value as $value) {
            $span = $this->dom->createElement('span', $value['NAME']);
            $div->appendChild($span);
        }

        return $div;
    }

    protected function getDefaultAttributes(): array
    {
        return [];
    }
}
