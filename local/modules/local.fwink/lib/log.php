<?

namespace Local\Fwink;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\SystemException;
use Local\Fwink\Tables\LogTable;

class Log extends ProtectedDataManager
{
    /**
     * @return Base
     * @throws ArgumentException
     * @throws SystemException
     */
    protected function getEntity(): Base
    {
        return LogTable::getEntity();
    }
}
