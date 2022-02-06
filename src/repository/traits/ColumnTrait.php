<?php
/**
 * Created by PhpStorm
 * User: elfuvo
 * Date: 2021-05-15
 * Time: 11:38
 */

namespace elfuvo\documentStore\repository\traits;

/**
 * Trait ColumnTrait
 * @package elfuvo\documentStore\repository\traits
 */
trait ColumnTrait
{
    /**
     * @param string $column - column name for selection.
     * @param bool $realValues - return only non-empty values.
     * @return array
     * @throws \elfuvo\documentStore\Exception
     */
    public function column(string $column, bool $realValues = true): array
    {
        $result = $this
            ->buildQuery()
            ->fields($column)
            ->execute()
            ->fetchAll();

        $values = array_column($result, $column);

        return $realValues ? array_filter($values) : $values;
    }
}
