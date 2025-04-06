<?

namespace Local\Fwink\Helpers;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\SystemException;
use CSite;
use Local\Fwink\Helpers\Client as HelpersClient;

class Folder
{
    public const PATH_COMPANY = '?mode=pages&page=companyblock';
    public const PATH_STAFF = '?mode=page&page=staff&num2';
	public const PATH_POST = '?mode=page&page=post';
	public const PATH_LIST = '?mode=page&page=list';

    /**
     * Get folder entity for log.
     *
     * @param $entityName
     * @param int $elementId
     *
     * @return string
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws SystemException
     */
    public static function getPathEntity($entityName, int $elementId = 0): string
    {
        switch ($entityName) {
            case 'Company':
                $path = self::PATH_COMPANY;
                break;
            case 'Staff':
                $path = self::PATH_STAFF;
                break;
            default:
                return '';
                break;
        }
        $path = $elementId ? $path . $elementId . '/' : $path;
        $path = self::get($path);

        return $path;
    }

    /**
     * Get a folder based on the site.
     *
     * @param $path
     *
     * @return string
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws SystemException
     */
    public static function get($path): string
    {
        $dirSite = self::getDirSite();
        $path = $dirSite . $path;

        return $path;
    }

    /**
     * Get site folder.
     *
     * @return string
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws SystemException
     */
    public static function getDirSite(): string
    {
		$helpdescdir=explode('/',$_SERVER['REDIRECT_URL'])[1];

		if (HelpersClient::isClient()) {
            $dir = '';
            $siteId = Option::get('local.fwink', 'local_tickets_site');
            $result = CSite::GetByID($siteId);
            if ($row = $result->Fetch()) {
                $dir = $row['DIR'];
            }
			return $dir . $helpdescdir.'/';		//return $dir . 'helpdesk/';
        }
		is_null($helpdescdir)&&$helpdescdir='';

//		return '/'.$helpdescdir.'/'; //
		return $helpdescdir;
    }
}
