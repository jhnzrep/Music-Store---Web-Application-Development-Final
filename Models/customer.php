<?php

require_once('../DBconfig/config.php');

class Customer extends DB{
    function create($Email, $HashedPassword) {
        $isCreatedId = null;
        $query  = $this->pdo->prepare("INSERT INTO `customer` (`FirstName`,`LastName`,`Password`,`Email`) VALUES ('', '', :hashedPassword,:email)");
        $query->bindParam(':hashedPassword', $HashedPassword);
        $query->bindParam(':email', $Email);
        if ($query->execute()) {
            $id = $this->pdo->lastInsertId();
            $queryInvoice  = $this->pdo->prepare("INSERT INTO `invoice`(`InvoiceDate`,`Total`, `CustomerId`) VALUES (NOW(), 0.0, :customerId)");
            $queryInvoice->bindParam(':customerId', $id);
            if($queryInvoice->execute()){
                $created = $id;
            }
        }
        return $created;
    }

    function getCustomerInfo($id){
        $query = $this->pdo->prepare('SELECT * FROM `customer` WHERE CustomerId =:id');
        $query->bindParam(':id', $id);
        $query->execute();
        $num = $query->rowCount();

        if($num > 0){
            while ($row = $query->fetch()){
                extract($row);
        
                $customer=array(
                    'Address' => $Address,
                    'City' => $City,
                    'State' => $State,
                    'Country' => $Country,
                    'PostalCode' => $PostalCode
                );
            }
        }
        return $customer;
    }

    function getCustomer($id){
        $query = $this->pdo->prepare('SELECT * FROM `customer` WHERE CustomerId =:id');
        $query->bindParam(':id', $id);
        $query->execute();
        $num = $query->rowCount();

        if($num > 0){
            while ($row = $query->fetch()){
                extract($row);
        
                $customer=array(
                    'CustomerId' => $CustomerId,
                    'FirstName' => $FirstName,
                    'LastName' => $LastName,
                    'Password' => $Password,
                    'Company' => $Company,
                    'Address' => $Address,
                    'City' => $City,
                    'State' => $State,
                    'Country' => $Country,
                    'PostalCode' => $PostalCode,
                    'Phone' => $Phone,
                    'Fax' => $Fax,
                    'Email' => $Email
                );
            }
        }
        return json_encode($customer);
    }

    function update($data, $customerId){
        $email = $data['Email'];                                                  
        $firstname = $data['FirstName'];
        $lastname = $data['LastName'];
        $company = $data['Company'];
        $address = $data['Address'];
        $city = $data['City'];
        $state = $data['State'];
        $country = $data['Country'];
        $postalCode = $data['PostalCode'];
        $phone = $data['Phone'];
        $fax = $data['Fax'];

        $query  = $this->pdo->prepare("UPDATE `customer` SET `FirstName`=:FirstName,`LastName`=:LastName, `Company`=:Company,`Address`=:Address,`City`=:City,`State`=:State,`Country`=:Country,`PostalCode`=:PostalCode,`Phone`=:Phone,`Fax`=:Fax,`Email`=:Email WHERE CustomerId = :CustomerId");
        $query->bindParam(':FirstName', $firstname);
        $query->bindParam(':LastName', $lastname);
        $query->bindParam(':Company', $company);
        $query->bindParam(':Address', $address);
        $query->bindParam(':City', $city);
        $query->bindParam(':State', $state);
        $query->bindParam(':Country', $country);
        $query->bindParam(':PostalCode', $postalCode);
        $query->bindParam(':Phone', $phone);
        $query->bindParam(':Fax', $fax);
        $query->bindParam(':Email', $email);
        $query->bindParam(':CustomerId', $customerId);

        if ($query->execute()) {
            return true;
        } else{
            return false;
        }
    }

    function changePass($customerId, $newpass){
        $customerId = htmlspecialchars($customerId);
        $newpassword = htmlspecialchars($newpass);
        $hashPass = password_hash($newpassword, PASSWORD_DEFAULT);
        $query = $this->pdo->prepare("UPDATE `customer` SET `Password` = :newpass WHERE `CustomerId` = :customerid");
        $query->bindParam(':newpass', $hashPass);
        $query->bindParam(':customerid', $customerId);
        if ($query->execute()) {
            return true;
        } else{
            return false;
        }
    }
}

?>
