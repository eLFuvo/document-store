<?php
/**
 * Created by PhpStorm
 * User: elfuvo
 * Date: 2021-05-14
 * Time: 10:17
 */

namespace elfuvo\documentStore\repository\traits;

use elfuvo\documentStore\entity\EntityInterface;
use mysql_xdevapi\CollectionModify;

/**
 * Class SetPropertyTrait
 * @package elfuvo\documentStore\repository\traits
 */
trait SetPropertyTrait
{
    /**
     * @param \elfuvo\documentStore\entity\EntityInterface $entity
     * @param string $property
     * @param $value
     * @return bool
     * @throws \elfuvo\documentStore\Exception
     */
    public function setProperty(EntityInterface $entity, string $property, $value): bool
    {
        $documentModify = $this->getCollection()
            ->modify('_id="' . $entity->getId() . '"');

        $this->setDocumentProperty($documentModify, $property, $value);
        $result = $documentModify->execute();

        return $result->getWarningsCount() === 0;
    }

    /**
     * @param \elfuvo\documentStore\entity\EntityInterface $entity
     * @param string $property
     * @return bool
     * @throws \elfuvo\documentStore\Exception
     */
    public function unsetProperty(EntityInterface $entity, string $property): bool
    {
        $result = $this->getCollection()
            ->modify('_id="' . $entity->getId() . '"')
            ->unset([$property])
            ->execute();

        return $result->getWarningsCount() === 0;
    }

    /**
     * @param \mysql_xdevapi\CollectionModify $modify
     * @param string $field
     * @param string|int|float|bool|array $value
     */
    protected function setDocumentProperty(CollectionModify $modify, string $field, $value)
    {
        if (is_array($value)) {
            if (empty($value) || (array_values($value) === $value && !is_array($value[0]))) { // sequential array - ['a', 'b', 'c']
                $modify->set($field, '[]');
                foreach ($value as $subValue) {
                    $modify->arrayAppend($field, $this->castValue($subValue));
                }
            } elseif (isset($value[0])) {// array of entities [['id' => 1, 'title'=>'foo'], ['id' => 2, 'title'=>'bar']]
                $modify->set($field, '[]');
                $index = 0;
                foreach ($value as $subValue) {
                    $this->setDocumentProperty($modify, $field . '[' . $index . ']', $subValue);
                    $index++;
                }
            } else {// associative array - ['id' => 1, 'title'=>'foo']
                $modify->set($field, '{}');
                foreach ($value as $subField => $subValue) {
                    if (is_numeric($subField)) { // arrays like [10 => 'foo', 20 => 'bar') must be saved as an object
                        // {"10": "foo", "20": "bar"}
                        // Attention! You shouldn't filter by this field
                        // EOL, TAB symbols will be broken with additional slashes - \\r\\n\\t
                        $modify->set($field, $subValue);
                        break;
                    } else {
                        $this->setDocumentProperty($modify, $field . '.' . $subField, $subValue);
                    }
                }
            }
        } else {
            $modify->set($field, $this->castValue($value));
        }
    }

    /**
     * @param $doc
     * @return false|string
     */
    protected function encode($doc)
    {
        return json_encode(
            $doc,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_LINE_TERMINATORS
        );
    }

    /**
     * @param $value
     * @return bool|float|string
     */
    protected function castValue($value)
    {
        if (is_bool($value) || is_int($value) || is_float($value) || is_null($value)) {
            return $value;
        } elseif ($value == 'null') {
            return null;
        } else {
            return (string)$value;
        }
    }
}
