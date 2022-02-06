<?php
/**
 * Created by PhpStorm
 * User: elfuvo
 * Date: 2021-04-24
 * Time: 19:16
 */

namespace elfuvo\documentStore\repository\traits;

use elfuvo\documentStore\Exception;
use elfuvo\documentStore\Expression;
use elfuvo\documentStore\repository\AbstractRepository;

/**
 * Class ConditionsFilterTrait
 * @package elfuvo\documentStore\repository\traits
 */
trait ConditionsFilterTrait
{
    /**
     * @param string[]|Expression[] $fields
     * @return AbstractRepository
     */
    public function select(array $fields): self
    {
        $this->fields = array_map(function ($field) {
            return (string)$field;
        }, $fields);

        return $this;
    }

    /**
     * @param int $limit
     * @return AbstractRepository
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @param int $offset
     * @return AbstractRepository
     */
    public function offset(int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @param Expression $having
     * @return AbstractRepository
     */
    public function having(Expression $having): self
    {
        $this->having = $having;

        return $this;
    }

    /**
     * @param array $grouping
     * @return AbstractRepository
     */
    public function groupBy(array $grouping): self
    {
        $this->groupBy = array_map(function ($field) {
            return (string)$field;
        }, $grouping);

        return $this;
    }

    /**
     * @param string $condition
     * @param string $column
     * @param $value
     * @return AbstractRepository
     * @throws \elfuvo\documentStore\Exception
     */
    public function andWhere(string $condition, string $column, $value): self
    {
        $this->addSimpleWhere('and', $condition, $column, $value);

        return $this;
    }

    /**
     * @param Expression $expression
     * @return AbstractRepository
     */
    public function andExprWhere(Expression $expression): self
    {
        $this->where[] = (empty($this->where) ? '' : ' and ') . $expression;
        if ($expression->hasBind()) {
            $this->bind = array_merge($this->bind, $expression->params);
        }
        return $this;
    }

    /**
     * @param string $condition
     * @param string $column
     * @param $value
     * @return AbstractRepository
     * @throws \elfuvo\documentStore\Exception
     */
    public function orWhere(string $condition, string $column, $value): self
    {
        $this->addSimpleWhere('or', $condition, $column, $value);

        return $this;
    }

    /**
     * @param Expression $expression
     * @return AbstractRepository
     */
    public function orExprWhere(Expression $expression): self
    {
        $this->where[] = (empty($this->where) ? '' : ' or ') . $expression;
        if ($expression->hasBind()) {
            $this->bind = array_merge($this->bind, $expression->params);
        }

        return $this;
    }

    /**
     * @param string $separator
     * @param array $where
     * @return AbstractRepository
     * @throws \elfuvo\documentStore\Exception
     */
    public function andGroupWhere(string $separator, array $where): self
    {
        $this->addGroupedWhere('and', $separator, $where);

        return $this;
    }

    /**
     * @param string $separator
     * @param array $where
     * @return AbstractRepository
     * @throws \elfuvo\documentStore\Exception
     */
    public function orGroupWhere(string $separator, array $where): self
    {
        $this->addGroupedWhere('or', $separator, $where);

        return $this;
    }

    /**
     * @param string $separator
     * @param string $condition
     * @param string $column
     * @param $value
     * @throws \elfuvo\documentStore\Exception
     */
    protected function addSimpleWhere(string $separator, string $condition, string $column, $value)
    {
        $this->checkCondition($condition);
        $bindName = 'value' . (count($this->where));
        $expression = empty($this->where) ? '' : ' ' . $separator . ' ';
        $expression .= preg_replace('#([^.\w\d_-]+)#', '', $column);
        $expression .= ' ' . $condition . ' :' . $bindName;
        $this->bind[$bindName] = $value;
        $this->where[] = $expression;
    }

    /**
     * @param string $groupSeparator
     * @param string $separator
     * @param array[]|Expression[] $where
     * @throws \elfuvo\documentStore\Exception
     */
    protected function addGroupedWhere(string $groupSeparator, string $separator, array $where)
    {
        $resultExpression = empty($this->where) ? '(' : ' ' . $groupSeparator . ' (';
        $separator = strtolower($separator) === 'or' ? 'or' : 'and';
        $index = 0;
        foreach ($where as $expression) {
            if (is_array($expression)) {
                if (count($expression) !== 3) {
                    throw new Exception('"where" expression must contains condition, column name and value.');
                }
                $this->checkCondition($expression[0]);
                $bindName = 'value' . (count($this->where));
                $resultExpression .= ($index == 0 ? '' : ' ' . $separator . ' ');
                $resultExpression .= preg_replace('#([^.\w\d_-]+)#', '', $expression[1]);
                $resultExpression .= ' ' . $expression[0] . ' :' . $bindName;
                $this->bind[$bindName] = $expression[2];
            } elseif ($expression instanceof Expression) {
                $resultExpression .= ($index == 0 ? '' : ' ' . $separator . ' ') . $expression;
            }
            $index++;
        }
        $this->where[] = $resultExpression . ')';
    }

    /**
     * @param array $sort
     * @return AbstractRepository
     */
    public function sort(array $sort): self
    {
        $this->sort = array_map(function ($field) {
            return (string)$field;
        }, $sort);

        return $this;
    }

    /**
     * @param string $condition
     * @return bool
     * @throws \elfuvo\documentStore\Exception
     */
    protected function checkCondition(string $condition): bool
    {
        if (!in_array(strtolower($condition), static::ALLOWED_CONDITION)) {
            throw new Exception('Only "' . implode(', ', static::ALLOWED_CONDITION) .
                '" conditions is allowed.');
        }

        return true;
    }
}
