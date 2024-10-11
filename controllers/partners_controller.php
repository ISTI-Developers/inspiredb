<?php
require_once 'controller.php';

class Partners extends Controller
{
    function retrievePartners()
    {
        $this->setStatement("SELECT * FROM partners WHERE `status` = 1");
        $this->statement->execute();
        return $this->statement->fetchAll();
    }
    function retrieveOnePartner($id)
    {
        $this->setStatement("SELECT * FROM partners WHERE partners_id = ? AND `status` = 1");
        $this->statement->execute([$id]);
        return $this->statement->fetch();
    }
    function insertPartner($name, $images)
    {
        $this->setStatement("INSERT INTO `partners`(`name`, `images`, `status`) VALUES (?,?,1)");
        return $this->statement->execute([$name, $images]);
    }
    function updatePartner($id, $name, $images = null)
    {
        if ($images !== null) {
            $this->setStatement("UPDATE `partners` SET `name` = ? , `images` = ? WHERE partners_id = ? AND `status` = 1");
            return $this->statement->execute([$name, $images, $id]);
        } else {
            $this->setStatement("UPDATE `partners` SET `name` = ?  WHERE partners_id = ? AND `status` = 1");
            return $this->statement->execute([$name, $id]);
        }
    }
    function deletePartner($id)
    {
        $this->setStatement("UPDATE `partners` SET `status` = 0 WHERE partners_id = ?");
        return $this->statement->execute([$id]);
    }
}
