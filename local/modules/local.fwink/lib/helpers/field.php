<?

namespace Local\Fwink\Helpers;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\SystemException;
use Local\Fwink\AccessControl\Configuration;
use Local\Fwink\AccessControl\Operations;

class Field
{
    /**
     * Check the field display to the user.
     *
     * @param $entityName
     * @param $fieldName
     *
     * @return bool
     * @throws ArgumentException
     * @throws SystemException
     */
    public static function checkShow($entityName, $fieldName): bool
    {
        $show = Configuration::getInstance()->get('entitiesDependent')[$entityName][$fieldName]['show'];
        if ($show) {
            foreach ($show as $entityDependentName => $operation) {
                if (Operations::checkAccess($operation, $entityDependentName) === false) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check the display of the link to the user.
     *
     * @param $entityName
     * @param $fieldName
     *
     * @return bool
     * @throws ArgumentException
     * @throws SystemException
     */
    public static function checkUrl($entityName, $fieldName): bool
    {
        $show = Configuration::getInstance()->get('entitiesDependent')[$entityName][$fieldName]['url'];
        if ($show) {
            foreach ($show as $entityDependentName => $operation) {
                if (Operations::checkAccess($operation, $entityDependentName) === false) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }
}
