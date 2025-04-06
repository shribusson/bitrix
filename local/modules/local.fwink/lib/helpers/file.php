<?

namespace Local\Fwink\Helpers;

class File
{
    public const MODULE_ID = 'local.fwink';
    public const PATH = '/ticket/';

    /**
     * Translation of bytes in KB, MB.
     *
     * @param $bytes
     * @param int $precision
     *
     * @return string
     */
    public static function humanBytes($bytes, $precision = 2): string
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= 1024 ** $pow;

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
