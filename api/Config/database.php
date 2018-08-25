<?php

namespace API\Config;

class Database
{

    // specify your own database credentials
    private $host = "localhost";
    private $dbName = "playlist";
    private $userName = "iamroot";
    private $password = "iamroot";
    public $db;

    /**
     * get the database connection
     *
     * @return PDO
     */
    public function getConnection()
    {
        $this->db = null;
        try {
            $this->db = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbName, $this->userName,
                $this->password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->db;
    }
}
