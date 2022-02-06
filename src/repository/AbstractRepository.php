<?php
/**
 * Created by PhpStorm
 * User: elfuvo
 * Date: 2021-04-20
 * Time: 15:01
 */

namespace elfuvo\documentStore\repository;

use elfuvo\documentStore\collection\CollectionInterface;
use elfuvo\documentStore\connection\ConnectionInterface;
use elfuvo\documentStore\entity\EntityInterface;
use elfuvo\documentStore\Exception;
use elfuvo\documentStore\repository\traits\BaseFilterTrait;
use elfuvo\documentStore\repository\traits\ColumnTrait;
use elfuvo\documentStore\repository\traits\ConditionsFilterTrait;
use elfuvo\documentStore\repository\traits\ExistsRecordTrait;
use elfuvo\documentStore\repository\traits\IndexByTrait;
use elfuvo\documentStore\repository\traits\InScopeTrait;
use elfuvo\documentStore\repository\traits\MaxScopeTrait;
use elfuvo\documentStore\repository\traits\MinScopeTrait;
use elfuvo\documentStore\repository\traits\ScalarScopeTrait;
use elfuvo\documentStore\repository\traits\SetPropertyTrait;
use mysql_xdevapi\Collection;
use mysql_xdevapi\CollectionFind;
use mysql_xdevapi\DocResult;
use mysql_xdevapi\Result;

/**
 * Class CollectionRepository
 * @package elfuvo\documentStore\collection
 */
abstract class AbstractRepository implements RepositoryInterface, RepositoryFilterInterface, RepositoryIndexByInterface
{
    use MaxScopeTrait;
    use MinScopeTrait;
    use ScalarScopeTrait;
    use BaseFilterTrait;
    use ConditionsFilterTrait;
    use SetPropertyTrait;
    use ColumnTrait;
    use ExistsRecordTrait;
    use IndexByTrait;
    use InScopeTrait;

    /**
     * @var Collection|null
     */
    protected ?Collection $collection = null;

    /**
     * @var \elfuvo\documentStore\collection\CollectionInterface|null
     */
    protected ?CollectionInterface $entityCollection = null;

    /**
     * @var \elfuvo\documentStore\connection\ConnectionInterface|null
     */
    protected static ?ConnectionInterface $db = null;

    /**
     * @var null|DocResult
     */
    protected ?DocResult $sqlState = null;

    /**
     * @inheritDoc
     */
    public function __construct(CollectionInterface $entityCollection)
    {
        $this->entityCollection = $entityCollection;
    }

    /**
     * @inheritDoc
     */
    public function getCollection(): Collection
    {
        if (is_null($this->collection)) {
            // get or create collection in document store
            $this->collection = static::getDb()->getSchema()->getCollection($this->entityCollection::collectionName());
            if (!$this->collection->existsInDatabase()) {
                $this->collection = static::getDb()->getSchema()->createCollection($this->entityCollection::collectionName());
            }
        }

        return $this->collection;
    }

    /**
     * @inheritDoc
     */
    public function count(string $field = '_id'): int
    {
        $query = $this->buildQuery();
        $fields = ['COUNT(' . $field . ') as cnt'];
        if ($this->groupBy) {
            foreach ($this->groupBy as $groupBy) {
                $fields[] = $groupBy;
            }
        }
        if ($this->having) {
            foreach ($this->having as $having) {
                if (!in_array($having, $fields)) {
                    $fields[] = $having;
                }
            }
        }
        $query->fields($fields);

        $result = $query->execute()
            ->fetchOne();

        return isset($result['cnt']) ? (int)$result['cnt'] : 0;
    }

    /**
     * @return \mysql_xdevapi\DocResult
     * @throws \elfuvo\documentStore\Exception
     */
    public function find(): DocResult
    {
        return $this->buildQuery()
            ->execute();
    }

    /**
     * @inheritDoc
     */
    public function all(bool $populate = true): array
    {
        $items = $this->find()->fetchAll();
        if ($this->indexField) {
            $items = $this->indexing($items);
        }

        if ($populate) {
            return array_map(
                [$this, 'populate'],
                $items
            );
        }

        return $items;
    }

    /**
     * @return array|null
     * @throws \elfuvo\documentStore\Exception
     */
    public function fetch(): ?array
    {
        if (!$this->sqlState) {
            $this->sqlState = $this->find();
        }

        return $this->sqlState->fetchOne();
    }

    /**
     * @inheritDoc
     */
    public function one(bool $populate = true)
    {
        if ($populate) {
            return $this->populate($this->find()->fetchOne());
        }

        return $this->find()->fetchOne();
    }

    /**
     * @param array|null $data
     * @return EntityInterface|null
     */
    protected function populate(?array $data): ?EntityInterface
    {
        return $data ? $this->entityCollection->populate($data) : null;
    }

    /**
     * @return \mysql_xdevapi\CollectionFind
     * @throws \elfuvo\documentStore\Exception
     */
    protected function buildQuery(): CollectionFind
    {
        // reset sql state for new query
        $this->sqlState = null;
        $query = $this->getCollection()
            ->find($this->getWhere())
            ->bind($this->getBind());

        if ($this->getSort()) {
            // there is must be an array of column names
            $query->sort($this->getSort());
        }
        if ($this->getOffset()) {
            $query->offset($this->getOffset());
        }
        if ($this->getLimit()) {
            $query->limit($this->getLimit());
        }
        if ($this->getFields()) {
            // there is must be an array of column names
            $query->fields($this->getFields());
        }
        if ($this->getHaving()) {
            $query->having($this->getHaving());
        }
        if ($this->getGroupBy()) {
            // there is must be an array of column names
            // ensure that you use aggregate functions for non-grouped fields
            $query->groupBy($this->getGroupBy());
        }

        return $query;
    }

    /**
     * Save document entity to database either creates a new document or
     * replaces an existing one.
     * @param EntityInterface $entity
     * @return Result
     * @throws \elfuvo\documentStore\Exception
     */
    public function save(EntityInterface $entity): Result
    {
        try {
            if (!$entity->getId()) {
                return $this->getCollection()->add($entity->extract())->execute();
            } else {
                return $this->getCollection()->addOrReplaceOne($entity->getId(), $entity->extract());
            }
        } catch (\mysql_xdevapi\Exception $e) {
            throw new Exception(
                $e->getPrevious() ? $e->getPrevious()->getMessage() : $e->getMessage()
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function delete(string $id): Result
    {
        return $this->getCollection()
            ->removeOne($id);
    }

    /**
     * get raw sql for mysqlsh JS
     *
     * @return string
     * @throws \elfuvo\documentStore\Exception
     */
    public function getRawSql(): string
    {
        $sql = 'db.' . $this->getCollection()->getName();
        $sql .= '.find(\'' . addslashes($this->getWhere()) . '\')';
        if ($this->getFields()) {
            $sql .= '.fields(' . json_encode($this->getFields()) . ')';
        }
        if ($this->getHaving()) {
            $sql .= '.having(\'' . addslashes($this->getHaving()) . '\')';
        }
        if ($this->getGroupBy()) {
            $sql .= '.groupBy(' . json_encode($this->getGroupBy()) . ')';
        }
        if ($this->getSort()) {
            $sql .= '.sort(' . json_encode($this->getSort()) . ')';
        }
        if ($this->getOffset()) {
            $sql .= '.offset(\'' . $this->getOffset() . '\')';
        }
        if ($this->getLimit()) {
            $sql .= '.limit(\'' . $this->getLimit() . '\')';
        }
        if ($this->getBind()) {
            foreach ($this->getBind() as $bind => $value) {
                if (!is_string($value)) {
                    $sql .= '.bind(\'' . $bind . '\', ' . json_encode($value) . ')';
                } else {
                    $value = preg_replace('#\'#', '\\\'', $value);
                    $sql .= '.bind(\'' . $bind . '\', \'' . $value . '\')';
                }
            }
        }

        return $sql;
    }

    /**
     * @return \elfuvo\documentStore\connection\ConnectionInterface|null
     */
    public static function getDb(): ConnectionInterface
    {
        return static::$db;
    }

    /**
     * @param \elfuvo\documentStore\connection\ConnectionInterface $db
     */
    public static function setDb(ConnectionInterface $db): void
    {
        static::$db = $db;
    }
}
