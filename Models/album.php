<?php

require_once('../DBConfig/config.php');

class Album extends DB{
    function list(){
        
        $query = "SELECT album.Title, artist.Name, album.AlbumId, album.ArtistId FROM `album` INNER JOIN artist ON album.ArtistId=artist.ArtistId";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    function get($id){
        $query = "SELECT album.*, artist.Name FROM `album` INNER JOIN artist ON album.ArtistId=artist.ArtistId WHERE AlbumId = ?";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':id', "%$id%");
        $stmt->execute([$id]);
        return $stmt;
    }

    function search($name){
      $query = "SELECT album.*, artist.Name FROM `album` INNER JOIN artist ON album.ArtistId=artist.ArtistId WHERE `Title` LIKE ?";

      $stmt = $this->pdo->prepare($query);
      $stmt->execute(['%' . $name . '%']);
      return $stmt;
    }

    function addAlbum($request) {
        $query = $this->pdo->prepare('INSERT INTO `album` (`Title`, `ArtistId`) VALUES (:title,:artistid)');
        $query->bindParam(':title', $request['Title']);
        $query->bindParam(':artistid', $request['ArtistId']);
        if ($query->execute()) {
            return true;
          } else{
            return false;
          }
      }

    function delete($id) {                                                  
        $query  = $this->pdo->prepare("DELETE A from invoiceline A INNER JOIN track B ON A.TrackId = B.TrackId WHERE B.AlbumId = :id; DELETE C FROM track C INNER JOIN album D ON C.AlbumId = D.AlbumId WHERE D.AlbumId = :id; DELETE FROM album WHERE album.AlbumId = :id; ");
        $query->bindParam(':id', $id);
        if ($query->execute()) {
            return true;
          } else{
            return false;
          }
    }

}
?>