<?

namespace Local\Fwink\Fields\Views\Edit\Settings;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use CJSCore;
use CUtil;
use Local\Fwink\Fields\Views\Base;
use Local\Fwink\Helpers\Encoding as HelpersEncoding;
use DOMElement;

class WeekDay extends Base
{
    private const WEEK_DAY_START = 1;
    private const WEEK_DAY_FINISH = 7;
    private const HOURS_MIN = 0;
    private const HOURS_MAX = 23;
    private const MINUTES_MIN = 0;
    private const MINUTES_MAX = 59;
    private const DEFAULT_HOUR_START = 9;
    private const DEFAULT_HOUR_FINISH = 18;

    protected function getNode()
    {
        $this->initScript();

        $table = $this->getTable();
        $table->appendChild($this->getHeader());

        $data = $this->getWeekday();
        foreach ($data as $value) {
            $table->appendChild($value);
        }

        return $table;
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

        Asset::getInstance()->addJs('/bitrix/js/local.fwink/switchery/js/switchery.js');
        Asset::getInstance()->addCss('/bitrix/js/local.fwink/switchery/css/switchery.css');

        Asset::getInstance()->addCss('/bitrix/css/local.fwink/selecttime.css');
    }

    private function getTable(): DOMElement
    {
        $wrapper = $this->dom->createElement('table');
        $wrapper->setAttribute('class', 'helpdesk_select_time-table');

        return $wrapper;
    }

    private function getHeader(): DOMElement
    {
        $tr = $this->getTr();
        $row = [
            $this->getTitle(Loc::getMessage('LOCAL_FIELDS_VIEWS_EDIT_WEEK_DAY_ACTIVE'), 'active'),
            $this->getTitle(Loc::getMessage('LOCAL_FIELDS_VIEWS_EDIT_WEEK_DAY')),
            $this->getTitle(Loc::getMessage('LOCAL_FIELDS_VIEWS_EDIT_WEEK_DAY_START'), 'time'),
            $this->getTitle(Loc::getMessage('LOCAL_FIELDS_VIEWS_EDIT_WEEK_DAY_FINISH'), 'time'),
        ];

        foreach ($row as $value) {
            $tr->appendChild($value);
        }

        return $tr;
    }

    private function getTr(): DOMElement
    {
        return $this->dom->createElement('tr');
    }

    private function getTitle(string $title, string $class = ''): DOMElement
    {
        $th = $this->dom->createElement('th', HelpersEncoding::toUtf($title));
        $th->setAttribute('class', $class);

        return $th;
    }

    private function getWeekday(): array
    {
        $data = [];
        foreach (range(self::WEEK_DAY_START, self::WEEK_DAY_FINISH) as $number) {
            $number = ((int)$number === 7) ? 0 : $number;
            $tr = $this->getTr();
            $row = [
                $this->getSwitchery('WORKTIME[' . $number . '][ACTIVE]', $this->getValueActive($number)),
                $this->dom->createElement(
                    'td',
                    HelpersEncoding::toUtf(Loc::getMessage('LOCAL_FIELDS_VIEWS_EDIT_WEEK_DAY_' . $number))
                ),
                $this->getTimeStart($number),
                $this->getTimeFinish($number)
            ];
            foreach ($row as $value) {
                $tr->appendChild($value);
            }
            $data[] = $tr;
        }

        return $data;
    }

    private function getSwitchery(string $name, bool $checked = false): DOMElement
    {
        $td = $this->dom->createElement('td');
        $input = $this->dom->createElement('input');
        $input->setAttribute('type', 'checkbox');
        $input->setAttribute('name', $name);
        $input->setAttribute('id', $name);
        $input->setAttribute('class', 'js-single');
        if ($checked) {
            $input->setAttribute('checked', $checked);
        }
        $td->appendChild($input);

        return $td;
    }

    private function getValueActive(int $weekDayId): bool
    {
        if ($this->value && is_array($this->value)) {
            foreach ($this->value as $value) {
                if ((int)$value['WEEK_DAY_ID'] === $weekDayId) {
                    return (bool)$value['ACTIVE'];
                }
            }

            return false;
        }

        return ($weekDayId <= 5 && $weekDayId !== 0);
    }

    private function getTimeStart(int $weekDayId): DOMElement
    {
        $td = $this->dom->createElement('td');

        $wrapper = $this->dom->createElement('div');
        $wrapper->setAttribute('class', 'helpdesk_select_time-item');

        $hour = $this->getHours($this->getValueTime($weekDayId, 'START')['HOURS'], 'START', $weekDayId);
        $wrapper->appendChild($hour);

        $separator = $this->getSeparator();
        $wrapper->appendChild($separator);

        $minutes = $this->getMinutes($this->getValueTime($weekDayId, 'START')['MINUTES'], 'START', $weekDayId);
        $wrapper->appendChild($minutes);
        $td->appendChild($wrapper);

        return $td;
    }

    private function getHours(int $default, string $name, int $weekDayId): DOMElement
    {
        $select = $this->dom->createElement('select');
        $select->setAttribute('class', 'js-select2');
        $select->setAttribute('data-minimum-results-for-search', 'Infinity');
        $select->setAttribute('name', 'WORKTIME[' . $weekDayId . '][HOURS][' . $name . ']');

        $values = [];
        foreach (range(self::HOURS_MIN, self::HOURS_MAX) as $number) {
            $values[] = [
                'TEXT' => str_pad($number, 2, '0', STR_PAD_LEFT),
                'VALUE' => $number,
                'SELECTED' => $default === $number
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

    private function getValueTime(int $weekDayId, string $type): array
    {
        $hours = 0;
        $minutes = 0;
        switch ($type) {
            case 'START':
                if ($this->value && is_array($this->value)) {
                    foreach ($this->value as $value) {
                        if ((int)$value['WEEK_DAY_ID'] === $weekDayId) {
                            $hours = floor($value['START'] / 3600);
                            $minutes = ($value['START'] % 3600) / 60;
                        }
                    }
                } else {
                    $hours = self::DEFAULT_HOUR_START;
                }
                break;
            case 'FINISH':
                if ($this->value && is_array($this->value)) {
                    foreach ($this->value as $value) {
                        if ((int)$value['WEEK_DAY_ID'] === $weekDayId) {
                            $hours = floor($value['FINISH'] / 3600);
                            $minutes = ($value['FINISH'] % 3600) / 60;
                        }
                    }
                } else {
                    $hours = self::DEFAULT_HOUR_FINISH;
                }
                break;
        }

        return [
            'HOURS' => $hours,
            'MINUTES' => $minutes
        ];
    }

    private function getSeparator(): DOMElement
    {
        $span = $this->dom->createElement('span', ':');
        $span->setAttribute('class', 'separator');

        return $span;
    }

    private function getMinutes(int $default, string $name, int $weekDayId): DOMElement
    {
        $select = $this->dom->createElement('select');
        $select->setAttribute('class', 'js-select2');
        $select->setAttribute('data-minimum-results-for-search', 'Infinity');
        $select->setAttribute('name', 'WORKTIME[' . $weekDayId . '][MINUTES][' . $name . ']');

        $values = [];
        foreach (range(self::MINUTES_MIN, self::MINUTES_MAX) as $number) {
            $values[] = [
                'TEXT' => str_pad($number, 2, '0', STR_PAD_LEFT),
                'VALUE' => $number,
                'SELECTED' => $default === $number
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

    private function getTimeFinish(int $weekDayId): DOMElement
    {
        $td = $this->dom->createElement('td');

        $wrapper = $this->dom->createElement('div');
        $wrapper->setAttribute('class', 'helpdesk_select_time-item');

        $hour = $this->getHours($this->getValueTime($weekDayId, 'FINISH')['HOURS'], 'FINISH', $weekDayId);
        $wrapper->appendChild($hour);

        $separator = $this->getSeparator();
        $wrapper->appendChild($separator);

        $minutes = $this->getMinutes($this->getValueTime($weekDayId, 'FINISH')['MINUTES'], 'FINISH', $weekDayId);
        $wrapper->appendChild($minutes);
        $td->appendChild($wrapper);

        return $td;
    }

    protected function getDefaultAttributes()
    {
        return [
            'class' => 'helpdesk_select_time'
        ];
    }
}
