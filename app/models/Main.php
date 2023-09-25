<?php

namespace app\models;

use PDO;
use vendor\core\Model;

class Main extends Model
{
    public $table = 'tree.tree';

    public function treeAll()
    {
        $sql = "SELECT id, pid, name FROM {$this->table} ORDER BY id";

        return $this->pdo->query($sql)->fetchAll();
    }

    public function renameBranch($id, $name)
    {
        $response = false;

        $data = [
            ':id' => empty($id) ? 1 : $id,
            ':name' => $name
        ];

        $sql = "UPDATE {$this->table}
                SET
                    name = :name 
                WHERE id = :id";

        if (!$response = $this->pdo->execute($sql, $data)) {
            $sql = "INSERT INTO {$this->table} (id, pid, name) VALUES (:id, 0, :name)
                    ON CONFLICT (id) DO NOTHING";
            $response = $this->pdo->execute($sql, $data);
        }

        return $response;
    }

    public function addBranch($pid, $name)
    {
        $data = [
            ':pid' => empty($pid) ? 1 : $pid,
            ':name' => $name
        ];

        $sql = "WITH parent_insert AS (
                    INSERT INTO {$this->table} (id, pid, name) VALUES (:pid, 0, 'Root')
                    ON CONFLICT (id) DO NOTHING
                    RETURNING id)
                INSERT INTO {$this->table} (pid, name) VALUES (COALESCE((SELECT id FROM parent_insert), :pid), :name)
                ON CONFLICT (id) 
                DO UPDATE 
                    SET 
                        pid = EXCLUDED.pid, 
                        name = EXCLUDED.name
                RETURNING id, pid";

        return $this->pdo->query($sql, $data)->fetch();
    }

    public function deleteBranch($id)
    {
        $data = [
            ':id' => $id
        ];

        $sql = "WITH RECURSIVE rec_table AS (
                    SELECT id, pid
                    FROM {$this->table}
                    WHERE id = :id
                    UNION ALL
                    SELECT t.id, t.pid
                    FROM {$this->table} t
                    JOIN rec_table d ON t.pid = d.id
                )
                DELETE FROM {$this->table}
                WHERE id IN (SELECT id FROM rec_table)
                RETURNING id";


        return $this->pdo->query($sql, $data)->fetch(PDO::FETCH_COLUMN);
    }

    public function countRecords()
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";

        return $this->pdo->query($sql)->fetch(PDO::FETCH_COLUMN);
    }

    public function getName($id)
    {
        $data = [
            ':id' => $id
        ];

        $sql = "SELECT name FROM {$this->table} WHERE id = :id";

        return $this->pdo->query($sql, $data)->fetch(PDO::FETCH_COLUMN);
    }

    public function countChild($id)
    {
        $data = [
            ':id' => $id
        ];

        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE pid = :id";

        return $this->pdo->query($sql, $data)->fetch(PDO::FETCH_COLUMN);
    }
}
