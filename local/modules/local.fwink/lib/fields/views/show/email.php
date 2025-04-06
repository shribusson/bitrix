<?

namespace Local\Fwink\Fields\Views\Show;

use Local\Fwink\Fields\Views\Base;

class Email extends Base
{
    protected function getNode()
    {
        $domain = $this->dom->createElement('a', $this->value);
        $domain->setAttribute('href', 'mailto::' . $this->value);
        return $domain;
    }

    protected function getDefaultAttributes(): array
    {
        return [];
    }
}
