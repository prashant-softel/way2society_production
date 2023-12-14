<?php
// get all receipt and payment from voucher
// iterate loop
// if ledger is not bank ignore and check if amount is not 0 and is that same amount in register
// e.g. debit = register.debig && debit != 0
// return the resister name and voucher id

include_once("../../../classes/include/dbop.class.php");
include_once("../../../classes/dbconst.class.php");
include_once("../../../classes/utility.class.php");

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

        echo "<center><button type='button' name='ExportToExcel' id='ExportToExcel' onClick='ExportToExcel();' style='font-size: 25px;'>Export To Excel</button><center><br>";
        for($i = $startNo; $i <= $endNo; $i++){

            $dbName = $dbPrefix . $i;
            $m_dbConn = new dbop(false, $dbName);
            $utility = new utility($m_dbConn);
            
            if ($m_dbConn->isConnected == false) {
                echo ' <br/><br/>.....Connection Failed For '.$dbName;
            } else {
                
                echo "<html><head><style>body { font: normal medium/1.4 sans-serif;}table {border-collapse: collapse;width: 100%;}th, td {padding: 0.25rem;text-align: left;border: 1px solid #ccc;}tbody tr:nth-child(odd) {background: #eee;} label{color:black}</style></head>";
                echo "<br /><br /><br />***************DBNAME : ".$dbName."*********************<br /><br />";
                echo "<div align='center' style='width:100%;font-weight: bold;'><div><label>Reconciliation corrupted entry</label></div>";
                echo "<table align='center' style='border-collapse: collapse;border:1px solid black;bgcolor:gray;color:black'><thead>
                <tr><th>VoucherID</th><th>Date</th><th>Ledger</th><th>Type</th><th>Column</th><th>Amount</th><th>Table</th></tr></thead>
                <tbody>";
                $qry = "SELECT * FROM voucher WHERE VoucherTypeID IN(".VOUCHER_PAYMENT.",".VOUCHER_RECEIPT.") AND RefTableID IN(".TABLE_CHEQUE_DETAILS.", ".TABLE_PAYMENT_DETAILS.")";
                $result = $m_dbConn->select($qry);

                $bankAndCashQuery = "SELECT APP_DEFAULT_BANK_ACCOUNT, APP_DEFAULT_CASH_ACCOUNT FROM appdefault";
                $bankAndCashResult = $m_dbConn->select($bankAndCashQuery);
                $bank = $bankAndCashResult[0]['APP_DEFAULT_BANK_ACCOUNT'];
                $cash = $bankAndCashResult[0]['APP_DEFAULT_CASH_ACCOUNT'];
                $CorruptedEntryArray = array();
                $cnt = 0;
                foreach ($result as $value) {
                    
                    extract($value);
                    $columnName = "";
                    $ledgerID = 0;
                    $Amount = 0;

                    if($Debit != 0 && $Credit == 0){

                        $columnName = "Debit";
                        $ledgerID = $By;
                        $Amount = $Debit;
                    }
                    else if($Debit == 0 && $Credit != 0){

                        $columnName = "Credit";
                        $ledgerID = $To;
                        $Amount = $Credit;
                    }

                    $ledgerDetail = $utility->getParentOfLedger($ledgerID);
                    $group = $ledgerDetail['group'];
                    $category = $ledgerDetail['category'];
                    $ledger_name = $ledgerDetail['ledger_name'];
                    $tableName = "";
                    if($group == ASSET && $category != $bank && $category != $cash){

                        $tableName = 'assetregister';
                    }
                    else if($group == LIABILITY){

                        $tableName = 'liabilityregister';
                    }
                    else if($group == INCOME){

                        $tableName = 'incomeregister';
                    }
                    else if($group == EXPENSE){

                        $tableName = 'expenseregister';
                    }

                    if(!empty($tableName)){

                        $registerQuery = "SELECT ID, ".$columnName."  FROM ".$tableName." WHERE ".$columnName." = $Amount AND VoucherID = $id";
                        $registerResult = $m_dbConn->select($registerQuery);

                        if(!empty($registerResult)){

                            $Type = ($VoucherTypeID == VOUCHER_RECEIPT)?'Receipt':'Payment';
                            echo "<tr><td>$id</td><td>$Date</td><td>$ledger_name</td><td>$Type</td><td>$columnName</td><td>$Amount</td><td>$tableName</td><tr/>";
                            
                            // $CorruptedEntryArray[$cnt]['VoucherID'] = $id;
                            // $CorruptedEntryArray[$cnt]['Date'] = $Date;
                            // $CorruptedEntryArray[$cnt]['Ledger'] = $ledger_name;
                            // $CorruptedEntryArray[$cnt]['Type'] = $Type;
                            // $CorruptedEntryArray[$cnt]['Column'] = $columnName;
                            // $CorruptedEntryArray[$cnt]['Amount'] = $Amount;
                            // $CorruptedEntryArray[$cnt]['Table'] = $tableName;
                            // $cnt++;
                        }   
                        
                    } 
                }
                
                echo "</tbody></table></html>";
            }
        }
    } catch (Exception $exp) {
        echo $exp;
        $m_dbConn->rollback();
    }
}   



					 
                   
                 
?>