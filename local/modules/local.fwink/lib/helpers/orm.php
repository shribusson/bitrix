<?

namespace Local\Fwink\Helpers;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity;
use Bitrix\Main\Entity\Base;

class Orm
{
    /**
     * @param array $select
     * @param Base $entity
     *
     * @return array
     * @throws ArgumentException
     */
    public static function getFieldAliases(array $select, Base $entity): array
    {
        $fieldAliases = [];

        foreach ($select as $key => $fieldName) {
            $isAlias = !is_numeric($key);
            $isReference = strpos($fieldName, '.') && (strpos($fieldName, ':') === false);

            if ($isReference !== false) {
                $chainOfFields = explode('.', $fieldName);
                $referenceFieldName = $chainOfFields[0];
                /** @var Entity\ReferenceField $referenceField */
                $referenceField = $entity->getField($referenceFieldName);
                $linkedFieldName = self::getLinkedFieldName($referenceField);

                if (!empty($linkedFieldName)) {
                    if ($isAlias) {
                        $fieldAliases[$linkedFieldName][] = $key;
                    } else {
                        $fieldAlias = self::getFieldNameInResultForReference($fieldName, $entity);
                        $fieldAliases[$linkedFieldName][] = $fieldAlias;
                    }
                }
            }
        }

        return $fieldAliases;
    }

    /**
     * @param Entity\ReferenceField $referenceField
     *
     * @return string
     */
    public static function getLinkedFieldName(Entity\ReferenceField $referenceField): string
    {
        $reference = $referenceField->getReference();
        $linkToCurrentTable = '';
        $linkedFieldName = '';

        foreach ($reference as $key => $value) {
            if (strpos($key, 'this') !== false) {
                $linkToCurrentTable = $key;
            }
            if (strpos($value, 'this') !== false) {
                $linkToCurrentTable = $value;
            }
        }

        if (!empty($linkToCurrentTable)) {
            $linkToCurrentTable = explode('.', $linkToCurrentTable);
            $linkedFieldName = $linkToCurrentTable[1];
        }

        return $linkedFieldName;
    }

    /**
     * @param $fieldNameInSelect
     * @param Base $entity
     *
     * @return mixed|string
     */
    public static function getFieldNameInResultForReference($fieldNameInSelect, Base $entity)
    {
        $entityCode = $entity->getCode();

        $fieldNameInResult = str_replace('.', '_', strtoupper($fieldNameInSelect));
        $fieldNameInResult = $entityCode . '_' . $fieldNameInResult;

        return $fieldNameInResult;
    }
}
