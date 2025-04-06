<?

namespace Local\Fwink\Fields\Views;

use Local\Fwink\Fields\FieldInfo;

interface ViewInterface
{
    public function setField(FieldInfo $field);

    public function setValue($value);

    public function setAttributes(array $attributes);

    public function getHtml();
}
