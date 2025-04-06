<?

namespace Local\Fwink\Fields\Value\Type;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserTable;
use CCrmViewHelper;
use CUser;
use Local\Fwink\Fields\Value\Base;
use Local\Fwink\Fields\Views\ViewInterface;
use Local\Fwink\Helpers\User as HelpersUser;

/**
 * Когда нельзя получить значения полей из SELECT и известен только ID пользователя.
 */
class Staff extends Base
{
    private $urlTemplate = '';
    private $showUrl = false;

    /**
     * @param $urlTemplate
     */
    public function setUrlTemplate($urlTemplate): void
    {
        $this->urlTemplate = $urlTemplate;
    }

    /**
     * @param ViewInterface $view
     *
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws SystemException
     */
    public function setDataToView(ViewInterface $view): void
    {
        $rawValue = $this->getRaw();

        if ($rawValue && CUser::GetByID($rawValue)->Fetch()) {
            $value = CCrmViewHelper::PrepareUserBaloonHtml([
                'PREFIX' => 'ROW_' . $rawValue . '_USER',
                'USER_ID' => $rawValue,
                'USER_NAME' => HelpersUser::formatName($this->getUserInfo($rawValue), '#LAST_NAME# #NAME#'),
                'USER_PROFILE_URL' => $this->getUrl($rawValue)
            ]);
        } else {
            $value = '[---]';
        }

        $view->setValue($value);
    }

    /**
     * @param $rawValue
     *
     * @return array
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws SystemException
     */
    private function getUserInfo($rawValue): array
    {
        return $this->getList($rawValue);
    }

    /**
     * @param $rawValue
     *
     * @return array
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws SystemException
     */
    public function getList($rawValue): array
    {
        $rows = [];
        $parameters = [
            'filter' => [
                'Bitrix\Main\UserGroupTable:USER.GROUP_ID' => HelpersUser::getGroupsOfStaff(),
                'ID' => $rawValue
            ],
            'select' => [
                'ID',
                'LOGIN',
                'NAME',
                'LAST_NAME'
            ]
        ];
        $result = UserTable::getList($parameters);
        while ($row = $result->fetch()) {
            $rows = $this->convertToUtf($row);
        }

        return $rows;
    }

    /**
     * @param $userId
     *
     * @return string
     */
    public function getUrl($userId): string
    {
        if ($this->isShowUrl()) {
            $urlParam = $this->getFieldFromRawValue($userId);
            return str_replace('#URL_PARAM#', $urlParam, $this->urlTemplate);
        }
        return '';
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
}
