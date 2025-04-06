<?

namespace Local\Fwink\Fields\Views\Show;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use Local\Fwink\Fields\Views\Base;
use Local\Fwink\Helpers\Encoding as HelpersEncoding;
use DOMElement;

class Files extends Base
{
    protected function getNode()
    {
        Asset::getInstance()->addCss('/bitrix/js/disk/css/legacy_uf_common.css');

        $parent = $this->dom->createElement('div');

        $div = $this->dom->createElement('div');
        $div->setAttribute('class', 'feed-com-files diskuf-files-entity');

        $title = $this->dom->createElement(
            'div',
            HelpersEncoding::toUtf(Loc::getMessage('LOCAL_FIELDS_VIEWS_SHOW_FILE_TITLE'))
        );
        $title->setAttribute('class', 'feed-com-files-title');

        $div->appendChild($title);

        $divFileParent = $this->dom->createElement('div');
        $divFileParent->setAttribute('class', 'feed-com-files-cont');

        foreach ($this->value as $file) {
            $divFile = $this->dom->createElement('div');
            $divFile->setAttribute('class', 'feed-com-file-wrap');

            $ico = $this->dom->createElement('span');
            $ico->setAttribute('class', 'feed-con-file-icon feed-file-icon-' . $file['EXTENSION']);

            $span = $this->dom->createElement('span');
            $span->setAttribute('class', 'feed-com-file-name-wrap');

            $url = $this->dom->createElement('a', $file['NAME']);
            $url->setAttribute('class', 'feed-com-file-name');
            $url->setAttribute('target', '_blank');
            $url->setAttribute('href', $file['PATH']);

            $size = $this->dom->createElement('span', $file['SIZE']);
            $size->setAttribute('class', 'feed-com-file-size');

            $span->appendChild($ico);
            $span->appendChild($url);
            $span->appendChild($size);

            if ($file['ACCESS_DELETE']) {
                $span->appendChild($this->getDelete($file));
            }

            $divFile->appendChild($span);
            $divFileParent->appendChild($divFile);

            $div->appendChild($divFileParent);
        }

        $parent->appendChild($div);

        return $parent;
    }

    private function getDelete($file): DOMElement
    {
        $url = $this->dom->createElement('a');
        $url->setAttribute('href', 'javascript:void(0)');
        $url->setAttribute('class', 'feed-com-file-delete');
        $url->setAttribute(
            'onclick',
            'BX.HelpsDesk.Comment.List.confirmDeleteFile(' . $file['ID'] . ')'
        );
        $delete = $this->dom->createElement('i');
        $delete->setAttribute('class', 'ti-trash');
        $url->appendChild($delete);

        return $url;
    }

    protected function getDefaultAttributes()
    {
        return [
            'class' => 'feed-com-files'
        ];
    }
}
