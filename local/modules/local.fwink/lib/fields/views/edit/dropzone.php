<?

namespace Local\Fwink\Fields\Views\Edit;

use Bitrix\Main\Page\Asset;
use Local\Fwink\Fields\Views\Base;

class Dropzone extends Base
{
    protected function getNode()
    {
        Asset::getInstance()->addJs('/bitrix/js/local.fwink/dropzone/js/jquery.filer.min.js');
        Asset::getInstance()->addCss('/bitrix/js/local.fwink/dropzone/css/jquery.filer.css');
        Asset::getInstance()->addCss('/bitrix/js/local.fwink/dropzone/css/themes/filer-theme.css');

        return $this->dom->createElement('input');
    }

    protected function getDefaultAttributes()
    {
        return [
            'name' => $this->field->getName(),
            'type' => 'file',
            'id' => 'filer_input1',
            'multiple' => 'multiple'
        ];
    }
}
