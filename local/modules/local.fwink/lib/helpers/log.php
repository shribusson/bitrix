<?

namespace Local\Fwink\Helpers;

use Bitrix\Main\Localization\Loc;

class Log
{
    /**
     * Get entity element.
     *
     * @return array
     */
    public static function getEntityElement(): array
    {
        return [
            'Ticket' => Loc::getMessage('LOCAL_FWINK_HELPERS_ENTITY_TICKET'),
            'Comment' => Loc::getMessage('LOCAL_FWINK_HELPERS_ENTITY_COMMENT'),
            'TicketObservers' => Loc::getMessage('LOCAL_FWINK_HELPERS_ENTITY_TICKET_OBSERVERS'),
            'Company' => Loc::getMessage('LOCAL_FWINK_HELPERS_ENTITY_COMPANY'),
            'Client' => Loc::getMessage('LOCAL_FWINK_HELPERS_ENTITY_CLIENT'),
            'Staff' => Loc::getMessage('LOCAL_FWINK_HELPERS_ENTITY_STAFF'),
            'WorkTime' => Loc::getMessage('LOCAL_FWINK_HELPERS_ENTITY_WORK_TIME'),
            'TicketFiles' => Loc::getMessage('LOCAL_FWINK_HELPERS_ENTITY_TICKET_FILES'),
            'Wiki' => Loc::getMessage('LOCAL_FWINK_HELPERS_ENTITY_WIKI'),
            'ObjectService' => Loc::getMessage('LOCAL_FWINK_HELPERS_ENTITY_OBJECT_SERVICE'),
            'Contract' => Loc::getMessage('LOCAL_FWINK_HELPERS_ENTITY_CONTRACT'),
        ];
    }

    /**
     * Get entity element for filter.
     *
     * @return array
     */
    public static function getEntityElementFilter(): array
    {
        return [
            'Ticket' => Loc::getMessage('LOCAL_FWINK_HELPERS_ENTITY_TICKET'),
            'Company' => Loc::getMessage('LOCAL_FWINK_HELPERS_ENTITY_COMPANY'),
            'Client' => Loc::getMessage('LOCAL_FWINK_HELPERS_ENTITY_CLIENT'),
            'ObjectService' => Loc::getMessage('LOCAL_FWINK_HELPERS_ENTITY_OBJECT_SERVICE'),
            'Staff' => Loc::getMessage('LOCAL_FWINK_HELPERS_ENTITY_STAFF'),
            'Contract' => Loc::getMessage('LOCAL_FWINK_HELPERS_ENTITY_CONTRACT'),
        ];
    }
}
