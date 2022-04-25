<?php

require_once('../DBConfig/config.php');
require_once('authentication.php');

class Cart extends DB{

    function getCart($iId){
        $query = 'SELECT track.Name, invoiceline.UnitPrice, invoiceline.Quantity FROM `invoiceline` INNER JOIN track ON track.TrackId=invoiceline.TrackId WHERE invoiceline.InvoiceId = :id';
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $iId);
        $stmt->execute();
        $num = $stmt->rowCount();
        
        if($num > 0){
            $cart['item'] = array();
            while ($row = $stmt->fetch()){
                extract($row);
        
                $item=array(
                    'Quantity' => $Quantity,
                    'UnitPrice' => $UnitPrice,
                    'Name' => $Name
                );
                array_push($cart['item'],  $item);
            }

            $pQuery = 'SELECT Total FROM `invoice` WHERE InvoiceId = :id';
            $pstmt = $this->pdo->prepare($pQuery);
            $pstmt->bindParam(':id', $iId);
            $pstmt->execute();
            $pnum = $pstmt->rowCount();
            if($pnum > 0){
                while ($prow = $pstmt->fetch()){
                    extract($prow);
                    $cart['Total'] = $Total;
                }
            }
            return $cart;
        }
    }

    function purchase($billing, $invoiceId, $customerId){
        $now = new DateTime('NOW');
        $InvoiceDate = $now->format('Y-m-d H:i:s');
    
        $query  = $this->pdo->prepare("UPDATE `invoice` SET `BillingAddress`=:BillingAddress,`BillingCity`=:City,`BillingCountry`=:Country,`BillingPostalCode`=:PostalCode,`InvoiceDate`=:InvoiceDate,`BillingState`=:BillingState WHERE InvoiceId = :InvoiceId");
        $query->bindParam(':BillingAddress', $billing['Address']);
        $query->bindParam(':City', $billing['City']);
        $query->bindParam(':BillingState', $billing['State']);
        $query->bindParam(':Country', $billing['Country']);
        $query->bindParam(':PostalCode', $billing['PostalCode']);
        $query->bindParam(':InvoiceDate', $InvoiceDate);
        $query->bindParam(':InvoiceId', $invoiceId);

        if ($query->execute()){
            $queryInvoice = $this->pdo->prepare("INSERT INTO `invoice`(`InvoiceDate`,`Total`, `CustomerId`, `BillingAddress`, `BillingCity`, `BillingState`, `BillingCountry`, `BillingPostalCode`) VALUES (:InvoiceDate, 0.0, :customerId, :BillingAddress, :City, :BillingState, :Country, :PostalCode)");
            $queryInvoice->bindParam(':customerId', $customerId);
            $queryInvoice->bindParam(':BillingAddress', $billing['Address']);
            $queryInvoice->bindParam(':City', $billing['City']);
            $queryInvoice->bindParam(':BillingState', $billing['State']);
            $queryInvoice->bindParam(':Country', $billing['Country']);
            $queryInvoice->bindParam(':PostalCode', $billing['PostalCode']);
            $queryInvoice->bindParam(':InvoiceDate', $InvoiceDate);
            if($queryInvoice->execute()){
                $authentication = new Authentication();
                $newid = $this->pdo->lastInsertId();
                return $authentication->newState($customerId, $newid);
            }
        } 
        else{
            return false;
        }
    }

    function addTo($song, $invoiceId){
        $trackId = $song['trackId'];
        $quantity = $song['quantity'];
        $unitPrice = $song['unitPrice'];
        
        $query = "INSERT INTO `invoiceline` (`InvoiceId`,`TrackId`, `UnitPrice`, `Quantity`) VALUES (:invoiceId, :trackId, :unitPrice, :quantity)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':trackId', $trackId);
        $stmt->bindParam(':invoiceId', $invoiceId);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':unitPrice', $unitPrice);

        $inQuery = 'UPDATE `invoice` SET Total = Total + :unitPrice*:quantity WHERE InvoiceId = :invoiceId';
        $instmt = $this->pdo->prepare($inQuery);
        $instmt->bindParam(':unitPrice', $unitPrice);
        $instmt->bindparam(':quantity', $quantity);
        $instmt->bindparam(':invoiceId', $invoiceId);
        
        if($stmt->execute()){
            if($instmt->execute()){
                return true;
            }
            else{
                return false;
            }
        } 
        else{
            return false;
        }

    }

}

?>