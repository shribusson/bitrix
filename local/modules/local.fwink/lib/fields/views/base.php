<?

namespace Local\Fwink\Fields\Views;

use Local\Fwink\Fields\FieldInfo;
use Local\Fwink\Helpers\Encoding as HelpersEncoding;
use DOMAttr;
use DOMDocument;
use DOMNode;

abstract class Base implements ViewInterface
{
    protected $field;
    protected $html = '';
    protected $htmlTitle = '';
    protected $attributes = [];

    protected $dom;
    protected $value;
    protected $parameters;
    private $defaultAttributes = [];

    public function __construct(array $parameters = [])
    {
        $this->dom = new DOMDocument();
        $this->parameters = $parameters;
    }

    /**
     * @param FieldInfo $field
     */
    public function setField(FieldInfo $field): void
    {
        $this->field = $field;
    }

    /**
     * Установить значение.
     *
     * @param $value
     */
    public function setValue($value): void
    {
        $this->value = $value ?: '';
    }

    /**
     * Добавить аттрибуты.
     *
     * @param array $attributes
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    /**
     * Получить HTML.
     *
     * @return string
     */
    public function getHtml(): string
    {
        $this->buildHtml();

        return HelpersEncoding::fromUtf($this->html);
    }

    /**
     * Сборка HTML.
     */
    private function buildHtml(): void
    {
        $domNode = $this->getNode();

        $this->dom->appendChild($domNode);
        $this->defaultAttributes = $this->getDefaultAttributes();
        $this->addAttributesToNode();

        $this->html = html_entity_decode($this->dom->saveHTML());

        $this->dom->removeChild($domNode);
    }

    /**
     * Создание элемента.
     *
     * @return mixed
     */
    abstract protected function getNode();

    /**
     * Установка атрибутов элемента по умолчанию.
     *
     * @return mixed
     */
    abstract protected function getDefaultAttributes();

    /**
     * Добавление новых атрибутов в элемент.
     */
    private function addAttributesToNode(): void
    {
        $attributes = $this->mergeAttributes($this->defaultAttributes, $this->attributes);
        foreach ($attributes as $attributeName => $attributeValue) {
            $this->dom->documentElement->setAttributeNode(new DOMAttr($attributeName, $attributeValue));
        }
    }

    /**
     * Соединение атрибутов.
     *
     * @param $attributes1
     * @param $attributes2
     *
     * @return array
     */
    private function mergeAttributes($attributes1, $attributes2): array
    {
        $attributes = array_merge($attributes1, $attributes2);

        foreach ($attributes as $attributeName => $attributeValue) {
            $attribute1Value = $attributes1[$attributeName];
            $attribute2Value = $attributes2[$attributeName];
            if ($attribute1Value !== null && $attribute2Value !== null) {
                $attributes[$attributeName] = $attribute1Value . ' ' . $attribute2Value;
            }
        }

        return $attributes;
    }

    /**
     * @return DOMDocument
     */
    public function getDom(): DOMDocument
    {
        return $this->dom;
    }

    protected function appendHTML(DOMNode $parent, $source): DOMNode
    {
        $tmpDoc = new DOMDocument();
        $tmpDoc->loadHTML('<?xml encoding="UTF-8">' . $source);
        foreach ($tmpDoc->getElementsByTagName('body')->item(0)->childNodes as $node) {
            $node = $parent->ownerDocument->importNode($node, true);
            $parent->appendChild($node);
        }

        return $parent;
    }

    protected function convertToUtf($rows)
    {
        if (is_array($rows)) {
            foreach ($rows as &$row) {
                foreach ($row as &$value) {
                    if (!is_array($value)) {
                        $value = HelpersEncoding::toUtf($value);
                    }
                }
            }
        }

        return $rows;
    }
}
