<?php
/**
 * Created by PhpStorm
 * User: elfuvo
 * Date: 2021-04-27
 * Time: 16:11
 */

namespace elfuvo\documentStore\migration;

use elfuvo\documentStore\Exception;
use mysql_xdevapi\Collection;

/**
 * Trait SchemaMigrationTrait
 * @package elfuvo\documentStore\migration
 */
trait SchemaMigrationTrait
{
    /**
     * @inheritDoc
     */
    public function createCollection(string $collection): Collection
    {
        return $this->getSchema()->createCollection($collection);
    }

    /**
     * @inheritDoc
     */
    public function dropCollection(string $collection): bool
    {
        return $this->getSchema()->dropCollection($collection);
    }

    /**
     * @inheritDoc
     */
    public function addProperty(string $collection, string $property, $defaultValue = null): bool
    {
        $result = $this->getSchema()->getCollection($collection)
            ->modify('true')
            ->patch(json_encode([$property => $defaultValue]))
            ->execute();

        return $result->getWarningsCount() === 0;
    }

    /**
     * @inheritDoc
     */
    public function dropProperty(string $collection, string $property): bool
    {
        $result = $this->getSchema()->getCollection($collection)
            ->modify('true')
            ->unset([$property])
            ->execute();

        return $result->getWarningsCount() === 0;
    }

    /**
     * @inheritDoc
     */
    public function createIndex(string $collection, Index $index): bool
    {
        if ($index->isValid()) {
            $this->getSchema()->getCollection($collection)->createIndex(
                $index->name,
                json_encode($index)
            );

            return true;
        } else {
            if ($index->isArray()) {
                throw new Exception('Index is invalid. Supported types for array field: UNSIGNED, CHAR(n).');
            }
            throw new Exception('Invalid index config');
        }
    }

    /**
     * @inheritDoc
     */
    public function dropIndex(string $collection, string $name): bool
    {
        return $this->getSchema()->getCollection($collection)->dropIndex($name);
    }
}
