<?

namespace Local\Fwink\Fields;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\ScalarField;
use Bitrix\Main\Localization\Loc;

/**
 * Класс помошник для получения: названия поля, имя поля, обязательность поля
 *
 * Class FieldInfo
 *
 * @package Local\Fwink\Fields
 */
class FieldInfo
{
    private $name;
    private $title;
    private $required;

    /**
     * FieldInfo constructor.
     *
     * @param array $params
     *
     * @throws ArgumentException
     */
    public function __construct(array $params)
    {
        if ($this->isValidParams($params)) {
            $this->name = (string)$params['NAME'];
            $this->title = (string)$params['TITLE'];
            $this->required = (bool)$params['REQUIRED'];
        } else {
            throw new ArgumentException(Loc::getMessage('LOCAL_FIELDS_FIELD_INFO_INVALID_FIELD'));
        }
    }

    /**
     * Проверка на существование переменных.
     *
     * @param array $params
     *
     * @return bool
     */
    private function isValidParams(array $params): bool
    {
    	if(empty($params['TITLE'])&&!empty($params['NAME'])){
			$params['TITLE']=$params['NAME'].'_CSTOM';
		}
        return isset($params['NAME'], $params['TITLE'], $params['REQUIRED']);
    }

    /**
     * Полученией всей информация о свойстве.
     *
     * @param ScalarField $field
     *
     * @return array
     */
    public static function getParametersForFieldInfo(ScalarField $field): array
    {
        return [
            'NAME' => $field->getName(),
            'TITLE' => $field->getTitle(),
            'REQUIRED' => $field->isRequired()
        ];
    }

    /**
     * Получение имени свойства в таблице.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Получение заголовка свойства.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Получение обязательности заполнения поля.
     *
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }
}
