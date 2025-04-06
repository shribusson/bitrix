<?

namespace Local\Fwink\Fields\Value\Type;

use Local\Fwink\Fields\Value\Base;
use Local\Fwink\Fields\Views\Show\Link;
use Local\Fwink\Fields\Views\ViewInterface;

class Url extends Base
{
    private $urlTemplate = '';
    private $fieldNameForUrlParam;
    private $fieldNameForUrlText;
    private $fieldNameForUrlTitle;
    private $fieldNameForUrlBold;
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
    }

    /**
     * @param ViewInterface $view
     *
     */
    public function setDataToView(ViewInterface $view): void
    {
        if ($view instanceof Link) {
            $value['URL'] = $this->getUrl();
            $value['TEXT'] = $this->getUrlText();
            $value['TITLE'] = $this->getUrlTitle();
            $value['BOLD'] = $this->getUrlBold();
            $value['TARGET_BLANK'] = $this->targetBlank;
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

    public function getUrlBold()
    {
        return $this->getFieldFromRawValue($this->fieldNameForUrlBold);
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
