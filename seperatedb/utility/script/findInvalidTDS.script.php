<?php


include_once("../../../classes/include/dbop.class.php");
include_once("../../../classes/dbconst.class.php");

session_start();
$parent = 0;
$bSuccess = true;

if (isset($bSuccess)) {
    try {
        error_reporting(1);
        $startNo = (int)$_REQUEST['startNo'];
        $endNo = (int)$_REQUEST['endNo'];
        $dbPrefix = "hostmjbt_society";
        $debug_trace = 1;

        for($i = $startNo; $i <= $endNo; $i++){

            $dbName = $dbPrefix . $i;
            $m_dbConn = new dbop(false, $dbName);
            
            if ($m_dbConn->isConnected == false) {
                echo ' <br/><br/>.....Connection Failed For '.$dbName;
            } else {
                
                echo "<br /><br /><br />***************DBNAME : ".$dbName."*********************<br /><br />";
                $qry = "SELECT InvoiceStatusID, sum(AmountReceived) as InvoiceAmount, sum(TDSAmount) as TDSAmount, GROUP_CONCAT(TDSVoucherNO) as TDSVouchers, InvoiceClearedVoucherNo FROM `invoicestatus` where InvoiceClearedVoucherNo != 0 and TDSVoucherNO != 0 GROUP BY `InvoiceClearedVoucherNo` ORDER BY `InvoiceClearedVoucherNo`";
                $result = $m_dbConn->select($qry);
    
                $invalidCnt = 0;
                foreach ($result as $key => $value) {
                    
                    extract($value);
                    $voucherQry = "SELECT Amount as paymentAmount, ChequeDate, ChequeNumber, InvoiceAmount, TDSAmount  FROM `paymentdetails` as p JOIN `voucher` as v ON p.id = v.RefNo WHERE v.VoucherNo = '$InvoiceClearedVoucherNo' group by v.VoucherNo";
                    $voucherResult = $m_dbConn->select($voucherQry);
    
                    if($voucherResult[0]['InvoiceAmount'] == $InvoiceAmount && $TDSAmount != 0 && $voucherResult[0]['TDSAmount'] != $TDSAmount){
    
                        $TDSAmountDiff = $TDSAmount - $voucherResult[0]['TDSAmount'];
                        $TDSVoucherNos = explode(',', $TDSVouchers);
                        $invalidCnt++;
                        if(count($TDSVoucherNos) == 1){
                            
                            $updateQry = "UPDATE invoicestatus SET TDSVoucherNo = 0, TDSAmount = 0 WHERE InvoiceStatusID = '$InvoiceStatusID'";
                            $m_dbConn->update($updateQry);
                            echo "<br/>============================================";
                            echo "<br />Counter : ".$invalidCnt;
                            echo "<br />Cheque No. : ".$voucherResult[0]['ChequeNumber'];
                            echo "<br />Cheque Date : ".$voucherResult[0]['ChequeDate'];
                            echo "<br />TDS Amount Diff : ".$TDSAmountDiff;
                            echo "<br />TDS Voucher No : ".$TDSVoucherNos[0];
                            echo "<br />Clear Voucher No : ".$InvoiceClearedVoucherNo;
                        }
                    }
                }
                echo "<br />Total Invalid Entry was ".$invalidCnt."</br>";
            }
        }
    } catch (Exception $exp) {
        echo $exp;
        $m_dbConn->rollback();
    }
}   



					 
                   
                 
?>