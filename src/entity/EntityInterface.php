<?php
/**
 * Created by PhpStorm
 * User: elfuvo
 * Date: 2021-04-20
 * Time: 12:53
 */

namespace elfuvo\documentStore\entity;

/**
 * Interface DocumentEntityInterface
 * @package elfuvo\documentStore\entity
 */
interface EntityInterface
{
    /**
     * Returns a 32 character uuid or integer id
     * @return string|int|null
     */
    public function getId();

    /**
     * @param string $id
     */
    public function setId(string $id): void;

    /**
     * convert raw data to entity object
     *
     * @param array $data
     * @return \elfuvo\documentStore\entity\EntityInterface
     */
    public function populate(array $data): EntityInterface;

    /**
     * convert entity object to array
     *
     * @return array|null
     */
    public function extract(): ?array;
}
