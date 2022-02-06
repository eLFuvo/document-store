<?php
/**
 * Created by PhpStorm
 * User: elfuvo
 * Date: 2021-04-20
 * Time: 15:00
 */

namespace elfuvo\documentStore\repository;

use elfuvo\documentStore\collection\CollectionInterface;
use elfuvo\documentStore\entity\EntityInterface;
use mysql_xdevapi\Collection;
use mysql_xdevapi\Result;

/**
 * Interface RepositoryInterface
 * @package elfuvo\documentStore\repository
 */
interface RepositoryInterface
{
    /**
     * RepositoryInterface constructor.
     *
     * @param CollectionInterface $schema
     */
    public function __construct(CollectionInterface $schema);

    /**
     * Counts all records in database according filter
     * @param string $field - field name that must be counted
     * @return int
     * @throws \elfuvo\documentStore\Exception
     */
    public function count(string $field = '_id'): int;

    /**
     * Get collection from the database.
     * @return Collection
     * @throws \elfuvo\documentStore\Exception
     */
    public function getCollection(): Collection;

    /**
     * Save entity to database either creates a new document or
     * replaces an existing one.
     * @param EntityInterface $entity
     * @return Result
     */
    public function save(EntityInterface $entity): Result;

    /**
     * Deletes document entity from database
     * @param string $id
     * @return Result
     * @throws \elfuvo\documentStore\Exception
     */
    public function delete(string $id): Result;

    /**
     * Fetch items one by one. Usage:
     * ```php
     * $list = [];
     *
     * $query = (new DocumentCollection)->getRepository()->sort(['_id desc']);
     * while($data = $query->fetch()){
     *  $list = (new DocumentEntity)->populate($data);
     * }
     * ```
     *
     * @return array|null
     * @throws \elfuvo\documentStore\Exception
     */
    public function fetch(): ?array;

    /**
     * @param bool $populate - convert raw data from DB to entities
     * @return array[]|EntityInterface[]
     * @throws \elfuvo\documentStore\Exception
     */
    public function all(bool $populate = true): array;

    /**
     * @param bool $populate - convert raw data from DB to entities
     * @return array|EntityInterface|null
     * @throws \elfuvo\documentStore\Exception
     */
    public function one(bool $populate = true);
}
