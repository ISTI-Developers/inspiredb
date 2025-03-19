<?php
require_once 'controller.php';

class Voucher extends Controller
{
    public function verifyVoucher($inputCode)
    {
        $this->setStatement("SELECT name, value FROM vouchers WHERE `status` = 1");
        $this->statement->execute();
        $vouchers = $this->statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($vouchers as $voucher) {
            $decryptedName = $this->decryptVoucher($voucher['name']);
            if ($decryptedName === $inputCode) {
                return [
                    "valid" => true,
                    "voucher" => [
                        "name" => $decryptedName,
                        "value" => $voucher['value']
                    ]
                ];
            }
        }
        return ["valid" => false]; // Voucher not found
    }


    public function retrieveVouchers($isAdmin = false)
    {
        $this->setStatement("SELECT * FROM vouchers WHERE `status` = 1");
        $this->statement->execute();
        $vouchers = $this->statement->fetchAll(PDO::FETCH_ASSOC);
        if ($isAdmin) {
            // Decrypt voucher names only for the admin
            foreach ($vouchers as &$voucher) {
                $voucher['name'] = $this->decryptVoucher($voucher['name']);
            }
        }
        return $vouchers;
    }

    function retrieveOneVoucher($id)
    {
        $this->setStatement("SELECT * FROM vouchers WHERE voucher_id = ? AND `status` = 1");
        $this->statement->execute([$id]);
        return $this->statement->fetch();
    }
    public function insertVoucher($name, $value)
    {
        $encryptedValue = $this->encryptVoucher($name);

        $this->setStatement("INSERT INTO `vouchers`(`name`, `value`, `status`) VALUES (?, ?, 1)");
        return $this->statement->execute([$encryptedValue, $value]);
    }



    function updateVoucher($id, $name, $value)
    {
        $encryptedValue = $this->encryptVoucher($name);

        $this->setStatement("UPDATE `vouchers` SET `name` = ?, `value` = ? WHERE `voucher_id` = ? AND `status` = 1");
        return $this->statement->execute([$encryptedValue, $value, $id]);
    }
    function deleteVoucher($id)
    {
        $this->setStatement("UPDATE `vouchers` SET `status` = 0 WHERE voucher_id = ?");
        return $this->statement->execute([$id]);
    }
}
