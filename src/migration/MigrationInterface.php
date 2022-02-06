<?php
/**
 * Created by PhpStorm
 * User: elfuvo
 * Date: 2021-04-27
 * Time: 15:39
 */

namespace elfuvo\documentStore\migration;

use mysql_xdevapi\Collection;
use mysql_xdevapi\Schema;

/**
 * Class MigrationInterface
 * @package elfuvo\documentStore\migration
 */
interface MigrationInterface
{
    /**
     * Create new collection in database
     * @link https://www.php.net/manual/en/mysql-xdevapi-schema.createcollection.php
     *
     * @param string $collection
     * @return \mysql_xdevapi\Collection
     */
    public function createCollection(string $collection): Collection;

    /**
     * Drop collection from database
     * @link https://www.php.net/manual/en/mysql-xdevapi-schema.dropcollection.php
     *
     * @param string $collection
     * @return bool
     */
    public function dropCollection(string $collection): bool;

    /**
     * Takes a patch object and applies it on one or more documents, and can update multiple document properties.
     * @link https://www.php.net/manual/en/mysql-xdevapi-collectionmodify.patch.php
     *
     * @param string $collection
     * @param string $property
     * @param null $defaultValue
     * @return bool
     */
    public function addProperty(string $collection, string $property, $defaultValue = null): bool;

    /**
     * Removes attributes from documents in a collection.
     * @link https://www.php.net/manual/en/mysql-xdevapi-collectionmodify.unset.php
     *
     * @param string $collection
     * @param string $property
     * @return bool
     */
    public function dropProperty(string $collection, string $property): bool;

    /**
     * Create collection index
     * @link https://www.php.net/manual/en/mysql-xdevapi-collection.createindex.php
     * @link https://dev.mysql.com/doc/x-devapi-userguide/en/collection-indexing.html
     *
     * @param string $collection
     * @param Index $index
     * @return bool
     * @throws \elfuvo\documentStore\Exception
     */
    public function createIndex(string $collection, Index $index): bool;

    /**
     * Drop collection index
     * @link https://www.php.net/manual/en/mysql-xdevapi-collection.dropindex.php
     *
     * @param string $collection
     * @param string $name
     * @return bool
     */
    public function dropIndex(string $collection, string $name): bool;

    /**
     * @return \mysql_xdevapi\Schema
     */
    public function getSchema(): Schema;
}
