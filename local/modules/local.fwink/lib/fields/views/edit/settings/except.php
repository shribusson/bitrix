<?

namespace Local\Fwink\Fields\Views\Edit\Settings;

use Bitrix\Main\Context\Culture;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Type\DateTime;
use CJSCore;
use CUtil;
use Local\Fwink\Fields\Views\Base;
use Local\Fwink\Helpers\Encoding as HelpersEncoding;
use DOMElement;

class Except extends Base
{
    private const HOURS_MIN = 0;
    private const HOURS_MAX = 23;
    private const MINUTES_MIN = 0;
    private const MINUTES_MAX = 59;
    private const DEFAULT_SECONDS_START = 36000;
    private const DEFAULT_SECONDS_FINISH = 68400;
    private $dateShort = 'DD.MM.YYYY';

    /**
     * @return DOMElement
     */
    protected function getNode(): DOMElement
    {
        $this->initScript();

        $div = $this->dom->createElement('div');

        $table = $this->getTable();
        $table->appendChild($this->getHeader());

        $data = $this->getData();
        foreach ($data as $value) {
            $table->appendChild($value);
        }
        $table->appendChild($this->getRowHidden());

        $div->appendChild($table);
        $div->appendChild($this->getButton());

        return $div;
    }

    /**
     *
     */
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

        Asset::getInstance()->addJs('/bitrix/js/local.fwink/switchery/js/switchery.js');
        Asset::getInstance()->addCss('/bitrix/js/local.fwink/switchery/css/switchery.css');

        Asset::getInstance()->addCss('/bitrix/css/local.fwink/selecttime.css');
    }

    /**
     * @return DOMElement
     */
    private function getTable(): DOMElement
    {
        $wrapper = $this->dom->createElement('table');
        $wrapper->setAttribute('class', 'helpdesk_select_time-table except');

        return $wrapper;
    }

    /**
     * @return DOMElement
     */
    private function getHeader(): DOMElement
    {
        $tr = $this->getTr();
        $row = [
            $this->getTitle(Loc::getMessage('LOCAL_FIELDS_VIEWS_EDIT_EXCEPT_ACTIVE'), 'active'),
            $this->getTitle(Loc::getMessage('LOCAL_FIELDS_VIEWS_EDIT_EXCEPT_DATE')),
            $this->getTitle(Loc::getMessage('LOCAL_FIELDS_VIEWS_EDIT_EXCEPT_START'), 'time'),
            $this->getTitle(Loc::getMessage('LOCAL_FIELDS_VIEWS_EDIT_EXCEPT_FINISH'), 'time'),
            $this->getTitle(Loc::getMessage('LOCAL_FIELDS_VIEWS_EDIT_EXCEPT_ACTION'), 'action'),
        ];

        foreach ($row as $value) {
            $tr->appendChild($value);
        }

        return $tr;
    }

    /**
     * @return DOMElement
     */
    private function getTr(): DOMElement
    {
        return $this->dom->createElement('tr');
    }

    /**
     * @param string $title
     * @param string $class
     *
     * @return DOMElement
     */
    private function getTitle(string $title, string $class = ''): DOMElement
    {
        $th = $this->dom->createElement('th', HelpersEncoding::toUtf($title));
        $th->setAttribute('class', $class);

        return $th;
    }

    /**
     * @return array
     */
    private function getData(): array
    {
        $data = [];
        foreach ($this->value as $value) {
            $tr = $this->getTr();
            /** @var DateTime $date */
            $date = $value['DATE'];
            $date = $date->toString(new Culture(['FORMAT_DATETIME' => $this->dateShort]));
            $row = [
                $this->getCheckbox($this->getNameElement('ACTIVE', $value['ID']), (bool)$value['ACTIVE']),
                $this->getDate($this->getNameElement('DATE', $value['ID']), $date, (bool)$value['ACTIVE']),
                $this->getTimeStart($value['ID'], $value['START']),
                $this->getTimeFinish($value['ID'], $value['FINISH']),
                $this->getDelete(),
            ];
            foreach ($row as $item) {
                $tr->appendChild($item);
            }
            $data[] = $tr;
        }

        return $data;
    }

    /**
     * @param string $name
     * @param bool $checked
     *
     * @return DOMElement
     */
    private function getCheckbox(string $name, bool $checked = true): DOMElement
    {
        $td = $this->dom->createElement('td');
        $input = $this->dom->createElement('input');
        $input->setAttribute('type', 'checkbox');
        $input->setAttribute('name', $name);
        $input->setAttribute('class', 'js-single');
        if ($checked) {
            $input->setAttribute('checked', $checked);
        }
        $td->appendChild($input);

        return $td;
    }

    /**
     * @param int $id
     * @param string $name
     *
     * @return string
     */
    private function getNameElement(string $name, int $id = 0): string
    {
        if ($id === 0) {
            return "EXCEPT_NEW[$id][$name]";
        }

        return "EXCEPT_UPDATE[$id][$name]";
    }

    /**
     * @param string $name
     * @param string $date
     * @param bool $active
     *
     * @return DOMElement
     */
    private function getDate(string $name, string $date = '', bool $active = true): DOMElement
    {
        $td = $this->dom->createElement('td');
        $input = $this->dom->createElement('input');
        $input->setAttribute('type', 'datetime');
        $input->setAttribute('name', $name);
        $input->setAttribute('value', $date);
        $input->setAttribute('class', 'helpdesk_form-control');
        $input->setAttribute('readonly', 'readonly');
        $input->setAttribute('onclick', 'BX.calendar({node: this, field: this, bTime: false, bHideTime: true});');
        $td->appendChild($input);

        if ($active) {
            $title = HelpersEncoding::toUtf(Loc::getMessage('LOCAL_FIELDS_VIEWS_EDIT_EXCEPT_ACTIVE_1'));
        } else {
            $title = HelpersEncoding::toUtf(Loc::getMessage('LOCAL_FIELDS_VIEWS_EDIT_EXCEPT_ACTIVE_0'));
        }
        $span = $this->dom->createElement('span', $title);
        $span->setAttribute('class', 'small');
        $td->appendChild($span);

        return $td;
    }

    /**
     * @param int $id
     * @param int $value
     *
     * @return DOMElement
     */
    private function getTimeStart(int $id, int $value): DOMElement
    {
        $td = $this->dom->createElement('td');

        $wrapper = $this->dom->createElement('div');
        $wrapper->setAttribute('class', 'helpdesk_select_time-item');

        $hour = $this->getHours(
            $this->getValueTime($value)['HOURS'],
            $this->getNameElement('HOURS_START', $id)
        );
        $wrapper->appendChild($hour);

        $separator = $this->getSeparator();
        $wrapper->appendChild($separator);

        $minutes = $this->getMinutes(
            $this->getValueTime($value)['MINUTES'],
            $this->getNameElement('MINUTES_START', $id)
        );
        $wrapper->appendChild($minutes);
        $td->appendChild($wrapper);

        return $td;
    }

    /**
     * @param int $value
     * @param string $name
     *
     * @return DOMElement
     */
    private function getHours(int $value, string $name): DOMElement
    {
        $select = $this->dom->createElement('select');
        $select->setAttribute('class', 'js-select2');
        $select->setAttribute('data-minimum-results-for-search', 'Infinity');
        $select->setAttribute('name', $name);

        $values = [];
        foreach (range(self::HOURS_MIN, self::HOURS_MAX) as $number) {
            $values[] = [
                'TEXT' => str_pad($number, 2, '0', STR_PAD_LEFT),
                'VALUE' => $number,
                'SELECTED' => $value === $number
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

    /**
     * @param $value
     *
     * @return array
     */
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
     * @param string $value
     *
     * @return array
     */
    private function getValueTime(string $value): array
    {
        $hours = floor($value / 3600);
        $minutes = ($value % 3600) / 60;

        return [
            'HOURS' => $hours,
            'MINUTES' => $minutes
        ];
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
     * @param int $value
     * @param string $name
     *
     * @return DOMElement
     */
    private function getMinutes(int $value, string $name): DOMElement
    {
        $select = $this->dom->createElement('select');
        $select->setAttribute('class', 'js-select2');
        $select->setAttribute('data-minimum-results-for-search', 'Infinity');
        $select->setAttribute('name', $name);

        $values = [];
        foreach (range(self::MINUTES_MIN, self::MINUTES_MAX) as $number) {
            $values[] = [
                'TEXT' => str_pad($number, 2, '0', STR_PAD_LEFT),
                'VALUE' => $number,
                'SELECTED' => $value === $number
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

    /**
     * @param int $id
     * @param int $value
     *
     * @return DOMElement
     */
    private function getTimeFinish(int $id, int $value): DOMElement
    {
        $td = $this->dom->createElement('td');

        $wrapper = $this->dom->createElement('div');
        $wrapper->setAttribute('class', 'helpdesk_select_time-item');

        $hour = $this->getHours(
            $this->getValueTime($value)['HOURS'],
            $this->getNameElement('HOURS_FINISH', $id)
        );
        $wrapper->appendChild($hour);

        $separator = $this->getSeparator();
        $wrapper->appendChild($separator);

        $minutes = $this->getMinutes(
            $this->getValueTime($value)['MINUTES'],
            $this->getNameElement('MINUTES_FINISH', $id)
        );
        $wrapper->appendChild($minutes);
        $td->appendChild($wrapper);

        return $td;
    }

    /**
     * @return DOMElement
     */
    private function getDelete(): DOMElement
    {
        $td = $this->dom->createElement('td');
        $td->setAttribute('class', 'remove');

        $span = $this->dom->createElement('span');
        $span->setAttribute('class', 'main-grid-buttons icon remove');
        $span->setAttribute('onclick', 'BX.HelpsDesk.Settings.TicketSlaSchedule.deleteExcept(this)');

        $td->appendChild($span);

        return $td;
    }

    /**
     * @return DOMElement
     */
    private function getRowHidden(): DOMElement
    {
        $tr = $this->getTr();
        $tr->setAttribute('class', 'hidden_add');
        $row = [
            $this->getCheckbox($this->getNameElement('ACTIVE')),
            $this->getDate($this->getNameElement('DATE')),
            $this->getTimeStart(0, self::DEFAULT_SECONDS_START),
            $this->getTimeFinish(0, self::DEFAULT_SECONDS_FINISH),
            $this->getDelete(),
        ];
        foreach ($row as $item) {
            $tr->appendChild($item);
        }

        return $tr;
    }

    /**
     * @return DOMElement
     */
    private function getButton(): DOMElement
    {
        $div = $this->dom->createElement('div');
        $div->setAttribute('class', 'add');

        $button = $this->dom->createElement('button', HelpersEncoding::toUtf(
            Loc::getMessage('LOCAL_FIELDS_VIEWS_EDIT_EXCEPT_ADD')
        ));
        $button->setAttribute('type', 'button');
        $button->setAttribute('class', 'ui-btn ui-btn-primary');
        $button->setAttribute('onclick', 'BX.HelpsDesk.Settings.TicketSlaSchedule.addExcept()');

        $div->appendChild($button);

        return $div;
    }

    /**
     * @return array
     */
    protected function getDefaultAttributes(): array
    {
        return [
            'class' => 'helpdesk_select_time'
        ];
    }
}
