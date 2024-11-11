<?php
require_once 'controller.php';

class Registration extends Controller
{
    function retrieveRegistrations()
    {
        $this->setStatement("SELECT registration.*, programs.title, programs.program_date FROM registration LEFT JOIN programs ON registration.program_id = programs.program_id WHERE registration.`status` = 1");
        $this->statement->execute();
        return $this->statement->fetchAll();
    }
    function retrieveOneRegistration($id)
    {
        $this->setStatement("SELECT * FROM registration WHERE registrant_id = ? AND `status` = 1");
        $this->statement->execute([$id]);
        return $this->statement->fetch();
    }
    function insertRegistration($registration_type, $first_name, $last_name, $email_address, $mobile_number, $tin_num, $source_platform, $program_id, $company_name, $position, $more_than_ten)
    {
        $this->setStatement("INSERT INTO `registration`(`registration_type`, `first_name`, `last_name`, `date_reg`, `email_address`, `mobile_number`, `tin_num`, `source_platform`, `program_id`, `company_name`, `position`,`more_than_ten`, `status`) VALUES (?,?,?,NOW(),?,?,?,?,?,?,?,?,1)");
        return $this->statement->execute([$registration_type, $first_name, $last_name, $email_address, $mobile_number, $tin_num, $source_platform, $program_id, $company_name, $position, $more_than_ten]);
    }
    function updateRegistration($id, $registration_type, $first_name, $last_name, $email_address, $mobile_number, $tin_num, $source_platform, $program_id, $company_name, $position, $more_than_ten)
    {
        $this->setStatement("UPDATE `registration` SET `registration_type` = ?, `first_name` = ? , `last_name` = ?, `email_address` = ?, `mobile_number` = ?, `tin_num` = ?, `source_platform` = ?, `program_id` = ?, `company_name` = ?, `position` = ?, `more_than_ten` = ? WHERE registrant_id = ? AND `status` = 1");
        return $this->statement->execute([$registration_type, $first_name, $last_name, $email_address, $mobile_number, $tin_num, $source_platform, $program_id, $company_name, $position, $more_than_ten, $id]);
    }
    function deleteRegistration($id)
    {
        $this->setStatement("UPDATE `registration` SET `status` = 0 WHERE registrant_id = ?");
        return $this->statement->execute([$id]);
    }
}
