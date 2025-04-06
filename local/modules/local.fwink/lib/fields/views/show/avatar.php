<?

namespace Local\Fwink\Fields\Views\Show;

use CFile;
use Local\Fwink\Fields\Views\Base;

class Avatar extends Base
{
    protected function getNode()
    {
        $span = $this->dom->createElement('span');
        $image = $this->getImage();
        if ($image) {
            $span->setAttribute('style', 'background-size: 100%; background-image: url("' . $image . '")');
        }

        return $span;
    }

    private function getImage()
    {
        $value = '';
        if ($this->value !== false) {
            $arFileTmp = CFile::ResizeImageGet(
                $this->value,
                ['width' => 100, 'height' => 100],
                BX_RESIZE_IMAGE_EXACT,
                true,
                ''
            );
            $value = $arFileTmp['src'];
        }

        return $value;
    }

    protected function getDefaultAttributes(): array
    {
        return [
            'class' => 'feed-com-avatar'
        ];
    }
}
