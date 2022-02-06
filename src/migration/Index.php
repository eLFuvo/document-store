<?php
/**
 * Created by PhpStorm
 * User: elfuvo
 * Date: 2021-04-27
 * Time: 14:33
 */

namespace elfuvo\documentStore\migration;

use JsonSerializable;

/**
 * Class Index
 * @package elfuvo\documentStore\migration
 *
 * @property string $name
 * @property bool $unique
 * @property \elfuvo\documentStore\migration\IndexField[] $fields
 */
class Index implements JsonSerializable
{
    /**
     * @var string|null
     */
    public ?string $name = null;

    /**
     * @var bool
     */
    public bool $unique = false;

    /**
     * @var array
     */
    public array $fields = [];

    /**
     * @var bool
     */
    protected bool $spatial = false;
    /**
     * @var bool
     */
    protected bool $isArray = false;

    /**
     * IndexField constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        foreach ($config as $property => $value) {
            $this->{$property} = $value;
        }
    }

    /**
     * @param IndexField $field
     */
    public function addField(IndexField $field)
    {
        array_push($this->fields, $field);
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        $this->fields = array_filter($this->fields, function (IndexField $field) {
            if ($field->isGeoJson()) {
                $this->spatial = true;
            }
            $this->isArray = $this->isArray || $field->array;

            return $field->isValid();
        });

        return $this->name > '' && !empty($this->fields);
    }

    /**
     * @return bool
     */
    public function isArray(): bool
    {
        return $this->isArray;
    }

    /**
     * @link https://dev.mysql.com/doc/x-devapi-userguide/en/collection-indexing.html#collection-index-definitions
     *
     * @return array|null
     */
    public function jsonSerialize(): ?array
    {
        return $this->isArray ?
            ['fields' => $this->fields] :
            [
                'fields' => $this->fields,
                'type' => $this->spatial ? 'SPATIAL' : 'INDEX',
                'unique' => $this->unique,
            ];
    }
}
