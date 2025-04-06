<?

namespace Local\Fwink\Fields\Value\Type;

use Bitrix\Main\UserTable;
use Local\Fwink\Fields\Value\Base;
use Local\Fwink\Fields\Views\ViewInterface;
use Local\Fwink\Helpers\Encoding as HelpersEncoding;
use Local\Fwink\Helpers\User as HelpersUser;

class Settings extends Base
{
    private $rawValue;
    private $valueId;

    public function setDataToView(ViewInterface $view): void
    {
        $this->rawValue = $this->getRaw();
        $this->valueId = $this->get();

        switch ($this->rawValue['TYPE']) {
            case 'LIST':
                $value = $this->getDataList();
                break;
            case 'STAFF':
                $value = $this->getDataStaff();
                break;
            case 'OBSERVERS':
                $value = $this->getDataObservers();
                break;
            default:
                $value = '[---]';
                break;
        }

        $view->setValue(HelpersEncoding::toUtf($value));
    }

    private function getDataList(): string
    {
        foreach ($this->rawValue['DATA'] as $key => $title) {
            if ($key === $this->rawValue['VALUE']) {
                return $title;
            }
        }

        return '';
    }

    private function getDataStaff(): string
    {
        $value = '';
        $parameters = [
            'select' => ['ID', 'LOGIN', 'NAME', 'LAST_NAME'],
            'filter' => [
                'ACTIVE' => 'Y',
                'ID' => $this->valueId
            ],
            'cache' => ['ttl' => 86400]
        ];
        $result = UserTable::getList($parameters);
        if ($row = $result->fetch()) {
            $value = $this->PrepareUserBaloonHtml([
                'PREFIX' => 'ROW_' . $row['ID'] . '_USER',
                'USER_ID' => $row['ID'],
                'USER_NAME' => HelpersUser::formatName($row, '#LAST_NAME# #NAME#'),
            ]);
        }

        return $value;
    }

    private function getDataObservers(): string
    {
        $value = [];
        $parameters = [
            'select' => ['ID', 'LOGIN', 'NAME', 'LAST_NAME'],
            'filter' => [
                'ACTIVE' => 'Y',
                'ID' => $this->valueId
            ],
            'cache' => ['ttl' => 86400]
        ];
        $result = UserTable::getList($parameters);
        while ($row = $result->fetch()) {
            $value[] = $this->PrepareUserBaloonHtml([
                'PREFIX' => 'ROW_' . $row['ID'] . '_USER',
                'USER_ID' => $row['ID'],
                'USER_NAME' => HelpersUser::formatName($row, '#LAST_NAME# #NAME#'),
            ]);
        }

        return implode('', $value);
    }

    private function prepareUserBaloonHtml($arParams): string
    {
        if (!is_array($arParams)) {
            return '';
        }

        $prefix = $arParams['PREFIX'] ?? '';
        $userID = isset($arParams['USER_ID']) ? (int)$arParams['USER_ID'] : 0;
        $userName = $arParams['USER_NAME'] ?? "[{$userID}]";
        if (isset($arParams['ENCODE_USER_NAME']) && $arParams['ENCODE_USER_NAME']) {
            $userName = htmlspecialcharsbx($userName);
        }
        $profilePath = $arParams['USER_PROFILE_URL'] ?? '';
        $baloonID = $prefix !== '' ? "BALLOON_{$prefix}_U_{$userID}" : "BALLOON_U_{$userID}";
        return '<a href="' . htmlspecialcharsbx($profilePath) . '" id="' . $baloonID . '"
        bx-tooltip-user-id="' . $userID . '" class="users">' . $userName . '</a>';
    }
}
