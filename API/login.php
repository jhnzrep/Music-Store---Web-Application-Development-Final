<?php
    require_once '../Models/authentication.php';

    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Methods: GET, POST');
    header("Content-Type: application/json; charset=UTF-8");

    $request = json_decode(file_get_contents('php://input'), true);
    $authentication = new Authentication();
    $stmt = $authentication->Login($request);

    if(isset($stmt)){
        if($stmt['state'] == 'authenticated'){
            $response = $authentication->Encrypt($stmt);
            echo json_encode($response);
        } else {
            http_response_code(401);
            $message = 'login failed';
            echo json_encode($message);
        }
    }
    else{
        echo json_encode('Failed to login');
    }
?>