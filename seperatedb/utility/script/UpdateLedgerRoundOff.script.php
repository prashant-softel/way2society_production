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
            $qry = "SELECT APP_DEFAULT_LEDGER_ROUND_OFF FROM `appdefault`";
            $result = $m_dbConn->select($qry);

            $LedgerRoundID  = $result[0]['APP_DEFAULT_LEDGER_ROUND_OFF'];

            $voucherQry = "SELECT id, `date`, `Credit`, `VoucherNo`, `RefNo` FROM `voucher` where `To` = 0 AND `Credit` != 0 AND VoucherTypeID = 1 and RefNo IN(SELECT ID FROM `billdetails` where Ledger_round_off != 0)";
           
            $voucherResult = $m_dbConn->select($voucherQry);

            if (!empty($voucherResult)) {

                $m_dbConn->begin_transaction();

                
                foreach ($voucherResult as $value) {

                    extract($value);

                    echo "<br><br>".$voucherTotalQry = "SELECT sum(Debit) as DebitTotal, sum(Credit) as CreditTotal FROM voucher WHERE VoucherNo = '$VoucherNo'";

                    $voucherTotalResult = $m_dbConn->select($voucherTotalQry);
                    
                    if($voucherTotalResult[0]['DebitTotal'] == $voucherTotalResult[0]['CreditTotal']){
                        
                        echo "<br>CASE 1";
                        updateRoundLedgerID($m_dbConn, $LedgerRoundID, $id);
                        updateIncomeRegister($m_dbConn, $LedgerRoundID, $id, $date, $Credit);
                    }

                    
                    else
                    {
                         
                        $decimalAmount = explode('.', $voucherTotalResult[0]['DebitTotal']);
                       
                        if($decimalAmount[1] != 0){

                            echo "<br>CASE 2 A";
                            $NewDebitAmount = $voucherTotalResult[0]['DebitTotal'] + $Credit;
                            
                            echo "<br><br>".$updateVoucherDebitQry = "UPDATE voucher SET Debit = $NewDebitAmount WHERE VoucherTypeID = 1 AND `BY` != 0 AND VoucherNo = '$VoucherNo'"; 
                            
                            $updateVoucherDebitResult = $m_dbConn->update($updateVoucherDebitQry);

                            updateRoundLedgerID($m_dbConn, $LedgerRoundID, $id);
                            updateAssetRegister($m_dbConn, $VoucherNo, $NewDebitAmount);
                            updateIncomeRegister($m_dbConn, $LedgerRoundID, $id, $date, $Credit);
                            updateBillDetail($m_dbConn, $RefNo, $NewDebitAmount, $Credit);
                        }
                        else{

                            echo "<br>CASE 2 B";
                            echo "<br><br>".$deleteLedger = "DELETE FROM voucher WHERE id = $id";
                            $m_dbConn->delete($deleteLedger);
                            updateBillDetail($m_dbConn, $RefNo, $voucherTotalResult[0]['DebitTotal'], 0);
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

function updateIncomeRegister($conn, $roundLedgerID, $voucherID, $date,$creditAmount){

    $subQry = "SELECT id from incomeregister WHERE LedgerID = '$roundLedgerID' and Is_Opening_Balance = 0 AND `VoucherID` = $voucherID";
    $subResult = $conn->select($subQry);

    if(empty($subResult)) {

        echo "<br><br>". $insetQry = "INSERT INTO incomeregister(`LedgerID`, `Date`, `VoucherID`, `VoucherTypeID`, `Debit`, `Credit`) VALUES ('$roundLedgerID','$date','$voucherID','".VOUCHER_SALES."','0','$creditAmount')";
        $insertResult = $conn->insert($insetQry);
    }
}

function updateAssetRegister($conn, $voucherNo, $debitAmount){

    echo "<br><br>". $updateQuery = "UPDATE assetregister SET Debit = $debitAmount WHERE VoucherTypeID = 1 AND VoucherID IN(SELECT id FROM voucher WHERE VoucherNo = '$voucherNo' and `By` != 0 and `VoucherTypeID` = 1 and `Debit` != 0)";
    $conn->update($updateQuery);
    
}

function updateRoundLedgerID($conn, $roundLedgerID, $voucherID){

    echo "<br><br>".$voucherUpdate = "UPDATE voucher SET `To` = '$roundLedgerID' where id = '$voucherID'";
    $updateResult = $conn->update($voucherUpdate);
}

function updateBillDetail($conn, $RefNo, $DebitAmount, $roundLedgerAmt){

    echo "<br><br>".$qry = "UPDATE billdetails SET TotalBillPayable = $DebitAmount, Ledger_round_off = $roundLedgerAmt WHERE ID = '$RefNo'";
    $result = $conn->update($qry);
}