<?php
/**
 * Created by PhpStorm
 * User: elfuvo
 * Date: 2021-04-20
 * Time: 10:14
 */

namespace elfuvo\documentStore\collection;

use elfuvo\documentStore\entity\EntityInterface;
use elfuvo\documentStore\repository\RepositoryInterface;

/**
 * Interface CollectionInterface
 * @package elfuvo\documentStore\collection
 */
interface CollectionInterface
{
    /**
     * get collection name for current model
     *
     * @return string
     */
    public static function collectionName(): string;

    /**
     * @return \elfuvo\documentStore\repository\RepositoryInterface
     */
    public function getRepository(): RepositoryInterface;

    /**
     * @return \elfuvo\documentStore\entity\EntityInterface
     */
    public function getEntity(): EntityInterface;

    /**
     * populate data as entity
     *
     * @param array $data
     * @return \elfuvo\documentStore\entity\EntityInterface
     */
    public function populate(array $data): EntityInterface;

    /**
     *
     * Save document entity to database either creates a new document or
     * replaces an existing one.
     *
     * @param EntityInterface $entity
     * @return bool
     * @throws \elfuvo\documentStore\Exception
     */
    public function save(EntityInterface $entity): bool;

    /**
     * Deletes document entity from database
     *
     * @param EntityInterface $entity
     * @return bool
     * @throws \elfuvo\documentStore\Exception
     */
    public function delete(EntityInterface $entity): bool;
}
