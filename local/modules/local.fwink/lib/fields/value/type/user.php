<?

namespace Local\Fwink\Fields\Value\Type;

use CUser;
use Local\Fwink\Fields\Value\Base;
use Local\Fwink\Fields\Views\ViewInterface;
use Local\Fwink\Helpers\User as HelpersUser;

class User extends Base
{
    private $urlTemplate = '';
    private $fieldNameForUserId;
    private $fieldNameForUserName;
    private $fieldNameForUserLastName;
    private $fieldNameForUserLogin;
    private $fieldNameForRowId;
    private $showUrl = false;

    public function __construct($fieldNames)
    {
        parent::__construct($fieldNames);

        if ($fieldNames['ROW_ID']) {
            $this->fieldNameForRowId = $fieldNames['USER_ID'];
        }

        if ($fieldNames['USER_ID']) {
            $this->fieldNameForUserId = $fieldNames['USER_ID'];
        }

        if ($fieldNames['USER_NAME']) {
            $this->fieldNameForUserName = $fieldNames['USER_NAME'];
        }

        if ($fieldNames['USER_LAST_NAME']) {
            $this->fieldNameForUserLastName = $fieldNames['USER_LAST_NAME'];
        }

        if ($fieldNames['USER_LOGIN']) {
            $this->fieldNameForUserLogin = $fieldNames['USER_LOGIN'];
        }
    }

    public function setDataToView(ViewInterface $view): void
    {

        $rowId = $this->getFieldFromRawValue($this->fieldNameForRowId);
        $userId = $this->get();

        if ($this->isShowUrl()) {
            if ($userId && CUser::GetByID($userId)->Fetch()) {
                $value = $this->prepareUserBaloonHtml([
                    'PREFIX' => 'ROW_' . $rowId . '_USER',
                    'USER_ID' => $userId,
                    'USER_NAME' => HelpersUser::formatName($this->getUserInfo(), '#LAST_NAME# #NAME#'),
                    'USER_PROFILE_URL' => $this->getUrl()
                ]);
            } else {
                $value = '[---]';
            }
        } else {
            $value = HelpersUser::formatName($this->getUserInfo(), '#LAST_NAME# #NAME#');
        }

        $view->setValue($value);
    }

    /**
     * @return bool
     */
    public function isShowUrl(): bool
    {
        return $this->showUrl;
    }

    /**
     * @param bool $showUrl
     */
    public function setShowUrl(bool $showUrl): void
    {
        $this->showUrl = $showUrl;
    }

    /**
     * @param $arParams
     *
     * @return string
     */
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
        bx-tooltip-user-id="' . $userID . '">' . $userName . '</a>';
    }

    private function getUserInfo(): array
    {
        return [
            'ID' => $this->getFieldFromRawValue($this->fieldNameForUserId),
            'NAME' => $this->getFieldFromRawValue($this->fieldNameForUserName),
            'LAST_NAME' => $this->getFieldFromRawValue($this->fieldNameForUserLastName),
            'LOGIN' => $this->getFieldFromRawValue($this->fieldNameForUserLogin)
        ];
    }

    public function getUrl(): string
    {
        $urlParam = $this->getFieldFromRawValue($this->fieldNameForUserId);
        return str_replace('#URL_PARAM#', $urlParam, $this->urlTemplate);
    }

    public function setUrlTemplate($urlTemplate): void
    {
        $this->urlTemplate = $urlTemplate;
    }
}
