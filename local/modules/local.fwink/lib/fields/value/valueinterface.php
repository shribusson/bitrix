<?

namespace Local\Fwink\Fields\Value;

use Local\Fwink\Fields\Views\ViewInterface;

interface ValueInterface
{
    public function forSave($value);

    public function isValueExist();

    public function get();

    public function set($value);

    public function setDataToView(ViewInterface $view);
}
