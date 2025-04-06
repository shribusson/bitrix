<?

namespace Local\Fwink\Fields\Value\Type;

use Local\Fwink\Fields\Value\Base;

class Boolean extends Base
{
    public function get()
    {
        return (bool)$this->getRaw();
    }

    public function forSave($value)
    {
        return (boolean)$value;
    }
}
