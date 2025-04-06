<?

namespace Local\Fwink\Fields;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Localization\Loc;

/**
 * Класс помошник для работы со списками
 *
 * Class GridList
 *
 * @package Local\Fwink\Fields
 */
class GridList
{
    private $showList = false;
    private $sort;
    private $filter;
    private $width;

    /**
     * FieldInfo constructor.
     *
     * @param array $params
     * @param string $fieldName
     *
     * @throws ArgumentException
     */
    public function __construct($params = [], string $fieldName = '')
    {
        if ($params['SHOW']) {
            $this->setShow($params['SHOW']);
        }

        if ($params['WIDTH']) {
            $this->setWidth($params['WIDTH']);
        }

        if ($this->isShow()) {
            if ($this->isValidParams($params)) {
                if ($params['SORT']) {
                    $this->setSort($params['SORT']);
                }
                if ($params['FILTER']) {
                    $this->setFilter($params['FILTER']);
                }
            } else {
                throw new ArgumentException(
                    Loc::getMessage(
                        'LOCAL_FIELDS_GRID_LIST_INVALID_FIELD',
                        [
                            '#FIELD_NAME#' => $fieldName,
                            '#NAMESPACE#' => __NAMESPACE__
                        ]
                    )
                );
            }
        }
    }

    private function setShow(bool $value): void
    {
        $this->showList = $value;
    }

    /**
     * @param mixed $width
     */
    private function setWidth($width): void
    {
        $this->width = $width;
    }

    public function isShow(): bool
    {
        return $this->showList;
    }

    /**
     * @param array $params
     *
     * @return bool
     */
    private function isValidParams($params = []): bool
    {
        return isset($params['SORT']);
    }

    /**
     * @param mixed $sort
     */
    private function setSort($sort): void
    {
        $this->sort = $sort;
    }

    /**
     * @param mixed $filter
     */
    private function setFilter($filter): void
    {
        $this->filter = $filter;
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function getSort($key)
    {
        return $this->sort[$key] ?: '';
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function getFilter($key)
    {
        return $this->filter[$key] ?: '';
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->width;
    }
}
