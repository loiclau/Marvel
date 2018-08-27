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

    /**
     * Playlists constructor.
     * @param $db
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        $sql = "SELECT * FROM " . $this->table . " ORDER BY `name` ASC";
        $stmt = $this->db->query($sql);
        $data = $stmt->fetchAll(\PDO::FETCH_OBJ);
        return array("data" => $data);
    }

    /**
     * @param $id
     * @return array
     */
    public function get($id)
    {
        $sql = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $data = $stmt->fetch(\PDO::FETCH_OBJ);
        return array("data" => $data);
    }

    /**
     * @param $id
     * @return array
     */
    public function getVideosFromPlaylists($id)
    {
        $sql = "SELECT v.*, pv.order FROM " . $this->table . " p , video v , playlist_to_video pv WHERE " .
            "p.id = pv.playlist_id AND pv.video_id = v.id AND p.id = :id ORDER BY pv.order";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $data = $stmt->fetchAll(\PDO::FETCH_OBJ);
        return array("data" => $data);;
    }

    /**
     * @param array $data
     * @return array
     */
    public function insert(array $data)
    {
        $sql = "INSERT INTO " . $this->table . " SET `name` = :name, created=:created";
        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(":name", $data['name']);
        $stmt->bindParam(":created", date('Y-m-d H:i:s'));
        $stmt->execute();

        $sql = "SELECT * FROM " . $this->table . " WHERE `name` = :name";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":name", $data['name']);
        $stmt->execute();
        $data = $stmt->fetch(\PDO::FETCH_OBJ);
        return array('data' => $data);
    }

    /**
     * @param array $data
     * @return array
     */
    public function addVideosToPlaylists(array $data)
    {
        $sql = "SELECT video_id, `order` FROM playlist_to_video WHERE playlist_id = :playlist_id " .
            "AND video_id != :video_id ORDER BY `order`";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":playlist_id", $data['playlist_id']);
        $stmt->bindParam(":video_id", $data['video_id']);
        $stmt->execute();
        $response = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $videoOrder = array('0' => '');
        foreach ($response AS $video) {
            array_push($videoOrder, $video['video_id']);
        }
        unset($videoOrder[0]);
        $newOrder = $this->orderPlaylist($videoOrder, $data['video_id'], $data['order']);

        $sql = "DELETE FROM playlist_to_video WHERE playlist_id = :playlist_id ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":playlist_id", $data['playlist_id']);
        $stmt->execute();

        $sql = "INSERT INTO `playlist_to_video` (`playlist_id`, `video_id`, `order`) VALUES";
        $sqlValue = '';

        foreach ($newOrder AS $order => $video) {
            $sqlValue .= '(' . $data['playlist_id'] . ', ' . $video . ', ' . $order . '),';
        }

        $sqlValue = rtrim($sqlValue, ', ');
        $sql .= $sqlValue;
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $data = $this->getVideosFromPlaylists($data['playlist_id']);

        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
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
        $sql = "DELETE FROM " . $this->table . " WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id", $id);
        $status = $stmt->execute();
        return $status;
    }

    /**
     * @param array $data
     * @return array
     */
    public function deleteVideosFromPlaylists(array $data)
    {
        $sql = "DELETE FROM playlist_to_video WHERE playlist_id = :playlist_id AND video_id = :video_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":playlist_id", $data['playlist_id']);
        $stmt->bindParam(":video_id", $data['video_id']);
        $stmt->execute();

        $data['video_id'] = 0;
        $data['order'] = 0;

        $data = $this->addVideosToPlaylists($data);

        return $data;
    }

    /**
     * @param $tab
     * @param $insertValue
     * @param $position
     * @return array
     */
    private function orderPlaylist($tab, $insertValue, $position)
    {
        $newTab = array('0' => '');
        $insert = false;
        foreach ($tab as $key => $value) {
            if ($key == $position) {
                array_push($newTab, $insertValue);
                $insert = true;
            }
            array_push($newTab, $value);
        }

        if (!$insert) {
            array_push($newTab, $insertValue);
        }
        unset($newTab[0]);
        return $newTab;
    }
}
