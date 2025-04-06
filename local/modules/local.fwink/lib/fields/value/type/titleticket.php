<?

namespace Local\Fwink\Fields\Value\Type;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Local\Fwink\Comment;
use Local\Fwink\Fields\Value\Base;
use Local\Fwink\Fields\Views\Show\Title;
use Local\Fwink\Fields\Views\ViewInterface;
use Local\Fwink\Helpers\Client as HelpersClient;

class TitleTicket extends Base
{
    private $urlTemplate = '';
    private $fieldNameForUrlParam;
    private $fieldNameForUrlText;
    private $fieldNameForUrlTitle;
    private $fieldNameForUrlBold;
    private $fieldNameForUrlStatus;
    private $showUrl = false;
    private $targetBlank = false;

    public function __construct($fieldNames)
    {
        parent::__construct($fieldNames);

        if ($fieldNames['URL_PARAM']) {
            $this->fieldNameForUrlParam = $fieldNames['URL_PARAM'];
        }

        if ($fieldNames['TEXT']) {
            $this->fieldNameForUrlText = $fieldNames['TEXT'];
        }

        if ($fieldNames['TITLE']) {
            $this->fieldNameForUrlTitle = $fieldNames['TITLE'];
        }

        if ($fieldNames['BOLD']) {
            $this->fieldNameForUrlBold = $fieldNames['BOLD'];
        }

        if ($fieldNames['STATUS']) {
            $this->fieldNameForUrlStatus = $fieldNames['STATUS'];
        }
    }

    /**
     * @param ViewInterface $view
     *
     * @throws ArgumentException
     * @throws SystemException
     */
    public function setDataToView(ViewInterface $view): void
    {
        if ($view instanceof Title) {
            $value['URL'] = $this->getUrl();
            $value['TEXT'] = $this->getUrlText();
            $value['TITLE'] = $this->getUrlTitle();
            $value['BOLD'] = $this->getUrlBold();
            $value['TARGET_BLANK'] = $this->targetBlank;
            $value['CLOSED'] = $this->isClosed();
            $value['COUNT'] = $this->getCount();
        } else {
            $value = $this->getUrlText();
        }

        $view->setValue($value);
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        if ($this->isShowUrl()) {
            $urlParam = $this->getFieldFromRawValue($this->fieldNameForUrlParam);

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

    /**
     * @return mixed|string
     */
    public function getUrlText()
    {
        $urlText = $this->getFieldFromRawValue($this->fieldNameForUrlText);
        $urlText = ($urlText ?? '');

        return $urlText;
    }

    /**
     * @return mixed|string
     */
    public function getUrlTitle()
    {
        $urlTitle = $this->getFieldFromRawValue($this->fieldNameForUrlTitle);
        $urlTitle = ($urlTitle ?? '');

        return $urlTitle;
    }

    /**
     * @return mixed
     */
    public function getUrlBold()
    {
        return $this->getFieldFromRawValue($this->fieldNameForUrlBold);
    }

    /**
     * @return bool
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    private function isClosed(): bool
    {
        $statusId = $this->getStatus();
        $statusFinalId = \Local\Fwink\Helpers\Status::getFinal();

        return $statusId === $statusFinalId;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->getFieldFromRawValue($this->fieldNameForUrlStatus);
    }

    /**
     * @return int
     * @throws ArgumentException
     * @throws SystemException
     */
    private function getCount(): int
    {
        $ticketId = $this->getFieldFromRawValue($this->fieldNameForUrlParam);
        $filter = ['TICKET_ID' => $ticketId];
        if (HelpersClient::isClient()) {
            $filter['PUBLIC'] = true;
        }

        return (new Comment())->getCount($filter);
    }

    /**
     * @param $urlTemplate
     */
    public function setUrlTemplate($urlTemplate): void
    {
        $this->urlTemplate = $urlTemplate;
    }

    /**
     * @param bool $targetBlank
     */
    public function setTargetBlank(bool $targetBlank): void
    {
        $this->targetBlank = $targetBlank;
    }
}
