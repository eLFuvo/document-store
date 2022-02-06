<?php
/**
 * Created by PhpStorm
 * User: elfuvo
 * Date: 2021-04-27
 * Time: 14:34
 */

namespace elfuvo\documentStore\migration;

use JsonSerializable;

/**
 * Class IndexField
 * @package elfuvo\yiiDocumentStore\migration
 *
 * @link https://www.php.net/manual/en/mysql-xdevapi-collection.createindex.php
 * @link https://dev.mysql.com/doc/x-devapi-userguide/en/collection-indexing.html#collection-index-definitions
 *
 * @property string $field - string, the full document path to the document member or field to be indexed.
 * @see IndexField::VALID_TYPE
 * @property string $type
 * @property int $length - optional, must be set after type
 * @property bool $required
 * @property bool $array - https://dev.mysql.com/doc/x-devapi-userguide/en/collection-indexing.html#collection-indexing-array
 * @property array|null $options - only for GEOJSON type
 * @property int|null $srid - only for GEOJSON type
 */
class IndexField implements JsonSerializable
{
    # public const TYPE_INT = 'INT';
    public const TYPE_TINYINT = 'TINYINT';
    public const TYPE_SMALLINT = 'SMALLINT';
    public const TYPE_MEDIUMINT = 'MEDIUMINT';
    public const TYPE_INTEGER = 'INTEGER';
    public const TYPE_BIGINT = 'BIGINT';
    public const TYPE_REAL = 'REAL';
    public const TYPE_FLOAT = 'FLOAT';
    public const TYPE_DOUBLE = 'DOUBLE';
    public const TYPE_DECIMAL = 'DECIMAL';
    public const TYPE_NUMERIC = 'NUMERIC';
    public const TYPE_DATE = 'DATE';
    public const TYPE_TIME = 'TIME';
    public const TYPE_TIMESTAMP = 'TIMESTAMP';
    public const TYPE_DATETIME = 'DATETIME';
    public const TYPE_TEXT = 'TEXT';
    public const TYPE_CHAR = 'CHAR';
    public const TYPE_GEOJSON = 'GEOJSON';
    public const TYPE_UNSIGNED = 'UNSIGNED';

    protected const TYPE_HAS_LENGTH = [
        self::TYPE_TEXT,
        self::TYPE_CHAR,
    ];

    protected const VALID_TYPE = [
        #  self::TYPE_INT,
        self::TYPE_TINYINT,
        self::TYPE_SMALLINT,
        self::TYPE_MEDIUMINT,
        self::TYPE_INTEGER,
        self::TYPE_BIGINT,
        self::TYPE_REAL,
        self::TYPE_FLOAT,
        self::TYPE_DOUBLE,
        self::TYPE_DECIMAL,
        self::TYPE_NUMERIC,
        self::TYPE_DATE,
        self::TYPE_TIME,
        self::TYPE_TIMESTAMP,
        self::TYPE_DATETIME,
        self::TYPE_TEXT,
        self::TYPE_CHAR,
        self::TYPE_GEOJSON,
        self::TYPE_UNSIGNED,
    ];

    /**
     *
     */
    protected const ARRAY_VALID_TYPE = [
        self::TYPE_UNSIGNED,
        self::TYPE_CHAR,
    ];

    /**
     * @var string|null
     */
    public ?string $field = null;

    /**
     * @var bool
     */
    public bool $required = false;

    /**
     * supported types: UNSIGNED, CHAR(n)
     *
     * @link https://dev.mysql.com/doc/x-devapi-userguide/en/collection-indexing.html#collection-indexing-array
     *
     * @var bool
     */
    public bool $array = false;

    /**
     * @var string|null
     */
    protected ?string $type = null;

    /**
     * @var bool
     */
    protected bool $geoJson = false;

    /**
     * @var int|null
     */
    protected ?int $length = null;

    /**
     * @var array|null
     */
    public ?array $options = null;

    /**
     * @link https://dev.mysql.com/doc/refman/8.0/en/spatial-geojson-functions.html#function_st-geomfromgeojson
     *
     * @var int|null
     */
    public ?int $srid = null;

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
     * @param string $type
     */
    public function setType(string $type)
    {
        $type = strtoupper($type);
        if (in_array($type, self::VALID_TYPE)) {
            $this->type = $type;
            $this->geoJson = $type === self::TYPE_GEOJSON;
        }
    }

    /**
     * @param int $length
     */
    public function setLength(int $length)
    {
        $this->length = $length > 0 ? $length : null;
    }

    /**
     * @return bool
     */
    public function isGeoJson(): bool
    {
        return $this->geoJson;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return in_array($this->type, self::VALID_TYPE) && preg_match('#\$\.(.+)#', $this->field)
            && ($this->array === true && $this->supportedArrayType() || $this->array === false);
    }

    /**
     * @return bool
     */
    public function supportedArrayType(): bool
    {
        return in_array($this->type, self::ARRAY_VALID_TYPE);
    }

    /**
     * @link https://dev.mysql.com/doc/x-devapi-userguide/en/collection-indexing.html#collection-index-definitions
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        $data = [
            'field' => $this->field,
            'type' => $this->type . ($this->length ? '(' . $this->length . ')' : ''),
        ];
        if ($this->array) {
            $data['array'] = true;
        } else {
            $data['required'] = $this->required;
        }
        if ($this->isGeoJson()) {
            if ($this->options) {
                $data['options'] = $this->options;
            }
            if ($this->srid) {
                $data['srid'] = $this->srid;
            }
        }

        return $data;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if (method_exists($this, 'set' . $name)) {
            call_user_func([$this, 'set' . $name], $value);
        }
    }
}
