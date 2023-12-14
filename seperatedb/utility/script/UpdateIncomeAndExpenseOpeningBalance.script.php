<?php
error_reporting(1);
ini_set('display_error', E_ALL);



include_once("../../../classes/include/dbop.class.php");

include_once("../../../classes/dbconst.class.php");






session_start();
$parent = 0;
$bSuccess = true;

if (isset($bSuccess)) {
    try {
        error_reporting(1);
        $startNo = (int)$_REQUEST['dbNo'];
        $dbPrefix = "hostmjbt_society";
        $dbName = $dbPrefix . $startNo;

        $m_dbConn = new dbop(false, $dbName);

        if ($m_dbConn->isConnected == false) {
            echo ' .....Connection Failed';
        } else {

            // Get All the Expense and Income Opening Balance from Ledger table

            $qry = "SELECT a.group_id, l.* FROM `ledger` as l JOIN account_category as a ON l.categoryid = a.category_id where group_id IN(" . EXPENSE . "," . INCOME . ")  and opening_balance != 0";
            $result = $m_dbConn->select($qry);

            //var_dump($result);

            if (!empty($result)) {

                $m_dbConn->begin_transaction();
                foreach ($result as $value) {

                    extract($value);

                    if ($group_id == EXPENSE) {

                        // check whether it's already present in register table or not

                        $subQry = "SELECT id from expenseregister WHERE LedgerID = '$id' and Is_Opening_Balance = 1";
                        $subResult = $m_dbConn->select($subQry);

                        if (empty($subResult)) {

                            $insetQry = "INSERT INTO `expenseregister`(`LedgerID`, `Date`, `VoucherID`, `VoucherTypeID`, `Debit`, `Credit`, `Is_Opening_Balance`) VALUES ('$id','$opening_date','0','0','$opening_balance','0','1')";
                            $insertResult = $m_dbConn->insert($insetQry);

                            if ($insertResult) {

                                echo "</br></BR> GROUP : EXPENSE , LedgerID : $id,  Amt : $opening_balance Added";
                            } else {
                                throw new Exception("Error Processing Request", 1);
                            }
                        }
                    } else if ($group_id == INCOME) {

                        $subQry = "SELECT id from incomeregister WHERE LedgerID = '$id' and Is_Opening_Balance = 1";
                        $subResult = $m_dbConn->select($subQry);

                        if (empty($subResult)) {

                            $insetQry = "INSERT INTO `incomeregister`(`LedgerID`, `Date`, `VoucherID`, `VoucherTypeID`, `Debit`, `Credit`, `Is_Opening_Balance`) VALUES ('$id','$opening_date','0','0','0','$opening_balance','1')";
                            $insertResult = $m_dbConn->insert($insetQry);

                            if ($insertResult) {

                                echo "</br></BR> GROUP : INCOME , LedgerID : $id,  Amt : $opening_balance Added";
                            } else {
                                throw new Exception("Error Processing Request", 1);
                            }
                        }
                    }
                }
                $m_dbConn->commit();
            } else {

                throw new Exception("Nothing to update", 1);
            }
        }
    } catch (Exception $exp) {
        echo $exp;
        $m_dbConn->rollback();
    }
}
