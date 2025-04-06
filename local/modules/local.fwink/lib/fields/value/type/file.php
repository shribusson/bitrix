<?

namespace Local\Fwink\Fields\Value\Type;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\SystemException;
use Local\Fwink\AccessControl\Exception;
use Local\Fwink\Fields\Value\Base;
use Local\Fwink\Helpers\Comment as HelpersComment;
use Local\Fwink\Helpers\Encoding as HelpersEncoding;

class File extends Base
{
    /**
     * @param $rawValue
     *
     * @return array|mixed|string
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws SystemException
     * @throws Exception
     */
    protected function getMainValue($rawValue)
    {
        $rows = HelpersComment::getFiles($rawValue);
        $rows = $this->convertToUtf($rows);

        return $rows;
    }

    /**
     * @param $rows
     *
     * @return array
     */
    protected function convertToUtf($rows): array
    {
        foreach ($rows as &$row) {
            foreach ($row as &$value) {
                $value = HelpersEncoding::toUtf($value);
            }
        }

        return $rows;
    }
}
