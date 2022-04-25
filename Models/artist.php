<?php

require_once('../DBConfig/config.php');
require_once('../Models/authentication.php');

class Artist extends DB{
    function list(){
        
        $query = "SELECT * FROM `artist`";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt;
    }
    
    function search($name){
        $query = 'SELECT * FROM `artist` WHERE `Name` LIKE ?';

        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['%' . $name . '%']);
        return $stmt;
    }

    function get($id){
        $query = 'SELECT * FROM `artist` WHERE ArtistId = ?';

        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':id', "%$id%");
        $stmt->execute([$id]);
        return $stmt;
    }

    function addArtist($request){
        $query  = $this->pdo->prepare("INSERT INTO `Artist` (`Name`) VALUES ( :name )");
        $query->bindParam(':name', $request['Name']);

        if ($query->execute()) {
            return true;
        } 
        else{
            return false;
        }
    }

    function delete($id) {
        $query  = $this->pdo->prepare("DELETE A from invoiceline A INNER JOIN track B ON A.TrackId = B.TrackId INNER JOIN album C ON B.AlbumId = C.AlbumId INNER JOIN artist D ON C.ArtistId = D.ArtistId WHERE D.ArtistId = :id; DELETE E FROM track E INNER JOIN album F ON E.AlbumId = F.AlbumId INNER JOIN artist G ON F.ArtistId = G.ArtistId WHERE G.ArtistId = :id; DELETE FROM album WHERE album.ArtistId = :id; DELETE FROM artist WHERE artist.ArtistId = :id;");
        $query->bindParam(':id', $id);

        if ($query->execute()){
            return json_encode($id);
        } 
        else{
            return false;
        }
    }



}
?>