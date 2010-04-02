<?php
class DataBase {
private $dbconn = "mysql:host=llde958.servidoresdns.net;dbname=qfz238";
private $user = "qfz238";
private $pass = "aLeX84";

function __construct() {
    $this->dbh = new PDO($this->dbconn, $this->user, $this->pass) or die ('Error connecting to mysql');
}

function get_new_id() {
    try {
        $sql = "SELECT id FROM coordinates ORDER BY id DESC LIMIT 0,1";
        $statement = $this->dbh->prepare($sql);
        $statement->execute();
        return $statement->fetch();
    } catch(Exception $e) {
        die("DB error!");
    }
}

function insert($id, $lat, $lng) {
    try {
        //Check if exists
        $sql = "SELECT id FROM coordinates WHERE id = ?";
        $statement = $this->dbh->prepare($sql);
        $statement->execute(array($id));

        if ($statement->fetch() == null)
            $sql = "INSERT INTO coordinates (id, latitude, longitude) VALUES (:id, :latitude, :longitude)";
        else
            $sql = "UPDATE coordinates SET latitude = :latitude, longitude = :longitude WHERE id = :id";

        $statement = $this->dbh->prepare($sql);
        $statement->bindParam(":id", $id);
        $statement->bindParam(":latitude", $lat);
        $statement->bindParam(":longitude", $lng);
        $statement->execute();
    } catch (Exception $e) {
        die("DB error!");
    }
}

function select($id) {
    try {
        $sql = "SELECT latitude, longitude FROM coordinates WHERE id = ?";
        $statement = $this->dbh->prepare($sql);
        $statement->execute(array($id));
        return $statement->fetch();
    } catch(Exception $e) {
        die("DB error!");
    }
}

function __destruct() {
    $this->dbh = null;
}
}
?>
