<?php
/**
 * Created by PhpStorm
 * User: elfuvo
 * Date: 2021-10-20
 * Time: 12:45
 */

namespace elfuvo\documentStore\repository\traits;

use elfuvo\documentStore\Expression;

/**
 * Class InScopeTrait
 * @package elfuvo\documentStore\repository\traits
 */
trait InScopeTrait
{
    /**
     * @param string $column
     * @param array $values
     * @return $this
     * @throws \elfuvo\documentStore\Exception
     */
    public function in(string $column, array $values): self
    {
        foreach ($values as &$value) {
            $value = static::getDb()->quoteName($value);
        }
        return $this->andExprWhere(new Expression($column . ' IN (' . implode(',', $values) . ')'));
    }
}
