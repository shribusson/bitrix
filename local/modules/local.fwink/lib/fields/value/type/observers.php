<?

namespace Local\Fwink\Fields\Value\Type;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use CFile;
use Local\Fwink\AccessControl\Exception;
use Local\Fwink\Fields\Value\Base;
use Local\Fwink\Fields\Views\ViewInterface;
use Local\Fwink\Helpers\User as HelpersUser;
use Local\Fwink\TicketObservers;

class Observers extends Base
{
    private $urlTemplate = '';
    private $showUrl = false;

    /**
     * @return mixed|string
     */
    public function getShortUserName()
    {
        $userInfo = $this->getUserInfo();

        return HelpersUser::getShortUserName($userInfo);
    }

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
     * @throws Exception
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function setDataToView(ViewInterface $view): void
    {
        $rawValue = $this->getRaw();
        $value = $this->getList($rawValue);
        $view->setValue($value);
    }

    /**
     * @param $rawValue
     *
     * @return array
     * @throws ArgumentException
     * @throws Exception
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getList($rawValue): array
    {
        $rows = [];
        $parameters = [
            'select' => [
                'USER_ID',
                'USER_LOGIN' => 'USER.LOGIN',
                'USER_NAME' => 'USER.NAME',
                'USER_LAST_NAME' => 'USER.LAST_NAME',
                'USER_PERSONAL_PHOTO' => 'USER.PERSONAL_PHOTO',
                'USER_WORK_POSITION' => 'USER.WORK_POSITION'
            ],
            'filter' => [
                'TICKET_ID' => $rawValue
            ]
        ];

        $result = (new TicketObservers())->getList($parameters);
        while ($row = $result->fetch()) {
            $row = $this->convertToUtf($row);
            $userInfo = [
                'LOGIN' => $row['USER_LOGIN'],
                'NAME' => $row['USER_NAME'],
                'LAST_NAME' => $row['USER_LAST_NAME']
            ];
            $rows[] = [
                'URL' => $this->getUrl($row['USER_ID']),
                'TEXT' => $this->getUserName($userInfo),
                'TITLE' => $this->getUserName($userInfo),
                'PHOTO' => $this->getUserPhoto($row['USER_PERSONAL_PHOTO']),
                'POSITION' => $row['USER_WORK_POSITION']
            ];
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
            return str_replace('#URL_PARAM#', $userId, $this->urlTemplate);
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

    /**
     * @param $userInfo
     *
     * @return mixed|string
     */
    public function getUserName($userInfo)
    {
        return HelpersUser::getUserName($userInfo);
    }

    /**
     * @param $photoId
     *
     * @return string
     */
    public function getUserPhoto($photoId): string
    {
        if ($photoId !== false) {
            $arFileTmp = CFile::ResizeImageGet(
                $photoId,
                ['width' => 100, 'height' => 100],
                BX_RESIZE_IMAGE_EXACT,
                true,
                ''
            );
        }

        return $arFileTmp['src'] ?? '';
    }
}
