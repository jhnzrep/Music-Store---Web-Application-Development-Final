<?php
    require_once '../Models/cart.php';
    require_once '../Models/authentication.php';
    require_once '../Models/customer.php';

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    $authentication = new Authentication();
    $request = json_decode(file_get_contents('php://input'), true);
    $decryption = $authentication->Decrypt($request['state']);

    $cart = new Cart();

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
            echo json_encode("Your authentication has expired");
            http_response_code(401);
        }
        else{
            switch($_SERVER['REQUEST_METHOD']){
                /*case 'PUT':
                    echo json_encode($cart->getCart($decoded['InvoiceId']));
                break;*/
                case 'POST':
                    switch($request['type']){
                        case 'purchase':
                            $response = $cart->purchase($request, $decoded['InvoiceId'], $decoded['CustomerId']);
                            /*if($response == false){
                                http_response_code(400)
                            }
                            else{*/
                                echo json_encode($response);
                            /*}*/
                        break;
                        case 'get':
                            $customer = new Customer();
                            $response = array($cart->getCart($decoded['InvoiceId']), $customer->getCustomerInfo($decoded['CustomerId']));
                            echo json_encode($response);
                            //echo json_encode($cart->getCart($decoded['InvoiceId']));
                        break;
                        default:
                            $result = $cart->addTo($request, $decoded['InvoiceId']);
                            if($result){
                                http_response_code(200);
                                echo json_encode('Track has been added to your cart');
                            }
                            else{
                                htpp_response_code(500);
                                echo json_encode('Failed to add track to cart.');
                            }
                        break;
                    }
            }
        }

    }
?>