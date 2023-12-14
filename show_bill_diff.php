<?php 
   header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
   header("Cache-Control: post-check=0, pre-check=0", false);
   header("Pragma: no-cache");
   include_once "classes/include/dbop.class.php";
   include_once("classes/dbconst.class.php");
   include "classes/include/fetch_data.php";
   include_once "classes/utility.class.php";
   include_once("classes/bill_diff.class.php");
   $m_dbConn = new dbop();
   $m_dbConnRoot = new dbop(true);
   $objFetchData = new FetchData($m_dbConn);
   $m_objUtility = new utility($dbConn);
   $obj_bill_diff= new Bill_Diff($m_dbConn, $m_dbConnRoot);
   $objFetchData->GetSocietyDetails($_SESSION['society_id']);
  

   $compare_1=array(
                    'year_id_1'=>$_REQUEST['year_id_1'],
                    'period_id_1'=>$_REQUEST['period_id_1'],
                    'bill_type_1'=>$_REQUEST['bill_type_1']
                   );
   
   $compare_2=array(
                    'year_id_2'=>$_REQUEST['year_id_2'],
                    'period_id_2'=>$_REQUEST['period_id_2'],
                    'bill_type_2'=>$_REQUEST['bill_type_2']
                   );
   $addition_request=array(
                    'skip_interest'=>isset($_REQUEST['skip_interest'])? 1 : 0,
                    'skip_gst'=>isset($_REQUEST['skip_gst'])? 1: 0,
                    'only_diff'=>isset($_REQUEST['only_diff'])? 1: 0
                   );
   $compare_bill=$obj_bill_diff->compare_bill($compare_1,$compare_2,$addition_request);

   $BillForA = $objFetchData->GetBillFor($_REQUEST["period_id_1"]);
   $BillFor_BillA = $m_objUtility->displayFormatBillFor($BillForA);

   $BillForB = $objFetchData->GetBillFor($_REQUEST["period_id_2"]);
   $BillFor_BillB = $m_objUtility->displayFormatBillFor($BillForB);
   
// echo "<pre>";
// print_r($addition_request);
   
   ?>
<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
      <script type="text/javascript" src="js/validate.js"></script>
      <script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
      <script type="text/javascript" src="js/ajax_new.js"></script>
      <title>W2S Bill Compare</title>
   </head>
   <body>
      <center>
         <div  class="row" id="bill_main" style="width:90%;">
            <div style="border:1px solid black;">
                <div id="bill_header" style="text-align:center;">
            <div id="society_name" style="font-weight:bold; font-size:16px;"><?php echo $objFetchData->objSocietyDetails->sSocietyName; ?></div>
            <!--<div id="society_type" style="font-weight:bold; font-size:20px;">PREMISES CO-OPERATIVE SOCIETY LTD.</div>-->
            <div id="society_reg" style="font-size:14px;"><?php if($objFetchData->objSocietyDetails->sSocietyRegNo <> "")
            {
               echo "Registration No. ".$objFetchData->objSocietyDetails->sSocietyRegNo; 
            }
            ?></div>
            <div id="society_address"; style="font-size:12px;"><?php echo $objFetchData->objSocietyDetails->sSocietyAddress; ?></div>
            <?php if($objFetchData->objSocietyDetails->sSocietyGSTINNo <> '')
         { ?>
            <div id="society_gstin"; style="font-size:12px;"><span>GSTIN No :&nbsp;&nbsp;</span><b><?php echo $objFetchData->objSocietyDetails->sSocietyGSTINNo; ?></b></div>
            <?php }?>
        </div>
               <div id="bill_subheader" style="text-align:center;padding: 5px;border-top:1px solid black;border-bottom:1px solid black;">
                  <div style="font-weight:bold; font-size:14px;"> Bill Difference of  <?php echo $BillFor_BillA;?>  compared to <?php echo $BillFor_BillB; ?> </div>
               </div>
            </div>
            <br>
            <div>
              <?php

              if(count($compare_bill)>0){

              foreach($compare_bill as $bill_diff){ 

                $objFetchData->GetMemberDetails($bill_diff["UnitID_A"]);
                $unitText = $objFetchData->getUnitPresentation($bill_diff["UnitID_A"]);
                $showInBillDetails = $objFetchData->GetFieldsToShowInBill($bill_diff["UnitID_A"]);
                $show_wing =$showInBillDetails[0]["show_wing"];
                $show_floor =$showInBillDetails[0]["show_floor"];
                $wing_areaDetails = $objFetchData->getWing_AreaDetails($bill_diff["UnitID_A"]);
               
               ?>


               <div class="diff_detail" >
                  <table style="width:100%;">
                     <tbody>
                        <tr>
                           <td style="width:15%;" >Name :</td>
                           <td id="owner_name" style="font-weight:bold;" colspan="5"> <?php echo $objFetchData->objMemeberDetails->sMemberName;?></td>
                        </tr>
                        <tr>
                           <td id="owner_unit" style="width:15%;"><?php echo $unitText;?> :</td>
                           <td style="font-weight:bold;width: 15%;"><?php echo $objFetchData->objMemeberDetails->sUnitNumber; ?></td>
                           <?php if($show_floor == false && $show_wing == false){ ?>
                                   <td>&nbsp;</td>
                                   <td>&nbsp;</td>
                                   <td>&nbsp;</td>
                                   <td>&nbsp;</td>
                           <?php } else { ?>
                           <?php if($show_floor) { ?>
                                   <td style="width:15%;">Floor No :</td>
                                   <td style="width:15%;"><?php echo $wing_areaDetails[0]['floor_no'] ?></td>
                           <?php if(!$show_wing) { ?>
                                   <td>&nbsp;</td>
                                   <td>&nbsp;</td>
                           <?php } } ?>
                           <?php if($show_wing) { ?> 
                                  <td style="width:15%;">Wing :</td>
                                  <td style="width:15%;"><?php echo $wing_areaDetails[0]['wing'] ?></td>
                           <?php }  } ?>
                         
                        </tr>
                     </tbody>
                  </table>
                  <br>
                  <div class="row">
                     <div class="col-md-6" style="padding-right: 0px;">
                        <table>
                            <tbody>
                               <tr>
                                  <td><span>Bill No : </span><span><?php echo $bill_diff['BillNumber_A']; ?></span></td>
                                  <td><span>Bill Date : </span><span><?php echo $bill_diff['BillDate_A']; ?></span></td>
                               </tr>
                            </tbody>
                         </table>
                        <table >
                           <thead>
                              <tr>
                                 <th>Particulars of Charges</th>
                                 <th>Amount(Rs.)</th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php 
                               $Voucher_Details_A=$bill_diff['Voucher_Details_A'];
                               foreach($Voucher_Details_A as $Details_A)
                               { ?>

                              <tr>
                                 <td><?php echo $Details_A['ledger_name'] ?></td>
                                 <td><?php echo $Details_A['Credit'] ?></td>
                              </tr>

                              <?php } ?>
                             
                           </tbody>
                        </table>
                     </div>
                     <div class="col-md-6" style="padding-left: 0px;">
                         <table>
                            <tbody>
                               <tr>
                                <td><span>Bill No : </span><span><?php echo $bill_diff['BillNumber_B']; ?></span></td>
                                  <td><span>Bill Date : </span><span><?php echo $bill_diff['BillDate_B']; ?></span></td>
                               </tr>
                            </tbody>
                         </table>
                        <table >
                           <thead>
                              <tr>
                                 <th>Particulars of Charges</th>
                                 <th>Amount(Rs.)</th>
                              </tr>
                           </thead>
                           <tbody>
                             <?php 
                               $Voucher_Details_B=$bill_diff['Voucher_Details_B'];
                               foreach($Voucher_Details_B as $Details_B)
                               { ?>

                              <tr>
                                 <td><?php echo $Details_B['ledger_name'] ?></td>
                                 <td><?php echo $Details_B['Credit'] ?></td>
                              </tr>

                              <?php } ?>
                           </tbody>
                        </table>
                     </div>
                  </div>
               </div>
               <br>
              <?php  }
              }
              else
              {?>
               <div style="border: 1px solid black;"><b style="color:red;">There is not any difference in bills</b></div>
             <?php }
             ?>
               
            </div>
         </div>
      </center>
   </body>
</html>
<style type="text/css">
   table, td, th {
   border: 1px solid black;
   padding-left: 5px;
   }
   table {
   width: 100%;
   border-collapse: collapse;
   }
   .diff_detail{
      border: 1px solid;
   }
</style>