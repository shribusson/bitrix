<?

namespace Local\Fwink\Fields\Value\Type;

use CFile;
use Local\Fwink\Fields\Value\Base;
use Local\Fwink\Fields\Views\ViewInterface;

class UserImage extends Base
{
    public function setDataToView(ViewInterface $view): void
    {
        $val = $this->getValue();
        if ($val !== false) {
            $arFileTmp = CFile::ResizeImageGet(
                $val,
                ['width' => 600, 'height' => 600],
                BX_RESIZE_IMAGE_EXACT,
                true,
                ''
            );
        }
        $value = !empty($arFileTmp['src']) ? $arFileTmp['src'] : '';
        $view->setValue($value);
    }

    public function getValue()
    {
        return $this->getFieldFromRawValue('VALUE');
    }
}
