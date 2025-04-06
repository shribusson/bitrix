<?

namespace Local\Fwink\Helpers;

class Filter
{
    public const DATE_FROM = '_from';
    public const DATE_TO = '_to';

    /**
     * Replacing '_from' and '_to' in ''.
     *
     * @param $str
     *
     * @return string
     */
    public static function trimRange($str): string
    {
        return str_replace([self::DATE_FROM, self::DATE_TO], '', $str);
    }
}
