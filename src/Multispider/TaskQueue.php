<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 20.10.16
 * Time: 2:51
 */

namespace Multispider;

/**
 * Работа с базой - упрощено
 * Class TaskQueue
 * @package Multispider
 */
class TaskQueue
{
    /**
     * @var \PDO
     */
    private $connection;
    /**
     * @var string
     */
    private $tableName;
    /**
     * @var int
     */
    private $insertBlockSize;
    /**
     * @var int
     */
    private $selectBlockSize;

    /**
     * TaskQueue constructor.
     * @param array $data
     */
    public function __construct(array $data = null)
    {
        try {
            $this->connection = new \PDO(Cli::app()->service('options')->db->connectionString, Cli::app()->service('options')->db->user, Cli::app()->service('options')->db->password);
        } catch (\Exception $exception) {
            Cli::app()->service('log')->error('PDO connection error');
            Cli::app()->exit(1, 'PDO connection error');
        }

        $this->tableName = Cli::app()->service('options')->db->tableName;
        $this->insertBlockSize = Cli::app()->service('options')->db->insertBlockSize;
        $this->selectBlockSize = Cli::app()->service('options')->db->selectBlockSize;

        if ($data)
            $this->storeData($data);
    }

    /**
     * close connection
     */
    public function __destruct()
    {
        $this->connection = null;
    }

    /**
     * init
     */
    public function init()
    {
        $this->query('DROP TABLE IF EXISTS ' . $this->tableName . ' CASCADE');
        $this->query('CREATE TABLE ' . $this->tableName . ' (id SERIAL PRIMARY KEY, path VARCHAR( 4095 ), mask VARCHAR( 150 ))');
    }

    /**
     * @param $sql
     * @return bool|\PDOStatement
     */
    private function query($sql)
    {
        $result = false;
        try {
            if (!$result = $this->connection->query($sql)) {
                $code = $this->connection->errorCode();
                $info = $this->connection->errorInfo();
                Cli::app()->service('log')->error('Query fail ' . $code . ' ' . $info[2]);
            }
        } catch (\PDOException $exception) {
            Cli::app()->service('log')->error('PDO Query fail');
        }
        return $result;
    }

    /**
     * Сохранить в таблицу "очереди"
     * @param array $data
     */
    public function storeData(array $data)
    {
        while ($data) {
            $query = '';
            $aliasList = [];
            $prepareAll = true;
            for ($i = 0; $i < $this->insertBlockSize && !empty($data); $i++) {
                $taskData = array_shift($data);

                if ($taskData instanceof TaskData) {
//                    $query .= '(' . pg_escape_string($taskData->getPath()) . ', ' . pg_escape_string($taskData->getMask()) . '), ';
                    $aliasList[':' . $i . '_0'] = $taskData->getPath();
                    $aliasList[':' . $i . '_1'] = $taskData->getMask();

                    $query .= '(:' . $i . '_0' . ', :' . $i . '_1' . '), ';
                } else {
                    Cli::app()->service('log')->error('Not a TaskData');
                    $prepareAll = false;
                }
            }
            $prepareRack = $i == $this->insertBlockSize;
            if ($query) {
                $query = 'INSERT INTO ' . $this->tableName . ' (path, mask) VALUES ' . substr($query, 0, -2);
                try {
                    if ($prepareAll && $prepareRack) {
                        $statement = $statement ?? $this->connection->prepare($query);
                        $statement->execute($aliasList);
                    } else {
                        $statementPart = $this->connection->prepare($query);
                        $statementPart->execute($aliasList);
                    }

                } catch (\PDOException $exception) {
                    Cli::app()->service('log')->error('Insert error');
                }
            }
        };
        if (!empty($statement) && $statement instanceof \PDOStatement) {
            $statement->closeCursor() && $statementPart = null;
        }
        if (!empty($statementPart) && $statementPart instanceof \PDOStatement) {
            $statementPart->closeCursor() && $statementPart = null;
        }
    }

    /**
     * Прочитать кусок таблицы "очереди"
     * полагаем что прочитанные данные будут обработаны
     * @return array
     */
    public function restoreData(): array
    {
        $resultArray = [];
        try {
            $this->connection->beginTransaction();

            $stmt = $this->connection->prepare("SELECT path, mask FROM " . $this->tableName . " LIMIT " . $this->selectBlockSize);
            if ($stmt->execute()) {
                while ($row = $stmt->fetch()) {
                    $resultArray[] = new TaskData($row[0], $row[1]);
                }
            } else {
                $code = $this->connection->errorCode();
                $info = $this->connection->errorInfo();
                throw new \Exception();
            }

            $count = $this->connection->exec("DELETE FROM " . $this->tableName . " WHERE id IN ( SELECT id FROM " . $this->tableName . " LIMIT " . $this->selectBlockSize . ")" );
            if (!$count) {
                $code = $this->connection->errorCode();
                $info = $this->connection->errorInfo();
                throw new \Exception();
            }

            $this->connection->commit() || Cli::app()->service('log')->error("Transaction commit failed");
        } catch (\Exception $exception) {
            $this->connection->rollBack() || Cli::app()->service('log')->error("Transaction rollback failed");;
            $resultArray = [];
        }
        return $resultArray;
    }
}