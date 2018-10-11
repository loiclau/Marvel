<?php

namespace API\Entity;

class Webservice implements WebserviceInterface
{
    private $db;

    /**
     * Webservice constructor.
     * @param $db
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * @return array
     */
    public function getAll() : array
    {
        $sql = "SELECT * FROM `" . static::TABLE . "` ORDER BY `" . static::ORDER . "` ASC";
        $stmt = $this->db->query($sql);
        $data = $stmt->fetchAll(\PDO::FETCH_OBJ);
        return array("data" => $data);
    }

    /**
     * @param $id
     * @return array
     */
    public function get($id) : array
    {
        $sql = "SELECT * FROM `" . static::TABLE . "` WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $data = $stmt->fetch(\PDO::FETCH_OBJ);
        return array('data' => $data);
    }

    /**
     * @param array $data
     * @return array
     */
    public function insert(array $data) : array
    {
        $queryData = '';
        $queryValues = array();

        foreach ($data AS $key => $value) {
            $queryData .= '`' . $key . '` = :' . $key . ', ';
            $queryValues[$key] = $value;
        }
        $queryData = rtrim($queryData, ', ');

        $sql = "INSERT INTO `" . static::TABLE . "` SET " . $queryData;
        $stmt = $this->db->prepare($sql);

        foreach ($queryValues AS $key => $value) {
            $stmt->bindValue(":" . $key, $value);
        }
        $stmt->execute();

        $sql = "SELECT * FROM `" . static::TABLE . "` WHERE id = (SELECT MAX(id) FROM `" . static::TABLE . "`)";

        $stmt = $this->db->query($sql);
        $data = $stmt->fetch(\PDO::FETCH_OBJ);
        return array('data' => $data);
    }

    /**
     * @param array $data
     * @return array
     */
    public function update(array $data) : array
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

        $sql = "UPDATE `" . static::TABLE . "` SET " . $queryData . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        foreach ($queryValues AS $key => $value) {
            $stmt->bindParam(":" . $key, $value);
        }
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        $data = $this->get($id);
        return $data;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $sql = "DELETE FROM `" . static::TABLE . "` WHERE id= :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id", $id);
        $status = $stmt->execute();
        return $status;
    }
}
