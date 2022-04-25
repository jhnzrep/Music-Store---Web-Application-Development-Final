<?php

require_once '../Models/artist.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$artist = new Artist();
$authentication = new Authentication();
$request = json_decode(file_get_contents('php://input'), true);

switch($_SERVER['REQUEST_METHOD']){
    case 'GET':
        if(isset($_GET['name'])){
            $stmt = $artist->search($_GET['name']);
            $num = $stmt->rowCount();
            if($num>0){
                $artist_arr["artist"]=array();
                while ($row = $stmt->fetch()){
            
                    extract($row);
                
                    $artist_item=array(
                        "ArtistId" => $ArtistId,
                        "Name" => $Name,
                    );
                
                    array_push($artist_arr["artist"], $artist_item);
                }
        
            http_response_code(200);
            echo json_encode($artist_arr["artist"]);
            }
            else{
                http_response_code(404);
                echo json_encode(
                    array("message" => "No Tracks with that name found")
                );
            }
        }
        else if(isset($_GET['id'])){
            $stmt = $artist->get($_GET['id']);
            $num = $stmt->rowCount();
            if($num>0){
                $artist_arr["artist"]=array();
                while ($row = $stmt->fetch()){
            
                    extract($row);
                
                    $artist_item=array(
                        "ArtistId" => $ArtistId,
                        "Name" => $Name,
                    );
                
                    array_push($artist_arr["artist"], $artist_item);
                }
            
                http_response_code(200);
            
                echo json_encode($artist_arr["artist"]);
            } 
            else{
                
                http_response_code(404);
                echo json_encode(
                    array("message" => "No Artists with that Id found")
                );
            }
        }
        else{
            $stmt = $artist->list();
            $num = $stmt->rowCount();
            if($num>0){
        
                $artist_arr["artist"]=array();
        
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        
                    extract($row);
        
                    $artist_item=array(
                        "ArtistId" => $ArtistId,
                        "Name" => $Name,
                    );
        
                    array_push($artist_arr["artist"],  $artist_item);
                }
        
                http_response_code(200);
        
                echo json_encode($artist_arr["artist"]);
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
                        $result = $artist->addArtist($request);
                        if($result){
                            echo json_encode("Artist has been added to the DB.");
                            http_response_code(201);
                        }
                        else{
                            echo json_encode("Failed to add artist to the DB.");
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
                        $result = $artist->delete($id);
                        if($result){
                            echo json_encode("Artist has been deleted from the DB.");
                            http_response_code(200);
                        }
                        else{
                            echo json_encode("Failed to delete artist from the DB.");
                            http_response_code(500);
                        }
                    }
                }
            }
    
        break;
}

?>