<?php
/**
 * Created by PhpStorm
 * User: elfuvo
 * Date: 2021-04-16
 * Time: 12:52
 */

namespace elfuvo\documentStore\connection;

use mysql_xdevapi\Schema;
use mysql_xdevapi\SqlStatement;

/**
 * Interface ConnectionInterface
 * @package elfuvo\documentStore\connection
 */
interface ConnectionInterface
{
    /**
     * @return bool check connection is alive
     */
    public function getIsActive(): bool;

    /**
     * Establishes a DB connection.
     * It does nothing if a DB connection has already been established.
     *
     * @return void
     * @throws \elfuvo\documentStore\Exception
     * @throws \mysql_xdevapi\Exception
     */
    public function open();

    /**
     * Closes the currently active DB connection.
     * It does nothing if the connection is already closed.
     *
     * @return void
     */
    public function close();

    /**
     * @return \mysql_xdevapi\Schema
     * @throws \elfuvo\documentStore\Exception
     *
     * @link https://www.php.net/manual/ru/mysql-xdevapi-session.getschema.php
     */
    public function getSchema(): Schema;

    /**
     * Start a new transaction.
     * @link https://www.php.net/manual/en/mysql-xdevapi-session.starttransaction.php
     *
     * @throws \elfuvo\documentStore\Exception
     *
     * @return void
     */
    public function beginTransaction(): void;

    /**
     * Commit the transaction.
     * @link https://www.php.net/manual/en/mysql-xdevapi-session.commit.php
     *
     * @return object|\mysql_xdevapi\SqlStatementResult - An SqlStatementResult object.
     * @link https://www.php.net/manual/en/class.mysql-xdevapi-sqlstatementresult.php
     */
    public function commit(): object;

    /**
     * Rollback the transaction.
     * @link https://www.php.net/manual/en/mysql-xdevapi-session.rollback.php
     */
    public function rollback(): void;

    /**
     * Generate a Universal Unique IDentifier (UUID) generated according to » RFC 4122.
     * @link https://www.php.net/manual/en/mysql-xdevapi-session.generateuuid.php
     *
     * @throws \elfuvo\documentStore\Exception
     *
     * @return string
     */
    public function generateUUID(): string;

    /**
     * @param string $query
     * @return \mysql_xdevapi\SqlStatement
     * @throws \elfuvo\documentStore\Exception
     */
    public function query(string $query): SqlStatement;

    /**
     * Add quotes
     * A quoting function to escape SQL names and identifiers. It escapes the identifier given in accordance to the settings of the current connection. This escape function should not be used to escape values.
     * @link https://www.php.net/manual/en/mysql-xdevapi-session.quotename.php
     * @param string $name
     * @return string
     */
    public function quoteName(string $name): string;

    /**
     * @return string name of the DB driver
     */
    public function getDriverName(): string;

    /**
     * @return string
     * @throws \elfuvo\documentStore\Exception
     */
    public function getServerVersion(): string;
}
