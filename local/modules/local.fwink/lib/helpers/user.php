<?

namespace Local\Fwink\Helpers;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use CSite;
use CUser;

class User
{
    public static $fieldForNameByDefault = 'LOGIN';

    /**
     * Get user name.
     *
     * @param array $user
     *
     * @return mixed|string
     */
    public static function getUserName(array $user)
    {
        $userName = $user[self::$fieldForNameByDefault];

        $name = $user['NAME'];
        $lastName = $user['LAST_NAME'];

        if ($lastName) {
            $userName = $lastName;

            if ($name) {
                $userName .= ' ' . $name;
            }
        }

        return $userName;
    }

    /**
     * Get format name user ['ID', 'NAME', 'LAST_NAME', 'LOGIN'].
     *
     * @param array $userInfo
     * @param string $format
     *
     * @return string
     */
    public static function formatName(array $userInfo = [], $format = ''): string
    {
        if ($format) {
            return CUser::FormatName($format, $userInfo, true);
        }
        return CUser::FormatName(CSite::GetNameFormat(), $userInfo, true);
    }

    /**
     * Get list active users for filter.
     *
     * @return array
     */
    public static function getActiveListFilter(): array
    {
        return [
            'Y' => Loc::getMessage('LOCAL_FWINK_HELPERS_USER_ACTIVE_Y'),
            'N' => Loc::getMessage('LOCAL_FWINK_HELPERS_USER_ACTIVE_N'),
        ];
    }

    /**
     * Get short user name.
     *
     * @param array $user
     *
     * @return mixed|string
     */
    public static function getShortUserName(array $user)
    {
        $userName = $user[self::$fieldForNameByDefault];

        $name = $user['NAME'];
        $lastName = $user['LAST_NAME'];

        if ($lastName) {
            $userName = $lastName;
            if ($name) {
                $initialLetterOfName = $name[0] . '.';
                $userName .= ' ' . $initialLetterOfName;
            }
        }

        return $userName;
    }

    /**
     * Check access user to another user.
     *
     * @param int $userId
     *
     * @return bool
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws SystemException
     */
    public static function checkUserAccessRole(int $userId): bool
    {
    	if((new CUser)->GetID()==1){
    		return true;
		}
    	if((
    		$GLOBALS['FWINK']['DOMAIN']=='bitrix24dev.softmonster.ru'||
            $GLOBALS['FWINK']['DOMAIN']=='asv24.softmonster.ru'
		)&&$GLOBALS['FWINK']['requestURL']['access']=='whateverdomaintokenfordemo'
		){
    		return true;
		}
    	if($GLOBALS['FWINK']['requestURL']['boolsignedParamsString']){
    		return true;
		}

        return false;
    }
}
