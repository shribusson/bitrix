<?

namespace Local\Fwink\Helpers;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\GroupTable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserTable;
use CUser;
use Local\Fwink\Helpers\User as HelpersUser;
use Local\Fwink\Tables\CompanyTable;

class Staff
{
    /**
     * Get responsible manager of the company.
     *
     * @param int $companyId
     *
     * @return mixed
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getResponsibleManager($companyId)
    {
        $managerId = '';
        if (!empty($companyId)) {
            $company = CompanyTable::getList([
                'select' => ['RESPONSIBLE_MANAGER_ID'],
                'filter' => ['ID' => $companyId]
            ]);
            $managerId = $company->fetch()['RESPONSIBLE_MANAGER_ID'];
        }

        return $managerId;
    }

    /**
     * Get list of group staff for Local\Fwink\Fields\Views\Edit\Select.
     *
     * @param string $userId
     *
     * @return array
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getSelectValuesGroup($userId = ''): array
    {
        $rowUserRole = HelpersUser::getRole((new CUser)->GetID());
        if ($rowUserRole === 'Admin') {
            $groupId = HelpersUser::getGroupsOfStaff();
        } else {
            $groupId = HelpersUser::getGroupsOfPersonal();
        }

        $selectValues = [];
        $arGroupsUser = CUser::GetUserGroup($userId);
        $parameters = [
            'filter' => [
                'ID' => $groupId
            ],
            'select' => [
                'ID',
                'NAME'
            ],
            'order' => [
                'ID' => 'DESC'
            ],
            'cache' => ['ttl' => 86400]
        ];
        $result = GroupTable::getList($parameters);
        while ($group = $result->fetch()) {
            $selectValues[] = [
                'VALUE' => $group['ID'],
                'TEXT' => $group['NAME'],
                'SELECTED' => in_array($group['ID'], $arGroupsUser, true),
                'ATTRIBUTES' => []
            ];
        }

        return $selectValues;
    }

    /**
     * Is the current user a specialist.
     *
     * @return bool
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function isPersonal(): bool
    {
        $personal = false;
        $userId = (int)(new CUser)->GetID();
        $parameters = [
            'filter' => [
                'Bitrix\Main\UserGroupTable:USER.GROUP_ID' => HelpersUser::getGroupsOfPersonal(),
                'ID' => $userId
            ],
            'select' => ['ID']
        ];
        $result = UserTable::getList($parameters);

        while ($user = $result->fetch()) {
            $personal = (int)$user['ID'] === $userId;
        }

        return $personal;
    }

    /**
     * Get list of user activity for Local\Fwink\Fields\Views\Edit\Select.
     *
     * @param string $selectedValue
     *
     * @return array
     */
    public static function getActiveValues($selectedValue = ''): array
    {
        return [
            [
                'VALUE' => 'Y',
                'TEXT' => self::getColor()['Y']['TITLE'],
                'SELECTED' => $selectedValue === 'Y',
                'ATTRIBUTES' => [
                    'data-color' => self::getColor()['Y']['COLOR']
                ]
            ],
            [
                'VALUE' => 'N',
                'TEXT' => self::getColor()['N']['TITLE'],
                'SELECTED' => $selectedValue === 'N',
                'ATTRIBUTES' => [
                    'data-color' => self::getColor()['N']['COLOR']
                ]
            ],
        ];
    }

    /**
     * Get list of user activity.
     *
     * @return array
     */
    public static function getColor(): array
    {
        return [
            'Y' => [
                'TITLE' => Loc::getMessage('LOCAL_FWINK_STAFF_ACTIVE_TITLE'),
                'COLOR' => '#5cb85c'
            ],
            'N' => [
                'TITLE' => Loc::getMessage('LOCAL_FWINK_STAFF_NO_ACTIVE_TITLE'),
                'COLOR' => '#8e9eb3'
            ],
        ];
    }

    /**
     * Is the user an admin, team lead, specialist.
     *
     * @param int $userId
     *
     * @return bool
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function isStaff(int $userId = 0): bool
    {
        $personal = false;
        $userId = $userId ?: (int)(new CUser)->GetID();
        $parameters = [
            'filter' => [
                'Bitrix\Main\UserGroupTable:USER.GROUP_ID' => HelpersUser::getGroupsOfStaff(),
                'ID' => $userId
            ],
            'select' => ['ID']
        ];
        $result = UserTable::getList($parameters);

        while ($user = $result->fetch()) {
            $personal = (int)$user['ID'] === $userId;
        }

        return $personal;
    }

    /**
     * Get list of staff for Local\Fwink\Fields\Views\Edit\MultiSelect.
     *
     * @param array $selectedValue
     *
     * @return array
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getSelectValuesMultiple($selectedValue = []): array
    {
        $selectValues = [];

        $parameters = [
            'filter' => [
                'GROUP_ID' => HelpersUser::getGroupsOfStaff(),
                'ACTIVE' => 'Y'
            ],
            'select' => [
                'ID',
                'NAME',
                'LAST_NAME',
                'LOGIN',
                'GROUP_ID' => "\Bitrix\Main\UserGroupTable:USER.GROUP_ID"
            ],
            'order' => [
                'LAST_NAME' => 'ASC',
                'NAME' => 'ASC'
            ]
        ];

        $result = UserTable::getList($parameters);

        while ($user = $result->fetch()) {
            $selectValues[] = [
                'VALUE' => $user['ID'],
                'TEXT' => HelpersUser::getUserName($user),
                'SELECTED' => in_array($user['ID'], $selectedValue, true)
            ];
        }

        return $selectValues;
    }

    /**
     * Get list of staff for Local\Fwink\Fields\Views\Edit\Select.
     *
     * @param string $selectedValue
     *
     * @return array
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getSelectValues($selectedValue = ''): array
    {
        $selectValues[] = [
            'VALUE' => '',
            'TEXT' => Loc::getMessage('LOCAL_FWINK_RESPONSIBLE_EMPTY')
        ];

        $parameters = [
            'filter' => [
                'GROUP_ID' => HelpersUser::getGroupsOfStaff(),
                'ACTIVE' => 'Y'
            ],
            'select' => [
                'ID',
                'NAME',
                'LAST_NAME',
                'LOGIN',
                'GROUP_ID' => "\Bitrix\Main\UserGroupTable:USER.GROUP_ID"
            ],
            'order' => [
                'LAST_NAME' => 'ASC',
                'NAME' => 'ASC'
            ]
        ];

        $result = UserTable::getList($parameters);

        while ($user = $result->fetch()) {
            $selectValues[] = [
                'VALUE' => $user['ID'],
                'TEXT' => HelpersUser::getUserName($user),
                'SELECTED' => (int)$user['ID'] === (int)$selectedValue
            ];
        }

        return $selectValues;
    }

    /**
     * Get list groups for filter.
     *
     * @return array
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getListGroupFilter(): array
    {
        $rows = [];
        $parameters = [
            'filter' => [
                'ID' => HelpersUser::getGroupsOfStaff()
            ],
            'select' => [
                'ID',
                'NAME'
            ],
            'order' => [
                'ID' => 'ASC'
            ]
        ];
        $result = GroupTable::getList($parameters);
        while ($row = $result->fetch()) {
            $rows[$row['ID']] = $row['NAME'];
        }

        return $rows;
    }

    /**
     * Get list staff for filter.
     *
     * @param bool $empty
     *
     * @return array
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getListFilter($empty = true): array
    {
        if ($empty) {
            $rows[''] = Loc::getMessage('LOCAL_FWINK_RESPONSIBLE_EMPTY');
        } else {
            $rows = [];
        }

        $parameters = [
            'filter' => [
                'Bitrix\Main\UserGroupTable:USER.GROUP_ID' => HelpersUser::getGroupsOfStaff()
            ],
            'select' => [
                'ID',
                'NAME',
                'LAST_NAME',
                'LOGIN'
            ],
            'order' => [
                'LAST_NAME' => 'ASC',
                'NAME' => 'ASC'
            ]
        ];
        $result = UserTable::getList($parameters);
        while ($row = $result->fetch()) {
            $rows[$row['ID']] = HelpersUser::formatName($row, '#LAST_NAME# #NAME#');
        }

        return $rows;
    }

    /**
     * Is the current user a admin.
     *
     * @return bool
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function isAdmin(): bool
    {
        $iaAdmin = false;
        $userId = (int)(new CUser)->GetID();
        $parameters = [
            'filter' => [
                'Bitrix\Main\UserGroupTable:USER.GROUP_ID' => HelpersUser::getGroupsOfAdmin(),
                'ID' => $userId
            ],
            'select' => ['ID']
        ];
        $result = UserTable::getList($parameters);

        while ($user = $result->fetch()) {
            $iaAdmin = (int)$user['ID'] === $userId;
        }

        return $iaAdmin;
    }
}
