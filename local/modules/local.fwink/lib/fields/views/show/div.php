<?

namespace Local\Fwink\Fields\Views\Show;

use Local\Fwink\Fields\Views\Base;

class Div extends Base
{
    protected function getNode()
    {
        return $this->dom->createElement('div', $this->value);
    }

    protected function getDefaultAttributes(): array
    {
        return [];
    }
}
