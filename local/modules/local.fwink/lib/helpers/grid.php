<?

namespace Local\Fwink\Helpers;

class Grid
{
    /**
     * Get headers for table.
     *
     * @param \Local\Fwink\Fields\Field $field
     * @param $fieldName
     *
     * @return array
     */
    public static function getHeader(\Local\Fwink\Fields\Field $field, $fieldName): array
    {
        return [
            'id' => $fieldName,
            'type' => 'text',
            'name' => $field->getInfo()->getTitle(),
            'sort' => $field->getGridList()->getSort('CODE'),
            'first_order' => $field->getGridList()->getSort('ORDER'),
            'default' => $field->getGridList()->getSort('DEFAULT'),
            'width' => $field->getGridList()->getWidth()
        ];
    }

    /**
     * Get filter for table.
     *
     * @param \Local\Fwink\Fields\Field $field
     *
     * @return array
     */
    public static function getFilter(\Local\Fwink\Fields\Field $field): array
    {
        $result = [];
        $type = $field->getGridList()->getFilter('TYPE');
        if ($type) {
            switch ($type) {
                case 'list':
                    $items = $field->getGridList()->getFilter('ITEMS');
                    if (!empty($items)) {
                        $result = [
                            'id' => $field->getGridList()->getFilter('CODE'),
                            'name' => $field->getInfo()->getTitle(),
                            'type' => $field->getGridList()->getFilter('TYPE'),
                            'default' => $field->getGridList()->getFilter('DEFAULT'),
                            'items' => $items,
                            'params' => $field->getGridList()->getFilter('PARAMS')
                        ];
                    }
                    break;
                case 'date':
                    $result = [
                        'id' => $field->getGridList()->getFilter('CODE'),
                        'name' => $field->getInfo()->getTitle(),
                        'type' => $field->getGridList()->getFilter('TYPE'),
                        'default' => $field->getGridList()->getFilter('DEFAULT'),
                        'exclude' => $field->getGridList()->getFilter('EXCLUDE'),
                    ];
                    break;
                case 'custom_entity':
                    $result = [
                        'id' => $field->getGridList()->getFilter('CODE'),
                        'name' => $field->getInfo()->getTitle(),
                        'type' => $field->getGridList()->getFilter('TYPE'),
                        'default' => $field->getGridList()->getFilter('DEFAULT'),
                        'selector' => $field->getGridList()->getFilter('SELECTOR'),
                        'params' => $field->getGridList()->getFilter('PARAMS')
                    ];
                    break;
                default:
                    $result = [
                        'id' => $field->getGridList()->getFilter('CODE'),
                        'name' => $field->getInfo()->getTitle(),
                        'type' => $field->getGridList()->getFilter('TYPE'),
                        'default' => $field->getGridList()->getFilter('DEFAULT'),
                    ];
                    break;
            }
        }

        return $result;
    }
}
