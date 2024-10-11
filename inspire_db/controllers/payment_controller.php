<?php
require_once 'controller.php';

class Payment extends Controller{
    function retrievePayments(){
        $this->setStatement("SELECT * FROM payment WHERE `status` = 1");
        $this->statement->execute();
        return $this->statement->fetchAll();
    }
    function retrieveOnePayment($id){
        $this->setStatement("SELECT * FROM payment WHERE payment_id = ? AND `status` = 1");
        $this->statement->execute([$id]);
        return $this->statement->fetch();
    }
    function insertPayment($bank_name, $card_name, $acc_num, $contact_num){
        $this->setStatement("INSERT INTO `payment`(`bank_name` ,`card_name`, `acc_num`, `contact_num`, `status`) VALUES (?,?,?,?,1)");
        return $this->statement->execute([$bank_name, $card_name, $acc_num, $contact_num]);
    }
    function updatePayment($id, $bank_name, $card_name, $acc_num, $contact_num){
        $this->setStatement("UPDATE `payment` SET `bank_name` = ? , `card_name` = ? , `acc_num` = ?, `contact_num` = ? WHERE `payment_id` = ? AND `status` = 1");
        return $this->statement->execute([$bank_name, $card_name, $acc_num, $contact_num, $id]);
    }
    function deletePayment($id){
        $this->setStatement("UPDATE `payment` SET `status` = 0 WHERE payment_id = ?");
        return $this->statement->execute([$id]);
    }   
}
?>