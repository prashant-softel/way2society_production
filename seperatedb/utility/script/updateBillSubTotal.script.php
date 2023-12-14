<?php


include_once("../../../classes/include/dbop.class.php");

include_once("../../../classes/dbconst.class.php");
include_once("../../../classes/genbill.class.php");

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

        $obj_genBill = new genbill($m_dbConn);

        $debug_trace = 1;

        if ($m_dbConn->isConnected == false) {
            echo ' .....Connection Failed';
        } else {

            $selectDefaultLedger = "SELECT APP_DEFAULT_INTEREST_ON_PRINCIPLE_DUE, APP_DEFAULT_LEDGER_ROUND_OFF, APP_DEFAULT_IGST, APP_DEFAULT_CGST, APP_DEFAULT_SGST, APP_DEFAULT_CESS, APP_DEFAULT_ADJUSTMENT_CREDIT FROM `appdefault`";
            
            $defaultLedgerDetail = $m_dbConn->select($selectDefaultLedger);

            $skipLedgerIDArr = $defaultLedgerDetail[0];

            

            $skipLedgerIdString = implode(', ',  $defaultLedgerDetail[0]);

            $sqlQuery = "SELECT `UnitID`, `BillSubTotal`, `AdjustmentCredit`,`BillTax`, `IGST`,`CGST`,`SGST`,`CESS`,`BillInterest`, `CurrentBillAmount`, `PrincipalArrears`, `InterestArrears`, `TotalBillPayable`,`PeriodID`,`BillType`, b.ID FROM `billdetails` as b JOIN (SELECT RefNo, Sum(Credit) as Credit FROM voucher WHERE VoucherTypeID = 1 and `To` != '' AND `To` NOT IN(".$skipLedgerIdString.") group by voucherNo) as v ON b.id = v.RefNo WHERE b.BillSubTotal != v.Credit limit 100";


            $allBillDetails = $m_dbConn->select($sqlQuery);

            if($debug_trace){

                echo "<pre>";
                print_r($skipLedgerIDArr);
                print_r($allBillDetails);
                echo "</pre>";

                //die();
            }
            
            if(count($allBillDetails) != 0){

            foreach ($allBillDetails as $key => $billDetails) {
                
                    
                    $CurrentBillAmount = $billDetails['BillSubTotal'] + $billDetails['AdjustmentCredit'] + $billDetails['BillTax'] + $billDetails['BillInterest'];
                    $UnitID = $billDetails['UnitID'];
                    $PeriodID = $billDetails['PeriodID'];
                    $InterestArrears =  $billDetails['InterestArrears'];
                    $PrincipalArrears = $billDetails['PrincipalArrears'];
                    $AdjustmentCredit = $billDetails['AdjustmentCredit'];
                    $BillType = $billDetails['BillType'];
                    
                    $CurrInterestAmount = $billDetails['BillInterest'];
                    
                    $BillRegisterData = $obj_genBill->objFetchData->GetValuesFromBillRegister($UnitID, $PeriodID, $BillType);
                    
                    $data = array();
                    for($iVal = 0; $iVal < sizeof($BillRegisterData) ; $iVal++) 
                    {
                        $BillDetails = $BillRegisterData[$iVal]["value"];
                        $LedgerID = $BillDetails->sHeader;
                        $LedgerAmount = $BillDetails->sHeaderAmount;
                        $LedgerVoucherID = $BillDetails->sVoucherID;
                        $TaxableFlag = $BillDetails->Taxable;
                        $Taxable_no_threshold = $BillDetails->Taxable_no_threshold;
                        if(!in_array($LedgerID, $skipLedgerIDArr))
                        {
                            $HeaderAndAmount = array("Head"=>$LedgerID, "Amt"=> $LedgerAmount, "HeadOldValue"=> $LedgerAmount, "VoucherID"=>$LedgerVoucherID, "Taxable" => $TaxableFlag, "Taxable_no_threshold" => $Taxable_no_threshold);

                            array_push($data, $HeaderAndAmount);
                        }
                    }
                    if(sizeof($data) > 0)
                    {
                        $obj_genBill->BillDetailsUpdate($data, $UnitID, $PeriodID, $CurrInterestAmount, $InterestArrears, $PrincipalArrears, $AdjustmentCredit, $BillType);
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



					 
                   
                 
?>