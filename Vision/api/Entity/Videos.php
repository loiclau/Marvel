<?php

namespace API\Entity;

class Videos extends Webservice
{

    // database connection and table name
    const TABLE = "video";
    const ORDER = "title";
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
     * @param $id
     * @return array
     */
    public function getPlaylistsFromVideos(int $id): array
    {
        $sql = "SELECT p.* FROM " . self::TABLE . " v , playlist p , playlist_to_video pv WHERE " .
            "p.id = pv.playlist_id AND pv.video_id = v.id AND v.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $data = $stmt->fetchAll(\PDO::FETCH_OBJ);
        return array('data' => $data);
    }
}
