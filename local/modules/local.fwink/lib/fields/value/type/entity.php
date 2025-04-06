<?

namespace Local\Fwink\Fields\Value\Type;

use Local\Fwink\Fields\Value\Base;
use Local\Fwink\Helpers\Log as HelpersLog;

class Entity extends Base
{
    protected function getMainValue($rawValue)
    {
        return $this->getName($rawValue);
    }

    protected function getName($rawValue): string
    {
        return $this->name()[$rawValue] ?: '';
    }

    protected function name(): array
    {
        return HelpersLog::getEntityElement();
    }
}
