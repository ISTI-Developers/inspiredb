<?php
require_once 'controller.php';

class Testimonials extends Controller{
    function retrieveTestimonials(){
        $this->setStatement("SELECT * FROM testimonials WHERE status = 1");
        $this->statement->execute();
        return $this->statement->fetchAll();
    }
    function retrieveOneTestimonial($id){
        $this->setStatement("SELECT * FROM testimonials WHERE testimonial_id = ?");
        $this->statement->execute([$id]);
        return $this->statement->fetch();
    }
    function insertTestimonial($recommendation, $name, $company, $position){
        $this->setStatement("INSERT INTO `testimonials`(`recommendation`, `name`, `company`, `position`, `status`) VALUES (?,?,?,?,1)");
        return $this->statement->execute([$recommendation, $name, $company, $position]);
    }
    function updateTestimonial($id, $recommendation, $name, $company, $position){
        $this->setStatement("UPDATE `testimonials` SET `recommendation` = ? , `name` = ? , `company` = ? , `position` = ? WHERE testimonial_id = ? AND `status` = 1");
        return $this->statement->execute([$recommendation, $name, $company, $position, $id]);
    }
    function deleteTestimonial($id){
        $this->setStatement("UPDATE `testimonials` SET `status` = 0 WHERE testimonial_id = ?");
        return $this->statement->execute([$id]);
    } 
}
?>