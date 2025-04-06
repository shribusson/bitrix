<?

namespace Local\Fwink\Fields\Value;

interface ValueFromDbInterface extends ValueInterface
{
    public function getRaw();

    public function setValueFromDb($row, array $selectFields = []);
}
