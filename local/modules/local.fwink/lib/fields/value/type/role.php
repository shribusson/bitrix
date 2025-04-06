<?

namespace Local\Fwink\Fields\Value\Type;

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Local\Fwink\Fields\Value\Base;
use Local\Fwink\Fields\Views\ViewInterface;
use Local\Fwink\Helpers\Encoding as HelpersEncoding;

class Role extends Base
{
    /**
     * @param ViewInterface $view
     *
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public function setDataToView(ViewInterface $view): void
    {
        $rawValue = $this->getRaw();
        $groups = \Local\Fwink\Helpers\User::getUserRole($rawValue['ID']);
        $groups = $this->convertToUtf($groups);

        $view->setValue($groups);
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
