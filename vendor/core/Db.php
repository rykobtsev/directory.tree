<?php

namespace vendor\core;

use PDO;

class Db
{
    private PDO $pdo;
    private static $instance;
    public static $countSql = 0;
    public static $queries = [];

    private function __construct()
    {
        $db = require ROOT . "/settings/config_db.php";

        $this->pdo = new PDO(
            'pgsql:
                host=' . $db['db_server'] . ';
                port=' . $db['db_port'] . ';
                dbname=' . $db['db_name'],
            $db['db_user'],
            $db['db_pass'],
            [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
            ]
        );
    }

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function execute($sql, $args = [])
    {
        self::$countSql++;
        self::$queries[] = $sql;

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($args);
    }

    public function query($sql, $args = [])
    {
        self::$countSql++;
        self::$queries[] = $sql;

        $stmt = $this->pdo->prepare($sql);

        if (empty($args)) {
            $stmt->execute();
        } else {
            if (isset($args[0])) {
                $stmt->execute($args);
            } else {
                foreach ($args as $key => $value) {
                    switch (gettype($value)) {
                        case 'integer':
                            $stmt->bindValue($key, $value, PDO::PARAM_INT);
                            break;
                        case 'boolean':
                            $stmt->bindValue($key, $value, PDO::PARAM_BOOL);
                            break;
                        case 'NULL':
                            $stmt->bindValue($key, $value, PDO::PARAM_NULL);
                            break;
                        default:
                            $stmt->bindValue($key, $value, PDO::PARAM_STR);
                            break;
                    }
                }
                $stmt->execute();
            }
        }

        // if (!empty($stmt->errorInfo()[2])) {
        //     $response = [];
        // }

        return isset($stmt) ? $stmt : false;
    }
}
