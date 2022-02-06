<?php
/**
 * Created by PhpStorm
 * User: elfuvo
 * Date: 2021-07-13
 * Time: 10:38
 */

namespace elfuvo\documentStore\repository\traits;

use elfuvo\documentStore\repository\RepositoryIndexByInterface;

/**
 * Trait IndexByTrait
 * @package elfuvo\documentStore\repository\traits
 */
trait IndexByTrait
{
    /**
     * @var string|null
     */
    protected ?string $indexField = null;

    /**
     * @var string
     */
    protected string $pickingStrategy = RepositoryIndexByInterface::PICKING_STRATEGY_FIRST;

    /**
     * @param string $field
     * @param string $strategy
     * @return $this
     */
    public function indexBy(
        string $field,
        string $strategy = RepositoryIndexByInterface::PICKING_STRATEGY_FIRST
    ): self {
        $this->indexField = $field;
        $this->pickingStrategy = $strategy;

        return $this;
    }

    /**
     * @param array $items
     * @return array
     */
    protected function indexing(array $items): array
    {
        if ($this->indexField) {
            $indexed = [];
            foreach ($items as $item) {
                if (!array_key_exists($this->indexField, $item)) {
                    continue;
                }
                $index = $item[$this->indexField];
                if ($this->pickingStrategy === RepositoryIndexByInterface::PICKING_STRATEGY_FIRST
                    && array_key_exists($index, $indexed)) {
                    continue;
                }
                $indexed[$index] = $item;
            }

            return $indexed;
        }

        return $items;
    }
}
