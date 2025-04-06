<?

namespace Local\Fwink\Fields\Value\Type;

use Local\Fwink\Fields\Value\Base;
use Local\Fwink\Fields\Views\Show\ColoredButton;
use Local\Fwink\Fields\Views\Show\ColoredValue;
use Local\Fwink\Fields\Views\Show\StatusInfo;
use Local\Fwink\Fields\Views\ViewInterface;

class Color extends Base
{
    private $fieldNameForTitle;
    private $fieldNameForColor;
    private $fieldNameForButton;

    public function __construct($fieldNames)
    {
        parent::__construct($fieldNames);

        if ($fieldNames['TITLE']) {
            $this->fieldNameForTitle = $fieldNames['TITLE'];
        }

        if ($fieldNames['COLOR']) {
            $this->fieldNameForColor = $fieldNames['COLOR'];
        }

        if ($fieldNames['BUTTON']) {
            $this->fieldNameForButton = $fieldNames['BUTTON'];
        }
    }

    public function setDataToView(ViewInterface $view): void
    {
        if ($view instanceof ColoredValue || $view instanceof ColoredButton || $view instanceof StatusInfo) {
            $value['TITLE'] = $this->getTitle();
            $value['COLOR'] = $this->getColor();
            $value['BUTTON'] = $this->getButton();
        } else {
            $value = $this->getTitle();
        }

        $view->setValue($value);
    }

    public function getTitle()
    {
        $title = $this->getFieldFromRawValue($this->fieldNameForTitle);
        $title = ($title ?? '');

        return $title;
    }

    public function getColor()
    {
        $color = $this->getFieldFromRawValue($this->fieldNameForColor);
        $color = ($color ?? '');

        return $color;
    }

    public function getButton()
    {
        $button = $this->getFieldFromRawValue($this->fieldNameForButton);
        $button = ($button ?? '');

        return $button;
    }
}
