<?php

require_once('../DBConfig/config.php');

class Authentication extends DB{

    function Login($request){
        $email = htmlspecialchars($request["Email"]);
        $password = htmlspecialchars($request["Password"]);
        
        $query = "SELECT * FROM `customer` WHERE Email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $num = $stmt->rowCount();

        $verification = array();
        $verification["state"] = "unauthenticated";

        if($num > 0){
            $result = $stmt->fetch();
            $dbPass = $result["Password"];
            $passChecked = password_verify($password, $dbPass);
            if($passChecked == true){
                $issuedAt = time();
                $expiresAt =  $issuedAt + 3600;
                $state = 'authenticated';
                $email = $result['Email'];
                $customerId = $result['CustomerId'];

                $InvoiceQuery = "SELECT InvoiceId FROM `invoice` WHERE CustomerId = :id ORDER BY InvoiceId DESC LIMIT 1";
                $stmt = $this->pdo->prepare($InvoiceQuery);
                $stmt->bindParam(':id', $customerId);
                $stmt->execute(); 
                $invoiceResult = $stmt->fetch(); 
                $invoiceId = $invoiceResult['InvoiceId'];        
                $verification = array('state' => $state, 'issuedAt' => $issuedAt, 'expiresAt' => $expiresAt, 'Email' => $email, 'CustomerId' => $customerId, 'InvoiceId' => $invoiceId);
            }
        }
        return $verification;
    }

    function newState($customerId, $invoiceId){
        $issuedAt = time();
        $expiresAt =  $issuedAt + 1800;
        $state = 'authenticated';
        $verification = array('state' => $state, 'issuedAt' => $issuedAt, 'expiresAt' => $expiresAt, 'CustomerId' => $customerId, 'InvoiceId' => $invoiceId);
        return $this->Encrypt($verification);
    }

    function loginAdmin($request){
        $query = 'SELECT * FROM admin';
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $num = $stmt->rowCount();

        $verification = array();
        $verification["state"] = "unauthenticated";

        if($num > 0){
            $res = $stmt->fetch();
            $password = $res['Password'];
            $passChecked = password_verify($request['Password'], $password);
            if($passChecked == true){
                $issuedAt = time();
                $expiresAt =  $issuedAt + 1800;
                $state = 'authenticated';
                $role = 'admin';

                $verification = array('state' => $state, 'role' => $role, 'issuedAt' => $issuedAt, 'expiresAt' => $expiresAt);
            }
        }
        return $verification;
    }

    //encryption source code: https://gist.github.com/xxalfa/bfce04823da603968c38c8884fb0a553
    public static function Encrypt($data){
        $pass = 'pOprAd05801';
        $method = 'AES-128-CBC';
        $initialization_vector_length = openssl_cipher_iv_length( $method );
        $initialization_vector = openssl_random_pseudo_bytes( $initialization_vector_length );

        $encrypted = openssl_encrypt(serialize($data), $method, $pass, $options=OPENSSL_RAW_DATA, $initialization_vector);
        $hmac = hash_hmac( 'sha256', $encrypted, $pass, $as_binary = true );
        $ciphertext = base64_encode($initialization_vector . $hmac . $encrypted);

        return $ciphertext;
    }

    public static function Decrypt($ciphertext){
        $ciphertext = base64_decode($ciphertext);
        $pass = 'pOprAd05801';
        $method = 'AES-128-CBC';
        $initialization_vector_length = openssl_cipher_iv_length( $method );
        $initialization_vector = substr( $ciphertext, 0, $initialization_vector_length );

        $hmac = substr( $ciphertext, $initialization_vector_length, $sha256_length = 32 );
        $ciphertext = substr( $ciphertext, $initialization_vector_length + $sha256_length );    
        $recalculated_hmac = hash_hmac( 'sha256', $ciphertext, $pass, $as_binary = true );
        if(hash_equals( $hmac, $recalculated_hmac )){
            $decrypted_data = openssl_decrypt( $ciphertext, $method, $pass, $options = OPENSSL_RAW_DATA, $initialization_vector );
            return json_encode(unserialize($decrypted_data));
        }
    }
}
?>