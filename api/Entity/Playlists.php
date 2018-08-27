<?php

namespace API\Entity;

class Playlists extends Webservice
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
        $sql = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array($id));
        $data = $stmt->fetch(\PDO::FETCH_OBJ);
        return $data;
    }

    public function getVideosFromPlaylists($id)
    {
        $sql = "SELECT v.* FROM " . $this->table . " p , video v , playlist_to_video pv WHERE " .
            "p.id = pv.playlist_id AND pc.video_id = v.id AND p.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array($id));
        $data = $stmt->fetch(\PDO::FETCH_OBJ);
        return $data;
    }

    public function insert(array $data)
    {

        $sql = "INSERT INTO " . $this->table . " SET `name` = :name, created=:created";
        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(":name", $data['name']);
        $stmt->bindParam(":created", date('Y-m-d H:i:s'));
        $status = $stmt->execute();

        $sql = "SELECT * FROM " . $this->table . " WHERE `name` = :name";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":name", $data['name']);
        $stmt->execute();
        $data = $stmt->fetch(\PDO::FETCH_OBJ);

        return array('status' => $status, 'data' => $data);
    }

    public function addVideosToPlaylist(array $data)
    {

        $sql = "INSERT INTO " . $this->table . " SET `name` = :name, created=:created";
        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(":name", $data['name']);
        $stmt->bindParam(":created", date('Y-m-d H:i:s'));
        $status = $stmt->execute();

        $sql = "SELECT * FROM " . $this->table . " WHERE `name` = :name";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":name", $data['name']);
        $stmt->execute();
        $data = $stmt->fetch(\PDO::FETCH_OBJ);

        return array('status' => $status, 'data' => $data);
    }

    public function update(array $data)
    {
        $queryData = '';
        $queryValues = array();
        $id = $data['id'];
        unset($data['id']);

        foreach ($data AS $key => $value) {
            $queryData .= '`' . $key . '` = :' . $key . ', ';
            $queryValues[$key] = $value;
        }
        $queryData = rtrim($queryData, ', ');

        $sql = "UPDATE " . $this->table . " SET " . $queryData . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        foreach ($queryValues AS $key => $value) {
            $stmt->bindParam(":" . $key, $value);
        }
        $stmt->bindParam(":id", $id);
        $status = $stmt->execute();

        $sql = "SELECT * FROM " . $this->table . " WHERE `id` = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $data = $stmt->fetch(\PDO::FETCH_OBJ);

        return array('status' => $status, 'data' => $data);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM " . $this->table . " WHERE id=?";
        $stmt = $this->db->prepare($sql);
        $status = $stmt->execute(array($id));
        return $status;
    }

    public function deleteVideosFromPlaylist(array $data)
    {

        $sql = "DELETE " . $this->table . " SET `name` = :name, created=:created";
        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(":name", $data['name']);
        $stmt->bindParam(":created", date('Y-m-d H:i:s'));
        $status = $stmt->execute();



        return array('status' => $status, 'data' => $data);
    }

    private function orderPlaylist($idPlaylist, $orderVideo, $order)
    {


    }


}
