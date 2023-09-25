<?php

namespace vendor\core;

use PDO;

abstract class Model
{
    protected $pdo;
    protected $table = '';
    protected $pk = 'id';

    public function __construct()
    {
        $this->pdo = Db::instance();
    }

    public function findAll()
    {
        $sql = "SELECT * FROM {$this->table}";

        return $this->pdo->query($sql)->fetchAll();
    }

    public function findOne($id, $field = '')
    {
        $data = [
            ':fields' => ($field ?: '*'),
            ':where_field' => $this->pk,
            ':id' => $id
        ];

        $sql = "SELECT :fields FROM {$this->table} WHERE :where_field = :id";

        return $this->pdo->query($sql, $data)->fetch();
    }
}
