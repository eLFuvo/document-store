<?php
/**
 * Created by PhpStorm
 * User: elfuvo
 * Date: 2021-04-21
 * Time: 16:54
 */

namespace elfuvo\documentStore\repository;

/**
 * Class RepositoryFilterInterface
 * @package elfuvo\documentStore\repository
 */
interface RepositoryFilterInterface
{
    /**
     * list of allowed conditions for comparison
     */
    public const ALLOWED_CONDITION = [
        '=',
        '!=',
        '<',
        '>',
        '>=',
        '<=',
        'in',
        'like',
    ];

    /**
     * @return int
     * @link https://www.php.net/manual/ru/mysql-xdevapi-collectionfind.offset.php
     */
    public function getOffset(): int;

    /**
     * @return int
     * @link https://www.php.net/manual/ru/mysql-xdevapi-collectionfind.limit.php
     */
    public function getLimit(): int;

    /**
     * @return string
     * @link https://www.php.net/manual/ru/mysql-xdevapi-collection.find.php
     */
    public function getWhere(): string;

    /**
     * @return array
     * @link https://www.php.net/manual/ru/mysql-xdevapi-collectionfind.bind.php
     */
    public function getBind(): array;

    /**
     * @return array|null - column names
     * @link https://www.php.net/manual/ru/mysql-xdevapi-collectionfind.fields.php
     */
    public function getFields(): ?array;

    /**
     * @return array|null - column names with sorting direction: ['position asc', 'date desc']
     * @link https://www.php.net/manual/ru/mysql-xdevapi-collectionfind.sort.php
     */
    public function getSort(): ?array;

    /**
     * @return string
     * @link https://www.php.net/manual/ru/mysql-xdevapi-collectionfind.having.php
     */
    public function getHaving(): string;

    /**
     * @return array
     * @link https://www.php.net/manual/ru/mysql-xdevapi-collectionfind.groupby.php
     */
    public function getGroupBy(): array;
}
