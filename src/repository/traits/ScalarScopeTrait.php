<?php
/**
 * Created by PhpStorm
 * User: elfuvo
 * Date: 2021-04-21
 * Time: 10:46
 */

namespace elfuvo\documentStore\repository\traits;

/**
 * Class ScalarScopeTrait
 * @package elfuvo\documentStore\repository\traits
 */
trait ScalarScopeTrait
{
    /**
     * select scalar value
     *
     * @param string $column
     * @return int|string|null|array
     */
    public function scalar(string $column)
    {
        $result = $this
            ->buildQuery()
            ->fields($column)
            ->execute()
            ->fetchOne();
        if (preg_match('#\.([\w]+)$#', $column, $matches)) {
            $column = $matches[1];
        }

        return $result[$column] ?? null;
    }
}
