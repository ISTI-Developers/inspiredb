<?php
require_once 'controller.php';

class Meal extends Controller
{
    // Read all meals
    public function getMeals()
    {
        $this->setStatement("SELECT * FROM meals WHERE `status` = 1");
        $this->statement->execute();
        return $this->statement->fetchAll();
    }
    // Read a single meal by ID
    public function getMealById($id)
    {
        $this->setStatement("SELECT * FROM meals WHERE meal_id = ? AND `status` = 1");
        $this->statement->execute([$id]);
        return $this->statement->fetch();
    }
    // Create a new meal
    public function createMeal($name, $price, $image)
    {
        $this->setStatement("INSERT INTO meals (name, price, image, status) VALUES (?, ?, ?, 1)");
        return $this->statement->execute([$name, $price, $image]);
    }

    // Update an existing meal
    public function updateMeal($id, $name, $price, $image = null)
    {
        if ($image !== null) {
            $this->setStatement("UPDATE `meals` SET `name` = ? , `image` = ?, `price` = ? WHERE meal_id = ? AND `status` = 1");
            return $this->statement->execute([$name, $image, $price, $id]);
        } else {
            $this->setStatement("UPDATE `meals` SET `name` = ? , `price` = ?  WHERE meal_id = ? AND `status` = 1");
            return $this->statement->execute([$name, $price, $id]);
        }
    }

    // Delete a meal
    public function deleteMeal($id)
    {
        $this->setStatement("UPDATE `meals` SET `status` = 0 WHERE meal_id = ?");
        return $this->statement->execute([$id]);
    }
}
