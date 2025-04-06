<?

namespace Local\Fwink\Fields\Views\Edit;

use Bitrix\Main\Page\Asset;
use Local\Fwink\Fields\Views\Base;
use DOMElement;

class SelectClient extends Base
{
    protected function getNode()
    {
        Asset::getInstance()->addJs('/bitrix/js/local.fwink/client/client.js');
        Asset::getInstance()->addCss('/bitrix/js/local.fwink/client/style.css');

        $wrapper = $this->dom->createElement('div');
        $companySelect = $this->getSelectCompany();
        $wrapper->appendChild($companySelect);
        $companySelect = $this->getSelectClient();
        $wrapper->appendChild($companySelect);

        return $wrapper;
    }

    private function getSelectCompany(): DOMElement
    {
        $value = $this->value;
        $value['COMPANY'] = $this->convertToUtf($value['COMPANY']);

        $wrapper = $this->dom->createElement('div');
        $wrapper->setAttribute('class', 'select-company');
        $optionListForCompany = $this->getOptionList($value['COMPANY']);
        $companySelect = $this->dom->createElement('select');
        if (count($optionListForCompany) > 0) {
            foreach ($optionListForCompany as $option) {
                $companySelect->appendChild($option);
            }
        }

        $companySelect->setAttribute('name', 'COMPANY_ID');
        $companySelect->setAttribute('class', 'js-select2');
        $companySelect->setAttribute('data-minimum-results-for-search', '10');

        $wrapper->appendChild($companySelect);

        return $wrapper;
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

    private function getSelectClient(): DOMElement
    {
        $value = $this->value;
        $value['CLIENT'] = $this->convertToUtf($value['CLIENT']);

        $wrapper = $this->dom->createElement('div');
        $wrapper->setAttribute('class', 'select-client m-t-5');
        $optionListForClient = $this->getOptionList($value['CLIENT']);
        $clientSelect = $this->dom->createElement('select');
        if (count($optionListForClient) > 0) {
            foreach ($optionListForClient as $option) {
                $clientSelect->appendChild($option);
            }
        }

        $clientSelect->setAttribute('name', 'CLIENT_ID');
        $clientSelect->setAttribute('class', 'js-select2');
        $clientSelect->setAttribute('data-minimum-results-for-search', '10');

        $wrapper->appendChild($clientSelect);

        return $wrapper;
    }

    protected function getDefaultAttributes()
    {
        return [
            'class' => 'select-client-block'
        ];
    }
}
