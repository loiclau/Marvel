<?php

namespace API\Entity;

class Playlists extends Webservice
{

    // database connection and table name
    const TABLE = "playlist";
    const ORDER = "name";
    protected $db;

    /**
     * Playlists constructor.
     * @param $db
     */
    public function __construct($db)
    {
        parent::__construct($db);
        $this->db = $db;
    }

    /**
     * @param array $data
     * @return array
     */
    public function insert(array $data): array
    {
        $data['created'] = date('Y-m-d H:i:s');
        $return = parent::insert($data);
        return $return;
    }

    /**
     * @param $id
     * @return array
     */
    public function getVideosFromPlaylists(int $id): array
    {
        $sql = "SELECT v.*, pv.order FROM " . self::TABLE . " p , video v , playlist_to_video pv WHERE " .
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
    public function addVideosToPlaylists(array $data): array
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
    public function deleteVideosFromPlaylists(array $data): array
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
    private function orderPlaylist($tab, $insertValue, $position): array
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
