<?php
/**
 * Created by PhpStorm
 * User: elfuvo
 * Date: 2021-04-27
 * Time: 15:49
 */

use elfuvo\documentStore\connection\Connection;
use elfuvo\documentStore\migration\AbstractSchemaMigration;
use elfuvo\documentStore\migration\MigrationInterface;
use mysql_xdevapi\Schema;

/**
 * Class Migration
 */
class Migration extends AbstractSchemaMigration implements MigrationInterface
{
    /**
     * @var Schema
     */
    protected Schema $schema;

    /**
     * @return \mysql_xdevapi\Schema
     * @throws \elfuvo\documentStore\Exception
     */
    public function getSchema(): Schema
    {
        if (is_null($this->schema)) {
            $this->schema = (new Connection([
                'database' => 'project',
                'username' => 'user',
                'password' => '123',
            ]))->getSchema();
        }

        return $this->schema;
    }

}
