<?php
require_once 'controller.php';

class Beverage extends Controller
{
    // Read all beverages
    public function getBeverages()
    {
        $this->setStatement("SELECT * FROM beverages WHERE `status` = 1");
        $this->statement->execute();
        return $this->statement->fetchAll();
    }
    // Read a single beverage by ID
    public function getBeverageById($id)
    {
        $this->setStatement("SELECT * FROM beverages WHERE beverage_id = ? AND `status` = 1");
        $this->statement->execute([$id]);
        return $this->statement->fetch();
    }
    // Create a new beverage
    public function createBeverage($name, $price, $image)
    {
        $this->setStatement("INSERT INTO beverages (name, price, image, status) VALUES (?, ?, ?, 1)");
        return $this->statement->execute([$name, $price, $image]);
    }

    // Update an existing beverage
    public function updateBeverage($id, $name, $price, $image = null)
    {
        if ($image !== null) {
            $this->setStatement("UPDATE `beverages` SET `name` = ? , `image` = ?, `price` = ? WHERE beverage_id = ? AND `status` = 1");
            return $this->statement->execute([$name, $image, $price, $id]);
        } else {
            $this->setStatement("UPDATE `beverages` SET `name` = ? , `price` = ?  WHERE beverage_id = ? AND `status` = 1");
            return $this->statement->execute([$name, $price, $id]);
        }
    }

    // Delete a beverage
    public function deleteBeverage($id)
    {
        $this->setStatement("UPDATE `beverages` SET `status` = 0 WHERE beverage_id = ?");
        return $this->statement->execute([$id]);
    }
}
