<?php
/**
 * Created by PhpStorm
 * User: elfuvo
 * Date: 2021-04-27
 * Time: 15:44
 */

namespace elfuvo\documentStore\migration;

use mysql_xdevapi\Schema;

/**
 * Trait SchemaMigrationTrait
 * @package elfuvo\documentStore\migration
 */
abstract class AbstractSchemaMigration implements MigrationInterface
{
    use SchemaMigrationTrait;

    /**
     * @inheritDoc
     */
    abstract public function getSchema(): Schema;
}
