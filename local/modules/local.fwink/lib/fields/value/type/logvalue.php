<?

namespace Local\Fwink\Fields\Value\Type;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\SystemException;
use Local\Fwink\Fields\Value\Base;
use Local\Fwink\Fields\Views\ViewInterface;
use Local\Fwink\Helpers\Folder as HelpersFolder;

class LogValue extends Base
{
    private $entityElement;

    /**
     * @param ViewInterface $view
     *
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws SystemException
     */
    public function setDataToView(ViewInterface $view): void
    {
        $rawValue = $this->getRaw();
        $this->entityElement = $rawValue['ENTITY_ELEMENT'];
        $value = [
            'OPERATION' => $rawValue['OPERATION'],
            'OLD' => $this->getValue($rawValue['OLD']),
            'NEW' => $this->getValue($rawValue['NEW'])
        ];

        $view->setValue($value);
    }

    /**
     * @param $parameters
     *
     * @return array
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws SystemException
     */
    private function getValue($parameters): array
    {
        $parameters = is_array($parameters) ? $this->convertToUtf($parameters) : $parameters;
        $parameters['VALUE'] = strip_tags($parameters['VALUE']);

        $checkData = $parameters['ID'] && $parameters['ENTITY'] && $parameters['VALUE'];
        $checkEntity = $this->entityElement !== $parameters['ENTITY'];

        if ($checkData && $checkEntity) {
            $result = [
                'URL' => $this->getUrl($parameters['ENTITY'], $parameters['ID']),
                'TEXT' => $parameters['VALUE'],
                'TITLE' => $parameters['VALUE']
            ];
        } else {
            $result = [
                'TEXT' => $parameters['VALUE'] ?: '[---]',
            ];
        }

        return $result;
    }

    /**
     * @param $entityName
     * @param $id
     *
     * @return string
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws SystemException
     */
    private function getUrl($entityName, $id): string
    {
        return HelpersFolder::getPathEntity($entityName, $id);
    }
}
