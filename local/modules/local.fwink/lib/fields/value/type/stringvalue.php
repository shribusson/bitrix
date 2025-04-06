<?

namespace Local\Fwink\Fields\Value\Type;

use Local\Fwink\Fields\Value\Base;
use Local\Fwink\Fields\Views\ViewInterface;

class StringValue extends Base
{
    private $fieldNameForContent;

    public function __construct(array $fieldNames)
    {
        parent::__construct($fieldNames);

        if ($fieldNames['CONTENT']) {
            $this->fieldNameForContent = $fieldNames['CONTENT'];
        }
    }

    /**
     * @param ViewInterface $view
     */
    public function setDataToView(ViewInterface $view): void
    {
        $value = $this->getContent();

        if (is_array($value)) {
            $value = $this->convertToUtf($value);
        }

        $view->setValue($value);
    }

    public function getContent()
    {
        $content = $this->getFieldFromRawValue($this->fieldNameForContent);
        $content = ($content ?? '');

        return $content;
    }
}
