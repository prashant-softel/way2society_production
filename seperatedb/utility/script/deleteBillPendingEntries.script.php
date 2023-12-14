<?php
include_once("../../../classes/include/dbop.class.php");
include_once("../../../classes/dbconst.class.php");
include_once("../../../classes/utility.class.php");

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
        $utility = new utility($m_dbConn);

        $debug_trace = 1;

        if ($m_dbConn->isConnected == false) {
            echo ' .....Connection Failed';
        } else {    

                $defaultQry = "SELECT APP_DEFAULT_BANK_ACCOUNT, APP_DEFAULT_CASH_ACCOUNT FROM appdefault";
                $defaultResult = $m_dbConn->select($defaultQry);

                $bankAccount = $defaultResult[0]['APP_DEFAULT_BANK_ACCOUNT'];
                $cashAccount = $defaultResult[0]['APP_DEFAULT_CASH_ACCOUNT'];

                $qry = "SELECT id, `By`, `To`, `Debit`, `Credit` FROM `voucher` where VoucherTypeID = 1 AND RefTableID = 1 and RefNo NOT IN(SELECT id from billdetails)";
                $result = $m_dbConn->select($qry);
                
                if(count($result) == 0){

                    throw new Exception("Nothing to delete", 1);
                }
                
                echo "<br>Total Row Will be delete : ".count($result);
                $count = 0;
                $DebitTotal = 0;
                $CreditTotal = 0;
                
                foreach ($result as $key => $row) {
                    
                    extract($row);

                    

                    if(!empty($By) && $By != 0 && $To == 0){

                        $ledgerID = $By;
                        $DebitTotal += $Debit;
                    }
                    else if(!empty($To) && $To != 0 && $By == 0){

                        $ledgerID = $To;
                        $CreditTotal += $Credit;
                    }

                    $parentArr = $utility->getParentOfLedger($ledgerID);

                    if($parentArr['group'] == ASSET){

                        if($parentArr['category'] == $bankAccount || $parentArr['category'] == $cashAccount){

                            $deleteQry = "DELETE FROM bankregister WHERE VoucherID = '$id' and Is_Opening_Balance = 0";
                        }
                        else{

                            $deleteQry = "DELETE FROM assetregister WHERE VoucherID = '$id' and Is_Opening_Balance = 0";
                        }
                    }   
                    else if($parentArr['group'] == LIABILITY){

                        $deleteQry = "DELETE FROM liabilityregister WHERE VoucherID = '$id' and Is_Opening_Balance = 0";
                    }
                    else if($parentArr['group'] == INCOME){

                        $deleteQry = "DELETE FROM incomeregister WHERE VoucherID = '$id' and Is_Opening_Balance = 0";
                    }
                    else if($parentArr['group'] == EXPENSE){

                        $deleteQry = "DELETE FROM expenseregister WHERE VoucherID = '$id' and Is_Opening_Balance = 0";
                    }

                    if(!empty($deleteQry)){

                        $count += $m_dbConn->delete($deleteQry);
                        $m_dbConn->delete("DELETE FROM voucher where id = '$id'");
                    }
                }

                echo "<br> ".$count." total row deleted from all register";
                echo "<br>All entries deleted successfully";
        }
    } catch (Exception $exp) {
        echo $exp;
        $m_dbConn->rollback();
    }
}   



					 
                   
                 
?>