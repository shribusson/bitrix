<?

namespace Local\Fwink\Fields\Views\Show;

use Local\Fwink\Fields\Views\Base;
use Local\Fwink\Helpers\Encoding as HelpersEncoding;
use Local\Fwink\Helpers\Ticket as HelpersTicket;

class Boolean extends Base
{
    protected function getNode()
    {
        $answer = HelpersTicket::getListFilterAnswer();
        $value = (int)$this->value;

        return $this->dom->createElement('span', HelpersEncoding::toUtf($answer[$value]));
    }

    protected function getDefaultAttributes(): array
    {
        return [];
    }
}
