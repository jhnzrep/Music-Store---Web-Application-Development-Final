<?php

require_once '../Models/track.php';
require_once '../Models/authentication.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$track = new Track();
$authentication = new Authentication();

$request = json_decode(file_get_contents('php://input'), true);

switch($_SERVER['REQUEST_METHOD']){
    case 'GET':
        if(isset($_GET['name'])){
            $stmt = $track->search($_GET['name']);
            $num = $stmt->rowCount();
            if($num>0){
                $track_arr["track"]=array();
                while ($row = $stmt->fetch()){
            
                    extract($row);
                
                    $track_item=array(
                        "TrackId" => $TrackId,
                        "Name" => $Name,
                        "AlbumId" => $AlbumId,
                        "MediaTypeId" => $MediaTypeId,
                        "GenreId" => $GenreId,
                        "Composer" => $Composer,
                        "Milliseconds" => $Milliseconds,
                        "Bytes" => $Bytes,
                        "UnitPrice" => $UnitPrice
                    );
                
                    array_push($track_arr["track"], $track_item);
                }
        
            http_response_code(200);
            echo json_encode($track_arr["track"]);
            }
            else{
                http_response_code(404);
                echo json_encode(
                    array("message" => "No Tracks with that name found")
                );
            }
        }
        else if(isset($_GET['id'])){
            $stmt = $track->get($_GET['id']);
            $num = $stmt->rowCount();
            if($num>0){
                $track_arr["track"]=array();
                while ($row = $stmt->fetch()){
            
                    extract($row);
                
                    $track_item=array(
                        "TrackId" => $TrackId,
                        "Name" => $Name,
                        "AlbumId" => $AlbumId,
                        "MediaTypeId" => $MediaTypeId,
                        "GenreId" => $GenreId,
                        "Composer" => $Composer,
                        "Milliseconds" => $Milliseconds,
                        "Bytes" => $Bytes,
                        "UnitPrice" => $UnitPrice
                    );
                
                    array_push($track_arr["track"], $track_item);
                }
        
            http_response_code(200);
            echo json_encode($track_arr["track"]);
            }
            else{
                http_response_code(404);
                echo json_encode(
                    array("message" => "No Tracks with that Id found")
                );
            }
        } 
        else
        {
            $stmt = $track->list();
            $num = $stmt->rowCount();
            if($num>0){

                $track_arr["track"]=array();

                while ($row = $stmt->fetch()){

                    extract($row);

                    $track_item=array(
                        "TrackId" => $TrackId,
                        "Name" => $Name,
                        "AlbumId" => $AlbumId,
                        "MediaTypeId" => $MediaTypeId,
                        "GenreId" => $GenreId,
                        "Composer" => $Composer,
                        "Milliseconds" => $Milliseconds,
                        "Bytes" => $Bytes,
                        "UnitPrice" => $UnitPrice,
                    );

                    array_push($track_arr["track"],  $track_item);
                }

                http_response_code(200);

                echo json_encode($track_arr["track"]);
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
                $result = $track->addTrack($request);
                if($result){
                    echo json_encode("Track has been added to the DB.");
                    http_response_code(201);
                }
                else{
                    echo json_encode("Failed to add song to the DB.");
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
                    $result = $track->delete($id);
                    if($result){
                        echo json_encode("Track has been deleted from the DB.");
                        http_response_code(200);
                    }
                    else{
                        echo json_encode("Failed to delete song from the DB.");
                        http_response_code(500);
                    }
                }
            }
        }

    break;

}

?>