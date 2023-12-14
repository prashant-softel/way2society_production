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
                $qry = "SELECT DATE_FORMAT(DATE(DATE_ADD(y.BeginingDate, INTERVAL -1 DAY)),'%Y-%m-%d') as openingDate FROM `society` s JOIN `year` y ON s.society_creation_yearid = y.YearID WHERE y.status = 'Y' order by y.YearID LIMIT 1";
                $result = $m_dbConn->select($qry);

                $openingDate = $result[0]['openingDate'];

                echo "<br>Opening YEAR : ".$openingDate;
                $totalInvalidDate = 0;
                $totalUpdatedRow = 0;

                if(!empty($openingDate) && strtotime($openingDate) != 0){

                    $ledgerSelect = "SELECT id, ledger_name, opening_date, opening_balance, opening_type from ledger WHERE `opening_date` > '$openingDate'";
                    $assetSelect = "SELECT * FROM assetregister WHERE Is_Opening_Balance = 1 and `Date` > '$openingDate'";
                    $liabilitySelect = "SELECT * FROM liabilityregister WHERE Is_Opening_Balance = 1 and `Date` > '$openingDate'";
                    $incomeSelect = "SELECT * FROM incomeregister WHERE Is_Opening_Balance = 1 and `Date` > '$openingDate'";
                    $expenseSelect = "SELECT * FROM expenseregister WHERE Is_Opening_Balance = 1 and `Date` > '$openingDate'";
                    $bankSelect = "SELECT * FROM bankregister WHERE Is_Opening_Balance = 1 and `Date` > '$openingDate'";

                    $selectResult0 = $m_dbConn->select($ledgerSelect);
                    $selectResult1 = $m_dbConn->select($assetSelect);
                    $selectResult2 = $m_dbConn->select($liabilitySelect);
                    $selectResult3 = $m_dbConn->select($incomeSelect);
                    $selectResult4 = $m_dbConn->select($expenseSelect);
                    $selectResult5 = $m_dbConn->select($bankSelect);


                    if(!empty($selectResult0)){

                        echo "<br>****************Ledger *****************";
                        echo "<pre>";
                        print_r($selectResult0);
                        echo "</pre>";
                        $totalInvalidDate += count($selectResult0);
                        
                    }
                    else{

                        echo "<br>****************Ledger *****************";
                        echo "<br>No Record";
                    }


                    if(!empty($selectResult1)){

                        echo "<br>****************Asset Register*****************";
                        echo "<pre>";
                        print_r($selectResult1);
                        echo "</pre>";
                        $totalInvalidDate += count($selectResult1);
                        
                    }
                    else{

                        echo "<br>****************Asset Register*****************";
                        echo "<br>No Record";
                    }

                    if(!empty($selectResult2)){

                        echo "<br>****************Liability Register*****************";
                        echo "<pre>";
                        print_r($selectResult2);
                        echo "</pre>";
                        $totalInvalidDate += count($selectResult2);
                        
                    }
                    else{

                        echo "<br>****************Liability Register*****************";
                        echo "<br>No Record";
                    } 

                    if(!empty($selectResult3)){

                        echo "<br>****************Income Register*****************";
                        echo "<pre>";
                        print_r($selectResult3);
                        echo "</pre>";
                        $totalInvalidDate += count($selectResult3);
                        
                    }
                    else{

                        echo "<br>****************Income Register*****************";
                        echo "<br>No Record";
                    } 

                    if(!empty($selectResult4)){

                        echo "<br>****************Expense Register*****************";
                        echo "<pre>";
                        print_r($selectResult4);
                        echo "</pre>";
                        $totalInvalidDate += count($selectResult4);
                        
                    }
                    else{

                        echo "<br>****************Expense Register*****************";
                        echo "<br>No Record";
                    } 

                    if(!empty($selectResult5)){

                        echo "<br>****************Bank Register*****************";
                        echo "<pre>";
                        print_r($selectResult5);
                        echo "</pre>";
                        $totalInvalidDate += count($selectResult5);
                        
                    }
                    else{

                        echo "<br>****************Bank Register*****************";
                        echo "<br>No Record";
                    } 

                    $qry0 = "UPDATE `ledger` SET `opening_date` = '$openingDate' WHERE `opening_date` > '$openingDate'";
                    $qry1 = "UPDATE `assetregister` SET `Date` = '$openingDate' WHERE Is_Opening_Balance = 1 and `Date` > '$openingDate'";
                    $qry2 = "UPDATE `liabilityregister` SET `Date` = '$openingDate' WHERE Is_Opening_Balance = 1 and `Date` > '$openingDate'";
                    $qry3 = "UPDATE `incomeregister` SET `Date` = '$openingDate' WHERE Is_Opening_Balance = 1 and `Date` > '$openingDate'";
                    $qry4 = "UPDATE `expenseregister` SET `Date` = '$openingDate' WHERE Is_Opening_Balance = 1 and `Date` > '$openingDate'";
                    $qry5 = "UPDATE `bankregister` SET `Date` = '$openingDate' WHERE Is_Opening_Balance = 1 and `Date` > '$openingDate'";

                    $totalUpdatedRow += $m_dbConn->update($qry0);
                    $totalUpdatedRow += $m_dbConn->update($qry1);
                    $totalUpdatedRow += $m_dbConn->update($qry2);
                    $totalUpdatedRow += $m_dbConn->update($qry3);
                    $totalUpdatedRow += $m_dbConn->update($qry4);
                    $totalUpdatedRow += $m_dbConn->update($qry5);
                }

                echo "<br>Total Invalid Row : ".$totalInvalidDate;
                echo "<br>Total Updated Row : ".$totalUpdatedRow;
            }
        }
    } catch (Exception $exp) {
        echo $exp;
        $m_dbConn->rollback();
    }
}   



					 
                   
                 
?>