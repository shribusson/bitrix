<?

namespace Local\Fwink\Fields\Views\Show;

use Local\Fwink\Fields\Views\Base;

class Span extends Base
{
    protected function getNode()
    {
        return $this->dom->createElement('span', $this->value);
    }

    protected function getDefaultAttributes(): array
    {
        return [];
    }
}
