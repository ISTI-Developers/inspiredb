<?php
require_once 'controller.php';

class Clients extends Controller
{
    function retrieveClients()
    {
        $this->setStatement("SELECT * FROM clients WHERE `status` = 1");
        $this->statement->execute();
        return $this->statement->fetchAll();
    }
    function retrieveOneClient($id)
    {
        $this->setStatement("SELECT * FROM clients WHERE client_id = ? AND `status` = 1");
        $this->statement->execute([$id]);
        return $this->statement->fetch();
    }
    function insertClient($brand, $images)
    {
        $this->setStatement("INSERT INTO `clients`(`brand`, `images`, `status`) VALUES (?,?,1)");
        return $this->statement->execute([$brand, $images]);
    }
    function updateClient($id, $brand, $images = null)
    {
        if ($images !== null) {
            $this->setStatement("UPDATE `clients` SET `brand` = ? , `images` = ? WHERE client_id = ? AND `status` = 1");
            return $this->statement->execute([$brand, $images, $id]);
        } else {
            $this->setStatement("UPDATE `clients` SET `brand` = ?  WHERE client_id = ? AND `status` = 1");
            return $this->statement->execute([$brand, $id]);
        }
    }
    function deleteClient($id)
    {
        $this->setStatement("UPDATE `clients` SET `status` = 0 WHERE client_id = ?");
        return $this->statement->execute([$id]);
    }
}
