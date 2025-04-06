<?

namespace Local\Fwink\Fields\Views\Edit;

use Bitrix\Main\Page\Asset;
use CJSCore;
use CUtil;
use Local\Fwink\Fields\Views\Base;
use Local\Fwink\Helpers\Encoding as HelpersEncoding;

class Editor extends Base
{
    protected function getNode()
    {
        $this->initScript();

        $text = HelpersEncoding::toUtf(isset($this->value['TEXT'])?$this->value['TEXT']:$this->value);
        $text = htmlentities($text);
		$new_elm = $this->dom->createElement('textarea', $text);
		$new_elm->setAttribute('style', 'width: 100%');
		return $new_elm;
        //return $this->dom->createElement('textarea', $text);
    }

    private function initScript(): void
    {
        CJSCore::Init(['jquery']);
        Asset::getInstance()->addJs('/bitrix/js/local.fwink/ckeditor4/js/ckeditor.js');
        CJSCore::RegisterExt('ckeditor', [
            'js' => '/bitrix/js/local.fwink/ckeditor4/js/lang.js',
			//'lang' => '/local/modules/local.fwink/lang/' . LANGUAGE_ID . '/js/ckeditor.php',
			'lang' => '/bitrix/js/local.fwink/ckeditor4/lang/ru/lang.php',
            'rel' => []
        ]);
        CUtil::InitJSCore(['ckeditor']);
    }

    protected function getDefaultAttributes()
    {
        $id = (is_array($this->value) && $this->value['ID']) ? $this->field->getName() . '_' . $this->value['ID'] : $this->field->getName();
        return [
            'name' => $this->field->getName(),
            'id' => 'helpdesk-edit_' . $id
        ];
    }
}
