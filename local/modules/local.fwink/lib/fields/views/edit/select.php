<?

namespace Local\Fwink\Fields\Views\Edit;

use Bitrix\Main\Page\Asset;
use CJSCore;
use CUtil;
use Local\Fwink\Fields\Views\Base;

class Select extends Base
{
    protected function getNode()
    {
        $this->initScript();
        $value = $this->convertToUtf($this->value);
        $optionList = $this->getOptionList($value);
        $select = $this->dom->createElement('select');
        $select->setAttribute('class', 'js-select2');
        $select->setAttribute('data-minimum-results-for-search', '10');

        if (count($optionList) > 0) {
            foreach ($optionList as $option) {
                $select->appendChild($option);
            }
        }

        return $select;
    }

    private function initScript(): void
    {
        CJSCore::Init(['jquery']);
        Asset::getInstance()->addJs('/bitrix/js/local.fwink/select2/js/select2.js');
        Asset::getInstance()->addJs('/bitrix/js/local.fwink/select2/js/init.js');
        Asset::getInstance()->addCss('/bitrix/js/local.fwink/select2/css/select2.css');
        CJSCore::RegisterExt('select2_lang', [
            'js' => '/bitrix/js/local.fwink/select2/js/lang.js',
            'lang' => '/local/modules/local.fwink/lang/' . LANGUAGE_ID . '/js/' . '/select2.php',
            'rel' => []
        ]);
        CUtil::InitJSCore(['select2_lang']);
    }

    protected function getOptionList($value): array
    {
        $options = [];

        foreach ($value as $listItem) {
            $option = $this->dom->createElement('option', $listItem['TEXT']);
            $option->setAttribute('value', $listItem['VALUE']);

            if ($listItem['SELECTED']) {
                $option->setAttribute('selected', 'selected');
            }
            if (count($listItem['ATTRIBUTES']) > 0) {
                foreach ($listItem['ATTRIBUTES'] as $attributeName => $attributeValue) {
                    $option->setAttribute($attributeName, $attributeValue);
                }
            }

            $options[] = $option;
        }

        return $options;
    }

    protected function getDefaultAttributes()
    {
        return [
            'name' => $this->field->getName()
        ];
    }
}
