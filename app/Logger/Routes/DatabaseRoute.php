<?php

declare(strict_types=1);

namespace App\Logger\Routes;

use App\Logger\Route;
use PDO;


/**
 * Class DatabaseRoute
 *
 * Создание таблицы:
 *
 * CREATE TABLE default_log (
 *      id integer PRIMARY KEY,
 *      date date,
 *      level varchar(16),
 *      message text,
 *      context text
 * );
 */
class DatabaseRoute extends Route
{
    /**
     * @var string Data Source Name
     * @see http://php.net/manual/en/pdo.construct.php
     */
    public $dsn;
    /**
     * @var string Имя пользователя БД
     */
    public $username;
    /**
     * @var string Пароль пользователя БД
     */
    public $password;
    /**
     * @var string Имя таблицы
     */
    public $table;

    /**
     * @var PDO Подключение к БД
     */
    private $connection;

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->connection = new PDO($this->dsn, $this->username, $this->password);
    }

    /**
     * @inheritdoc
     */
    public function log($level, $message, array $context = []) : void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO ' . $this->table . ' (date, level, message, context) ' .
            'VALUES (:date, :level, :message, :context)'
        );
        $statement->bindParam(':date', $this->getDate());
        $statement->bindParam(':level', $level);
        $statement->bindParam(':message', $message);
        $statement->bindParam(':context', $this->contextStringify($context));
        $statement->execute();
    }
}
