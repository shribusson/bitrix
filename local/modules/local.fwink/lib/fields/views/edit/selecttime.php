<?

namespace Local\Fwink\Fields\Views\Edit;

use Bitrix\Main\Page\Asset;
use Local\Fwink\Fields\Views\Base;
use DOMElement;

class SelectTime extends Base
{
    protected function getNode()
    {
        Asset::getInstance()->addCss('/bitrix/css/local.fwink/selecttime.css');

        $wrapper = $this->dom->createElement('div');
        $hour = $this->getHours();
        $wrapper->appendChild($hour);

        $separator = $this->getSeparator();
        $wrapper->appendChild($separator);

        $minutes = $this->getMinutes();
        $wrapper->appendChild($minutes);

        return $wrapper;
    }

    /**
     * @return DOMElement
     */
    private function getHours(): DOMElement
    {
        $select = $this->dom->createElement('select');
        $select->setAttribute('class', 'js-select2');
        $select->setAttribute('data-minimum-results-for-search', 'Infinity');

        $values = [];
        foreach (range(0, 23) as $number) {
            $values[] = [
                'TEXT' => str_pad($number, 2, '0', STR_PAD_LEFT),
                'VALUE' => $number
            ];
        }

        $optionList = $this->getOptionList($values);
        if (count($optionList) > 0) {
            foreach ($optionList as $option) {
                $select->appendChild($option);
            }
        }

        return $select;
    }

    private function getOptionList($value): array
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

    /**
     * @return DOMElement
     */
    private function getSeparator(): DOMElement
    {
        $span = $this->dom->createElement('span', ':');
        $span->setAttribute('class', 'separator');

        return $span;
    }

    /**
     * @return DOMElement
     */
    private function getMinutes(): DOMElement
    {
        $select = $this->dom->createElement('select');
        $select->setAttribute('class', 'js-select2');
        $select->setAttribute('data-minimum-results-for-search', 'Infinity');

        $values = [];
        foreach (range(0, 59) as $number) {
            $values[] = [
                'TEXT' => str_pad($number, 2, '0', STR_PAD_LEFT),
                'VALUE' => $number
            ];
        }

        $optionList = $this->getOptionList($values);
        if (count($optionList) > 0) {
            foreach ($optionList as $option) {
                $select->appendChild($option);
            }
        }

        return $select;
    }

    protected function getDefaultAttributes()
    {
        return [
            'class' => 'helpdesk_select_time'
        ];
    }
}
