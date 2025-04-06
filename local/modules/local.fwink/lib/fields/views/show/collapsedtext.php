<?

namespace Local\Fwink\Fields\Views\Show;

use Bitrix\Main\Page\Asset;
use CJSCore;
use CUtil;
use Local\Fwink\Fields\Views\Base;

class CollapsedText extends Base
{
    protected function getNode()
    {
        $this->initScript();

        return $this->dom->createElement('div', htmlspecialcharsEx($this->value));
    }

    private function initScript(): void
    {
        CJSCore::Init(['jquery']);
        Asset::getInstance()->addJs('/bitrix/js/local.fwink/readmore/readmore.js');
        CJSCore::RegisterExt('collapsed_text', [
            'js' => '/bitrix/js/local.fwink/readmore/collapsedtext.js',
            'lang' => '/local/modules/local.fwink/lang/' . LANGUAGE_ID . '/js/collapsedtext.php',
            'rel' => []
        ]);
        CUtil::InitJSCore(['collapsed_text']);
    }

    protected function getDefaultAttributes()
    {
        return [
            'class' => 'helpdesk_collapsed-text'
        ];
    }
}
