<?php

require_once ('../Models/album.php');
require_once ('../Models/authentication.php');

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$album = new Album();
$authentication = new Authentication();
$request = json_decode(file_get_contents('php://input'), true);

switch($_SERVER['REQUEST_METHOD']){
    case 'GET':
        if(isset($_GET['name'])){
            $stmt = $album->search($_GET['name']);
            $num = $stmt->rowCount();
            if($num>0){
                $album_arr["album"]=array();
                while ($row = $stmt->fetch()){
            
                    extract($row);
                
                    $album_item=array(
                        "AlbumId" => $AlbumId,
                        "Title" => $Title,
                        "Name" => $Name,
                    );
                
                    array_push($album_arr["album"], $album_item);
                }
            
                http_response_code(200);
            
                echo json_encode($album_arr["album"]);
            } else {
                
                http_response_code(404);
                echo json_encode(
                    array("message" => "No Albums with that Id found")
                );
            }
        }
        else if(isset($_GET['id'])){
            $stmt = $album->get($_GET['id']);
            $num = $stmt->rowCount();
            if($num>0){
                $album_arr["album"]=array();
                while ($row = $stmt->fetch()){
            
                    extract($row);
                
                    $album_item=array(
                        "AlbumId" => $AlbumId,
                        "Title" => $Title,
                        "Name" => $Name,
                    );
                
                    array_push($album_arr["album"], $album_item);
                }
            
                http_response_code(200);
            
                echo json_encode($album_arr["album"]);
            } else {
                
                http_response_code(404);
                echo json_encode(
                    array("message" => "No Albums with that Id found")
                );
            }
        }
        else{
            $stmt = $album->list();
            $num = $stmt->rowCount();
            if($num>0){
        
                $album_arr["album"]=array();
        
                while ($row = $stmt->fetch()){
        
                    extract($row);
        
                    $album_item=array(
                        "AlbumId" => $AlbumId,
                        "Title" => $Title,
                        "Name" => $Name,
                        "ArtistId" => $ArtistId,
                    );
        
                    array_push($album_arr["album"],  $album_item);
                }
        
                http_response_code(200);
        
                echo json_encode($album_arr["album"]);
            }
        }
        break;
        case 'POST':
            $decryption = $authentication->Decrypt($request['state']);
            $decoded = json_decode($decryption, true);
    
            if($decoded['state'] != "authenticated" || $decoded['role'] != 'admin'){
                echo json_encode("You are not authorized.");
                http_response_code(500);
            }
            else{
                $current = time();
                $expiresAt = $decoded['expiresAt'];
                $didExpire =  $expiresAt - $current;
                if($didExpire < 0){
                    echo json_encode("Your authentication has expired");
                    http_response_code(401);
                } 
                else{
                    $result = $album->addAlbum($request);
                    if($result){
                        echo json_encode("Album has been added to the DB.");
                        http_response_code(201);
                    }
                    else{
                        echo json_encode("Failed to add album to the DB.");
                        http_response_code(500);
                    }
                }
            }       
    
        break;
        case 'DELETE':
            if(isset($_GET['id'])){
                $decryption = $authentication->Decrypt($request['state']);
                $decoded = json_decode($decryption, true);
    
                if($decoded['state'] != "authenticated" || $decoded['role'] != 'admin'){
                    echo json_encode("You are not authorized.");
                    http_response_code(500);
                }
                else{
                    $current = time();
                    $expiresAt = $decoded['expiresAt'];
                    $didExpire =  $expiresAt - $current;
                    if($didExpire < 0){
                        echo json_encode("Your authentication has expired");
                        http_response_code(401);
                    }
                    else{
                        $id = $_GET['id'];
                        $result = $album->delete($id);
                        if($result){
                            echo json_encode("Album has been deleted from the DB.");
                            http_response_code(200);
                        }
                        else{
                            echo json_encode("Failed to delete album from the DB.");
                            http_response_code(500);
                        }
                    }
                }
            }
    
        break;
}

?>