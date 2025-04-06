<?

namespace Local\Fwink\Fields\Value\Type;

use Bitrix\Main\Localization\Loc;
use Local\Fwink\Fields\Value\Base;
use Local\Fwink\Fields\Views\Show\Client as ClientView;
use Local\Fwink\Fields\Views\Show\Link;
use Local\Fwink\Fields\Views\ViewInterface;
use Local\Fwink\Helpers\Encoding as HelpersEncoding;
use Local\Fwink\Helpers\User as HelpersUser;

class Client extends Base
{
    private $urlTemplateForUser = '';
    private $urlTemplateForCompany = '';
    private $fieldNameForUserId;
    private $fieldNameForUserName;
    private $fieldNameForUserLastName;
    private $fieldNameForUserLogin;
    private $nameType = 'full';
    private $showUrl = false;

    private $fieldNameForCompanyTitle;
    private $fieldNameForCompanyId;

    public function __construct($fieldNames)
    {
        parent::__construct($fieldNames);

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

        if ($fieldNames['COMPANY_TITLE']) {
            $this->fieldNameForCompanyTitle = $fieldNames['COMPANY_TITLE'];
        }

        if ($fieldNames['COMPANY_ID']) {
            $this->fieldNameForCompanyId = $fieldNames['COMPANY_ID'];
        }
    }

    public function setDataToView(ViewInterface $view): void
    {
        if ($view instanceof ClientView) {
            $value['COMPANY_URL'] = $this->getCompanyUrl();
            $value['COMPANY_TITLE'] = $this->getCompanyTitle();
            $value['USER_URL'] = $this->getUserUrl();
            $value['USER_NAME'] = $this->getUserName();
            $value['USER_FULL_NAME'] = $this->getFullUserName();
        } elseif ($view instanceof Link) {
            $value['URL'] = $this->getUserUrl();
            $value['TEXT'] = $this->getUserName();
            $value['TITLE'] = $this->getFullUserName();
        } else {
            $value = is_int($this->get()) ? $this->getUserName() : $this->get();
        }

        $view->setValue($value);
    }

    public function getCompanyUrl(): string
    {
        if ($this->isShowUrl()) {
            $urlParam = $this->getFieldFromRawValue($this->fieldNameForCompanyId);

            if (empty($urlParam)) {
                $url = '';
            } else {
                $url = $this->urlTemplateForCompany . $urlParam . '/';
            }

            return $url;
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

    public function getCompanyTitle()
    {
        $companyTitle = $this->getFieldFromRawValue($this->fieldNameForCompanyTitle);
        $companyTitle = empty($companyTitle) ? HelpersEncoding::toUtf(
            Loc::getMessage('LOCAL_FIELDS_VALUE_TYPE_NO_COMPANY')
        ) : $companyTitle;

        return $companyTitle;
    }

    public function getUserUrl(): string
    {
        if ($this->isShowUrl()) {
            $urlParam = $this->getFieldFromRawValue($this->fieldNameForUserId);
            return $this->urlTemplateForUser . $urlParam . '/';
        }
        return '';
    }

    public function getUserName()
    {
        $userName = $this->getFullUserName();

        if ($this->nameType === 'short') {
            $userName = $this->getShortUserName();
        }

        return $userName;
    }

    public function getFullUserName()
    {
        $userInfo = $this->getUserInfo();
        return HelpersUser::getUserName($userInfo);
    }

    private function getUserInfo(): array
    {
        return [
            'LOGIN' => $this->getFieldFromRawValue($this->fieldNameForUserLogin),
            'NAME' => $this->getFieldFromRawValue($this->fieldNameForUserName),
            'LAST_NAME' => $this->getFieldFromRawValue($this->fieldNameForUserLastName)
        ];
    }

    public function getShortUserName()
    {
        $userInfo = $this->getUserInfo();
        return HelpersUser::getShortUserName($userInfo);
    }

    public function setUrlTemplateForCompany($urlTemplate): void
    {
        $this->urlTemplateForCompany = $urlTemplate;
    }

    public function setUrlTemplateForUser($urlTemplate): void
    {
        $this->urlTemplateForUser = $urlTemplate;
    }

    public function setNameType($nameType): void
    {
        if (in_array($nameType, ['short', 'full'])) {
            $this->nameType = $nameType;
        }
    }
}
