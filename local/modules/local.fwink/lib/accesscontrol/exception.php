<?

namespace Local\Fwink\AccessControl;

use Bitrix\Main\Localization\Loc;
use Throwable;

class Exception extends \Exception
{
    public function __construct($operationName = '', $entityName = '', $code = 0, Throwable $previous = null)
    {
        $message = Loc::getMessage(
            'LOCAL_FWINK_ACCESS_CONTROL_EXCEPTION',
            [
                '#OPERATION_NAME#' => $operationName,
                '#ENTITY_NAME#' => $entityName,
            ]
        );
        parent::__construct($message, $code, $previous);
    }
}
