<?php
/**
 * Created by PhpStorm
 * User: elfuvo
 * Date: 2021-04-16
 * Time: 12:52
 */

namespace elfuvo\documentStore\connection;

use elfuvo\documentStore\Exception;
use mysql_xdevapi\Schema;
use mysql_xdevapi\Session;
use mysql_xdevapi\SqlStatement;

use function mysql_xdevapi\getSession;

/**
 * Class Connection
 * @package elfuvo\documentStore
 */
class Connection implements ConnectionInterface
{
    /**
     * @var string|null the Data Source Name, or DSN, contains the information required to connect to the database.
     * Please refer to the [Mysql_xdevapi](https://www.php.net/manual/ru/function.mysql-xdevapi-getsession.php) on
     * the format of the DSN string.
     */
    public ?string $dsn = null;

    /**
     * @var string the hostname or ip address to use for connecting to the mysqlx server. Defaults to 'localhost'.
     */
    public string $hostname = 'localhost';

    /**
     * @var int the port to use for connecting to the mysql server. Default port is 33060.
     */
    public int $port = 33060;

    /**
     * @var string the username for establishing DB connection.
     */
    public string $username = '';

    /**
     * @var string the password for establishing DB connection.
     */
    public string $password = '';

    /**
     * @var string|null required
     * database (schema) name of Document store
     */
    public ?string $database = null;

    /**
     * @var array additional attributes for connection
     * @link https://www.php.net/manual/ru/function.mysql-xdevapi-getsession.php
     */
    public array $attributes = [];

    /**
     * @var bool whether to enable [savepoint](https://www.php.net/manual/ru/mysql-xdevapi-session.setsavepoint.php).
     */
    public bool $enableSavepoint = false;

    /**
     * @var \mysql_xdevapi\Session|null
     */
    protected ?Session $session = null;

    /**
     * @var \mysql_xdevapi\Schema|null
     */
    protected ?Schema $schema = null;

    /**
     * @var array transactions save points for rollback
     */
    protected array $savepoint = [];

    /**
     * @inheritDoc
     */
    public function getIsActive(): bool
    {
        return !is_null($this->session);
    }

    /**
     * @return string get/generate DSN string for connection
     * @throws \elfuvo\documentStore\Exception
     */
    protected function getDsn(): string
    {
        if (is_null($this->dsn)) {
            $this->dsn = "mysqlx://" .
                $this->username . ':' . $this->password . '@' .
                $this->hostname .
                ($this->port ? ':' . $this->port : '') .
                ($this->database ? '/' . $this->database : '');
        } elseif (!$this->database
            && preg_match('#/([\w]+)$#', $this->dsn,
                $matches)) { // get schema name from DSN string 'mysqlx://root@localhost:33060/default-schema'
            $this->database = $matches[1];
            $this->dsn = (string)preg_replace('#/([\w]+)$#', '', $this->dsn);
        } elseif (!$this->dsn) {
            throw new Exception('DSN is invalid. Must be in format mysqlx://[user]:[password]@[host]:[port]/[schema]');
        }

        return $this->dsn;
    }

    /**
     * @inheritDoc
     */
    public function open()
    {
        if ($this->session !== null) {
            return;
        }

        $dsn = $this->getDsn();
        if (!$this->database) {
            throw new Exception('Name of the database must be set');
        }

        $this->session = getSession($dsn .
            ($this->attributes ? '?' . http_build_query($this->attributes) : '')
        );
        $this->query('use ' . $this->database)->execute();

        $this->schema = $this->session->getSchema($this->session->quoteName($this->database));
        if (!$this->schema->existsInDatabase()) {
            throw new Exception('Schema "' . $this->database . '" does not exists.');
        }
    }

    /**
     * @inheritDoc
     */
    public function close()
    {
        if ($this->session !== null) {
            $this->session->close();
            $this->session = null;
        }
        $this->schema = null;
        $this->savepoint = [];
    }

    /**
     * @inheritDoc
     */
    public function beginTransaction(): void
    {
        $this->open();

        $this->session->startTransaction();
        if ($this->enableSavepoint === true) {
            // set save point for transaction
            $savepoint = uniqid('transaction-');
            /**
             * @link https://www.php.net/manual/en/mysql-xdevapi-session.setsavepoint.php
             */
            $this->session->setSavepoint($savepoint);

            // remember last savepoint
            array_push($this->savepoint, $savepoint);
        }
    }

    /**
     * @inheritDoc
     */
    public function commit(): object
    {
        /** @var \mysql_xdevapi\SqlStatementResult $result */
        $result = $this->session->commit();
        if ($result->getWarningsCount() === 0) {// for db save point is already removed by commit operation
            array_pop($this->savepoint);
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function rollback(): void
    {
        $savepoint = array_pop($this->savepoint);
        if ($savepoint) {
            $this->session->rollbackTo($savepoint);
            $this->session->releaseSavepoint($savepoint);
        } else {
            $this->session->rollback();
        }
    }

    /**
     * @inheritDoc
     */
    public function generateUUID(): string
    {
        $this->open();

        return $this->session->generateUUID();
    }

    /**
     * @inheritDoc
     */
    public function query(string $query): SqlStatement
    {
        $this->open();

        return $this->session->sql($query);
    }

    /**
     * @inheritDoc
     */
    public function getSchema(): Schema
    {
        $this->open();

        return $this->schema;
    }

    /**
     * @inheritDoc
     */
    public function quoteName($name): string
    {
        return $this->session->quoteName($name);
    }

    /**
     * @inheritDoc
     */
    public function getDriverName(): string
    {
        return 'mysqlx';
    }

    /**
     * @inheritDoc
     */
    public function getServerVersion(): string
    {
        $this->open();

        return $this->session->getServerVersion();
    }

    /**
     * Reset the connection after cloning.
     */
    public function __clone()
    {
        $this->session = null;
        $this->schema = null;
        $this->savepoint = [];
    }
}
