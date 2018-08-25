<?php

namespace API\Entity;

class Playlist
{

    // database connection and table name
    private $db;
    private $table = "playlist";

    // object properties
    public $id;
    public $name;
    public $order;
    public $created;

    // constructor with $db as database connection
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM " . $this->table . " ORDER BY `name` ASC";
        $stmt = $this->db->query($sql);
        $data = $stmt->fetchAll(\PDO::FETCH_OBJ);
        return $data;
    }

    public function get($id)
    {
        $sql = "SELECT * FROM " . $this->table . " WHERE id=?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array($id));
        $data = $stmt->fetch(\PDO::FETCH_OBJ);
        return $data;
    }

    public function insert(array $data)
    {
        $sql = "INSERT INTO " . $this->table . " (`name`, `order`,`created`) VALUES (?,?,?)";
        $stmt = $this->db->prepare($sql);
        $status = $stmt->execute($data);
        return $status;
    }


    public function update(array $data)
    {
        $sql = "UPDATE " . $this->table . " SET";
        $stmt = $this->db->prepare($sql);
        $status = $stmt->execute($data);
        return $status;
    }

    public function delete($id)
    {
        $sql = "DELETE FROM " . $this->table . " WHERE id=?";
        $stmt = $this->db->prepare($sql);
        $status = $stmt->execute(array($id));
        return $status;
    }
}
