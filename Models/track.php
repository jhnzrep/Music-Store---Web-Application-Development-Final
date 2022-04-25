<?php

require_once('../DBConfig/config.php');

class Track extends DB{

    function list(){
        
        $query = "SELECT * FROM `track`";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    function search($name){
        $query = 'SELECT * FROM `track` WHERE `Name` LIKE ?';

        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['%' . $name . '%']);
        return $stmt;
    }

    function get($id){
        $query = 'SELECT * FROM `track` WHERE TrackId = ?';

        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':id', "%$id%");
        $stmt->execute([$id]);
        return $stmt;

        /*$row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->Name = $row['Name'];
        $this->AlbumId = $row['AlbumId'];
        $this->MediaTypeId = $row['MediaTypeId'];
        $this->GenreId = $row['GenreId'];
        $this->Composer = $row['Composer'];
        $this->Milliseconds = $row['Milliseconds'];
        $this->Bytes = $row['Bytes'];
        $this->UnitPrice = $row['UnitPrice'];*/
    }

    function addTrack($request){
        $Name = $request['Name'];                                             
        $AlbumId = $request['AlbumId'];                                                  
        $MediaTypeId = $request['MediaTypeId'];                                                  
        $GenreId = $request['GenreId'];
        $Composer = $request['Composer'];                                                  
        $Milliseconds = $request['Milliseconds'];                                                  
        $Bytes = $request['Bytes'];   
        $UnitPrice = $request['UnitPrice']; 

        $query  = $this->pdo->prepare("INSERT INTO `track` (`Name`, `AlbumId`, `MediaTypeId`, `GenreId`, `Composer`, `Milliseconds`, `Bytes`, `UnitPrice`) VALUES ( :name, :albumid, :mediatypeid, :genreid, :composer, :milliseconds, :bytes, :unitprice )");
        $query->bindParam(':name', $Name);
        $query->bindParam(':albumid', $AlbumId);
        $query->bindParam(':mediatypeid', $MediaTypeId);
        $query->bindParam(':genreid', $GenreId);
        $query->bindParam(':composer', $Composer);
        $query->bindParam(':milliseconds', $Milliseconds);
        $query->bindParam(':bytes', $Bytes);
        $query->bindParam(':unitprice', $UnitPrice);

        if ($query->execute()) {
            return true;
        } 
        else{
            return false;
        }
    }

    function delete($id) {
        $query  = $this->pdo->prepare('DELETE FROM `Track` WHERE TrackId = :id;');
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