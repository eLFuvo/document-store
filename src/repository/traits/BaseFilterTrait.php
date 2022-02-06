<?php
/**
 * Created by PhpStorm
 * User: elfuvo
 * Date: 2021-04-24
 * Time: 19:14
 */

namespace elfuvo\documentStore\repository\traits;

use elfuvo\documentStore\Expression;

/**
 * Class BaseFilterTrait
 * @package elfuvo\documentStore\repository\traits
 */
trait BaseFilterTrait
{
    /**
     * @var int
     */
    public int $offset = 0;

    /**
     * @var int
     */
    public int $limit = 0;

    /**
     * @var array
     */
    protected array $where = [];

    /**
     * @var array
     */
    protected array $bind = [];

    /**
     * @var array
     */
    protected array $sort = [];

    /**
     * @var array
     */
    protected array $fields = [];

    /**
     * @var Expression|null
     */
    protected ?Expression $having = null;

    /**
     * @var array
     */
    protected array $groupBy = [];

    /**
     * @inheritDoc
     */
    public function getWhere(): string
    {
        return $this->where ? implode(' ', $this->where) : 'true';
    }

    /**
     * @inheritDoc
     */
    public function getBind(): array
    {
        return $this->bind ?: [];
    }

    /**
     * @inheritDoc
     */
    public function getFields(): ?array
    {
        return $this->fields ?: null;
    }

    /**
     * @inheritDoc
     */
    public function getSort(): ?array
    {
        return $this->sort ?: null;
    }

    /**
     * @inheritDoc
     */
    public function getHaving(): string
    {
        return (string)$this->having;
    }

    /**
     * @inheritDoc
     */
    public function getGroupBy(): array
    {
        return $this->groupBy ?: [];
    }

    /**
     * @inheritDoc
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @inheritDoc
     */
    public function getLimit(): int
    {
        return $this->limit;
    }
}
