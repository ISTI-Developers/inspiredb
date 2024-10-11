<?php
require_once 'controller.php';

class Experts extends Controller{
    function retrieveExperts(){
        $this->setStatement("SELECT * FROM experts WHERE `status` = 1");
        $this->statement->execute();
        return $this->statement->fetchAll();
    }
    function retrieveOneExpert($id){
        $this->setStatement("SELECT * FROM experts WHERE expert_id = ? AND `status` = 1");
        $this->statement->execute([$id]);
        return $this->statement->fetch();
    }
    function insertExpert($name, $images, $description, $biography){
        $this->setStatement("INSERT INTO `experts`(`name`, `images`, `description`, `biography`, `status`) VALUES (?,?,?,?,1)");
        return $this->statement->execute([$name, $images, $description, $biography]);
    }
    function updateExpert($id, $name, $images, $description, $biography){
        $this->setStatement("UPDATE `experts` SET `name` = ? , `images` = ?, `description` = ?, `biography` = ? WHERE expert_id = ? AND `status` = 1");
        return $this->statement->execute([$name, $images, $description, $biography, $id]);
    }
    function deleteExpert($id){
        $this->setStatement("UPDATE `experts` SET `status` = 0 WHERE expert_id = ?");
        return $this->statement->execute([$id]);
    }   
}
?>