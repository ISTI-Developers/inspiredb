<?php
require_once 'controller.php';

class Newsletter extends Controller{
    function retrieveNewsletter(){
        $this->setStatement("SELECT * FROM newsletter");
        $this->statement->execute();
        return $this->statement->fetchAll();
    }
    function retrieveOneNewsletter($id){
        $this->setStatement("SELECT * FROM newsletter WHERE email = ?");
        $this->statement->execute([$id]);
        return $this->statement->fetch();
    }
    function insertNewsletter($name, $email){
        $this->setStatement("INSERT INTO `newsletter`(`name`, `email`) VALUES (?,?)");
        return $this->statement->execute([$name, $email]);
    }
    function deleteNewsletter($id){
        $this->setStatement("DELETE FROM `newsletter` WHERE subscription_id = ?");
        return $this->statement->execute([$id]);
    }   
}
?>