<?php
require_once 'controller.php';

class Inquiry extends Controller{
    function retrieveInquiry(){
        $this->setStatement("SELECT * FROM inquiry WHERE `status` = 1");
        $this->statement->execute();
        return $this->statement->fetchAll();
    }
    function retrieveOneInquiry($id){
        $this->setStatement("SELECT * FROM inquiry WHERE inquiry_id = ? AND `status` = 1");
        $this->statement->execute([$id]);
        return $this->statement->fetch();
    }
    function insertInquiry($company_name, $company_information, $name, $designation, $email, $contact_number, $event_title, $event_description, $event_setup, $event_location, $is_recorded, $is_streamed, $event_date, $speaker_timeslot, $audience_size, $audience_profile, $training_objective, $documents = ""){
        $this->setStatement("INSERT INTO `inquiry`(`company_name`, `company_information`, `name`, `designation`, `email`, `contact_number`, `event_title`, `event_description`, `event_setup`, `event_location`, `is_recorded`, `is_streamed`, `event_date`, `speaker_timeslot`, `audience_size`, `audience_profile`, `training_objective`, `documents`, `status`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,1)");
        return $this->statement->execute([$company_name, $company_information, $name, $designation, $email, $contact_number, $event_title, $event_description, $event_setup, $event_location, $is_recorded, $is_streamed, $event_date, $speaker_timeslot, $audience_size, $audience_profile, $training_objective, $documents]);
    }
    function updateInquiry($id, $company_name, $company_information, $name, $designation, $email, $contact_number, $event_title, $event_description, $event_setup, $event_location, $is_recorded, $is_streamed, $event_date, $speaker_timeslot, $audience_size, $audience_profile, $training_objective, $documents){
        $this->setStatement("UPDATE `inquiry` SET `company_name` = ? , `company_information` = ? , `name` = ? , `designation` = ? , `email` = ? , `contact_number` = ? , `event_title` = ? , `event_description` = ? , `event_setup` = ? , `event_location` = ? , `is_recorded` = ? , `is_streamed` = ? , `event_date` = ? , `speaker_timeslot` = ? , `audience_size` = ? , `audience_profile` = ? , `training_objective` = ? , `documents` = ?  WHERE inquiry_id = ? AND `status` = 1");
        return $this->statement->execute([$company_name, $company_information, $name, $designation, $email, $contact_number, $event_title, $event_description, $event_setup, $event_location, $is_recorded, $is_streamed, $event_date, $speaker_timeslot, $audience_size, $audience_profile, $training_objective, $documents, $id]);
    }
    function deleteInquiry($id){
        $this->setStatement("UPDATE `inquiry` SET `status` = 0 WHERE inquiry_id = ?");
        return $this->statement->execute([$id]);
    }   
}
?>