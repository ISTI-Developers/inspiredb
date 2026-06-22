<?php
require_once 'controller.php';

class Referral extends Controller
{
    function retrieveReferrals()
    {
        $this->setStatement("SELECT * FROM referrals WHERE `status` = 1");
        $this->statement->execute();
        return $this->statement->fetchAll();
    }
    function retrieveOneReferral($id)
    {
        $this->setStatement("SELECT * FROM referrals WHERE referral_id = ? AND `status` = 1");
        $this->statement->execute([$id]);
        return $this->statement->fetch();
    }
    function insertReferral($name, $code)
    {
        $this->setStatement("INSERT INTO `referrals`(`referrer_name` ,`referral_code`, `status`) VALUES (?,?,1)");
        return $this->statement->execute([$name, $code]);
    }
    function updateReferral($id, $name, $code)
    {
        $this->setStatement("UPDATE `referrals` SET `referrer_name` = ?, `referral_code` = ? WHERE `referral_id` = ? AND `status` = 1");
        return $this->statement->execute([$name, $code, $id]);
    }
    function deleteReferral($id)
    {
        $this->setStatement("UPDATE `referrals` SET `status` = 0 WHERE referral_id = ?");
        return $this->statement->execute([$id]);
    }
}
