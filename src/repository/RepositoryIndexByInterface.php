<?php
/**
 * Created by PhpStorm
 * User: elfuvo
 * Date: 2021-07-13
 * Time: 10:41
 */

namespace elfuvo\documentStore\repository;

/**
 * Interface RepositoryIndexByInterface
 * @package elfuvo\documentStore\repository
 */
interface RepositoryIndexByInterface
{
    public const PICKING_STRATEGY_FIRST = 'first';
    public const PICKING_STRATEGY_LAST = 'last';

    /**
     * @param string $field
     * @param string $strategy
     * @return $this
     */
    public function indexBy(
        string $field,
        string $strategy = RepositoryIndexByInterface::PICKING_STRATEGY_FIRST
    ): self;
}
