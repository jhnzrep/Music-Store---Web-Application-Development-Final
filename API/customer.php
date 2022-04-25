<?php
    require_once '../Models/customer.php';
    require_once '../Models/authentication.php';

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    $request = json_decode(file_get_contents('php://input'), true);
    $customer = new Customer();

    switch($_SERVER['REQUEST_METHOD']){
        case 'GET':
            echo $customer->getCustomer($_GET['id']);
        case 'POST':
            $Email = $request['Email'];
            $hashedPassword = password_hash($request['Password'], PASSWORD_DEFAULT);
            $invoiceId = $customer->create($Email,$hashedPassword);
            if(isset($invoiceId)){
                http_response_code(200);
                echo json_encode($invoiceId);
            } else {
                echo 'Error';
            }
        break;
        case 'PUT':
            if($request['state'] == null){
                echo json_encode("Missing authentication");
                http_response_code(500);
                break;
            }
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
                    $updateStatus = $customer->update($request, $customerId);
                    if($updateStatus) {
                        http_response_code(200);
                        echo json_encode("Customer updated");
                    } else {
                        echo json_encode("Failure");
                    }
                }
            }
        break;
    }
?>