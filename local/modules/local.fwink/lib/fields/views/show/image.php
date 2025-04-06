<?

namespace Local\Fwink\Fields\Views\Show;

use Local\Fwink\Fields\Views\Base;

class Image extends Base
{
    protected function getNode()
    {
        $image = $this->dom->createElement('img');
        $image->setAttribute('src', $this->value);

        return $image;
    }

    protected function getDefaultAttributes()
    {
        return [];
    }
}
