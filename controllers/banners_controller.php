<?php
require_once 'controller.php';

class Banners extends Controller
{
    function retrieveBanners()
    {
        $this->setStatement("SELECT * FROM banners WHERE `status` = 1");
        $this->statement->execute();
        return $this->statement->fetchAll();
    }
    function retrieveOneBanner($id)
    {
        $this->setStatement("SELECT * FROM banners WHERE banner_id = ? AND `status` = 1");
        $this->statement->execute([$id]);
        return $this->statement->fetch();
    }
    function insertBanner($name, $banner)
    {
        $this->setStatement("INSERT INTO `banners`(`name`, `banner`, `status`) VALUES (?,?,1)");
        return $this->statement->execute([$name, $banner]);
    }
    function updateBanner($id, $name, $banner = null)
    {
        if ($banner !== null) {
            $this->setStatement("UPDATE `banners` SET `name` = ? , `banner` = ? WHERE banner_id = ? AND `status` = 1");
            return $this->statement->execute([$name, $banner, $id]);
        } else {
            $this->setStatement("UPDATE `banners` SET `name` = ?  WHERE banner_id = ? AND `status` = 1");
            return $this->statement->execute([$name, $id]);
        }
    }
    function deleteBanner($id)
    {
        $this->setStatement("UPDATE `banners` SET `status` = 0 WHERE banner_id = ?");
        return $this->statement->execute([$id]);
    }
}
