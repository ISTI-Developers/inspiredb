<?php
require_once 'controller.php';

class Programs extends Controller
{
    function retrievePrograms()
    {
        $this->setStatement("SELECT * FROM programs WHERE `status` = 1");
        $this->statement->execute();
        return $this->statement->fetchAll();
    }
    function retrieveOneProgram($id)
    {
        $this->setStatement("SELECT programs.*, COUNT(registration.registrant_id) as parts FROM programs LEFT JOIN registration ON programs.program_id = registration.program_id WHERE programs.program_id = ? AND programs.`status` = 1;");
        $this->statement->execute([$id]);
        return $this->statement->fetch();
    }
    public function retrieveProgramDetails($id)
    {
        $this->setStatement("SELECT title, price FROM programs WHERE program_id = ?");
        $this->statement->execute([$id]);
        return $this->statement->fetch(PDO::FETCH_ASSOC); // Use associative array for easier access
    }

    function insertProgram($title, $image, $facilitator, $overview, $description, $venue, $category, $program_date, $time_start, $time_end, $num_reg_limit, $date_reg_limit, $price, $agenda = "none")
    {
        $this->setStatement("SET time_zone = '+8:00';");
        $this->statement->execute();
        $this->setStatement("INSERT INTO `programs`(`title`, `image`,`facilitator`, `overview`, `description`, `venue`, `category`, `program_date`, `time_start`, `time_end`, `num_reg_limit`, `date_reg_limit`, `price`, `agenda`, `status`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,1)");
        return $this->statement->execute([$title, $image, $facilitator, $overview, $description, $venue, $category, $program_date, $time_start, $time_end, $num_reg_limit, $date_reg_limit, $price, $agenda]);
    }

    function updateProgram($id, $title, $image, $facilitator, $overview, $description, $venue, $category, $program_date, $time_start, $time_end, $num_reg_limit, $date_reg_limit, $price, $agenda = "none")
    {
        $this->setStatement("SET time_zone = '+8:00';");
        $this->statement->execute();
        $this->setStatement("UPDATE `programs` SET `title` = ? ,`image` = ?,`facilitator` = ?, `overview` = ?, `description` = ?, `venue` = ?, `category` = ?, `program_date` = ?, `time_start` = ?, `time_end` = ?, `num_reg_limit` = ?, `date_reg_limit` = ?, `price` = ?, `agenda` = ? WHERE program_id = ? AND `status` = 1");
        return $this->statement->execute([$title, $image, $facilitator, $overview, $description, $venue, $category, $program_date, $time_start, $time_end, $num_reg_limit, $date_reg_limit, $price, $agenda, $id]);
    }
    function updateIsFeatured($id, $isFeatured)
    {
        $this->setStatement("UPDATE `programs` SET `isFeatured` = ? WHERE `program_id` = ?");
        return $this->statement->execute([$isFeatured ? 1 : 0, $id]);
    }

    function deleteProgram($id)
    {
        $this->setStatement("UPDATE `programs` SET `status` = 0 WHERE program_id = ?");
        return $this->statement->execute([$id]);
    }

}
