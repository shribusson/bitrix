<?

namespace Local\Fwink\Fields\Views\Show;

use Local\Fwink\Fields\Views\Base;

class Domain extends Base
{
    protected function getNode()
    {
        $domain = $this->dom->createElement('a', $this->value);
        $domain->setAttribute('href', '//' . $this->value);
        $domain->setAttribute('target', '_blank');
        return $domain;
    }

    protected function getDefaultAttributes(): array
    {
        return [];
    }
}
