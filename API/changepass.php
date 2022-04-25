<?php
    require_once '../Models/customer.php';
    require_once '../Models/authentication.php';

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    $request = json_decode(file_get_contents('php://input'), true);
    $customer = new Customer();

    $authentication = new Authentication();
    $decryption = $authentication->Decrypt($request['state']);
    $decoded = json_decode($decryption, true);
    if($decoded['state'] != "authenticated"){
        echo json_encode("You are not logged in");
        http_response_code(500);
    }
    else{
        $current = time();
        $expiresAt = $decoded['expiresAt'];
        $didExpire =  $expiresAt - $current;
        if($didExpire < 0){
            json_encode("Your authentication has expired");
            http_response_code(401);
        }
        else{
            $customerId = $decoded['CustomerId'];
            $newpass = $request['Password'];
            $result = $customer->changePass($customerId, $newpass);
            if($result == true){
                json_encode("Password changed!");
                http_response_code(200);
            }
            else{
                json_encode("error");
            }
        }
    }
?>