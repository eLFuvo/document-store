<?php
/**
 * Created by PhpStorm
 * User: elfuvo
 * Date: 2021-04-20
 * Time: 10:21
 */

namespace elfuvo\documentStore\collection;

use elfuvo\documentStore\entity\EntityInterface;
use elfuvo\documentStore\repository\RepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Class AbstractCollection
 * @package elfuvo\documentStore\collection
 */
abstract class AbstractCollection extends Collection implements CollectionInterface
{
    /**
     * cache or not selected documents
     * after caching you can sort/filter documents
     * @see ArrayCollection::filter()
     *
     * @var bool
     */
    protected bool $cache = false;

    /**
     * @var \elfuvo\documentStore\repository\RepositoryInterface|null
     */
    protected ?RepositoryInterface $repository = null;

    /**
     * @var string
     */
    protected string $entityClass;

    /**
     * @inheritDoc
     */
    abstract public static function collectionName(): string;

    /**
     * @inheritDoc
     */
    abstract public function getRepository(): RepositoryInterface;

    /**
     * @inheritDoc
     */
    public function getEntity(): EntityInterface
    {
        return new $this->entityClass();
    }

    /**
     * @param array $data
     * @return \elfuvo\documentStore\entity\EntityInterface
     */
    public function populate(array $data): EntityInterface
    {
        $entity = new $this->entityClass();
        $entity->populate($data);
        if ($this->cache) {
            $this->add($entity);
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function save(EntityInterface $entity): bool
    {
        $result = $this->getRepository()->save($entity);
        if (!$entity->getId() && $ids = $result->getGeneratedIds()) {
            $entity->setId(array_shift($ids));
        }

        return $result->getWarningsCount() === 0;
    }

    /**
     * @inheritDoc
     */
    public function delete(EntityInterface $entity): bool
    {
        $result = $this->getRepository()->delete($entity->getId());

        return $result->getWarningsCount() === 0;
    }
}
