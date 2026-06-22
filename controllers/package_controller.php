<?php
require_once 'controller.php';

class PackageMeal extends Controller
{
    // ✅ Get all package meals

    public function getPackages()
    {
        $this->setStatement("SELECT * FROM packages WHERE `status` = 1");
        $this->statement->execute();
        return $this->statement->fetchAll();
    }

    // ✅ Get a single package meal by ID
    public function getPackageById($id)
    {
        $this->setStatement("SELECT * FROM packages WHERE id = ? AND `status` = 1");
        $this->statement->execute([$id]);
        return $this->statement->fetch();
    }

    // ✅ Create a new package meal (Stores meals as JSON + price)
    public function createPackage($name, $meals, $price, $category, $image)
    {
        $this->setStatement("SELECT COUNT(*) FROM packages WHERE name = ? AND status = 1");
        $this->statement->execute([$name]);
        if ($this->statement->fetchColumn() > 0) {
            return "Package with this name already exists.";
        }

        try {
            $this->setStatement("INSERT INTO packages (name, meals, price, category, image, status) VALUES (?, ?, ?, ?, ?, 1)");
            return $this->statement->execute([$name, $meals, $price, $category, $image]);
        } catch (PDOException $e) {
            error_log("Create Package Error: " . $e->getMessage());
            return false;
        }
    }


    public function updatePackage($id, $name, $meals, $price, $category, $image = null)
    {
        try {
            if ($image !== null) {
                $this->setStatement("UPDATE packages SET `name` = ?, `meals` = ?, `price` = ?, `category` = ?, `image` = ? WHERE id = ? AND status = 1");
                $success = $this->statement->execute([$name, $meals, $price, $category, $image, $id]);
            } else {
                $this->setStatement("UPDATE packages SET `name` = ?, `meals` = ?, `price` = ?, `category` = ? WHERE id = ? AND status = 1");
                $success = $this->statement->execute([$name, $meals, $price, $category, $id]);
            }

            return $success ? "Package updated successfully!" : "Failed to update package.";
        } catch (PDOException $e) {
            error_log("Update Package Error: " . $e->getMessage());
            return false;
        }
    }



    public function deletePackage($id)
    {
        $this->setStatement("SELECT id FROM packages WHERE id = ? AND status = 1");
        $this->statement->execute([$id]);
        if (!$this->statement->fetch()) {
            return "Package not found.";
        }

        $this->setStatement("UPDATE packages SET status = 0 WHERE id = ?");
        return $this->statement->execute([$id]) ? "Package deleted successfully." : "Failed to delete package.";
    }
}
