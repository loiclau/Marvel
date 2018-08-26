<?php

namespace API\Entity;

class Videos extends Webservice
{

    protected $db;
    protected $table;

    // object properties
    public $id;
    public $title;
    public $thumbnail;
    public $created;

    public function __construct($db)
    {
        $this->table = 'video';
        $this->db = $db;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM " . $this->table . " ORDER BY title ASC";
        $stmt = $this->db->query($sql);
        $data = $stmt->fetchAll(\PDO::FETCH_OBJ);
        return $data;
    }

    public function get($id)
    {
        $sql = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array($id));
        $data = $stmt->fetch(\PDO::FETCH_OBJ);
        return $data;
    }

    public function insert(array $data)
    {

        $sql = "INSERT INTO " . $this->table . " SET title = :title, thumbnail=:thumbnail, created=:created";
        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(":title", $data['title']);
        $stmt->bindParam(":thumbnail", $data['thumbnail']);
        $stmt->bindParam(":created", date('Y-m-d H:i:s'));
        $status = $stmt->execute();

        $sql = "SELECT * FROM " . $this->table . " WHERE title = :title";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":title", $data['title']);
        $stmt->execute();
        $data = $stmt->fetch(\PDO::FETCH_OBJ);

        return array('status' => $status, 'data' => $data);
    }

    public function update(array $data)
    {
        $sql = "UPDATE " . $this->table . " SET";
        $stmt = $this->db->prepare($sql);
        $status = $stmt->execute(array());
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
